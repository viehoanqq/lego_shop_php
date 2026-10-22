<?php
require_once __DIR__ . '/../models/RefundModel.php';

class AdminRefundController extends Controller {
    private $refundModel;
    private $limit = 8;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admin/login"); exit;
        }
        $this->refundModel = $this->model('RefundModel');
    }

    public function index() {
        $filters = [
            'keyword' => trim($_GET['keyword'] ?? ''),
            'status'  => $_GET['status'] ?? 'all',
            'type'    => $_GET['type'] ?? 'all',
        ];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $total = $this->refundModel->countAdminList($filters);

        $this->view('admin/refunds', [
            'title'       => 'Yêu cầu hoàn tiền',
            'refunds'     => $this->refundModel->getAdminList($filters, $this->limit, ($page - 1) * $this->limit),
            'counts'      => $this->refundModel->countByStatus(),
            'filters'     => $filters,
            'currentPage' => $page,
            'totalPages'  => (int)ceil($total / $this->limit),
        ]);
    }

    public function detail($id = 0) {
        $refund = $this->refundModel->getDetail((int)$id);
        if (!$refund) { header('Location: /lego_shop_php/admin/refund'); exit; }

        $orderModel = $this->model('OrderModel');
        $this->view('admin/refund_detail', [
            'title'   => 'Yêu cầu hoàn tiền #' . $refund['id'],
            'refund'  => $refund,
            'order'   => $orderModel->getOrderById($refund['order_id']),
            'items'   => $orderModel->getOrderItems($refund['order_id']),
            'history' => $orderModel->getOrderHistory($refund['order_id']),
            'qr_url'  => RefundModel::buildRefundQrUrl($refund),
            'banks'   => RefundModel::BANKS,
        ]);
    }

    private function back($id, $msg = null, $error = null) {
        if ($msg) set_flash_message('msg', $msg);
        if ($error) set_flash_message('error', $error);
        header('Location: /lego_shop_php/admin/refund/detail/' . (int)$id); exit;
    }

    private function loadForAction($id) {
        $refund = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $this->refundModel->getDetail((int)$id) : null;
        if (!$refund) { header('Location: /lego_shop_php/admin/refund'); exit; }
        return $refund;
    }

    public function approve($id = 0) {
        $refund = $this->loadForAction($id);
        $note = trim($_POST['admin_note'] ?? '');
        if ($this->refundModel->approve($refund, $_SESSION['admin_id'], $note)) {
            $this->back($id, 'Đã duyệt yêu cầu.');
        }
        $this->back($id, null, 'Yêu cầu không còn ở trạng thái chờ xử lý!');
    }

    public function reject($id = 0) {
        $refund = $this->loadForAction($id);
        $note = trim($_POST['admin_note'] ?? '');
        if ($refund['type'] !== 'return') {
            $this->back($id, null, 'Không thể từ chối yêu cầu này.');
        }
        if (mb_strlen($note) < 5) {
            $this->back($id, null, 'Vui lòng nhập lý do từ chối.');
        }
        if ($this->refundModel->reject($refund, $_SESSION['admin_id'], $note)) {
            $this->back($id, 'Đã từ chối yêu cầu trả hàng.');
        }
        $this->back($id, null, 'Không thể từ chối yêu cầu này!');
    }

    public function complete($id = 0) {
        $refund = $this->loadForAction($id);
        if (!RefundModel::hasBankInfo($refund)) {
            $this->back($id, null, 'Chưa có tài khoản nhận tiền.');
        }
        $note = trim($_POST['admin_note'] ?? '');
        if ($this->refundModel->complete($refund, $_SESSION['admin_id'], $note)) {
            $msg = 'Đã hoàn tiền.';
            $this->back($id, $msg);
        }
        $this->back($id, null, 'Không thể hoàn tất yêu cầu này.');
    }

    // Admin nhập/sửa tài khoản nhận tiền (VD: khách gọi điện báo hoặc đơn do admin hủy)
    public function updateBank($id = 0) {
        $refund = $this->loadForAction($id);
        $bank_name = trim($_POST['bank_name'] ?? '');
        $number = preg_replace('/\s+/', '', $_POST['bank_account_number'] ?? '');
        $name = mb_strtoupper(trim($_POST['bank_account_name'] ?? ''));

        $error = RefundModel::validateBankInfo($bank_name, $number, $name);
        if ($error) $this->back($id, null, $error);
        if (!in_array($refund['status'], ['pending', 'approved'])) $this->back($id, null, 'Yêu cầu đã kết thúc, không thể sửa!');

        $this->refundModel->updateBankInfo($refund['id'], $bank_name, $number, $name);
        $this->back($id, 'Đã cập nhật tài khoản nhận tiền.');
    }
}
