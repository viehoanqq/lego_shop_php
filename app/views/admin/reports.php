<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
</head>

<style>
    .img-report { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; }
    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); max-height: 65vh; overflow-y: auto; }
    .lego-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .lego-table th { position: sticky; top: 0; background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; z-index: 5; font-weight: 700; letter-spacing: 0.5px; }
    .lego-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    .table-container::-webkit-scrollbar { width: 6px; }
    .table-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }

    /* LAYOUT FORM */
    .filter-split-layout { display: flex; gap: 40px; flex-wrap: wrap; }
    .filter-col { flex: 1; min-width: 300px; display: flex; flex-direction: column; gap: 15px; }
    .filter-col-right { border-left: 1px dashed #cbd5e1; padding-left: 40px; justify-content: space-between; }
    .filter-row { display: flex; gap: 15px; width: 100%; }
    .filter-group { flex: 1; }
    
    .search-wrapper { position: relative; width: 100%; }
    .search-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
    .search-wrapper input { width: 100%; height: 42px; padding: 0 15px 0 35px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: 0.2s; font-size: 13px; }
    .search-wrapper input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }

    .form-control-ui { width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 12px; font-size: 13px; outline: none; transition: 0.2s; color: #1e293b; background: #fff; }
    .form-control-ui:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .filter-label { font-weight: 700; font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: block; }

    /* NÚT */
    .btn-submit-filter { background: #3b82f6; color: #fff; height: 42px; border: none; padding: 0 20px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; text-decoration: none;}
    .btn-submit-filter:hover { background: #2563eb; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
    
    .btn-outline-danger { background: transparent; color: #ef4444; border: 1px solid #ef4444; height: 42px; width: 42px; border-radius: 8px; display: inline-flex; justify-content: center; align-items: center; cursor: pointer; transition: 0.2s; text-decoration: none; }
    .btn-outline-danger:hover { background: #fef2f2; }

    .btn-detail { background: #eff6ff; color: #3b82f6; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 700; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; border: 1px solid transparent; }
    .btn-detail:hover { background: #3b82f6; color: #fff; border-color: #3b82f6; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2); transform: translateY(-2px); }

    /* HÀNG TỔNG CỘNG CHUẨN */
    .row-total-footer { background: #fef08a !important; font-weight: 800; font-size: 14px; color: #1e293b; }
    .row-total-footer td { border-top: 2px solid #eab308; }
    .badge-qty-in { background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 12px; font-weight: 700; font-size: 12px; display: inline-block; min-width: 40px; text-align: center; }
    .badge-qty-out { background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 12px; font-weight: 700; font-size: 12px; display: inline-block; min-width: 40px; text-align: center; }
</style>

<form method="GET" action="/lego_shop_php/adminreport" id="reportForm" style="background: #fff; padding: 25px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;">
    <div class="filter-split-layout">
        <div class="filter-col">
            <div class="filter-row">
                <div class="filter-group">
                    <label class="filter-label">Tìm kiếm sản phẩm</label>
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="keyword" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" placeholder="Tên sản phẩm hoặc mã SKU..." onchange="this.form.submit()">
                    </div>
                </div>
            </div>
            <div class="filter-row">
                <div class="filter-group">
                    <label class="filter-label">Danh mục LEGO</label>
                    <select name="category_id" class="form-control-ui" style="cursor: pointer;" onchange="this.form.submit()">
                        <option value="all">-- Tất cả danh mục --</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($filters['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="filter-col filter-col-right">
            <div class="filter-row">
                <div class="filter-group" style="width: 100%;">
                    <label class="filter-label">Khoảng thời gian báo cáo</label>
                    <div style="position: relative;">
                        <i class="fa-regular fa-calendar" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                        <input type="text" id="reportrange" class="form-control-ui" style="padding-left: 40px; cursor: pointer; background: #fff; font-weight: 600; color: #3b82f6;" readonly>
                        <i class="fa-solid fa-chevron-down" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 12px;"></i>
                    </div>
                    <input type="hidden" name="start_date" id="start_date" value="<?= $filters['start_date'] ?? date('Y-m-01') ?>">
                    <input type="hidden" name="end_date" id="end_date" value="<?= $filters['end_date'] ?? date('Y-m-d') ?>">
                </div>
            </div>
            
            <div class="filter-row" style="align-items: flex-end; justify-content: flex-end; margin-top: 10px;">
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-submit-filter"><i class="fa-solid fa-filter"></i> ÁP DỤNG LỌC</button>
                    <a href="/lego_shop_php/adminreport" class="btn-outline-danger" title="Xóa bộ lọc"><i class="fa-solid fa-rotate-right"></i></a>
                    
                    <?php
                        $export_params = http_build_query([
                            'action' => 'export_excel',
                            'keyword' => $filters['keyword'] ?? '',
                            'category_id' => $filters['category_id'] ?? 'all',
                            'start_date' => $filters['start_date'] ?? date('Y-m-01'),
                            'end_date' => $filters['end_date'] ?? date('Y-m-d')
                        ]);
                    ?>
                    <a href="/lego_shop_php/adminreport?<?= $export_params ?>" class="btn-submit-filter" style="background: #10b981; color: #fff;">
                        <i class="fa-solid fa-file-excel"></i> XUẤT EXCEL
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="table-container">
    <table class="lego-table">
        <thead>
            <tr>
                <th style="width: 30%;">Sản phẩm & Thông tin</th>
                <th style="text-align: center;">SL Nhập</th>
                <th style="text-align: center;">SL Bán</th>
                <th style="text-align: right;">Tổng vốn nhập</th>
                <th style="text-align: right;">Tổng doanh thu</th>
                <th style="text-align: right;">Dòng tiền (Cash Flow)</th>
                <th style="text-align: center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $sum_qty_in = 0; $sum_qty_out = 0;
                $sum_import_cost = 0; $sum_revenue = 0; $sum_difference = 0;
            ?>
            <?php if(!empty($reports)): ?>
                <?php foreach($reports as $r): 
                    $difference = $r['total_revenue'] - $r['total_import_cost'];
                    $sum_qty_in += $r['qty_in']; $sum_qty_out += $r['qty_out'];
                    $sum_import_cost += $r['total_import_cost']; $sum_revenue += $r['total_revenue'];
                    $sum_difference += $difference;
                    
                    // Kiểm tra trạng thái
                    $isDeleted = (isset($r['status']) && $r['status'] == 3);
                    $isLocked = (isset($r['status']) && $r['status'] == 2);
                    
                    // Thiết lập CSS theo trạng thái để code HTML bên dưới gọn hơn
                    $rowStyle = $isDeleted ? 'background-color: #f8fafc;' : '';
                    $opacityStyle = $isDeleted ? 'opacity: 0.6;' : '';
                    $nameColor = ($isDeleted || $isLocked) ? '#94a3b8' : '#1e293b';
                    $imgFilter = $isDeleted ? 'filter: grayscale(100%);' : '';
                ?>
                <tr style="transition: 0.2s; <?= $rowStyle ?>">
                    <td>
                        <div style="display: flex; align-items: center; gap: 15px; <?= $opacityStyle ?>">
                            
                            <img src="/lego_shop_php/public/assets/images/<?= !empty($r['main_image']) ? $r['main_image'] : 'default.jpg' ?>" 
                                 class="img-report" 
                                 onerror="this.src='https://placehold.co/50x50?text=LEGO'"
                                 style="<?= $imgFilter ?>">
                                 
                            <div>
                                <div style="font-weight: 700; color: <?= $nameColor ?>; font-size: 14px; margin-bottom: 6px; line-height: 1.4;">
                                    <?= htmlspecialchars($r['name']) ?>
                                </div>
                                
                                <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 8px;">
                                    
                                    <span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; color: #64748b; border: 1px solid #e2e8f0;">
                                        SKU: <?= strtoupper($r['sku']) ?>
                                    </span>
                                    
                                    <?php if($isDeleted): ?>
                                        <span style="color: #475569; font-size: 10px; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-weight: 700; border: 1px solid #cbd5e1; white-space: nowrap;">
                                            <i class="fa-solid fa-ban"></i> Ngừng kinh doanh
                                        </span>
                                    <?php elseif($isLocked): ?>
                                        <span style="color: #cf48006c; font-size: 10px; background: #ffffff; padding: 2px 6px; border-radius: 4px; font-weight: 700; border: 1px solid #fde68a; white-space: nowrap;">
                                            <i class="fa-solid fa-lock"></i> Đang khóa
                                        </span>
                                    <?php endif; ?>
                                    
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center;"><?= $r['qty_in'] > 0 ? '<span class="badge-qty-in">+'.number_format($r['qty_in']).'</span>' : '<span style="color:#cbd5e1">-</span>' ?></td>
                    <td style="text-align: center;"><?= $r['qty_out'] > 0 ? '<span class="badge-qty-out">-'.number_format($r['qty_out']).'</span>' : '<span style="color:#cbd5e1">-</span>' ?></td>
                    <td style="text-align: right; color: #64748b; font-weight: 600;"><?= number_format($r['total_import_cost'], 0, ',', '.') ?>đ</td>
                    <td style="text-align: right; color: #10b981; font-weight: 600;"><?= number_format($r['total_revenue'], 0, ',', '.') ?>đ</td>
                    <td style="text-align: right; font-weight: 800; color: <?= $difference >= 0 ? '#10b981' : '#ef4444' ?>;"><?= number_format($difference, 0, ',', '.') ?>đ</td>
                    <td style="text-align: center;">
                        <a href="/lego_shop_php/adminreport/productDetail/<?= $r['id'] ?>?start=<?= $filters['start_date'] ?? '' ?>&end=<?= $filters['end_date'] ?? '' ?>" class="btn-detail">
                            <i class="fa-solid fa-chart-line"></i> Phân tích
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 60px; color: #94a3b8; font-size: 14px;">
                        <i class="fa-solid fa-folder-open" style="font-size: 32px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                        Không có dữ liệu giao dịch trong khoảng thời gian này.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        
        <?php if(!empty($reports)): ?>
        <tfoot>
            <tr class="row-total-footer">
                <td style="text-transform: uppercase;">TỔNG CỘNG</td>
                <td style="text-align: center;"><?= number_format($sum_qty_in) ?></td>
                <td style="text-align: center;"><?= number_format($sum_qty_out) ?></td>
                <td style="text-align: right;"><?= number_format($sum_import_cost, 0, ',', '.') ?>đ</td>
                <td style="text-align: right;"><?= number_format($sum_revenue, 0, ',', '.') ?>đ</td>
                <td style="text-align: right; color: <?= $sum_difference >= 0 ? '#166534' : '#991b1b' ?>;"><?= number_format($sum_difference, 0, ',', '.') ?>đ</td>
                <td></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
</div>

<script>
// Chạy hàm này ngay lập tức khi file JS được load (không chờ document.ready)
(function initDateRangePicker() {
    // Đảm bảo jQuery và daterangepicker đã sẵn sàng
    if (typeof $ === 'undefined' || !$.fn.daterangepicker) {
        setTimeout(initDateRangePicker, 100); // Nếu chưa có, thử lại sau 0.1s
        return;
    }

    var startInput = $('#start_date');
    var endInput = $('#end_date');
    var reportRange = $('#reportrange');

    // Tránh khởi tạo 2 lần nếu chuyển trang AJAX
    if (reportRange.data('daterangepicker')) {
        reportRange.daterangepicker('destroy'); 
    }

    var start = moment(startInput.val(), 'YYYY-MM-DD');
    var end = moment(endInput.val(), 'YYYY-MM-DD');

    function cb(s, e) {
        reportRange.val(s.format('DD/MM/YYYY') + '  -  ' + e.format('DD/MM/YYYY'));
        startInput.val(s.format('YYYY-MM-DD'));
        endInput.val(e.format('YYYY-MM-DD'));
    }

    reportRange.daterangepicker({
        startDate: start,
        endDate: end,
        showDropdowns: true, 
        ranges: {
           'Hôm nay': [moment(), moment()],
           'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           '7 ngày qua': [moment().subtract(6, 'days'), moment()],
           '30 ngày qua': [moment().subtract(29, 'days'), moment()],
           'Tháng này': [moment().startOf('month'), moment().endOf('month')],
           'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            format: 'DD/MM/YYYY', separator: " - ", applyLabel: "Đồng ý", cancelLabel: "Hủy",
            fromLabel: "Từ", toLabel: "Đến", customRangeLabel: "Tùy chọn ngày...",
            daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
            monthNames: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
            firstDay: 1 
        }
    }, cb);

    cb(start, end);

    reportRange.on('apply.daterangepicker', function(ev, picker) {
        $('#reportForm').submit();
    });
})();
</script>