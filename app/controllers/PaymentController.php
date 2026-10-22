<?php
require_once __DIR__ . '/../models/PaymentModel.php';

class PaymentController extends Controller {

    private function jsonResponse($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function index() {
        header("Location: /lego_shop_php/home");
        exit;
    }

    // =========================================================================
    // WEBHOOK NGÂN HÀNG: /lego_shop_php/payment/webhook
    // Hỗ trợ SePay (header "Authorization: Apikey <key>") và Casso (header "Secure-Token: <key>")
    // Có thể truyền ?key=<key> nếu dịch vụ không gửi được header
    // =========================================================================
    public function webhook() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
        }

        $paymentModel = $this->model('PaymentModel');
        $settings = $paymentModel->getSettings();
        // Key ưu tiên lấy từ file .env (PAYMENT_WEBHOOK_KEY); chỉ dùng key trong DB khi .env chưa đặt
        $expected_key = (string)(env('PAYMENT_WEBHOOK_KEY') ?: ($settings['payment_webhook_key'] ?? ''));

        $headers = array_change_key_case(function_exists('getallheaders') ? getallheaders() : [], CASE_LOWER);
        $auth = $headers['authorization'] ?? ($_SERVER['HTTP_AUTHORIZATION'] ?? '');
        $provided_key = trim(preg_replace('/^(Apikey|Bearer)\s+/i', '', $auth));
        if ($provided_key === '') $provided_key = $headers['secure-token'] ?? ($_GET['key'] ?? '');

        if ($expected_key === '' || !hash_equals($expected_key, (string)$provided_key)) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid JSON'], 400);
        }

        // Chuẩn hóa về danh sách giao dịch [{ref, amount, content}]
        $transactions = [];
        if (isset($payload['data']) && is_array($payload['data'])) {
            // Casso: { error: 0, data: [ { tid, amount, description, ... } ] }
            $provider = 'casso';
            $list = isset($payload['data'][0]) ? $payload['data'] : [$payload['data']];
            foreach ($list as $tx) {
                if ((int)($tx['amount'] ?? 0) <= 0) continue; // bỏ qua tiền ra
                $transactions[] = [
                    'ref' => (string)($tx['tid'] ?? $tx['id'] ?? ''),
                    'amount' => (int)$tx['amount'],
                    'content' => (string)($tx['description'] ?? ''),
                ];
            }
        } else {
            // SePay: { id, transferType: "in", transferAmount, content, referenceCode, ... }
            $provider = 'sepay';
            if (($payload['transferType'] ?? 'in') === 'in') {
                $transactions[] = [
                    'ref' => (string)($payload['id'] ?? $payload['referenceCode'] ?? ''),
                    'amount' => (int)($payload['transferAmount'] ?? 0),
                    'content' => (string)($payload['content'] ?? $payload['description'] ?? ''),
                ];
            }
        }

        $results = [];
        foreach ($transactions as $tx) {
            $order_id = PaymentModel::extractOrderId($tx['content']);
            try {
                $results[] = $paymentModel->confirmPayment($order_id, $tx['amount'], $provider, $tx['ref'] ?: null, $tx['content'], $raw);
            } catch (Exception $e) {
                $results[] = 'error';
            }
        }

        // Luôn trả 200 + success để dịch vụ không gửi lại liên tục
        $this->jsonResponse(['success' => true, 'results' => $results]);
    }

    // =========================================================================
    // API cho trang thanh toán hỏi trạng thái định kỳ: /payment/status?order_id=
    // =========================================================================
    public function status() {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Chưa đăng nhập'], 401);
        }

        $order_id = (int)($_GET['order_id'] ?? 0);
        $paymentModel = $this->model('PaymentModel');
        $paymentModel->expireUnpaidOrders($_SESSION['user_id']);

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id);
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy đơn hàng'], 404);
        }

        $this->jsonResponse([
            'success' => true,
            'status' => $order['status'],
            'payment_status' => $order['payment_status'],
        ]);
    }

    // =========================================================================
    // GIẢ LẬP THANH TOÁN (chỉ khi bật payment_sandbox trong shop_settings)
    // Dùng để demo trên localhost, nơi ngân hàng không gọi webhook được
    // =========================================================================
    public function simulate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Yêu cầu không hợp lệ'], 400);
        }

        $paymentModel = $this->model('PaymentModel');
        $settings = $paymentModel->getSettings();
        if ((int)($settings['payment_sandbox'] ?? 0) !== 1) {
            $this->jsonResponse(['success' => false, 'message' => 'Chế độ giả lập đang tắt'], 403);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $order_id = (int)($input['order_id'] ?? 0);

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id);
        if (!$order || $order['user_id'] != $_SESSION['user_id'] || $order['payment_method'] !== 'vietqr') {
            $this->jsonResponse(['success' => false, 'message' => 'Không tìm thấy đơn hàng'], 404);
        }

        $ref = 'SANDBOX' . time() . $order_id;
        $result = $paymentModel->confirmPayment($order_id, (int)$order['total_amount'], 'sandbox', $ref, $order['payment_code'], '');

        $messages = [
            'paid' => 'Giả lập thanh toán thành công!',
            'already_paid' => 'Đơn hàng đã được thanh toán trước đó.',
            'order_cancelled' => 'Đơn hàng đã bị hủy do quá hạn thanh toán.',
        ];
        $this->jsonResponse(['success' => $result === 'paid', 'result' => $result, 'message' => $messages[$result] ?? 'Không thể xác nhận thanh toán']);
    }
}
