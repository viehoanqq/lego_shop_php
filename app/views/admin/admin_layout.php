<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Quản lý LEGO Store' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/lego_shop_php/public/assets/css/admin_global.css">
    <link rel="stylesheet" href="/lego_shop_php/public/assets/css/admin.css">
    <link rel="stylesheet" href="/lego_shop_php/public/assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="/lego_shop_php/public/assets/css/admin_profile.css">
    <script src="/lego_shop_php/public/assets/js/admin.js" defer></script>
</head>
<body>
    <?php require_once __DIR__ . '/../components/admin_sidebar.php'; ?>

    <div class="main-content" id="admin-main-content">
        <?php require_once __DIR__ . '/../components/admin_header.php'; ?>

        <section class="content">
            <?php 
                if (file_exists(__DIR__ . '/../' . $view_content . '.php')) {
                    require_once __DIR__ . '/../' . $view_content . '.php'; 
                }
            ?>
        </section>
    </div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const contentArea = document.querySelector('#admin-main-content');
    const sidebarLinks = document.querySelectorAll('.side-bar .menu a');

    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');

            // Không chặn link Logout hoặc link ra ngoài
            if (href.includes('logout') || href.startsWith('http')) return;

            e.preventDefault(); // Ngăn load lại trang
            
            // Hiệu ứng mờ dần cho chuyên nghiệp
            contentArea.style.opacity = '0.5';
            contentArea.style.transition = '0.3s';

            fetch(href)
                .then(response => response.text())
                .then(html => {
                    // Tạo một tài liệu ảo để bóc tách dữ liệu trả về
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.querySelector('#admin-main-content').innerHTML;
                    const newTitle = doc.querySelector('title').innerText;

                    // Cập nhật nội dung và tiêu đề trang
                    contentArea.innerHTML = newContent;
                    document.title = newTitle;
                    contentArea.style.opacity = '1';

                    // Cập nhật URL trên thanh địa chỉ để nút Back/Forward vẫn dùng được
                    window.history.pushState({path: href}, '', href);

                    // Cập nhật class Active cho menu
                    sidebarLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');

                    // ========================================================
                    // ĐÂY LÀ ĐOẠN FIX LỖI: ĐÁNH THỨC CÁC THẺ SCRIPT BỊ NGỦ QUÊN
                    // ========================================================
                    const scripts = contentArea.querySelectorAll('script');
                    scripts.forEach(oldScript => {
                        const newScript = document.createElement('script');
                        // Copy các thuộc tính (ví dụ: src="")
                        Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                        // Copy mã JS bên trong
                        newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                        // Thay thế script cũ bằng script mới để trình duyệt chịu chạy
                        oldScript.parentNode.replaceChild(newScript, oldScript);
                    });
                    // ========================================================

                })
                .catch(err => {
                    console.error('Lỗi load trang:', err);
                    window.location.href = href; // Nếu lỗi AJAX thì cho load trang kiểu cũ
                });
        });
    });

    // Xử lý khi bấm nút Back/Forward của trình duyệt
    window.addEventListener('popstate', function() {
        window.location.reload();
    });
});
</script>
</body>
</html>