<style>
    /* CSS dùng chung với trang quản lý sản phẩm */
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

<div class="admin-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding: 10px;">
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

<?php if(isset($is_form) && $is_form === true): ?>
    <div class="form-container">
        <h3 style="margin-top:0; color: #2d3748;"><i class="fa-solid fa-cart-plus"></i> Lập phiếu nhập kho mới</h3>
        <form id="importForm" style="margin-top: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 20px; background: #f7fafc; padding: 15px; border-radius: 8px;">
                <div class="form-group" style="margin: 0;">
                    <label>Nhà cung cấp <span style="color:red">*</span></label>
                    <select id="supplier_id" class="form-control" required>
                        <option value="">-- Chọn nhà cung cấp --</option>
                        <?php foreach($suppliers as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin: 0;">
                    <label>Nhân viên tiếp nhận</label>
                    <input type="text" class="form-control" value="<?= $_SESSION['admin_name'] ?>" readonly style="background: #edf2f7; cursor: not-allowed; color: #4a5568; font-weight: 600;">
                </div>
            </div>

            <table class="lego-table" id="importTable" style="margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th style="width: 45%;">Sản phẩm nhập</th>
                        <th style="width: 15%; text-align: center;">Số lượng</th>
                        <th style="width: 20%; text-align: right;">Giá nhập (đ)</th>
                        <th style="width: 15%; text-align: right;">Thành tiền</th>
                        <th style="width: 5%; text-align: center;"></th>
                    </tr>
                </thead>
                <tbody>
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
                <a href="/lego_shop_php/adminimport" style="padding: 10px 20px; color: #718096; text-decoration: none; font-weight: 600; background: #edf2f7; border-radius: 6px; display:flex; align-items:center;">Hủy bỏ</a>
            </div>
        </form>
    </div>

    <script>
        const productsData = <?= json_encode($products ?? []) ?>;

        function addRow() {
            const tbody = document.querySelector('#importTable tbody');
            const rowId = 'row_' + Date.now();
            let options = productsData.map(p => `<option value="${p.id}">${p.name} (Tồn: ${p.stock_quantity})</option>`).join('');

            const rowHtml = `
                <tr id="${rowId}">
                    <td>
                        <select class="form-control" style="width:100%" required>
                            <option value="">-- Chọn sản phẩm --</option>
                            ${options}
                        </select>
                    </td>
                    <td><input type="number" class="form-control qty-input" value="1" min="1" oninput="calculateRow('${rowId}')" style="text-align:center;"></td>
                    <td><input type="number" class="form-control price-input" placeholder="0" min="0" oninput="calculateRow('${rowId}')" style="text-align:right;"></td>
                    <td style="text-align: right; font-weight: 700; color: #2d3748;" class="row-total">0đ</td>
                    <td style="text-align: center;">
                        <button type="button" onclick="document.getElementById('${rowId}').remove(); updateGrandTotal();" style="color: #e53e3e; border:none; background:none; cursor:pointer;">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>`;
            tbody.insertAdjacentHTML('beforeend', rowHtml);
        }

        function calculateRow(rowId) {
            const row = document.getElementById(rowId);
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = qty * price;
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
            const rows = document.querySelectorAll('#importTable tbody tr');
            if (rows.length === 0) return alert("Vui lòng thêm ít nhất một sản phẩm!");

            // Xác nhận nếu chọn "Hoàn tất"
            if(status === 'completed') {
                if(!confirm("Hành động này sẽ tính lại giá và cập nhật thẳng vào kho. Bạn không thể sửa phiếu sau khi hoàn tất. Tiếp tục?")) return;
            }

            const formData = {
                supplier_id: document.getElementById('supplier_id').value,
                status: status, // Truyền trạng thái (draft/completed)
                products: Array.from(rows).map(row => ({
                    product_id: row.querySelector('select').value,
                    quantity: row.querySelector('.qty-input').value,
                    price: row.querySelector('.price-input').value
                }))
            };

            try {
                const response = await fetch('/lego_shop_php/adminimport/store', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });
                const result = await response.json();
                if(result.success) {
                    window.location.href = '/lego_shop_php/adminimport?msg=success';
                } else {
                    window.location.href = '/lego_shop_php/adminimport?error=1';
                }
            } catch (err) {
                window.location.href = '/lego_shop_php/adminimport?error=1';
            }
        }

        window.onload = addRow;
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
                            <i class="fa-solid fa-circle-info"></i> Xem
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