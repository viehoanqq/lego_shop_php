<?php
require_once __DIR__ . '/../models/PaymentModel.php';

class AdminPaymentController extends Controller {
    private $limit = 10;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admin/login"); exit;
        }
    }

    // Nhật ký giao dịch VietQR (webhook ngân hàng, giả lập, xác nhận thủ công)
    public function index() {
        $paymentModel = $this->model('PaymentModel');
        $filters = [
            'keyword' => trim($_GET['keyword'] ?? ''),
            'status'  => $_GET['status'] ?? 'all',
        ];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $total = $paymentModel->countTransactions($filters);

        $this->view('admin/payments', [
            'title'        => 'Giao dịch thanh toán',
            'transactions' => $paymentModel->getTransactions($filters, $this->limit, ($page - 1) * $this->limit),
            'stats'        => $paymentModel->getTransactionStats(),
            'settings'     => $paymentModel->getSettings(),
            'filters'      => $filters,
            'currentPage'  => $page,
            'totalPages'   => (int)ceil($total / $this->limit),
        ]);
    }
}
