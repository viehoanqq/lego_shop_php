<?php
require_once __DIR__ . '/../models/VoucherModel.php';

class AdminVoucherController extends Controller {
    private $voucherModel;
    private $limit = 8;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admin/login"); exit;
        }
        $this->voucherModel = $this->model('VoucherModel');
    }

    public function index() {
        $filters = [
            'keyword' => trim($_GET['keyword'] ?? ''),
            'state'   => $_GET['state'] ?? 'all',
        ];
        $page = max(1, (int)($_GET['page'] ?? 1));
        $total = $this->voucherModel->countAdminVouchers($filters['keyword'], $filters['state']);

        $this->view('admin/vouchers', [
            'title'       => 'Quản lý mã giảm giá',
            'vouchers'    => $this->voucherModel->getAdminVouchers($filters['keyword'], $filters['state'], $this->limit, ($page - 1) * $this->limit),
            'filters'     => $filters,
            'currentPage' => $page,
            'totalPages'  => (int)ceil($total / $this->limit),
            'totalItems'  => $total,
            'counts'      => [
                'running'  => $this->voucherModel->countAdminVouchers('', 'running'),
                'upcoming' => $this->voucherModel->countAdminVouchers('', 'upcoming'),
                'ended'    => $this->voucherModel->countAdminVouchers('', 'ended'),
                'inactive' => $this->voucherModel->countAdminVouchers('', 'inactive'),
            ],
            'is_form'     => false,
        ]);
    }

    private function showForm($voucher = null) {
        // Dữ liệu cũ khi lưu bị lỗi -> điền lại form
        $old = $_SESSION['voucher_old'] ?? null;
        unset($_SESSION['voucher_old']);

        $this->view('admin/vouchers', [
            'title'   => $voucher ? 'Sửa mã giảm giá' : 'Thêm mã giảm giá',
            'voucher' => $voucher,
            'form'    => $old ?: $voucher,
            'usages'  => $voucher ? $this->voucherModel->getUsages($voucher['id']) : [],
            'is_form' => true,
        ]);
    }

    public function add() {
        $this->showForm();
    }

    public function edit($id = 0) {
        $voucher = $this->voucherModel->getById((int)$id);
        if (!$voucher) { header('Location: /lego_shop_php/admin/voucher'); exit; }
        $this->showForm($voucher);
    }

    // Đọc + kiểm tra dữ liệu form. Trả về [data, error]
    private function readForm($exclude_id = null) {
        $toDate = function ($v) {
            $v = trim($v ?? '');
            return $v === '' ? null : date('Y-m-d H:i:s', strtotime($v));
        };
        $toIntOrNull = function ($v) {
            $v = trim((string)($v ?? ''));
            return $v === '' ? null : (int)$v;
        };

        $d = [
            'code'            => strtoupper(trim($_POST['code'] ?? '')),
            'description'     => trim($_POST['description'] ?? ''),
            'discount_type'   => ($_POST['discount_type'] ?? 'fixed') === 'percent' ? 'percent' : 'fixed',
            'discount_value'  => (int)($_POST['discount_value'] ?? 0),
            'max_discount'    => $toIntOrNull($_POST['max_discount'] ?? ''),
            'min_order_value' => max(0, (int)($_POST['min_order_value'] ?? 0)),
            'usage_limit'     => $toIntOrNull($_POST['usage_limit'] ?? ''),
            'per_user_limit'  => (int)($_POST['per_user_limit'] ?? 1),
            'start_date'      => $toDate($_POST['start_date'] ?? ''),
            'end_date'        => $toDate($_POST['end_date'] ?? ''),
            'status'          => isset($_POST['status']) ? 'active' : 'inactive',
        ];
        if ($d['discount_type'] === 'fixed') $d['max_discount'] = null;

        $error = null;
        if (!preg_match('/^[A-Z0-9_-]{3,30}$/', $d['code'])) {
            $error = 'Mã chỉ gồm chữ in hoa, số, "-" hoặc "_" (3-30 ký tự)!';
        } elseif ($this->voucherModel->codeExists($d['code'], $exclude_id)) {
            $error = "Mã {$d['code']} đã tồn tại!";
        } elseif ($d['discount_type'] === 'percent' && ($d['discount_value'] < 1 || $d['discount_value'] > 100)) {
            $error = 'Phần trăm giảm phải từ 1 đến 100!';
        } elseif ($d['discount_type'] === 'fixed' && $d['discount_value'] < 1000) {
            $error = 'Số tiền giảm tối thiểu 1.000đ!';
        } elseif ($d['max_discount'] !== null && $d['max_discount'] < 1000) {
            $error = 'Mức giảm tối đa phải từ 1.000đ (hoặc để trống)!';
        } elseif ($d['usage_limit'] !== null && $d['usage_limit'] < 1) {
            $error = 'Tổng lượt dùng phải lớn hơn 0 (hoặc để trống = không giới hạn)!';
        } elseif ($d['per_user_limit'] < 1) {
            $error = 'Số lượt mỗi khách tối thiểu là 1!';
        } elseif ($d['start_date'] && $d['end_date'] && $d['end_date'] <= $d['start_date']) {
            $error = 'Ngày kết thúc phải sau ngày bắt đầu!';
        }
        return [$d, $error];
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /lego_shop_php/admin/voucher'); exit; }
        [$data, $error] = $this->readForm();
        if ($error) {
            $_SESSION['voucher_old'] = $data;
            set_flash_message('error', $error);
            header('Location: /lego_shop_php/admin/voucher/add'); exit;
        }
        if ($this->voucherModel->insert($data)) {
            set_flash_message('msg', 'Đã thêm mã ' . $data['code']);
        } else {
            set_flash_message('error', 'Lỗi hệ thống, không thể thêm mã!');
        }
        header('Location: /lego_shop_php/admin/voucher'); exit;
    }

    public function update($id = 0) {
        $id = (int)$id;
        $voucher = $this->voucherModel->getById($id);
        if (!$voucher || $_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /lego_shop_php/admin/voucher'); exit; }

        [$data, $error] = $this->readForm($id);
        // Đã có người dùng thì không cho đổi mã/kiểu giảm để lịch sử đơn hàng còn đúng nghĩa
        if (!$error && $this->voucherModel->isUsed($id) && ($data['code'] !== $voucher['code'] || $data['discount_type'] !== $voucher['discount_type'])) {
            $error = 'Mã đã có đơn sử dụng, không thể đổi tên mã hoặc kiểu giảm giá!';
        }
        if (!$error && $data['usage_limit'] !== null && $data['usage_limit'] < (int)$voucher['used_count']) {
            $error = "Tổng lượt dùng không được nhỏ hơn số lượt đã dùng ({$voucher['used_count']})!";
        }
        if ($error) {
            $_SESSION['voucher_old'] = $data;
            set_flash_message('error', $error);
            header('Location: /lego_shop_php/admin/voucher/edit/' . $id); exit;
        }

        if ($this->voucherModel->update($id, $data)) {
            set_flash_message('msg', 'Đã cập nhật mã ' . $data['code']);
        } else {
            set_flash_message('error', 'Lỗi hệ thống, không thể cập nhật!');
        }
        header('Location: /lego_shop_php/admin/voucher'); exit;
    }

    public function toggle($id = 0) {
        if ($this->voucherModel->toggleStatus((int)$id)) {
            set_flash_message('msg', 'Đã đổi trạng thái mã giảm giá');
        }
        header('Location: /lego_shop_php/admin/voucher'); exit;
    }

    public function delete($id = 0) {
        $id = (int)$id;
        if ($this->voucherModel->isUsed($id)) {
            $this->voucherModel->deactivate($id);
            set_flash_message('msg', 'Mã đã có đơn sử dụng, đã chuyển sang Ngừng áp dụng.');
        } elseif ($this->voucherModel->deleteForever($id)) {
            set_flash_message('msg', 'Đã xóa mã giảm giá');
        } else {
            set_flash_message('error', 'Không thể xóa mã giảm giá!');
        }
        header('Location: /lego_shop_php/admin/voucher'); exit;
    }
}
