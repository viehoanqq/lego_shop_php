<style>

/* ===== RESET NHẸ ===== */
* {
    box-sizing: border-box;
}

/* ===== HEADER ===== */
.header { 
    background: #fff; 
    padding: 20px; 
    border-radius: 12px; 
    box-shadow: 0 2px 12px rgba(0,0,0,0.05); 
    margin-bottom: 25px;

    display: flex; 
    justify-content: space-between; 
    align-items: flex-end; 
    gap: 20px;
    flex-wrap: wrap;
}

.header-left { 
    flex: 1; 
    min-width: 280px; 
}

.header-left h2 { 
    margin-bottom: 15px; 
    color: #1a202c; 
    font-size: 22px; 
    font-weight: 700; 
}

/* ===== SEARCH ===== */
.search-form { 
    display: flex; 
    gap: 10px; 
    align-items: center; 
    flex-wrap: wrap; 
}

.search-box {
    position: relative;
    flex: 2;
    min-width: 220px;
}

.search-box input {
    width: 100%;
    padding: 10px 100px 10px 35px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: 0.2s;
}

.search-box input:focus {
    border-color: #3182ce;
    box-shadow: 0 0 0 2px rgba(49,130,206,0.1);
}

/* icon */
.search-box i {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
}

/* button trong input */
.btn-search-inside {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    
    background: #3182ce;
    color: #fff;
    border: none;
    padding: 5px 14px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;

    height: 30px;
    display: flex;
    align-items: center;
}

.btn-search-inside:hover {
    background: #2b6cb0;
}

/* select */
.form-control {
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
}

/* ===== BUTTON ADD ===== */
.btn-add-product {
    background: #3182ce; 
    color: #fff; 
    text-decoration: none; 
    padding: 10px 20px; 
    border-radius: 8px; 
    font-weight: 600; 
    display: flex; 
    align-items: center; 
    gap: 8px;
    transition: 0.2s;
}

.btn-add-product:hover {
    background: #2b6cb0;
    transform: translateY(-1px);
}

/* ===== TABLE ===== */
.table-container { 
    background: #fff; 
    border-radius: 12px; 
    box-shadow: 0 2px 12px rgba(0,0,0,0.08); 
    max-height: 70vh;
    overflow-y: auto; 
}

.lego-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

/* header table */
.lego-table th { 
    position: sticky; 
    top: 0; 
    z-index: 10;

    background: #f8fafc; 
    padding: 14px; 
    text-align: left; 
    font-size: 12px; 
    text-transform: uppercase; 
    color: #64748b;

    border-bottom: 2px solid #e2e8f0; 
}

/* body */
.lego-table td { 
    padding: 14px; 
    border-bottom: 1px solid #f1f5f9; 
    vertical-align: middle; 
}

/* scrollbar */
.table-container::-webkit-scrollbar { width: 6px; }
.table-container::-webkit-scrollbar-thumb { 
    background: #cbd5e0; 
    border-radius: 10px; 
}

/* ===== PRODUCT CELL ===== */
.product-cell { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
}

.img-product { 
    width: 55px; 
    height: 55px; 
    border-radius: 8px; 
    object-fit: cover; 
    border: 1px solid #e2e8f0; 
}

/* ===== TEXT STYLE ===== */
.price-tag { 
    color: #2b6cb0; 
    font-weight: 700; 
}

.status-pill { 
    width: 8px; 
    height: 8px; 
    border-radius: 50%; 
    display: inline-block; 
    margin-right: 6px; 
}

/* ===== STOCK ===== */
.stock-badge { 
    padding: 4px 10px; 
    border-radius: 20px; 
    font-size: 11px; 
    font-weight: 700; 
}

.stock-low { 
    background: #fff5f5; 
    color: #c53030; 
}

.stock-ok { 
    background: #f0fff4; 
    color: #2f855a; 
}

.stock-empty { 
    background: #fff5f5; 
    color: #c53030; 
}

