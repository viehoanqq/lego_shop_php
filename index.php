<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

require_once 'app/core/Env.php';
Env::load(__DIR__ . '/.env');

// Chỉ hiện lỗi PHP ra màn hình khi APP_DEBUG=true (tránh lộ đường dẫn, câu SQL khi chạy thật)
ini_set('display_errors', env('APP_DEBUG', false) ? '1' : '0');

// ---------------------------------------------------------------------
// SESSION
// - Cookie: HttpOnly (JS không đọc được), SameSite=Lax (giảm CSRF), Secure khi chạy HTTPS
// - Tự đăng xuất sau SESSION_IDLE_MINUTES phút không thao tác (mặc định 120)
// ---------------------------------------------------------------------
$idle_minutes = max(5, (int)env('SESSION_IDLE_MINUTES', 120));
ini_set('session.use_strict_mode', '1');
ini_set('session.gc_maxlifetime', (string)($idle_minutes * 60));
session_set_cookie_params([
    'lifetime' => 0,                 // cookie mất khi đóng trình duyệt
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $idle_minutes * 60) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

require_once 'app/core/App.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Database.php';

$app = new App();
