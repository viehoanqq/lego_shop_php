<?php
require_once __DIR__ . '/../models/VoucherModel.php';
require_once __DIR__ . '/../models/PaymentModel.php';
require_once __DIR__ . '/../models/RefundModel.php';

class ProfileController extends Controller {
    
    // Hàm khởi tạo
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_account_id'])) {
            header("Location: /lego_shop_php/account/login");
            exit;
        }
    }

    // 1. Trang thông tin cá nhân
    public function index() {
        $userModel = $this->model('UserModel');
        $user_info = $userModel->getUserProfile($_SESSION['user_account_id']);

        $msg = $_SESSION['profile_msg'] ?? null;
        $msg_type = $_SESSION['profile_msg_type'] ?? null;
        unset($_SESSION['profile_msg'], $_SESSION['profile_msg_type']);

        $this->view('user/profile/info', [
            'title' => 'Thông tin cá nhân',
            'active_tab' => 'info',
            'user_info' => $user_info, 
            'msg' => $msg,
            'msg_type' => $msg_type
        ]);
    }

    // 2. Xử lý Cập nhật
    public function updateInfo() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            
            // --- 1. XÁC THỰC RỖNG & ĐỊNH DẠNG ---
            if (empty($fullname) || empty($phone)) {
                $_SESSION['profile_msg'] = "Vui lòng nhập đầy đủ Họ tên và Số điện thoại!";
                $_SESSION['profile_msg_type'] = "error";
                header("Location: /lego_shop_php/profile/index");
                exit;
            }

            if (strlen($fullname) < 2 || strlen($fullname) > 50) {
                $_SESSION['profile_msg'] = "Họ tên phải từ 2 đến 50 ký tự!";
                $_SESSION['profile_msg_type'] = "error";
                header("Location: /lego_shop_php/profile/index");
                exit;
            }

            if (!preg_match('/^(0[3|5|7|8|9])+([0-9]{8})$/', $phone)) {
                $_SESSION['profile_msg'] = "Số điện thoại không hợp lệ!";
                $_SESSION['profile_msg_type'] = "error";
                header("Location: /lego_shop_php/profile/index");
                exit;
            }

            $userModel = $this->model('UserModel');

            // --- 2. [TÍNH NĂNG MỚI] KIỂM TRA XEM CÓ THAY ĐỔI GÌ KHÔNG ---
            $currentInfo = $userModel->getUserProfile($_SESSION['user_account_id']);
            if ($currentInfo['fullname'] === $fullname && $currentInfo['phone'] === $phone) {
                $_SESSION['profile_msg'] = "Bạn chưa thay đổi thông tin nào!";
                $_SESSION['profile_msg_type'] = "error"; // Sẽ hiển thị Toast màu đỏ
                header("Location: /lego_shop_php/profile/index");
                exit;
            }

            // --- 3. KIỂM TRA TRÙNG SĐT & TIẾN HÀNH LƯU ---
            if ($userModel->checkPhoneExists($phone, $_SESSION['user_account_id'])) {
                $_SESSION['profile_msg'] = "Số điện thoại này đã được sử dụng bởi tài khoản khác!";
                $_SESSION['profile_msg_type'] = "error";
            } else {
                $result = $userModel->updateUserProfile($_SESSION['user_account_id'], $fullname, $phone);

                if ($result) {
                    $_SESSION['user_fullname'] = $fullname; 
                    $_SESSION['profile_msg'] = "Cập nhật thông tin thành công!";
                    $_SESSION['profile_msg_type'] = "success";
                } else {
                    $_SESSION['profile_msg'] = "Hệ thống bận, vui lòng thử lại sau!";
                    $_SESSION['profile_msg_type'] = "error";
                }
            }
            
            header("Location: /lego_shop_php/profile/index");
            exit;
        }
    }

    // 3. Trang Đơn hàng
    // 3.5 Trang địa chỉ
    public function addresses() {
        // Gọi Model lấy danh sách địa chỉ
        $userModel = $this->model('UserModel');
        
        // LƯU Ý: Lấy từ $_SESSION['user_id'] (id của bảng users), KHÔNG PHẢI user_account_id
        $addresses = $userModel->getUserAddresses($_SESSION['user_id']);

        // Hứng thông báo nếu có (phục vụ cho chức năng Thêm/Sửa/Xóa sau này)
        $msg = $_SESSION['address_msg'] ?? null;
        $msg_type = $_SESSION['address_msg_type'] ?? null;
        unset($_SESSION['address_msg'], $_SESSION['address_msg_type']);

        $this->view('user/profile/addresses', [
            'title' => 'Địa chỉ của tôi',
            'active_tab' => 'addresses',
            'addresses' => $addresses, // Đẩy mảng dữ liệu ra View
            'msg' => $msg,
            'msg_type' => $msg_type
        ]);
    }
    // [MỚI THÊM] - Xử lý Form Thêm địa chỉ
    public function addAddress() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['receiver_name'] ?? '');
            $phone = trim($_POST['receiver_phone'] ?? '');
            $street = trim($_POST['street'] ?? '');
            $city = trim($_POST['city'] ?? '');

            // Xác thực cơ bản
            if (empty($name) || empty($phone) || empty($street) || empty($city)) {
                $_SESSION['address_msg'] = "Vui lòng nhập đầy đủ thông tin bắt buộc!";
                $_SESSION['address_msg_type'] = "error";
            } elseif (!preg_match('/^(0[3|5|7|8|9])+([0-9]{8})$/', $phone)) {
                $_SESSION['address_msg'] = "Số điện thoại không hợp lệ!";
                $_SESSION['address_msg_type'] = "error";
            } else {
                $userModel = $this->model('UserModel');
                $result = $userModel->addUserAddress($_SESSION['user_id'], $_POST);

                if ($result) {
                    $_SESSION['address_msg'] = "Thêm địa chỉ mới thành công!";
                    $_SESSION['address_msg_type'] = "success";
                } else {
                    $_SESSION['address_msg'] = "Hệ thống bận, vui lòng thử lại!";
                    $_SESSION['address_msg_type'] = "error";
                }
            }
            
            // Xong việc thì quay lại trang danh sách địa chỉ
            header("Location: /lego_shop_php/profile/addresses");
            exit;
        }
    }
    // 4. Trang Đổi mật khẩu
    public function password() {
        $msg = $_SESSION['profile_msg'] ?? null;
        $msg_type = $_SESSION['profile_msg_type'] ?? null;
        unset($_SESSION['profile_msg'], $_SESSION['profile_msg_type']);

        $this->view('user/profile/password', [
            'title' => 'Đổi mật khẩu',
            'active_tab' => 'password',
            'msg' => $msg,
            'msg_type' => $msg_type
        ]);
    }
    // [MỚI] - Xử lý Nút "Thiết lập mặc định"
    public function setDefaultAddress() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['address_id'])) {
            $userModel = $this->model('UserModel');
            if ($userModel->setDefaultAddress($_POST['address_id'], $_SESSION['user_id'])) {
                $_SESSION['address_msg'] = "Đã thay đổi địa chỉ mặc định!";
                $_SESSION['address_msg_type'] = "success";
            }
        }
        header("Location: /lego_shop_php/profile/addresses");
        exit;
    }

    // [MỚI] - Xử lý Form Sửa địa chỉ
    public function editAddress() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['address_id'])) {
            $address_id = $_POST['address_id'];
            $phone = trim($_POST['receiver_phone'] ?? '');

            if (!preg_match('/^(0[3|5|7|8|9])+([0-9]{8})$/', $phone)) {
                $_SESSION['address_msg'] = "Số điện thoại không hợp lệ!";
                $_SESSION['address_msg_type'] = "error";
            } else {
                $userModel = $this->model('UserModel');
                if ($userModel->updateUserAddress($address_id, $_SESSION['user_id'], $_POST)) {
                    $_SESSION['address_msg'] = "Cập nhật địa chỉ thành công!";
                    $_SESSION['address_msg_type'] = "success";
                } else {
                    $_SESSION['address_msg'] = "Lỗi hệ thống, vui lòng thử lại!";
                    $_SESSION['address_msg_type'] = "error";
                }
            }
            header("Location: /lego_shop_php/profile/addresses");
            exit;
        }
    }
    // [MỚI] - Xử lý chức năng Xóa địa chỉ
    public function deleteAddress() {
        if (isset($_GET['id'])) {
            $address_id = $_GET['id'];
            $userModel = $this->model('UserModel');

            if ($userModel->deleteUserAddress($address_id, $_SESSION['user_id'])) {
                $_SESSION['address_msg'] = "Đã xóa địa chỉ thành công!";
                $_SESSION['address_msg_type'] = "success";
            } else {
                $_SESSION['address_msg'] = "Lỗi: Không thể xóa địa chỉ mặc định!";
                $_SESSION['address_msg_type'] = "error";
            }
        }
        
        // Xóa xong thì quay lại trang danh sách
        header("Location: /lego_shop_php/profile/addresses");
        exit;
    }

    // [MỚI] - Xử lý Form Đổi mật khẩu Khách hàng
    public function actionUpdatePassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $old_pass = $_POST['old_password'] ?? '';
            $new_pass = $_POST['new_password'] ?? '';
            $confirm_pass = $_POST['confirm_password'] ?? '';

            if (empty($old_pass) || empty($new_pass) || empty($confirm_pass)) {
                $_SESSION['profile_msg'] = "Vui lòng nhập đầy đủ các trường mật khẩu!";
                $_SESSION['profile_msg_type'] = "error";
            } elseif ($new_pass !== $confirm_pass) {
                $_SESSION['profile_msg'] = "Mật khẩu xác nhận không khớp!";
                $_SESSION['profile_msg_type'] = "error";
            } elseif (strlen($new_pass) < 6) {
                $_SESSION['profile_msg'] = "Mật khẩu mới phải từ 6 ký tự trở lên!";
                $_SESSION['profile_msg_type'] = "error";
            } else {
                $userModel = $this->model('AccountModel');
                
                // Kiểm tra mật khẩu cũ
                if ($userModel->verifyOldPassword($_SESSION['user_account_id'], $old_pass)) {
                    // Đổi mật khẩu
                    $userModel->updatePassword($_SESSION['user_account_id'], $new_pass);
                    $_SESSION['profile_msg'] = "Đổi mật khẩu thành công!";
                    $_SESSION['profile_msg_type'] = "success";
                } else {
                    $_SESSION['profile_msg'] = "Mật khẩu hiện tại không chính xác!";
                    $_SESSION['profile_msg_type'] = "error";
                }
            }
            
            header("Location: /lego_shop_php/profile/password");
            exit;
        }
    }
    public function addAddressAjax() {
    // Tắt hiển thị lỗi trực tiếp ra màn hình để tránh làm bẩn chuỗi JSON
    ini_set('display_errors', 0); 
    header('Content-Type: application/json');

    try {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $model = $this->model('UserModel');
        
        // Thực hiện lưu
        $new_id = $model->saveAddress($user_id, $input); 
        
        if ($new_id) {
            echo json_encode(['success' => true, 'new_id' => $new_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể lưu địa chỉ vào Database']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit; // Luôn exit để đảm bảo không có HTML thừa từ Header/Footer chèn vào
}


    // =========================================================================
    // 6. HIỂN THỊ TRANG LỊCH SỬ ĐƠN HÀNG CỦA USER
    // =========================================================================
    public function orders() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        // Phải đăng nhập mới xem được lịch sử
        if (!isset($_SESSION['user_id'])) {
            header("Location: /lego_shop_php/account/login");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // Tự hủy các đơn VietQR quá hạn thanh toán trước khi hiển thị
        $paymentModel = $this->model('PaymentModel');
        $paymentModel->expireUnpaidOrders($user_id);

        // Gọi Model để lấy tất cả đơn hàng của user này
        $orderModel = $this->model('OrderModel');
        // Đảm bảo bạn đã viết hàm getOrdersByUserId trong OrderModel nhé!
        $orders = $orderModel->getOrdersByUserId($user_id);

        // Trỏ đến view orders.php mà bạn vừa tạo
        $this->view('/user/profile/orders', [
            'title' => 'Lịch sử mua hàng',
            'active_tab' => 'orders',
            'orders' => $orders,
            'banks' => RefundModel::BANKS
        ]);
    }

    // =========================================================================
    // KHO VOUCHER CỦA KHÁCH
    // =========================================================================
    public function vouchers() {
        $voucherModel = $this->model('VoucherModel');
        $this->view('user/profile/vouchers', [
            'title' => 'Mã giảm giá',
            'active_tab' => 'vouchers',
            'vouchers' => $voucherModel->getAvailableVouchers($_SESSION['user_id'])
        ]);
    }

    // =========================================================================
    // DANH SÁCH YÊU CẦU HOÀN TIỀN
    // =========================================================================
    public function refunds() {
        $refundModel = $this->model('RefundModel');
        $this->view('user/profile/refunds', [
            'title' => 'Yêu cầu hoàn tiền',
            'active_tab' => 'refunds',
            'refunds' => $refundModel->getByUser($_SESSION['user_id'])
        ]);
    }

    // =========================================================================
    // API: KHÁCH BỔ SUNG TÀI KHOẢN NHẬN TIỀN HOÀN (VD: đơn do cửa hàng hủy)
    // =========================================================================
    public function updateRefundBankAjax() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $order_id = (int)($input['order_id'] ?? 0);
        $bank_name = trim($input['bank_name'] ?? '');
        $number = preg_replace('/\s+/', '', $input['bank_account_number'] ?? '');
        $holder = mb_strtoupper(trim($input['bank_account_name'] ?? ''));

        $refundModel = $this->model('RefundModel');
        $refund = $refundModel->getLatestByOrder($order_id);
        if (!$refund || $refund['user_id'] != $_SESSION['user_id'] || !in_array($refund['status'], ['pending', 'approved'])) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy yêu cầu hoàn tiền đang xử lý!']);
            exit;
        }
        $error = RefundModel::validateBankInfo($bank_name, $number, $holder);
        if ($error) {
            echo json_encode(['success' => false, 'message' => $error]);
            exit;
        }
        $refundModel->updateBankInfo($refund['id'], $bank_name, $number, $holder);
        echo json_encode(['success' => true, 'message' => 'Đã cập nhật tài khoản nhận tiền hoàn!']);
        exit;
    }

    // =========================================================================
    // API: GỬI YÊU CẦU TRẢ HÀNG / HOÀN TIỀN CHO ĐƠN ĐÃ GIAO (multipart/form-data)
    // =========================================================================
    public function requestRefundAjax() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ!']);
            exit;
        }

        $order_id = (int)($_POST['order_id'] ?? 0);
        $reason = trim($_POST['reason'] ?? '');
        $bank_name = trim($_POST['bank_name'] ?? '');
        $bank_account_number = preg_replace('/\s+/', '', $_POST['bank_account_number'] ?? '');
        $bank_account_name = mb_strtoupper(trim($_POST['bank_account_name'] ?? ''));

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id);
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng!']);
            exit;
        }

        if (mb_strlen($reason) < 10) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng mô tả lý do trả hàng (tối thiểu 10 ký tự)!']);
            exit;
        }

        $error = RefundModel::validateBankInfo($bank_name, $bank_account_number, $bank_account_name);
        if ($error) {
            echo json_encode(['success' => false, 'message' => $error]);
            exit;
        }

        $refundModel = $this->model('RefundModel');
        $settings = $this->model('PaymentModel')->getSettings();
        $check = $refundModel->checkReturnEligibility($order, (int)($settings['refund_days'] ?? 7));
        if (!$check['ok']) {
            echo json_encode(['success' => false, 'message' => $check['message']]);
            exit;
        }

        // Ảnh minh chứng (không bắt buộc)
        $evidence = null;
        if (!empty($_FILES['evidence']) && $_FILES['evidence']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['evidence'];
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $mime = $file['error'] === UPLOAD_ERR_OK ? mime_content_type($file['tmp_name']) : '';
            if (!isset($allowed[$mime]) || $file['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'Ảnh minh chứng phải là JPG/PNG/WEBP và nhỏ hơn 5MB!']);
                exit;
            }
            $dir = __DIR__ . '/../../public/assets/images/refunds/';
            if (!is_dir($dir)) mkdir($dir, 0775, true);
            $evidence = 'refund_' . $order_id . '_' . time() . '.' . $allowed[$mime];
            move_uploaded_file($file['tmp_name'], $dir . $evidence);
            $evidence = 'refunds/' . $evidence;
        }

        $db = $orderModel->getConnection();
        $db->begin_transaction();
        try {
            $refundModel->create([
                'order_id' => $order_id,
                'user_id' => $_SESSION['user_id'],
                'type' => 'return',
                'amount' => (int)$order['total_amount'],
                'reason' => $reason,
                'evidence_image' => $evidence,
                'bank_name' => $bank_name,
                'bank_account_number' => $bank_account_number,
                'bank_account_name' => $bank_account_name,
            ]);
            $orderModel->addHistory($order_id, 'refund_requested', 'Khách yêu cầu trả hàng: ' . $reason);
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Đã gửi yêu cầu trả hàng.']);
        } catch (Exception $e) {
            $db->rollback();
            error_log('Refund request error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại!']);
        }
        exit;
    }

    // =========================================================================
    // 7. API ĐỂ LẤY CHI TIẾT ĐƠN HÀNG HIỂN THỊ LÊN POPUP (AJAX)
    // =========================================================================
    public function getOrderDetailsAjax() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        header('Content-Type: application/json'); // Báo cho web biết mình trả về JSON
        
        // Lấy ID đơn hàng từ JS gửi lên
        $input = json_decode(file_get_contents('php://input'), true);
        $order_id = $input['order_id'] ?? 0;
        $user_id = $_SESSION['user_id'] ?? 0;

        if (!$order_id || !$user_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id);

        // Bảo mật: Không tìm thấy đơn, hoặc đơn đó không phải của user đang đăng nhập
        if (!$order || $order['user_id'] != $user_id) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
            exit;
        }

        // Lấy chi tiết các sản phẩm trong đơn đó
        $items = $orderModel->getOrderItems($order_id);
        
        // [THÊM DÒNG NÀY] Lấy lịch sử dòng thời gian từ bảng order_history
        $history = $orderModel->getOrderHistory($order_id);

        // Thông tin hoàn tiền + điều kiện được trả hàng
        $refundModel = $this->model('RefundModel');
        $settings = $this->model('PaymentModel')->getSettings();
        $refund = $refundModel->getLatestByOrder($order_id);
        if ($refund) {
            $refund['status_label'] = RefundModel::STATUS_LABELS[$refund['status']] ?? $refund['status'];
            $refund['type_label'] = RefundModel::TYPE_LABELS[$refund['type']] ?? $refund['type'];
        }
        $eligibility = $refundModel->checkReturnEligibility($order, (int)($settings['refund_days'] ?? 7));

        // Trả kết quả về cho JS vẽ lên Modal
        echo json_encode([
            'success' => true,
            'order' => $order,
            'items' => $items,
            'history' => $history, // [THÊM DÒNG NÀY] Đẩy lịch sử ra cho file JS nhận
            'refund' => $refund,
            'can_return' => $eligibility['ok'],
            'return_deadline' => $eligibility['deadline'] ? date('d/m/Y H:i', $eligibility['deadline']) : null
        ]);
        exit;
    }
}