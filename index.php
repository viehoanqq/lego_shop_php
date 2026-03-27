<?php
// Dòng này phải là dòng ĐẦU TIÊN, không được có khoảng trắng phía trước
session_start(); 
date_default_timezone_set('Asia/Ho_Chi_Minh'); // thêm dòng này để đặt múi giờ mặc định cho toàn bộ ứng dụng, tránh lỗi lệch giờ khi lưu vào database hoặc hiển thị ra ngoài
// Sau đó mới đến các lệnh require hoặc include khác
require_once 'app/core/App.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Database.php';

$app = new App();