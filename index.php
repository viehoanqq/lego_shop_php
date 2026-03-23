<?php
// Dòng này phải là dòng ĐẦU TIÊN, không được có khoảng trắng phía trước
session_start(); 

// Sau đó mới đến các lệnh require hoặc include khác
require_once 'app/core/App.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Database.php';

$app = new App();