/* ===== ACTION ===== */
.btn-action { 
    text-decoration: none; 
    padding: 6px; 
    border-radius: 6px; 
    transition: 0.2s; 
}

.btn-action:hover { 
    background: #f1f5f9; 
}

/* ===== ALERT ===== */
#status-alert-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    width: 320px;
}

.alert-box {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px;
    border-radius: 10px;
    margin-bottom: 10px;
    color: #fff;
    font-weight: 600;
}

.success-js { background: #38a169; }
.error-js { background: #e53e3e; }

/* ===== PAGINATION ===== */
.pagination { 
    display: flex; 
    justify-content: center; 
    gap: 5px; 
    margin-top: 20px; 
}

.page-link { 
    padding: 8px 14px; 
    border: 1px solid #e2e8f0; 
    border-radius: 6px; 
    text-decoration: none; 
    color: #4a5568; 
}

.page-link.active { 
    background: #3182ce; 
    color: #fff; 
}

/* ===== FORM ===== */
.form-container {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-bottom: 25px;
}

.form-group {
    margin-bottom: 15px;
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 6px;
}

/* input + textarea */
.form-control {
    padding: 10px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: 0.2s;
}

.form-control:focus {
    border-color: #3182ce;
    box-shadow: 0 0 0 2px rgba(49,130,206,0.1);
    outline: none;
}

/* textarea */
textarea.form-control {
    resize: vertical;
}

/* button submit */
.btn-submit {
    background: #38a169;
    color: #fff;
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.btn-submit:hover {
    background: #2f855a;
    transform: translateY(-1px);
}

.error-text {
    color: #e53e3e;
    font-size: 12px;
    margin-top: 4px;
}

.input-error {
    border-color: #e53e3e ;
}

.input-success {
    border-color: #38a169 ;
}

</style>

<?php 
$session_msg = get_flash_message('msg');
$session_error = get_flash_message('error');
?>

<?php if($session_msg || $session_error): ?>
    <div id="status-alert-container" style="margin-bottom: 20px;">
        
        <?php if($session_msg): ?>
            <div class="alert-box success-js">
                <i class="fa-solid fa-circle-check"></i>
                <span>
                    <?php
                        switch($session_msg) {
                            case 'success':                 echo "Thêm sản phẩm thành công!"; break;
                            case 'updated':                 echo "Đã cập nhật thông tin sản phẩm!"; break;
                            case 'show':                    echo "Đã mở khóa sản phẩm!"; break;
                            case 'hidden':                  echo "Đã khóa sản phẩm khỏi cửa hàng!"; break;
                            case 'deleted':                 echo "Đã xóa sản phẩm thành công!"; break;
                            default:        echo "Thao tác thành công!"; break;
                        }
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if($session_error): ?>
            <div class="alert-box error-js">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>
                    <?php
                        switch($session_error) {
                            case 'empty':                   echo "Vui lòng điền đầy đủ các trường bắt buộc!"; break;
                            case 'db':                      echo "Lỗi hệ thống: Không thể xử lý dữ liệu."; break;
                            case 'sku_exists':              echo "Lỗi: Mã SKU này đã tồn tại!"; break;
                            case 'already_hidden':          echo "Sản phẩm này đã bị khóa!"; break;
                            case 'already_shown':           echo "Sản phẩm này hiện đang được mở bán!"; break;
                            case 'notfound':                echo "Không tìm thấy sản phẩm yêu cầu!"; break;
                            case 'has_history':             echo "Không thể xóa! Sản phẩm này đã có trong phiếu nhập hoặc đơn hàng."; break;
                            case 'hidden_due_to_history':   echo "Không thể xóa! Sản phẩm này đã có trong phiếu nhập hoặc đơn hàng. Hệ thống đã tự động <b>Khóa</b> sản phẩm này!"; break;
                            case 'cat_is_locked':           echo "Danh mục của sản phẩm này đã bị khóa, không thể mở."; break;
                            case 'db':                      echo "Lỗi hệ thống: Không thể xóa dữ liệu vào lúc này."; break;
                            default:                        echo "Có lỗi xảy ra, vui lòng thử lại."; break;
                        }
                    ?>
                </span>
            </div>
        <?php endif; ?>
        
    </div>
<?php endif; ?>

<div class="header">
    <div class="header-left">
        <h2> Quản lý Sản Phẩm</h2>
        
        <form action="/lego_shop_php/adminproduct" method="GET" class="search-form">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>

                <input type="text" name="keyword"
                    placeholder="Tìm tên sản phẩm..."
                    value="<?= $_GET['keyword'] ?? '' ?>">

                <button type="submit" class="btn-search-inside">
                    Tìm kiếm
                </button>
            </div>

            <select name="category" class="form-control" onchange="this.form.submit()" style="flex: 1; min-width: 150px; cursor: pointer;">
                <option value="all">-- Tất cả danh mục --</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= $cat['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status" class="form-control" onchange="this.form.submit()" style="flex: 1; min-width: 150px; cursor: pointer;">
                <option value="1,2" <?= (!isset($_GET['status']) || $_GET['status'] == '1,2') ? 'selected' : '' ?>>Tất cả trạng thái</option>
                <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : '' ?>>Đang bán</option>
                <option value="2" <?= (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : '' ?>>Tạm khóa</option>
            </select>
        </form>
    </div>

    <?php if(!isset($is_form) || $is_form === false): ?>
        <a href="/lego_shop_php/adminproduct/add" class="btn-add-product">
            <i class="fa-solid fa-plus"></i> Thêm sản phẩm
        </a>
    <?php endif; ?>
</div>

<?php if(isset($is_form) && $is_form === true): ?>
    <div class="form-container">
        <h3 style="margin-top:0;"><?= isset($product) ? '🛠️ Chỉnh sửa: ' . $product['name'] : '🆕 Thêm sản phẩm mới' ?></h3>
        <form action="<?= isset($product) ? '/lego_shop_php/adminproduct/update/'.$product['id'] : '/lego_shop_php/adminproduct/store' ?>" method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                <div>
                    <div class="form-group">
                        <label>Tên sản phẩm LEGO</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= $product['name'] ?? '' ?>" required>
                        <small class="error-text"></small>
                    </div>
                    <div style="display: flex; gap: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label>Mã SKU</label>
                            <input type="text" id="sku" name="sku" class="form-control" value="<?= $product['sku'] ?? '' ?>" required>
                            <small class="error-text"></small>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Dòng LEGO</label>
                            <select name="category_id" class="form-control">
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($product) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= $cat['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mô tả chi tiết</label>
                        <textarea name="description" class="form-control" rows="5"><?= $product['description'] ?? '' ?></textarea>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label>Giá bán (VNĐ)</label>
                        <input type="number" name="selling_price" class="form-control" value="<?= $product['selling_price'] ?? '' ?>" style="font-weight: bold; color: #2b6cb0;"
                        readonly>
                    </div>
                    <div class="form-group">
                        <label>Số mảnh ghép (Pieces)</label>
                        <input type="number" id="pieces" name="pieces" class="form-control" value="<?= $product['pieces'] ?? '' ?>">
                        <small class="error-text"></small>
                    </div>
                    <div class="form-group">
                        <label>Hình ảnh</label>
                        <input type="file" name="main_image" class="form-control">
                        <?php if(isset($product['main_image'])): ?>
                            <img src="/lego_shop_php/public/assets/images/<?= $product['main_image'] ?>" style="width: 100px; margin-top: 10px; border-radius: 8px;">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button type="submit" class="btn-submit">Lưu dữ liệu</button>
                <a href="/lego_shop_php/adminproduct" style="padding: 10px 20px; color: #718096; text-decoration: none;">Hủy bỏ</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<div class="table-container">
    <table class="lego-table">
        <thead>
            <tr>
                <th style="width: 30%;">Sản phẩm</th>
                <th>Dòng LEGO</th>
                <th>Giá bán</th>
                <th>Thông số</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th style="text-align: center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <div class="product-cell">
                            <img src="/lego_shop_php/public/assets/images/<?= !empty($p['main_image']) ? $p['main_image'] : 'default.jpg' ?>" 
                                class="img-product" 
                                onerror="this.src='https://placehold.co/60x60?text=LEGO'">
                            <div>
                                <a href="/lego_shop_php/adminproduct/detail/<?= $p['id'] ?>" 
                                style="text-decoration: none; display: block; group">
                                    <div style="font-weight: 700; color: #3182ce; transition: 0.2s;" onmouseover="this.style.color='#2c5282'" onmouseout="this.style.color='#3182ce'">
                                        <?= htmlspecialchars($p['name']) ?> 
                                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px; margin-left: 4px; opacity: 0.5;"></i>
                                    </div>
                                </a>
                                <div style="font-size: 11px; color: #a0aec0; letter-spacing: 0.5px;">SKU: <?= strtoupper($p['sku']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size: 13px; font-weight: 500; color: #4a5568;">
                        <span style="background: #edf2f7; padding: 3px 8px; border-radius: 4px;"><?= $p['category_name'] ?></span>
                    </td>
                    <td class="price-tag"><?= number_format($p['selling_price'], 0, ',', '.') ?>đ</td>
                    <td style="font-size: 13px; color: #718096;">
                        <strong><?= number_format($p['pieces']) ?></strong> pcs
                    </td>
                    <td>
                        <?php if($p['stock_quantity'] <= 0): ?>
                            <span class="stock-badge stock-empty">Hết hàng</span>
                        <?php elseif($p['stock_quantity'] <= $p['min_stock_level']): ?>
                            <span class="stock-badge stock-low">Sắp hết: <?= $p['stock_quantity'] ?></span>
                        <?php else: ?>
                            <span class="stock-badge stock-ok">Còn: <?= $p['stock_quantity'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($p['status'] == 1): ?>
                            <span style="color: #38a169; font-size: 13px; font-weight: 600;"><span class="status-pill" style="background:#48bb78"></span>Đang bán</span>
                        <?php else: ?>
                            <span style="color: #e53e3e; font-size: 13px; font-weight: 600;"><span class="status-pill" style="background:#f56565"></span>Tạm khóa</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="/lego_shop_php/adminproduct/edit/<?= $p['id'] ?>" class="btn-action" title="Chỉnh sửa" style="color: #3182ce;">
                                <i class="fa-solid fa-pen-to-square"></i> Sửa
                            </a>
                            <?php 
                                // 1. Tính toán trạng thái ngay trong vòng lặp
                                $isActive = ($p['status'] == 1); // 1 là đang bán, 2 là tạm khóa
                                $btnIcon  = $isActive ? 'fa-lock' : 'fa-lock-open';
                                $btnColor = $isActive ? '#e53e3e' : '#38a169'; // Đỏ khi sắp Khóa, Xanh khi sắp Mở
                                $btnText  = $isActive ? 'Khóa' : 'Mở';
                            ?>

                            <a href="/lego_shop_php/adminproduct/toggleStatus/<?= $p['id'] ?>?current=<?= $p['status'] ?>" 
                                class="btn-action btn-toggle-status" 
                                title="<?= $isActive ? 'Tạm dừng bán' : 'Mở bán lại' ?>" 
                                style="color: <?= $btnColor ?>;"
                                onclick="return confirm('Xác nhận <?= mb_strtolower($btnText) ?> sản phẩm này?')">
                                
                                <i class="fa-solid <?= $btnIcon ?>"></i>
                                <span style="margin-left: 4px; font-weight: 600;">
                                    <?= $btnText ?>
                                </span>
                            </a>

                            <!-- <?php if($p['status'] != 0): ?>
                                <a href="/lego_shop_php/adminproduct/hide/<?= $p['id'] ?>" 
                                class="btn-action" 
                                style="color: #e53e3e;" 
                                title="Ẩn hoàn toàn (Xóa mềm)"
                                onclick="return confirm('Sản phẩm sẽ bị ẩn hoàn toàn. Xác nhận?')">
                                    <i class="fa-solid fa-eye-slash"></i> Ẩn
                                </a>
                            <?php else: ?>
                                <a href="/lego_shop_php/adminproduct/show/<?= $p['id'] ?>" 
                                class="btn-action" 
                                style="color: #718096;" 
                                title="Hiện lại">
                                    <i class="fa-solid fa-eye"></i> Hiện
                                </a>
                            <?php endif; ?> -->

                            <a href="/lego_shop_php/adminproduct/delete/<?= $p['id'] ?>" class="btn-action" style="color: #e53e3e;" onclick="return confirm('Xóa vĩnh viễn sản phẩm?')">
                                <i class="fa-solid fa-trash"></i> Xóa
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #a0aec0;">Chưa có sản phẩm nào trong kho hàng.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php if(isset($totalPages) && $totalPages > 1): ?>
    <div class="pagination">
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>" 
           class="page-link <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
            <i class="fa-solid fa-chevron-left"></i>
        </a>

        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
               class="page-link <?= ($i == $currentPage) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>" 
           class="page-link <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    </div>
    <div style="text-align: center; color: #718096; font-size: 13px; margin-top: 10px;">
        Hiển thị trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng <?= $totalItems ?> sản phẩm)
    </div>
<?php endif; ?>

<script>
    const form = document.querySelector("form");
    const nameInput = document.getElementById("name");
    const skuInput = document.getElementById("sku");
    const piecesInput = document.getElementById("pieces");

    // ===== VALIDATE FUNCTIONS =====
    function showError(input, message) {
        input.classList.add("input-error");
        input.classList.remove("input-success");
        input.nextElementSibling.innerText = message;
    }

    function showSuccess(input) {
        input.classList.remove("input-error");
        input.classList.add("input-success");
        input.nextElementSibling.innerText = "";
    }

    // ===== RULES =====
    function validateName() {
        const value = nameInput.value.trim();
        if (value === "") {
            showError(nameInput, "Không được để trống");
            return false;
        }
        if (value.length < 3) {
            showError(nameInput, "Tối thiểu 3 ký tự");
            return false;
        }
        showSuccess(nameInput);
        return true;
    }

    function validateSku() {
        const value = skuInput.value.trim();
        const regex = /^[A-Z0-9\-]+$/;

        if (value === "") {
            showError(skuInput, "Không được để trống");
            return false;
        }
        if (!regex.test(value)) {
            showError(skuInput, "Chỉ gồm chữ IN HOA, số, dấu -");
            return false;
        }
        showSuccess(skuInput);
        return true;
    }

    function validatePieces() {
        const value = parseInt(piecesInput.value);

        if (isNaN(value) || value <= 0) {
            showError(piecesInput, "Phải > 0");
            return false;
        }
        showSuccess(piecesInput);
        return true;
    }

    // ===== REALTIME =====
    nameInput.addEventListener("input", validateName);
    skuInput.addEventListener("input", validateSku);
    piecesInput.addEventListener("input", validatePieces);

    // ===== SUBMIT =====
    form.addEventListener("submit", function(e) {
        const isValid =
            validateName() &
            validateSku() &
            validatePieces();

        if (!isValid) {
            e.preventDefault();
        }
    });
    // Tự động ẩn thông báo sau 5 giây
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert-box');
        alerts.forEach(el => {
            el.style.transition = "opacity 0.5s ease";
            el.style.opacity = "0";
            setTimeout(() => el.style.display = 'none', 500);
        });
    }, 5000);
</script>