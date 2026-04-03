<?php
// Dòng này phải là dòng ĐẦU TIÊN, không được có khoảng trắng phía trước
session_start(); 
date_default_timezone_set('Asia/Ho_Chi_Minh');
require_once 'app/core/App.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Database.php';

$app = new App();