<div class="breadcrumb-wrapper" style="background-color: #fff; border-bottom: 1px solid #eee; padding: 12px 0; margin-bottom: 25px;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
        <ul style="display: flex; list-style: none; gap: 8px; font-size: 14px; color: #666; align-items: center; margin: 0;">
            
            <li>
                <a href="/lego_shop_php/home" style="color: #a4161a; text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-house"></i> Trang chủ
                </a>
            </li>
            
            <?php if(isset($parent_title) && isset($parent_link)): ?>
                <li style="color: #ccc; font-size: 12px;"><i class="fa-solid fa-chevron-right"></i></li>
                <li>
                    <a href="<?= htmlspecialchars($parent_link) ?>" style="color: #666; text-decoration: none; transition: 0.2s;">
                        <?= htmlspecialchars($parent_title) ?>
                    </a>
                </li>
            <?php endif; ?>

            <li style="color: #ccc; font-size: 12px;"><i class="fa-solid fa-chevron-right"></i></li>
            <li style="font-weight: 700; color: #333;">
                <?= isset($title) ? htmlspecialchars($title) : 'Tất cả sản phẩm' ?>
            </li>
            
        </ul>
    </div>
</div>

<style>
    .breadcrumb-wrapper ul li a:hover { color: #a4161a !important; }
</style>