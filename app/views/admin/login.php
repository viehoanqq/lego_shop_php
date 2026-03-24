<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - LEGO World Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="/lego_shop_php/public/assets/css/admin.css">
</head>
<body class="admin-body">

<div class="login-admin-wrapper">
    <div class="login-admin-header">
        <img src="/lego_shop_php/public/assets/images/logo.png" class="admin-logo" alt="LEGO Logo">
        <h2>ADMIN PANEL</h2>
        <p>Control panel login</p>
    </div>

    <div class="login-admin-form">
        <?php if(isset($error)): ?>
            <p style="color: #ff6b6b; font-size: 13px; text-align: center; margin-bottom: 20px;">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $error ?>
            </p>
        <?php endif; ?>

        <form action="/lego_shop_php/admin/login" method="POST">
            <div class="admin-form-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="username" placeholder="admin" required autocomplete="off">
            </div>
            
            <div class="admin-form-group">
                <i class="fa-solid fa-key"></i>
                <input type="password" name="password" placeholder="••••••••••••" required>
            </div>

            <button type="submit" class="btn-admin-login">Login</button>
        </form>
    </div>

    <div class="ocean">
        <div class="wave"></div>
        <div class="wave"></div>
    </div>
</div>

</body>
</html>