<style>
    /* ===== CÁC CSS CHUNG ===== */
    * { box-sizing: border-box; }
    
    /* TABS CSS */
    .tabs-container { display: flex; gap: 15px; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 0; }
    .tab-btn { padding: 12px 24px; background: transparent; border: none; font-size: 15px; font-weight: 600; color: #64748b; cursor: pointer; position: relative; transition: 0.3s; margin-bottom: -2px; border-bottom: 2px solid transparent; display: flex; align-items: center; gap: 8px;}
    .tab-btn:hover { color: #3b82f6; }
    .tab-btn.active { color: #3b82f6; border-bottom: 2px solid #3b82f6; }
    .tab-content { display: none; animation: fadeIn 0.3s ease; }
    .tab-content.active { display: block; }

    .header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; gap: 20px; background: #fff; padding: 20px 25px; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .header-left { flex: 1; display: flex; flex-direction: column; }
    .header-left h2 { margin: 0; font-size: 20px; font-weight: 700; color: #1e293b; }
    .header-left p { color: #64748b; font-size: 14px; margin-top: 4px; }
    .filter-form { display: flex; gap: 12px; align-items: center; margin-top: 15px; width: 100%; }
    
    .search-wrapper { position: relative; flex: unset; width: 280px; }
    .search-wrapper input { width: 100%; height: 40px; padding: 0 15px 0 38px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; transition: 0.2s; font-size: 13px; }
    .search-wrapper input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.15); }
    .search-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    
    .form-control { height: 40px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 12px; background: #fff; transition: 0.2s; font-size: 13px; outline: none; }
    .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.15); }
    
    .btn-filter-action { height: 40px; padding: 0 16px; border-radius: 8px; border: none; background: #3b82f6; color: #fff; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: 0.2s; font-size: 13px; }
    .btn-filter-action:hover { background: #2563eb; }
    .btn-outline { background: #fff; color: #3b82f6; border: 1px solid #3b82f6; }
    .btn-outline:hover { background: #eff6ff; }
    
    .table-container { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .lego-table { width: 100%; border-collapse: collapse; }
    .lego-table thead { background: #f8fafc; }
    .lego-table th { text-align: left; padding: 15px; color: #475569; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; font-weight: 700; }
    .lego-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .lego-table tbody tr:hover { background: #f8fafc; }
    
    .product-cell { display: flex; align-items: center; gap: 12px; }
    .img-product { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0; background: #fff; }
    
    .stock-badge { padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; width: 120px; justify-content: center; }
    .stock-low { background: #fef08a; color: #a16207; border: 1px solid #fde047; }
    .stock-empty { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; animation: pulseRed 1.5s infinite; }
    .stock-ok { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    
    @keyframes pulseRed {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .filter-threshold-group {
        display: flex; align-items: center; gap: 0;
        background: #f8fafc; border: 1px solid #cbd5e1;
        border-radius: 8px; padding: 2px;
    }
    .filter-threshold-label { font-size: 12px; font-weight: 700; color: #475569; padding: 0 10px; white-space: nowrap; }
    .filter-threshold-input { border: 1px solid #e2e8f0; background: #fff; height: 34px; border-radius: 6px; width: 90px; padding: 0 10px; outline: none; font-size: 13px; }
    .filter-threshold-input:focus { border-color: #3b82f6; }
    .filter-threshold-btn { background: #ff390d; color: #fff; height: 34px; padding: 0 12px; border-radius: 6px; border: none; font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 6px; margin-left: 4px; font-size: 12px; }
    .filter-threshold-btn:hover { background: #8e1a00; }
    
    .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 25px; }
    .page-link { padding: 8px 14px; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #475569; background: #fff; font-weight: 600; transition: 0.2s; }
    .page-link:hover { background: #f8fafc; }
    .page-link.active { background: #3b82f6; color: #fff; border-color: #3b82f6; }

    .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(2px); }
    .modal-content { background: #fff; padding: 25px; width: 800px; max-width: 90%; border-radius: 14px; margin: 50px auto; box-shadow: 0 10px 40px rgba(0,0,0,0.15); animation: fadeIn 0.25s ease; max-height: 85vh; overflow-y: auto;}
    .modal-content h3 { margin-top: 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px;}
    
    .combo-box-wrapper { position: relative; width: 100%; }
    .combo-search-input { width: 100%; padding: 10px 35px 10px 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; font-size: 13px; color: #1e293b; }
    .combo-search-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .combo-dropdown-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; }
    .combo-dropdown-list { display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; max-height: 250px; overflow-y: auto; z-index: 9999; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 5px 0; margin: 0; list-style: none; }
    .combo-item { padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #1e293b; display: flex; gap: 12px; align-items: center; }
    .combo-item:last-child { border-bottom: none; }
    .combo-item:hover { background-color: #f8fafc; }
    .combo-empty { padding: 10px 15px; color: #94a3b8; text-align: center; font-style: italic; }

    .btn-add-row { background: #fff; color: #3b82f6; border: 1px dashed #3b82f6; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; margin-top: 15px; display: inline-flex; align-items: center; gap: 5px; }
    .btn-add-row:hover { background: #eff6ff; }
    .btn-action-small { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; color: #475569; transition: 0.2s;}
    .btn-action-small:hover { background: #3b82f6; color: white; border-color: #3b82f6;}
    
    @keyframes fadeIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<div class="tabs-container">
    <button class="tab-btn active" onclick="switchTab('overview')">
        <i class="fa-solid fa-boxes-stacked"></i> Tổng quan & Tra cứu
    </button>
    <button class="tab-btn" onclick="switchTab('alerts')">
        <i class="fa-solid fa-triangle-exclamation"></i> Cảnh báo & Tùy chỉnh
        <?php if($totalItems > 0): ?>
            <span style="background:#ef4444; color:white; padding:2px 8px; border-radius:12px; font-size:11px; margin-left:6px;"><?= $totalItems ?></span>
        <?php endif; ?>
    </button>
</div>

<div id="tab-overview" class="tab-content">
    <div class="header" style="margin-bottom: 15px;">
        <div class="header-left">
            <h2>Tra cứu tồn kho theo ngày</h2>
            <p>Chọn một ngày trong quá khứ để xem sổ sách (Chỉ tính các đơn hàng và phiếu nhập đã hoàn tất).</p>
            
            <form action="/lego_shop_php/admininventory" method="GET" class="filter-form" style="margin-bottom: 0;">
                <input type="hidden" name="tab" value="overview">
                
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="overview_keyword" value="<?= htmlspecialchars($overview_keyword ?? '') ?>" placeholder="Tìm Tên hoặc SKU...">
                </div>
                <button type="submit" class="btn-filter-action" style="padding: 0 12px; margin-right: 20px;">Tìm</button>
                
                <div style="flex: 1;"></div>
                
                <span style="font-size: 13px; font-weight: 600; color: #475569;">Tra cứu theo ngày:</span>
                <input type="date" id="snapshotDate" class="form-control" style="width: 140px;" value="<?= date('Y-m-d') ?>" onkeydown="if(event.key === 'Enter') fetchSnapshot()">
                <button type="button" class="btn-filter-action" onclick="fetchSnapshot()">Tra cứu <i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
    </div>

    <div class="table-container">
        <table class="lego-table" id="overviewTable">
            <thead>
                <tr>
                    <th style="width: 40%;">Sản phẩm</th>
                    <th style="text-align: right;">Giá nhập (WAC)</th>
                    <th style="text-align: center;">Tồn kho tại ngày</th>
                    <th style="text-align: right;">Tổng giá trị vốn</th>
                    <th style="text-align: center;">Thẻ Kho</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($overview_products)): ?>
                    <?php foreach($overview_products as $ap): ?>
                        <?php 
                            $wac = $ap['import_price'] ?? 0; 
                            $val = $ap['stock_quantity'] * $wac;
                        ?>
                        <tr class="overview-row" data-id="<?= $ap['id'] ?>" data-min="<?= $ap['min_stock_level'] ?>" data-search="<?= strtolower($ap['name'] . ' ' . $ap['sku']) ?>">
                            <td>
                                <div class="product-cell">
                                    <img src="/lego_shop_php/public/assets/images/<?= $ap['image_url'] ?? 'default.jpg' ?>" class="img-product" onerror="this.src='https://placehold.co/52x52?text=LEGO'">
                                    <div>
                                        <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($ap['name']) ?></div>
                                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">SKU: <?= strtoupper($ap['sku']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: right; color: #475569; font-weight: 600;"><?= number_format($wac, 0, ',', '.') ?> đ</td>
                            
                            <td style="text-align: center;">
                                <span class="snap-qty" style="font-weight: 800; font-size: 16px; color: <?= $ap['stock_quantity'] <= $ap['min_stock_level'] ? '#ef4444' : '#10b981' ?>;">
                                    <?= $ap['stock_quantity'] ?>
                                </span>
                            </td>
                            
                            <td style="text-align: right; color: #3b82f6; font-weight: 700; font-size: 15px;">
                                <span class="snap-val"><?= number_format($val, 0, ',', '.') ?></span> đ
                            </td>
                            
                            <td style="text-align: center;">
                                <button type="button" class="btn-action-small" onclick="openHistory(<?= $ap['id'] ?>, '<?= addslashes($ap['name']) ?>')">
                                    <i class="fa-solid fa-clock-rotate-left"></i> Lịch sử
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; padding: 30px;">Không tìm thấy sản phẩm nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($overview_totalPages > 1): ?>
        <div class="pagination">
            <?php if ($overview_currentPage > 1): ?>
                <a href="?tab=overview&overview_page=<?= $overview_currentPage - 1 ?>&overview_keyword=<?= urlencode($overview_keyword) ?>" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $overview_totalPages; $i++): ?>
                <a href="?tab=overview&overview_page=<?= $i ?>&overview_keyword=<?= urlencode($overview_keyword) ?>" class="page-link <?= ($i == $overview_currentPage) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            
            <?php if ($overview_currentPage < $overview_totalPages): ?>
                <a href="?tab=overview&overview_page=<?= $overview_currentPage + 1 ?>&overview_keyword=<?= urlencode($overview_keyword) ?>" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<div id="tab-alerts" class="tab-content">
    <div class="header">
        <div class="header-left">
            <h2>Cảnh báo Tồn kho</h2>
            <p>Hiển thị các sản phẩm chạm ngưỡng báo động. Cài đặt ngưỡng chung cho toàn bộ kho.</p>

            <form action="/lego_shop_php/admininventory" method="GET" class="filter-form">
                <input type="hidden" name="tab" value="alerts"> 
                
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="keyword" class="form-control" placeholder="Tìm tên hoặc mã SKU..." value="<?= htmlspecialchars($keyword) ?>">
                </div>

                <div style="flex: 1;"></div>
                
                <div class="filter-threshold-group" style="padding: 4px 6px; background: #eff6ff; border: 1px solid #bfdbfe;">
                    <span class="filter-threshold-label" style="color: #1e40af;">Báo động khi tồn kho ≤ </span>
                    <input type="number" name="threshold" class="filter-threshold-input" value="<?= $current_threshold ?>" min="0" style="border-color: #93c5fd; font-weight: 800; color: #1d4ed8;">
                    <button type="submit" class="filter-threshold-btn" style="background: #3b82f6;">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu & Lọc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="table-container">
        <table class="lego-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Sản phẩm</th>
                    <th>Danh mục LEGO</th>
                    <th style="text-align: center;">Tồn kho thực tế</th>
                    <th style="text-align: center;">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($products)): ?>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td>
                            <div class="product-cell">
                                <img src="/lego_shop_php/public/assets/images/<?= $p['main_image'] ?? 'default.jpg' ?>" class="img-product" onerror="this.src='https://placehold.co/52x52?text=LEGO'">
                                <div>
                                    <div style="font-weight: 700; color: #1e293b;"><?= htmlspecialchars($p['name']) ?></div>
                                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">SKU: <?= strtoupper($p['sku']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span style="background: #f1f5f9; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 700; color: #475569;"><?= htmlspecialchars($p['category_name']) ?></span></td>
                        <td style="text-align: center;">
                            <b style="color: #ef4444; font-size: 18px;"><?= $p['stock_quantity'] ?></b>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($p['stock_quantity'] <= 0): ?>
                                <span class="stock-badge stock-empty"><i class="fa-solid fa-circle-exclamation"></i> HẾT HÀNG</span>
                            <?php else: ?>
                                <span class="stock-badge stock-low"><i class="fa-solid fa-triangle-exclamation"></i> SẮP HẾT</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align: center; padding: 60px; color: #94a3b8;"><i class="fa-solid fa-check-circle" style="font-size: 32px; display: block; margin-bottom: 10px; color: #10b981; opacity: 0.6;"></i> Tuyệt vời! Không có sản phẩm nào sắp hết hàng.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="?tab=alerts&page=<?= $currentPage - 1 ?>&keyword=<?= urlencode($keyword) ?>" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?tab=alerts&page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>" class="page-link <?= ($i == $currentPage) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($currentPage < $totalPages): ?>
                <a href="?tab=alerts&page=<?= $currentPage + 1 ?>&keyword=<?= urlencode($keyword) ?>" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<div id="historyModal" class="modal">
    <div class="modal-content" style="width: 800px;">
        <h3>
            <i class="fa-solid fa-clock-rotate-left" style="color:#3b82f6;"></i> 
            Thẻ kho ngày: <span id="histDateDisplay" style="color:#ef4444; font-weight: bold;"></span>
        </h3>
        <p style="margin-bottom: 15px; color: #475569;">Sản phẩm: <b id="histProductName" style="color: #1e293b;"></b></p>
        
        <div style="display: flex; justify-content: space-between; background: #f8fafc; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
            <div style="font-size: 13px; font-weight: 600; color: #475569;">
                Tồn đầu ngày: <span id="histOpeningStock" style="color:#3b82f6; font-size:16px; margin-left: 5px;">...</span>
            </div>
            <div style="font-size: 13px; font-weight: 600; color: #475569;">
                Tồn cuối ngày: <span id="histClosingStock" style="color:#10b981; font-size:16px; margin-left: 5px;">...</span>
            </div>
        </div>
        
        <table class="lego-table">
            <thead>
                <tr>
                    <th>Thời gian</th>
                    <th style="text-align: center;">Phân loại</th>
                    <th style="text-align: center;">Biến động</th>
                    <th>Mã tham chiếu / Ghi chú</th>
                </tr>
            </thead>
            <tbody id="histBody"></tbody>
        </table>
        
        <div style="text-align: right; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
            <button onclick="document.getElementById('historyModal').style.display='none'" class="btn-filter-action" style="background:#f1f5f9; color:#475569; display:inline-flex;">Đóng lại</button>
        </div>
    </div>
</div>

<div id="settingModal" class="modal">
    <div class="modal-content">
        <h3><i class="fa-solid fa-sliders" style="color:#3b82f6;"></i> Cập nhật mức cảnh báo tồn kho</h3>
        
        <table class="lego-table" id="updateTable">
            <thead>
                <tr>
                    <th style="width: 60%;">Chọn sản phẩm</th>
                    <th style="width: 25%; text-align: center;">Mức tối thiểu</th>
                    <th style="width: 15%; text-align: center;">Xóa</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <button type="button" class="btn-add-row" onclick="addRow()"><i class="fa-solid fa-plus"></i> Thêm sản phẩm</button>

        <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #e2e8f0; padding-top: 20px;">
            <button onclick="closeSettingModal()" class="btn-filter-action" style="background:#f1f5f9; color:#475569;">Hủy bỏ</button>
            <button onclick="submitBulkUpdate()" class="btn-filter-action"><i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi</button>
        </div>
    </div>
</div>

<script>
    window.productsData = <?= json_encode($all_products ?? []) ?>;

    function switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        document.querySelector(`.tab-btn[onclick="switchTab('${tabId}')"]`).classList.add('active');
        document.getElementById(`tab-${tabId}`).classList.add('active');
    }

    var urlParams = new URLSearchParams(window.location.search);
var activeTab = urlParams.get('tab');

if (activeTab === 'alerts' || urlParams.has('threshold') || urlParams.has('keyword')) {
    switchTab('alerts');
} else {
    // Mặc định luôn là overview
    switchTab('overview');
}

    function filterOverviewTable() {
        let input = document.getElementById("quickSearchOverview").value.toLowerCase();
        let rows = document.querySelectorAll(".overview-row");
        rows.forEach(row => {
            let searchData = row.getAttribute("data-search");
            row.style.display = searchData.includes(input) ? "" : "none";
        });
    }

    async function fetchSnapshot() {
        const date = document.getElementById('snapshotDate').value;
        if(!date) return;
        
        // Hiệu ứng UX: Đổi chữ nút thành Đang tải...
        const btn = document.querySelector('button[onclick="fetchSnapshot()"]');
        const oldText = btn.innerHTML;
        btn.innerHTML = 'Đang tải <i class="fa-solid fa-spinner fa-spin"></i>';

        try {
            const res = await fetch(`/lego_shop_php/admininventory/getSnapshotAjax?date=${date}`);
            const result = await res.json();
            
            if (result.success) {
                // TẠO TỪ ĐIỂN MAP: Biến mảng lộn xộn thành một danh sách có thể tra cứu bằng ID
                const snapshotMap = {};
                result.data.forEach(item => {
                    snapshotMap[item.id] = item;
                });

                // CHẠY LẶP VÀ CẬP NHẬT CHÍNH XÁC
                const rows = document.querySelectorAll('#overviewTable tbody tr.overview-row');
                rows.forEach(row => {
                    const pId = row.getAttribute('data-id'); // Lấy ID của dòng HTML hiện tại
                    const snapItem = snapshotMap[pId];       // Tra cứu số liệu chuẩn xác bằng ID
                    
                    if (snapItem) {
                        // Tính toán số lượng và tiền
                        let qty = parseInt(snapItem.historical_stock);
                        let wac = parseFloat(snapItem.import_price || 0);
                        let val = qty * wac;
                        
                        // Đẩy dữ liệu ra HTML
                        row.querySelector('.snap-qty').innerText = qty;
                        row.querySelector('.snap-val').innerText = new Intl.NumberFormat('vi-VN').format(val);
                        
                        // Đổi màu sắc (Đỏ nếu <= min_stock_level, Xanh nếu an toàn)
                        let minStock = parseInt(row.getAttribute('data-min')) || 0;
                        row.querySelector('.snap-qty').style.color = (qty <= minStock) ? '#ef4444' : '#10b981';
                    }
                });
            }
        } catch (err) { 
            console.error("Lỗi:", err); 
        } finally {
            btn.innerHTML = oldText; // Trả lại chữ cho nút
        }
    }

    async function openHistory(id, name) {
        const selectedDate = document.getElementById('snapshotDate').value;
        const dateObj = new Date(selectedDate);
        const displayDate = ("0" + dateObj.getDate()).slice(-2) + '/' + ("0" + (dateObj.getMonth() + 1)).slice(-2) + '/' + dateObj.getFullYear();

        document.getElementById('histProductName').innerText = name;
        document.getElementById('histDateDisplay').innerText = displayDate;
        document.getElementById('histOpeningStock').innerText = '...';
        document.getElementById('histClosingStock').innerText = '...';
        document.getElementById('histBody').innerHTML = '<tr><td colspan="4" style="text-align:center;">Đang tải...</td></tr>';
        document.getElementById('historyModal').style.display = 'block';

        try {
            const res = await fetch(`/lego_shop_php/admininventory/getStockCardAjax?product_id=${id}&date=${selectedDate}`);
            const result = await res.json();
            if (result.success) {
                document.getElementById('histOpeningStock').innerText = result.data.opening_stock;
                document.getElementById('histClosingStock').innerText = result.data.closing_stock;

                let html = '';
                const transactions = result.data.transactions;
                if (transactions.length === 0) { 
                    html = '<tr><td colspan="4" style="text-align:center; color: #64748b;">Không có biến động nào trong ngày này.</td></tr>'; 
                } else {
                    transactions.forEach(item => {
                        let isPlus = item.qty_change > 0;
                        let color = isPlus ? '#10b981' : '#ef4444';
                        let sign = isPlus ? '+' : '';
                        let typeBadge = '';
                        
                        if (item.type === 'import') {
                            typeBadge = '<span style="background:#dcfce7; color:#15803d; padding:2px 8px; border-radius:4px; font-size: 11px;">Nhập kho</span>';
                        } else if (item.type === 'export') {
                            typeBadge = '<span style="background:#fee2e2; color:#b91c1c; padding:2px 8px; border-radius:4px; font-size: 11px;">Xuất bán</span>';
                        }

                        let safeNote = item.note.replace(/PN-(\d+)/g, '<a href="/lego_shop_php/adminimport/detail/$1" target="_blank" style="color:#3b82f6;">PN-$1</a>')
                                                .replace(/DH-(\d+)/g, '<a href="/lego_shop_php/adminorder/detail/$1" target="_blank" style="color:#3b82f6;">DH-$1</a>');

                        html += `<tr>
                            <td style="color:#64748b; font-size:12px;">${item.created_at}</td>
                            <td style="text-align: center;">${typeBadge}</td>
                            <td style="color:${color}; font-weight:bold; font-size:14px; text-align:center;">${sign}${item.qty_change}</td>
                            <td style="font-size:12px;">${safeNote}</td>
                        </tr>`;
                    });
                }
                document.getElementById('histBody').innerHTML = html;
            }
        } catch(err) { document.getElementById('histBody').innerHTML = '<tr><td colspan="4">Lỗi mạng.</td></tr>'; }
    }

    function openSettingModal() {
        document.getElementById('settingModal').style.display = 'block';
        document.querySelector('#updateTable tbody').innerHTML = ''; 
        addRow(); 
    }

    function closeSettingModal() { document.getElementById('settingModal').style.display = 'none'; }

    function addRow() {
        const tbody = document.querySelector('#updateTable tbody');
        const rowId = 'row_' + Date.now();
        
        let listHtml = window.productsData.map(p => {
            let searchStr = (p.name + " " + p.sku).toLowerCase();
            let safeName = p.name.replace(/'/g, "\\'");
            let safeImg = p.image_url ? p.image_url : 'default.jpg';
            return `
            <li class="combo-item" data-search="${searchStr}" onclick="selectProduct('${rowId}', ${p.id}, '${safeName}', ${p.min_stock_level})">
                <img src="/lego_shop_php/public/assets/images/${safeImg}" style="width:40px; height:40px; border-radius:6px; object-fit:cover; border: 1px solid #e2e8f0;" onerror="this.src='https://placehold.co/40x40?text=LEGO'">
                <div style="flex: 1;">
                    <div style="font-weight: 700;">${p.name}</div>
                    <span class="combo-item-sku" style="font-size:11px;">SKU: ${p.sku} | Mức hiện tại: <b style="color:#3b82f6">${p.min_stock_level}</b></span>
                </div>
            </li>`;
        }).join('');

        const rowHtml = `
            <tr id="${rowId}">
                <td style="padding: 10px 0;">
                    <div class="combo-box-wrapper" id="combo_${rowId}">
                        <input type="text" class="combo-search-input" placeholder="Gõ tên hoặc mã SKU..." onfocus="openDropdown('${rowId}')" onkeyup="filterDropdown('${rowId}', this.value)" autocomplete="off">
                        <i class="fa-solid fa-chevron-down combo-dropdown-icon"></i>
                        <input type="hidden" class="real-product-id">
                        <ul class="combo-dropdown-list">
                            ${listHtml}
                            <li class="combo-empty" style="display:none;">Không tìm thấy...</li>
                        </ul>
                    </div>
                </td>
                <td style="padding: 10px; text-align:center;">
                    <input type="number" class="form-control min-stock-input" value="0" min="0" style="text-align:center; width: 80px; margin: 0 auto; font-weight: bold; color: #3b82f6;">
                </td>
                <td style="padding: 10px; text-align: center;">
                    <button type="button" onclick="document.getElementById('${rowId}').remove();" style="color: #ef4444; border:none; background:none; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
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
            } else { item.style.display = 'none'; }
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
        if (rows.length === 0) return alert("Vui lòng thêm sản phẩm!");
        const dataToSend = [];
        let isValid = true;
        rows.forEach(row => {
            const pId = row.querySelector('.real-product-id').value;
            const minStock = row.querySelector('.min-stock-input').value;
            if (!pId) {
                isValid = false;
                row.querySelector('.combo-search-input').style.borderColor = '#ef4444';
            } else {
                row.querySelector('.combo-search-input').style.borderColor = '#cbd5e1';
                dataToSend.push({ product_id: pId, min_stock: minStock });
            }
        });

        if (!isValid) return alert("Có sản phẩm chưa được chọn đúng từ danh sách dropdown!");

        try {
            const response = await fetch('/lego_shop_php/admininventory/updateBulkMinStock', {
                method: 'POST', headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ items: dataToSend })
            });
            const result = await response.json();
            if(result.success) {
                alert("Cập nhật thành công!");
                window.location.href = "/lego_shop_php/admininventory?tab=alerts";
            } else { alert("Lỗi: " + result.message); }
        } catch (err) { alert("Lỗi mạng!"); }
    }
</script>