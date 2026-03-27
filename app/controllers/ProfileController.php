<?php
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
        
        // Gọi Model để lấy tất cả đơn hàng của user này
        $orderModel = $this->model('OrderModel');
        // Đảm bảo bạn đã viết hàm getOrdersByUserId trong OrderModel nhé!
        $orders = $orderModel->getOrdersByUserId($user_id);

        // Trỏ đến view orders.php mà bạn vừa tạo
        $this->view('/user/profile/orders', [
            'title' => 'Lịch sử mua hàng',
            'orders' => $orders
        ]);
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

        // Trả kết quả về cho JS vẽ lên Modal
        echo json_encode([
            'success' => true,
            'order' => $order,
            'items' => $items,
            'history' => $history // [THÊM DÒNG NÀY] Đẩy lịch sử ra cho file JS nhận
        ]);
        exit;
    }
}