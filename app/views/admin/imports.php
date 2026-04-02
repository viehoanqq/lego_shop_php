<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    /* CSS CŨ CỦA BẠN (GIỮ NGUYÊN) */
    .table-container { 
        background: #fff; 
        border-radius: 12px; 
        box-shadow: 0 2px 12px rgba(0,0,0,0.08); 
        margin-top: 10px;
        max-height: 70vh; 
        overflow-y: auto; 
    }

    .lego-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .lego-table th { 
        position: sticky; 
        top: 0; 
        z-index: 10;
        background: #f8fafc; 
        padding: 15px; 
        text-align: left; 
        color: #64748b; 
        font-size: 13px; 
        text-transform: uppercase; 
        border-bottom: 2px solid #e2e8f0; 
    }
    .lego-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    .table-container::-webkit-scrollbar { width: 6px; }
    .table-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
    
    .form-container { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 30px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; }
    .form-control:focus { border-color: #3182ce; box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1); }
    .btn-submit { color: white; padding: 10px 25px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
    .btn-action { text-decoration: none; padding: 6px 12px; border-radius: 6px; transition: 0.2s; font-weight: 600; }
    .btn-action:hover { background: #f1f5f9; }

    /* --- CSS làm đẹp cho thanh tìm kiếm Select2 --- */
    .select2-container .select2-selection--single {
        height: 40px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        display: flex;
        align-items: center;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #475569 !important;
        font-weight: 500;
    }
</style>

<?php if(isset($_GET['msg']) || isset($_GET['error'])): ?>
    <div id="status-alert-container" style="margin-bottom: 20px;">
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div class="alert-box success-js" style="padding: 15px; border-radius: 8px; background: #f0fff4; color: #2f855a; border: 1px solid #c6f6d5; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-check"></i>
                <span>✨ Lưu phiếu nhập kho thành công!</span>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert-box error-js" style="padding: 15px; border-radius: 8px; background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>❌ Lỗi hệ thống: Không thể xử lý dữ liệu nhập kho.</span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="admin-header1" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding: 10px;">
    <div>
        <h2 style="margin:0; color: #1a202c;">📦 Quản Lý Nhập Kho</h2>
        <small style="color: #718096;">Lịch sử và Lập phiếu nhập</small>
    </div>
    <?php if(!isset($is_form) || $is_form === false): ?>
        <a href="/lego_shop_php/adminimport/create" style="background: #3182ce; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">
            <i class="fa-solid fa-plus"></i> Lập phiếu nhập mới
        </a>
    <?php endif; ?>
</div>

<?php if(!isset($is_form) || $is_form === false): ?>
<div style="background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
    
    <form id="filterForm" action="/lego_shop_php/adminimport" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 180px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; display: block; margin-bottom: 5px;">Tìm kiếm</label>
            <div style="position: relative;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></i>
                <input type="text" name="keyword" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" 
                       placeholder="Nhập mã PN-..." class="form-control" 
                       style="width: 100%; padding: 10px 10px 10px 35px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;"
                       onkeypress="if(event.keyCode==13) { this.form.submit(); return false; }">
            </div>
        </div>

        <div style="flex: 1; min-width: 180px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; display: block; margin-bottom: 5px;">Nhà cung cấp</label>
            <select name="supplier_id" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; cursor: pointer;">
                <option value="">-- Tất cả đối tác --</option>
                <?php foreach($suppliers as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= (($filters['supplier_id']??'') == $s['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div style="flex: 1; min-width: 130px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; display: block; margin-bottom: 5px;">Trạng thái</label>
            <select name="status" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; cursor: pointer;">
                <option value="">-- Tất cả --</option>
                <option value="completed" <?= (($filters['status']??'') == 'completed') ? 'selected' : '' ?>>Hoàn tất</option>
                <option value="draft" <?= (($filters['status']??'') == 'draft') ? 'selected' : '' ?>>Bản nháp</option>
            </select>
        </div>

        <div style="flex: 1; min-width: 130px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; display: block; margin-bottom: 5px;">Từ ngày</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($filters['start_date']??'') ?>" onchange="this.form.submit()" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; cursor: pointer;">
        </div>

        <div style="flex: 1; min-width: 130px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; display: block; margin-bottom: 5px;">Đến ngày</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($filters['end_date']??'') ?>" onchange="this.form.submit()" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; cursor: pointer;">
        </div>

        <div>
            <a href="/lego_shop_php/adminimport" style="display: inline-flex; align-items: center; justify-content: center; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; text-decoration: none; padding: 0 15px; border-radius: 6px; font-weight: 600; height: 42px; transition: 0.2s;">
                <i class="fa-solid fa-rotate-right" style="margin-right: 5px;"></i> Xóa
            </a>
        </div>

    </form>
</div>
<?php endif; ?>

<?php if(isset($is_form) && $is_form === true): ?>
    <div class="form-container">
        <h3 style="margin-top:0; color: #2d3748;">
            <i class="fa-solid <?= isset($receipt) ? 'fa-pen-to-square' : 'fa-cart-plus' ?>"></i> 
            <?= isset($receipt) ? 'Tiếp tục lập Phiếu nhập nháp (#PN-' . $receipt['id'] . ')' : 'Lập phiếu nhập kho mới' ?>
        </h3>

        <form id="importForm" style="margin-top: 20px;">
            <<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 30px; margin-bottom: 20px; background: #f7fafc; padding: 15px; border-radius: 8px;">
                <div class="form-group" style="margin: 0;">
                    <label>Nhà cung cấp <span style="color:red">*</span></label>
                    <select id="supplier_id" class="form-control" required>
                        <option value="">-- Chọn nhà cung cấp --</option>
                        <?php foreach($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= (isset($receipt) && $receipt['supplier_id'] == $s['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin: 0;">
                    <label>Nhân viên tiếp nhận</label>
                    <input type="text" class="form-control" value="<?= $_SESSION['admin_name'] ?>" readonly style="background: #edf2f7; cursor: not-allowed; color: #4a5568; font-weight: 600;">
                </div>
                
                <div class="form-group" style="margin: 0;">
    <label>Ngày & Giờ nhập kho <span style="color:#718096; font-size:12px; font-weight:normal;">(Mặc định hiện tại)</span></label>
    <input type="datetime-local" id="import_date" class="form-control" 
           value="<?= isset($receipt) ? date('Y-m-d\TH:i', strtotime($receipt['created_at'])) : date('Y-m-d\TH:i') ?>">
</div>
            </div>
            <<table class="lego-table" id="importTable" style="margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th style="width: 5%; text-align: center;">STT</th>
                        <th style="width: 40%;">Sản phẩm nhập</th>
                        <th style="width: 15%; text-align: center;">Số lượng</th>
                        <th style="width: 20%; text-align: right;">Giá nhập (đ)</th>
                        <th style="width: 15%; text-align: right;">Thành tiền</th>
                        <th style="width: 5%; text-align: center;"></th>
                    </tr>
                </thead>
                <tbody id="import-body">
                    <?php if(isset($receipt_details) && !empty($receipt_details)): ?>
                        <?php $stt = 1; foreach($receipt_details as $index => $item): ?>
                            <?php $rowId = 'row_old_' . $index; ?>
                            <tr id="<?= $rowId ?>" class="product-row"> <td class="row-number" style="text-align: center; font-weight: bold; color: #64748b; vertical-align: middle;"><?= $stt++ ?></td>
                                
                                <td>
                                <input type="text" class="form-control display-product-input" list="product-suggestions" 
                                       placeholder="Gõ tên sản phẩm..." 
                                       value="<?= htmlspecialchars($item['product_name']) ?> (Tồn: <?= $item['current_stock'] ?>)" 
                                       onchange="handleProductSelect(this)" required>
                                
                                <input type="hidden" class="real-product-id" value="<?= $item['product_id'] ?>">
                            </td>
                                <td><input type="number" class="form-control qty-input" value="<?= $item['quantity'] ?>" min="1" oninput="calculateRow('<?= $rowId ?>')" style="text-align:center;"></td>
                                <td><input type="number" class="form-control price-input" value="<?= $item['price'] ?>" min="0" oninput="calculateRow('<?= $rowId ?>')" style="text-align:right;"></td>
                                <td style="text-align: right; font-weight: 700; color: #2d3748; vertical-align: middle;" class="row-total"><?= number_format($item['quantity'] * $item['price'], 0, ',', '.') ?>đ</td>
                                <td style="text-align: center; vertical-align: middle;">
                                    <button type="button" onclick="removeRow('<?= $rowId ?>')" style="color: #e53e3e; border:none; background:none; cursor:pointer; font-size: 16px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 2px dashed #e2e8f0; padding-top: 20px;">
                <button type="button" onclick="addRow()" class="btn-action" style="background: #fff; border: 1px solid #3182ce; color: #3182ce;">
                    <i class="fa-solid fa-plus"></i> Thêm dòng sản phẩm
                </button>
                <div style="text-align: right;">
                    <p style="margin:0; color: #718096; font-size: 13px; text-transform: uppercase; font-weight: 600;">Tổng tiền thanh toán</p>
                    <h2 id="displayGrandTotal" style="margin:0; color: #e53e3e;">0đ</h2>
                </div>
            </div>

            <div style="margin-top: 30px; display: flex; gap: 15px;">
                <button type="button" class="btn-submit" onclick="submitImportForm('completed')" style="background: #10b981;">
                    <i class="fa-solid fa-check-double"></i> Hoàn tất nhập kho
                </button>
                <button type="button" class="btn-submit" onclick="submitImportForm('draft')" style="background: #f59e0b;">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu nháp
                </button>
                
                <?php if(isset($is_edit) && $is_edit): ?>
                    <a href="/lego_shop_php/adminimport/detail/<?= $receipt['id'] ?>" style="padding: 10px 20px; color: #718096; text-decoration: none; font-weight: 600; background: #edf2f7; border-radius: 6px; display:flex; align-items:center;">Hủy bỏ sửa</a>
                <?php else: ?>
                    <a href="/lego_shop_php/adminimport" style="padding: 10px 20px; color: #718096; text-decoration: none; font-weight: 600; background: #edf2f7; border-radius: 6px; display:flex; align-items:center;">Hủy bỏ</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <script>
        const productsData = <?= json_encode($products ?? []) ?>;
        
        // --- XÁC ĐỊNH TRẠNG THÁI: LÀ SỬA HAY TẠO MỚI ---
        const isEdit = <?= isset($receipt) ? 'true' : 'false' ?>;
        const receiptId = <?= $receipt['id'] ?? 'null' ?>;

        // Hàm thêm dòng
function addRow() {
            const tbody = document.querySelector('#importTable tbody');
            const rowId = 'row_' + Date.now();

            const rowHtml = `
                <tr id="${rowId}" class="product-row">
                    <td class="row-number" style="text-align: center; font-weight: bold; color: #64748b; vertical-align: middle;"></td>
                    <td>
                        <input type="text" class="form-control display-product-input" list="product-suggestions" 
                               placeholder="Gõ tên hoặc mã SKU..." onchange="handleProductSelect(this)" required>
                        <input type="hidden" class="real-product-id" required>
                    </td>
                    <td><input type="number" class="form-control qty-input" value="1" min="1" oninput="calculateRow('${rowId}')" style="text-align:center;"></td>
                    <td><input type="number" class="form-control price-input" placeholder="0" min="0" oninput="calculateRow('${rowId}')" style="text-align:right;"></td>
                    <td style="text-align: right; font-weight: 700; color: #2d3748; vertical-align: middle;" class="row-total">0đ</td>
                    <td style="text-align: center; vertical-align: middle;">
                        <button type="button" onclick="removeRow('${rowId}')" style="color: #e53e3e; border:none; background:none; cursor:pointer; font-size: 16px;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
            
            tbody.insertAdjacentHTML('beforeend', rowHtml);
            updateRowNumbers();
        }

        // Hàm xóa dòng và cập nhật lại mọi thứ
        function removeRow(rowId) {
            document.getElementById(rowId).remove();
            updateGrandTotal();
            updateRowNumbers(); // Đánh lại số thứ tự sau khi xóa
        }

        // Hàm TỰ ĐỘNG CẬP NHẬT SỐ THỨ TỰ (1, 2, 3...)
        function updateRowNumbers() {
            const rows = document.querySelectorAll('#importTable tbody .product-row');
            rows.forEach((row, index) => {
                row.querySelector('.row-number').innerText = index + 1;
            });
        }

        // Khởi tạo thanh tìm kiếm (Đã tắt dấu x)
        function initSelect2() {
            $('.product-select').select2({
                placeholder: "Gõ mã SKU hoặc tên LEGO để tìm...",
                allowClear: false, // <-- TẮT DẤU "x" Ở ĐÂY
                width: '100%',
                language: { noResults: function() { return "Không tìm thấy sản phẩm nào!"; } }
            });
        }
        function calculateRow(rowId) {
            const row = document.getElementById(rowId);
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = qty * price;
            
            if (qty > 0) row.querySelector('.qty-input').style.borderColor = '#e2e8f0';
            if (price > 0) row.querySelector('.price-input').style.borderColor = '#e2e8f0';

            row.querySelector('.row-total').innerText = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
            updateGrandTotal();
        }

        function updateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('#importTable tbody tr').forEach(row => {
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                grandTotal += (qty * price);
            });
            document.getElementById('displayGrandTotal').innerText = new Intl.NumberFormat('vi-VN').format(grandTotal) + 'đ';
        }

        async function submitImportForm(status) {
            const supplierId = document.getElementById('supplier_id').value;
            if (!supplierId) {
                document.getElementById('supplier_id').style.borderColor = '#e53e3e';
                return alert("Vui lòng chọn Nhà cung cấp!");
            } else {
                document.getElementById('supplier_id').style.borderColor = '#e2e8f0';
            }

            const rows = document.querySelectorAll('#importTable tbody tr');
            if (rows.length === 0) return alert("Vui lòng thêm ít nhất một sản phẩm vào phiếu nhập!");

            const productsDataToSend = [];
            let isValid = true;
            let errorMessage = "";

            rows.forEach(row => {
                const productId = row.querySelector('.real-product-id')?.value;
                
                // LỖI 1 ĐÃ FIX: Đổi class tìm kiếm thành .display-product-input cho khớp với HTML
                const searchInput = row.querySelector('.display-product-input'); 
                const qtyInput = row.querySelector('.qty-input');
                const priceInput = row.querySelector('.price-input');
                
                const qty = parseFloat(qtyInput?.value) || 0;
                const price = parseFloat(priceInput?.value) || 0;

                // Thêm câu lệnh if để chống lỗi null
                if (searchInput) searchInput.style.borderColor = '#e2e8f0';
                if (qtyInput) qtyInput.style.borderColor = '#e2e8f0';
                if (priceInput) priceInput.style.borderColor = '#e2e8f0';

                if (!productId) {
                    isValid = false;
                    if (searchInput) searchInput.style.borderColor = '#e53e3e';
                    errorMessage = "Có ô sản phẩm chưa được chọn đúng từ danh sách gợi ý.";
                } else if (qty <= 0) {
                    isValid = false;
                    if (qtyInput) qtyInput.style.borderColor = '#e53e3e';
                    errorMessage = "Số lượng nhập phải lớn hơn 0.";
                } else if (price <= 0) {
                    isValid = false;
                    if (priceInput) priceInput.style.borderColor = '#e53e3e';
                    errorMessage = "Giá nhập vào phải lớn hơn 0.";
                } else {
                    // Đẩy dữ liệu vào mảng đã kiểm duyệt
                    productsDataToSend.push({
                        product_id: productId,
                        quantity: qty,
                        price: price
                    });
                }
            });

            if (!isValid) return alert("LỖI: " + errorMessage + " Vui lòng kiểm tra lại!");

            if(status === 'completed') {
                if(!confirm("Hành động này sẽ tính lại giá vốn (WAC) và cập nhật thẳng vào kho. Bạn không thể sửa phiếu sau khi hoàn tất. Xác nhận tiếp tục?")) return;
            }
            // --- BẮT ĐẦU THÊM LOGIC LẤY NGÀY ---
            let importDate = document.getElementById('import_date').value;
            if (!importDate) {
                // Nếu User cố tình xóa trắng ô ngày, JS sẽ tự động lấy ngày hôm nay theo giờ máy tính
                importDate = new Date().toISOString().split('T')[0]; 
            }
            // --- KẾT THÚC ---
            // LỖI 2 ĐÃ FIX: Dùng mảng productsDataToSend đã được bắt lỗi thay vì map lại từ HTML
            const formData = {
                supplier_id: supplierId,
                status: status,
                products: productsDataToSend,
                import_date: importDate
            };

            const targetUrl = isEdit 
                ? '/lego_shop_php/adminimport/updateDraft/' + receiptId 
                : '/lego_shop_php/adminimport/store';

            try {
                const response = await fetch(targetUrl, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });
                const result = await response.json();
                
                if(result.success) {
                    // LỖI 3 ĐÃ FIX: Dùng đúng biến isEdit và receiptId
                    if (isEdit) {
                        window.location.href = `/lego_shop_php/adminimport/detail/${receiptId}?msg=updated`;
                    } else {
                        window.location.href = '/lego_shop_php/adminimport?msg=success';
                    }
                } else {
                    alert("Lỗi: " + result.message);
                }
            } catch (err) {
                alert("Lỗi kết nối mạng hoặc lỗi server!");
            }
        }
        
        function initSelect2() {
            $('.product-select').select2({
                placeholder: "🔍 Gõ mã SKU hoặc tên LEGO để tìm...",
                allowClear: true,
                width: '100%',
                language: { noResults: function() { return "Không tìm thấy sản phẩm nào!"; } }
            });
        }

        function handleProductSelect(input) {
            const val = input.value;
            const datalist = document.getElementById('product-suggestions');
            const options = datalist.options;
            let foundId = "";
            
            // Dò tìm ID thực sự của sản phẩm dựa trên đoạn text vừa gõ
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === val) {
                    foundId = options[i].getAttribute('data-id');
                    break;
                }
            }

            // Gắn ID vào ô ẩn
            const hiddenInput = input.parentElement.querySelector('.real-product-id');
            hiddenInput.value = foundId;

            // Bắt lỗi: Nếu gõ linh tinh không có trong danh sách
            if (!foundId && val.trim() !== "") {
                alert("Sản phẩm không hợp lệ! Vui lòng click chọn từ danh sách gợi ý.");
                input.value = "";
            }
        }

        $(document).ready(function() {
            initSelect2();
            updateGrandTotal(); // Cập nhật tổng tiền ngay khi load trang
            
            // Chỉ đẻ ra dòng mới nếu là TẠO MỚI (không có dữ liệu cũ)
            <?php if(!isset($receipt_details) || empty($receipt_details)): ?>
                addRow();
            <?php endif; ?>
        });
    </script>
<?php endif; ?>
<div class="table-container">
    <table class="lego-table">
        <thead>
            <tr>
                <th style="width: 100px; text-align: center;">Mã Phiếu</th>
                <th>Thời gian</th>
                <th>Nhà cung cấp</th>
                <th style="text-align: center;">Trạng thái</th>
                <th style="text-align: right;">Tổng giá trị</th>
                <th style="text-align: center;">Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($imports)): ?>
                <?php foreach ($imports as $item): ?>
                <tr>
                    <td style="text-align: center;">
                        <span style="background: #edf2f7; color: #4a5568; padding: 4px 8px; border-radius: 4px; font-weight: 700; font-family: monospace;">
                            #PN-<?= $item['id'] ?>
                        </span>
                    </td>
                    <td style="color: #718096; font-size: 13px;">
                        <?= date('d/m/Y - H:i', strtotime($item['created_at'])) ?>
                    </td>
                    <td style="font-weight: 600; color: #2d3748;">
                        <?= htmlspecialchars($item['supplier_name']) ?>
                    </td>
                    <td style="text-align: center;">
                        <?php if($item['status'] === 'completed'): ?>
                            <span style="background: #d1fae5; color: #059669; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                <i class="fa-solid fa-check"></i> Hoàn tất
                            </span>
                        <?php else: ?>
                            <span style="background: #fef3c7; color: #d97706; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                                <i class="fa-solid fa-pen"></i> Bản nháp
                            </span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: right; font-weight: 700; color: #2b6cb0;">
                        <?= number_format($item['total_amount'], 0, ',', '.') ?>đ
                    </td>
                    <td style="text-align: center;">
                        <a href="/lego_shop_php/adminimport/detail/<?= $item['id'] ?>" class="btn-action" style="color: #3182ce;">
                            <i class="fa-solid fa-circle-info"></i> Kiểm tra
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: #a0aec0;">
                        Chưa có lịch sử nhập kho nào.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Tự động ẩn thông báo alert sau 5 giây
setTimeout(function() {
    let alerts = document.querySelectorAll('.alert-box');
    alerts.forEach(el => {
        el.style.transition = "opacity 0.5s ease";
        el.style.opacity = "0";
        setTimeout(() => el.style.display = 'none', 500);
    });
}, 5000);

</script>
<datalist id="product-suggestions">
        <?php foreach($products as $p): ?>
            <option data-id="<?= $p['id'] ?>" value="<?= htmlspecialchars($p['name']) ?> (Tồn: <?= $p['stock_quantity'] ?>)"></option>
        <?php endforeach; ?>
    </datalist>