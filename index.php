<?php
// index.php

// Kiểm tra đường dẫn có đúng folder app/core/ không
require_once 'app/core/App.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Database.php';

$app = new App(); // 