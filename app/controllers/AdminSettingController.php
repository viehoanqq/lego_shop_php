<?php
class AdminSettingController extends Controller {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
    }

    public function index() {
        $settingModel = $this->model('SettingModel');
        $settings = $settingModel->getSettings();
        
        $this->view('admin/settings', [
            'title' => 'Cài đặt hệ thống',
            'settings' => $settings
        ]);
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $settingModel = $this->model('SettingModel');
            
            $logo_name = null;
            // Chú ý: Đường dẫn upload này phải khớp với cấu trúc thư mục của bạn
            if (isset($_FILES['logo_upload']) && $_FILES['logo_upload']['error'] === UPLOAD_ERR_OK) {
                $logo_name = $this->saveUploadedImage($_FILES['logo_upload']['tmp_name'], $_FILES['logo_upload']['size']);
            }

            if ($settingModel->updateSettings($_POST, $logo_name)) {
                header("Location: /lego_shop_php/admin/setting?msg=success");
            } else {
                header("Location: /lego_shop_php/admin/setting?error=1");
            }
            exit;
        }
    }
}