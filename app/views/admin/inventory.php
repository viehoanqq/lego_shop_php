<style>
    /* ===== CÁC CSS CŨ VÀ MỚI ===== */
    * { box-sizing: border-box; }
    
    /* TABS CSS */
    .tabs-container { display: flex; gap: 15px; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 0; }
    .tab-btn { padding: 12px 24px; background: transparent; border: none; font-size: 15px; font-weight: 600; color: #64748b; cursor: pointer; position: relative; transition: 0.3s; margin-bottom: -2px; border-bottom: 2px solid transparent; display: flex; align-items: center; gap: 8px;}
    .tab-btn:hover { color: #3182ce; }
    .tab-btn.active { color: #3182ce; border-bottom: 2px solid #3182ce; }
    .tab-content { display: none; animation: fadeIn 0.3s ease; }
    .tab-content.active { display: block; }

    .header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; gap: 20px; background: #fff; padding: 20px 25px; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .header-left { flex: 1; display: flex; flex-direction: column; }
    .header-left h2 { margin: 0; font-size: 20px; font-weight: 700; }
    .header-left p { color: #718096; font-size: 14px; margin-top: 4px; }
    .filter-form { display: flex; gap: 12px; align-items: center; margin-top: 15px; }
    
    .search-wrapper { position: relative; flex: 2; }
    .search-wrapper input { width: 100%; height: 42px; padding: 0 110px 0 38px; border-radius: 10px; border: 1px solid #e2e8f0; outline: none; transition: 0.2s; }
    .search-wrapper input:focus { border-color: #3182ce; box-shadow: 0 0 0 2px rgba(49,130,206,0.15); }
    .search-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #a0aec0; }
    .btn-search-inside { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); height: 30px; padding: 0 14px; border-radius: 6px; border: none; background: #3182ce; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; }
    .btn-search-inside:hover { background: #2b6cb0; }
    
    .form-control { height: 42px; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0 12px; background: #fff; transition: 0.2s; }
    .form-control:focus { border-color: #3182ce; box-shadow: 0 0 0 2px rgba(49,130,206,0.15); }
    .btn-filter-action { height: 42px; padding: 0 16px; border-radius: 10px; border: none; background: #3182ce; color: #fff; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s; }
    .btn-filter-action:hover { background: #2b6cb0; }
    
    .table-container { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .lego-table { width: 100%; border-collapse: collapse; }
    .lego-table thead { background: #f8fafc; }
    .lego-table th { text-align: left; padding: 15px; color: #4a5568; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #edf2f7; }
    .lego-table td { padding: 15px; border-bottom: 1px solid #edf2f7; vertical-align: middle; }
    .lego-table tbody tr:hover { background: #f9fafb; }
    .product-cell { display: flex; align-items: center; gap: 12px; }
    .img-product { width: 52px; height: 52px; border-radius: 10px; object-fit: cover; border: 1px solid #e2e8f0; }
    .stock-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .stock-low { background: #fffaf0; color: #dd6b20; border: 1px solid #fbd38d; }
    .stock-empty { background: #fff5f5; color: #e53e3e; border: 1px solid #feb2b2; }
    
    .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 25px; }
    .page-link { padding: 8px 14px; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #4a5568; background: #fff; font-weight: 600; transition: 0.2s; }
    .page-link:hover { background: #edf2f7; }
    .page-link.active { background: #3182ce; color: #fff; border-color: #3182ce; }

    /* MODAL CSS */
    .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.45); backdrop-filter: blur(2px); }
    .modal-content { background: #fff; padding: 25px; width: 800px; max-width: 90%; border-radius: 14px; margin: 50px auto; box-shadow: 0 10px 40px rgba(0,0,0,0.1); animation: fadeIn 0.25s ease; max-height: 85vh; overflow-y: auto;}
    .modal-content h3 { margin-top: 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px;}
    
    .combo-box-wrapper { position: relative; width: 100%; }
    .combo-search-input { width: 100%; padding: 10px; padding-right: 35px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; background-color: #fff; font-size: 13px; color: #1a202c; }
    .combo-search-input:focus { border-color: #3182ce; box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1); }
    .combo-dropdown-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; transition: transform 0.2s; }
    .combo-dropdown-list { display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; max-height: 250px; overflow-y: auto; z-index: 9999; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 5px 0; margin: 0; list-style: none; }
    .combo-item { padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #1e293b; display: flex; gap: 12px; align-items: center; }
    .combo-item:last-child { border-bottom: none; }
    .combo-item:hover { background-color: #f8fafc; color: #3182ce; }
    .combo-empty { padding: 10px 15px; color: #94a3b8; text-align: center; font-style: italic; }

    .btn-add-row { background: #fff; color: #3182ce; border: 1px dashed #3182ce; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; margin-top: 15px; display: inline-flex; align-items: center; gap: 5px; }
    .btn-add-row:hover { background: #ebf8ff; }
    .btn-action-small { background: #edf2f7; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; color: #4a5568; transition: 0.2s;}
    .btn-action-small:hover { background: #3182ce; color: white; border-color: #3182ce;}
    
    @keyframes fadeIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<div class="tabs-container">
    <button class="tab-btn active" onclick="switchTab('overview')">
        <i class="fa-solid fa-boxes-stacked"></i> Tổng quan & Tra cứu
    </button>
    <button class="tab-btn" onclick="switchTab('alerts')">
        <i class="fa-solid fa-triangle-exclamation"></i> Cảnh báo & Tùy chỉnh
        <?php if($totalItems > 0): ?>
            <span style="background:#e53e3e; color:white; padding:2px 8px; border-radius:12px; font-size:11px; margin-left:6px;"><?= $totalItems ?></span>
        <?php endif; ?>
    </button>
</div>

<div id="tab-overview" class="tab-content active">

    <div class="header" style="margin-bottom: 15px;">
        <div class="header-left">
            <h2>Tra cứu tồn kho theo ngày</h2>
            <p style="color: #718096; font-size: 14px;">Chọn một ngày trong quá khứ để xem sổ sách (Chỉ tính các đơn hàng và phiếu nhập đã hoàn tất).</p>
            
            <div style="display: flex; gap: 15px; align-items: center; margin-top: 15px; width: 100%;">
                <div class="search-wrapper" style="width: 300px; flex: unset;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="quickSearchOverview" onkeyup="filterOverviewTable()" placeholder="Tìm Tên hoặc SKU..." style="padding-right: 15px;">
                </div>
                
                <div style="flex: 1;"></div>
                
                <span style="font-size: 13px; font-weight: 600; color: #4a5568;">Tra cứu theo ngày:</span>
                <input type="date" id="snapshotDate" class="form-control" style="width: 150px; margin: 0;" value="<?= date('Y-m-d') ?>" onkeydown="if(event.key === 'Enter') fetchSnapshot()">
                <button class="btn-filter-action" onclick="fetchSnapshot()">Tra cứu <i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </div>
    </div>

    <div class="table-container" style="max-height: 600px; overflow-y: auto;">
        <table class="lego-table" id="overviewTable">
            <thead style="position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                <tr>
                    <th style="width: 35%;">Sản phẩm</th>
                    <th style="text-align: right;">Giá nhập (WAC)</th>
                    <th style="text-align: center;">Tồn kho tính đến ngày tra cứu</th>
                    <th style="text-align: center;">Tổng giá trị vốn</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($all_products as $ap): ?>
                    <?php 
                        $wac = $ap['import_price'] ?? 0; 
                        $val = $ap['stock_quantity'] * $wac;
                    ?>
                    <tr class="overview-row" data-search="<?= strtolower($ap['name'] . ' ' . $ap['sku']) ?>">
                        <td>
                            <div class="product-cell">
                                <img src="/lego_shop_php/public/assets/images/<?= $ap['image_url'] ?? 'default.jpg' ?>" class="img-product" onerror="this.src='https://placehold.co/52x52?text=LEGO'">
                                <div>
                                    <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($ap['name']) ?></div>
                                    <div style="font-size: 11px; color: #a0aec0;">SKU: <?= strtoupper($ap['sku']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: right; color: #4a5568; font-weight: 600;"><?= number_format($wac, 0, ',', '.') ?> đ</td>
                        
                        <td style="text-align: center;">
                            <span class="snap-qty" style="font-weight: 700; font-size: 15px; color: <?= $ap['stock_quantity'] <= $ap['min_stock_level'] ? '#e53e3e' : '#38a169' ?>;">
                                <?= $ap['stock_quantity'] ?>
                            </span>
                        </td>
                        
                        <td style="text-align: right; color: #2b6cb0; font-weight: 700;">
                            <span class="snap-val"><?= number_format($val, 0, ',', '.') ?></span> đ
                        </td>
                        
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <button class="btn-action-small" onclick="openHistory(<?= $ap['id'] ?>, '<?= addslashes($ap['name']) ?>')" title="Thẻ kho (Lịch sử)"><i class="fa-solid fa-clock-rotate-left"></i></button>
                                <button class="btn-action-small" onclick="openAdjust(<?= $ap['id'] ?>, '<?= addslashes($ap['name']) ?>', <?= $ap['stock_quantity'] ?>)" title="Điều chỉnh số lượng"><i class="fa-solid fa-pen"></i></button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="tab-alerts" class="tab-content">
    <div class="header">
        <div class="header-left">
            <h2>Cảnh báo hết hàng trong kho</h2>
            <p style="color: #718096; font-size: 14px;">Danh sách sản phẩm có số lượng tồn kho chạm mức tối thiểu.</p>

            <form action="/lego_shop_php/admininventory" method="GET" class="filter-form" style="width: 100%;">
                <input type="hidden" name="tab" value="alerts"> 
                
                <div class="search-wrapper" style="width: 250px; flex: unset;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm tên hoặc mã SKU..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                </div>

                <div style="display: flex; align-items: center; gap: 10px; margin-left: 10px;">
                    <span style="font-size: 13px; font-weight: 600; color: #4a5568;">Sản phẩm tồn dưới :</span>
                    <input type="number" name="custom_threshold" class="form-control" style="width: 200px;" placeholder="Nhập số lượng sản phẩm" value="<?= htmlspecialchars($_GET['custom_threshold'] ?? '') ?>" min="0">
                </div>
                
                <button type="submit" class="btn-filter-action" style="background: #2b6cb0;">Lọc <i class="fa-solid fa-magnifying-glass"></i></button>
                
                <div style="flex: 1;"></div>
                
                <button type="button" class="btn-filter-action" onclick="openSettingModal()">
                    <i class="fa-solid fa-pen-to-square"></i> Cập nhật mức cảnh báo
                </button>
            </form>
        </div>
    </div>

    <div class="table-container">
        <table class="lego-table">
            <thead>
                <tr>
                    <th style="width: 35%;">Sản phẩm</th>
                    <th>Dòng LEGO</th>
                    <th style="text-align: center;">Tồn kho thực tế</th>
                    <th style="text-align: center;">Ngưỡng cảnh báo</th>
                    <th style="text-align: center;">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($products)): ?>
                    <?php foreach ($products as $p): ?>
                    <tr style="text-align: left;">
                        <td>
                            <div class="product-cell">
                                <img src="/lego_shop_php/public/assets/images/<?= $p['main_image'] ?? 'default.jpg' ?>" class="img-product" onerror="this.src='https://placehold.co/52x52?text=LEGO'">
                                <div>
                                    <div style="font-weight: 700; color: #3182ce;"><?= htmlspecialchars($p['name']) ?></div>
                                    <div style="font-size: 11px; color: #a0aec0;">SKU: <?= strtoupper($p['sku']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span style="background: #edf2f7; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; color: #4a5568;"><?= htmlspecialchars($p['category_name']) ?></span></td>
                        <td style="text-align: center;">
                            <b style="color: #e53e3e; font-size: 16px;"><?= $p['stock_quantity'] ?></b>
                        </td>
                        <td style="text-align: center;"><span style="color: #718096; font-weight: 600;">≤ <?= $p['min_stock_level'] ?></span></td>
                        <td style="text-align: center;">
    <?php if ($p['stock_quantity'] <= 0): ?>
        <span class="stock-badge stock-empty">Hết hàng</span>
    <?php elseif ($p['stock_quantity'] <= $p['min_stock_level']): ?>
        <span class="stock-badge stock-low">Sắp hết</span>
    <?php else: ?>
        <span class="stock-badge" style="background: #e6fffa; color: #2f855a; border: 1px solid #2f855a;">Còn hàng</span>
    <?php endif; ?>
</td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; padding: 50px;">Tuyệt vời! Không có sản phẩm nào nằm trong danh sách cảnh báo.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?tab=alerts&page=<?= $currentPage - 1 ?>&keyword=<?= urlencode($keyword) ?>&custom_threshold=<?= $custom_threshold ?? '' ?>" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?tab=alerts&page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>&custom_threshold=<?= $custom_threshold ?? '' ?>" class="page-link <?= ($i == $currentPage) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?tab=alerts&page=<?= $currentPage + 1 ?>&keyword=<?= urlencode($keyword) ?>&custom_threshold=<?= $custom_threshold ?? '' ?>" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<div id="adjustModal" class="modal">
    <div class="modal-content" style="width: 500px;">
        <h3><i class="fa-solid fa-pen-to-square" style="color:#3182ce;"></i> Kiểm kho & Điều chỉnh</h3>
        <p style="margin-bottom: 10px;">Sản phẩm: <b id="adjProductName" style="color: #1e293b;"></b></p>
        <p style="margin-bottom: 20px;">Tồn trên hệ thống: <b id="adjOldStock" style="color:#e53e3e; font-size: 18px;"></b></p>
        
        <input type="hidden" id="adjProductId">
        
        <label style="font-weight: 600; display: block; margin-bottom: 5px;">Tồn kho đếm thực tế:</label>
        <input type="number" id="adjRealStock" class="form-control" min="0" style="font-size: 16px; font-weight: bold; color: #3182ce;">
        
        <label style="font-weight: 600; display: block; margin-bottom: 5px;">Lý do chênh lệch (Bắt buộc ghi chú):</label>
        <input type="text" id="adjReason" class="form-control" placeholder="VD: Hàng móp méo, đếm sai tháng trước...">
        
        <div style="text-align: right; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
            <button onclick="document.getElementById('adjustModal').style.display='none'" class="btn-filter-action" style="background:#edf2f7; color:#4a5568; border:none; display:inline-flex;">Hủy bỏ</button>
            <button onclick="submitAdjust()" class="btn-filter-action" style="display: inline-flex; margin-left: 10px;"><i class="fa-solid fa-floppy-disk"></i> Lưu điều chỉnh</button>
        </div>
    </div>
</div>

<div id="historyModal" class="modal">
    <div class="modal-content" style="width: 800px;">
        <h3><i class="fa-solid fa-clock-rotate-left" style="color:#3182ce;"></i> Thẻ kho (Lịch sử Nhập / Xuất / Điều chỉnh)</h3>
        <p style="margin-bottom: 15px;">Sản phẩm: <b id="histProductName"></b></p>
        
        <table class="lego-table" style="font-size: 13px;">
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th style="text-align: center;">Phân loại</th>
                    <th style="text-align: center;">Biến động</th>
                    <th>Mã tham chiếu / Ghi chú</th>
                </tr>
            </thead>
            <tbody id="histBody">
            </tbody>
        </table>
        
        <div style="text-align: right; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 15px;">
            <button onclick="document.getElementById('historyModal').style.display='none'" class="btn-filter-action" style="background:#edf2f7; color:#4a5568; border:none; display:inline-flex; float:right;">Đóng lại</button>
            <div style="clear: both;"></div>
        </div>
    </div>
</div>

<div id="settingModal" class="modal">
    <div class="modal-content">
        <h3><i class="fa-solid fa-sliders" style="color:#3182ce;"></i> Cập nhật mức cảnh báo (Min Stock)</h3>
        
        <table class="lego-table" id="updateTable">
            <thead>
                <tr>
                    <th style="width: 60%; background: #fff;">Chọn sản phẩm</th>
                    <th style="width: 25%; text-align: center; background: #fff;">Mức cảnh báo mới</th>
                    <th style="width: 15%; text-align: center; background: #fff;">Xóa</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

        <button type="button" class="btn-add-row" onclick="addRow()"><i class="fa-solid fa-plus"></i> Thêm sản phẩm khác</button>

        <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #e2e8f0; padding-top: 20px;">
            <button onclick="closeSettingModal()" class="btn-filter-action" style="background:#edf2f7; color:#4a5568; border:none;">Hủy bỏ</button>
            <button onclick="submitBulkUpdate()" class="btn-filter-action"><i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi</button>
        </div>
    </div>
</div>

<script>
    const productsData = <?= json_encode($all_products ?? []) ?>;

    // --- 1. JS XỬ LÝ TABS ---
    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.querySelector(`.tab-btn[onclick="switchTab('${tabId}')"]`).classList.add('active');
        document.getElementById(`tab-${tabId}`).classList.add('active');
    }

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('custom_threshold') || urlParams.has('keyword') || urlParams.get('tab') === 'alerts') {
        switchTab('alerts');
    }

    // --- 2. TÌM KIẾM NHANH TAB TỔNG QUAN ---
    function filterOverviewTable() {
        let input = document.getElementById("quickSearchOverview").value.toLowerCase();
        let rows = document.querySelectorAll(".overview-row");
        rows.forEach(row => {
            let searchData = row.getAttribute("data-search");
            row.style.display = searchData.includes(input) ? "" : "none";
        });
    }

    // --- 3. API TRA CỨU NGÀY ---
    async function fetchSnapshot() {
        const date = document.getElementById('snapshotDate').value;
        if(!date) return;
        
        try {
            const res = await fetch(`/lego_shop_php/admininventory/getSnapshotAjax?date=${date}`);
            const result = await res.json();
            
            if (result.success) {
                const rows = document.querySelectorAll('#overviewTable tbody tr');
                
                rows.forEach((row, index) => {
                    const snapItem = result.data[index];
                    if (snapItem) {
                        let qty = parseInt(snapItem.historical_stock);
                        row.querySelector('.snap-qty').innerText = qty;
                        
                        let wac = parseFloat(snapItem.import_price || 0);
                        let val = qty * wac;
                        row.querySelector('.snap-val').innerText = new Intl.NumberFormat('vi-VN').format(val);
                        
                        row.querySelector('.snap-qty').style.color = qty <= 0 ? '#e53e3e' : '#38a169';
                    }
                });
                alert('Tra cứu thành công!')
            }
        } catch (err) {
            console.error("Lỗi kết nối khi tra cứu!");
        }
    }

    // --- 4. API KIỂM KHO (MODAL ĐIỀU CHỈNH) ---
    function openAdjust(id, name, oldStock) {
        document.getElementById('adjProductId').value = id;
        document.getElementById('adjProductName').innerText = name;
        document.getElementById('adjOldStock').innerText = oldStock;
        document.getElementById('adjRealStock').value = oldStock;
        document.getElementById('adjReason').value = '';
        document.getElementById('adjustModal').style.display = 'block';
    }

    async function submitAdjust() {
        const pid = document.getElementById('adjProductId').value;
        const real = document.getElementById('adjRealStock').value;
        const reason = document.getElementById('adjReason').value;
        
        if (real === "") return alert('Vui lòng nhập số lượng đếm được!');
        if (!reason) return alert('Vui lòng ghi chú lý do điều chỉnh kho!');

        try {
            const res = await fetch('/lego_shop_php/admininventory/adjustStockAjax', {
                method: 'POST', 
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({product_id: pid, real_stock: real, reason: reason})
            });
            const result = await res.json();
            if(result.success) { 
                alert('Đã cập nhật kho thành công!'); 
                location.reload(); 
            } else { alert('Lỗi: ' + result.message); }
        } catch(err) { alert('Lỗi mạng!'); }
    }

    // --- 5. API LẤY THẺ KHO (GẮN LINK TỰ ĐỘNG BẰNG REGEX) ---
    async function openHistory(id, name) {
        document.getElementById('histProductName').innerText = name;
        document.getElementById('historyModal').style.display = 'block';
        document.getElementById('histBody').innerHTML = '<tr><td colspan="4" style="text-align:center;">Đang tải dữ liệu...</td></tr>';

        try {
            const res = await fetch(`/lego_shop_php/admininventory/getStockCardAjax?product_id=${id}`);
            const result = await res.json();
            
            let html = '';
            if (result.data.length === 0) { 
                html = '<tr><td colspan="4" style="text-align:center;">Chưa có lịch sử giao dịch.</td></tr>'; 
            } else {
                result.data.forEach(item => {
                    let isPlus = item.qty_change > 0;
                    let color = isPlus ? '#38a169' : '#e53e3e';
                    let sign = isPlus ? '+' : '';
                    
                    let typeBadge = '';
                    if (item.type === 'import') {
                        typeBadge = '<span style="background:#e6fffa; color:#2f855a; padding:3px 8px; border-radius:12px;">Nhập kho</span>';
                    } else if (item.type === 'export') {
                        typeBadge = '<span style="background:#fff5f5; color:#c53030; padding:3px 8px; border-radius:12px;">Xuất bán</span>';
                    } else {
                        typeBadge = '<span style="background:#fffaf0; color:#c05621; padding:3px 8px; border-radius:12px;">Điều chỉnh</span>';
                    }

                    // TỰ ĐỘNG GẮN LINK CHO MÃ ĐƠN HÀNG VÀ PHIẾU NHẬP BẰNG REGEX
                    let safeNote = item.note;
                    safeNote = safeNote.replace(/PN-(\d+)/g, '<a href="/lego_shop_php/adminimport/detail/$1" target="_blank" style="color: #3182ce; font-weight: 700; text-decoration: underline;">PN-$1</a>');
                    safeNote = safeNote.replace(/DH-(\d+)/g, '<a href="/lego_shop_php/adminorder/detail/$1" target="_blank" style="color: #e53e3e; font-weight: 700; text-decoration: underline;">DH-$1</a>');

                    html += `<tr>
                        <td style="color:#718096; font-size:12px;">${item.created_at}</td>
                        <td style="text-align: center;">${typeBadge}</td>
                        <td style="color:${color}; font-weight:bold; font-size:15px; text-align:center;">${sign}${item.qty_change}</td>
                        <td>${safeNote}</td>
                    </tr>`;
                });
            }
            document.getElementById('histBody').innerHTML = html;
        } catch(err) {
            document.getElementById('histBody').innerHTML = '<tr><td colspan="4" style="text-align:center; color:red;">Lỗi kết nối tải dữ liệu.</td></tr>';
        }
    }

    // --- 6. JS MODAL CẬP NHẬT MỨC CẢNH BÁO ---
    function openSettingModal() {
        document.getElementById('settingModal').style.display = 'block';
        document.querySelector('#updateTable tbody').innerHTML = ''; 
        addRow(); 
    }

    function closeSettingModal() { document.getElementById('settingModal').style.display = 'none'; }

    function addRow() {
        const tbody = document.querySelector('#updateTable tbody');
        const rowId = 'row_' + Date.now();
        
        let listHtml = productsData.map(p => {
            let searchStr = (p.name + " " + p.sku).toLowerCase();
            let safeName = p.name.replace(/'/g, "\\'");
            let safeImg = p.image_url ? p.image_url : 'default.jpg';
            return `
            <li class="combo-item" data-search="${searchStr}" onclick="selectProduct('${rowId}', ${p.id}, '${safeName}', ${p.min_stock_level})">
                <img src="/lego_shop_php/public/assets/images/${safeImg}" style="width:40px; height:40px; border-radius:4px; object-fit:cover; border: 1px solid #cbd5e1;" onerror="this.src='https://placehold.co/40x40?text=LEGO'">
                <div style="flex: 1;">
                    <div style="font-weight: 700;">${p.name}</div>
                    <span class="combo-item-sku">SKU: ${p.sku} | Mức cảnh báo cũ: <b style="color:#dd6b20">${p.min_stock_level}</b></span>
                </div>
            </li>`;
        }).join('');

        const rowHtml = `
            <tr id="${rowId}">
                <td style="padding: 10px 0;">
                    <div class="combo-box-wrapper" id="combo_${rowId}">
                        <input type="text" class="combo-search-input" placeholder="Gõ tên hoặc mã sản phẩm..." onfocus="openDropdown('${rowId}')" onkeyup="filterDropdown('${rowId}', this.value)" autocomplete="off">
                        <i class="fa-solid fa-chevron-down combo-dropdown-icon"></i>
                        <input type="hidden" class="real-product-id">
                        <ul class="combo-dropdown-list">
                            ${listHtml}
                            <li class="combo-empty" style="display:none;">Không tìm thấy sản phẩm...</li>
                        </ul>
                    </div>
                </td>
                <td style="padding: 10px; text-align:center;">
                    <input type="number" class="form-control min-stock-input" value="0" min="0" style="text-align:center; width: 80px; margin: 0 auto;">
                </td>
                <td style="padding: 10px; text-align: center;">
                    <button type="button" onclick="document.getElementById('${rowId}').remove();" style="color: #e53e3e; border:none; background:none; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', rowHtml);
    }

    function openDropdown(rowId) {
        document.querySelectorAll('.combo-dropdown-list').forEach(el => el.style.display = 'none');
        const combo = document.getElementById(`combo_${rowId}`);
        combo.querySelector('.combo-dropdown-list').style.display = 'block';
        filterDropdown(rowId, '');
    }

    function filterDropdown(rowId, keyword) {
        const combo = document.getElementById(`combo_${rowId}`);
        const items = combo.querySelectorAll('.combo-item');
        let hasResult = false;
        keyword = keyword.toLowerCase().trim();

        if(keyword !== '') combo.querySelector('.real-product-id').value = '';

        items.forEach(item => {
            if (item.getAttribute('data-search').includes(keyword)) {
                item.style.display = 'flex'; hasResult = true;
            } else {
                item.style.display = 'none';
            }
        });
        combo.querySelector('.combo-empty').style.display = hasResult ? 'none' : 'block';
    }

    function selectProduct(rowId, productId, productName, oldMin) {
        const combo = document.getElementById(`combo_${rowId}`);
        combo.querySelector('.combo-search-input').value = productName;
        combo.querySelector('.real-product-id').value = productId;
        combo.querySelector('.combo-dropdown-list').style.display = 'none';
        document.getElementById(rowId).querySelector('.min-stock-input').value = oldMin;
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.combo-box-wrapper')) {
            document.querySelectorAll('.combo-dropdown-list').forEach(el => el.style.display = 'none');
        }
    });

    async function submitBulkUpdate() {
        const rows = document.querySelectorAll('#updateTable tbody tr');
        if (rows.length === 0) return alert("Vui lòng thêm ít nhất một sản phẩm!");

        const dataToSend = [];
        let isValid = true;

        rows.forEach(row => {
            const pId = row.querySelector('.real-product-id').value;
            const minStock = row.querySelector('.min-stock-input').value;
            
            if (!pId) {
                isValid = false;
                row.querySelector('.combo-search-input').style.borderColor = '#e53e3e';
            } else {
                row.querySelector('.combo-search-input').style.borderColor = '#e2e8f0';
                dataToSend.push({ product_id: pId, min_stock: minStock });
            }
        });

        if (!isValid) return alert("Có sản phẩm chưa được chọn đúng từ danh sách. Vui lòng kiểm tra lại viền đỏ!");

        try {
            const response = await fetch('/lego_shop_php/admininventory/updateBulkMinStock', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ items: dataToSend })
            });
            const result = await response.json();
            if(result.success) {
                alert("Cập nhật thành công!");
                window.location.href = "/lego_shop_php/admininventory?tab=alerts";
            } else {
                alert("Lỗi: " + result.message);
            }
        } catch (err) {
            alert("Lỗi kết nối mạng!");
        }
    }
</script>