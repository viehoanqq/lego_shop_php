<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Quản lý LEGO Store' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@200;1000&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/lego_shop_php/public/assets/css/admin_global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>
<body>

    <?php require_once __DIR__ . '/../../views/components/sidebar.php'; ?>

    <div class="main-content">
        <?php require_once __DIR__ . '/../../views/components/admin_header.php'; ?>

        <section class="content">
            <?php require_once __DIR__ . '/../../views/' . $view . '.php'; ?>
        </section>
    </div>

</body>
</html>