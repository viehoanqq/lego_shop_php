<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .report-container { font-family: inherit; color: #1e293b; font-size: 14px; }
    .report-container h2 { font-size: 26px; margin: 10px 0 5px 0; }
    
    .report-stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; margin-bottom: 30px; }
    .report-card { background: #fff; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .card-label { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 6px; }
    .card-date { font-size: 11px; color: #94a3b8; display: block; margin-bottom: 8px; }
    .card-value { font-size: 24px; font-weight: 800; }
    
    .card-profit-focus { border-top: 4px solid #7c3aed; background: #fbfaff; }
    .card-profit-focus .card-value { font-size: 28px; color: #7c3aed; }

    .report-main-content { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 30px; align-items: start; }
    .analysis-side { display: flex; flex-direction: column; gap: 20px; }
    .chart-card { background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; }
    
    /* Khối Darkmode Phân tích */
    .insight-card { background: #111827; color: #fff; padding: 25px; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3); position: relative; overflow: hidden; }
    .insight-card::before { content: ""; position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(59, 130, 246, 0.1); border-radius: 50%; }
    .insight-title { font-size: 14px; text-transform: uppercase; color: #9ca3af; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .insight-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; position: relative; z-index: 1; }
    .insight-item { font-size: 13px; color: #9ca3af; font-weight: 500; }
    .insight-item b { font-size: 22px; display: block; margin-top: 8px; color: #fff; letter-spacing: -0.5px; }

    /* Timeline */
    .timeline-side { position: relative; padding-left: 15px; border-left: 2px solid #f1f5f9; }
    .timeline-row { display: flex; align-items: center; margin-bottom: 12px; background: #fff; border: 1px solid #f1f5f9; border-radius: 8px; padding: 12px 18px; cursor: pointer; transition: 0.2s; text-decoration: none; color: inherit; }
    .timeline-row:hover { border-color: #3182ce; box-shadow: 0 4px 12px rgba(49, 130, 206, 0.1); transform: translateX(5px); }
    .tm-date { width: 110px; font-size: 12px; font-weight: 700; color: #64748b; line-height: 1.3; }
    .tm-type { flex: 1; font-size: 14px; font-weight: 600; padding: 0 15px; border-left: 1px solid #f1f5f9; }
    .tm-ref { font-family: monospace; color: #3182ce; font-size: 12px; font-weight: 700; background: #eff6ff; padding: 2px 6px; border-radius: 4px; margin-left: 5px; }
    .tm-qty { width: 75px; text-align: right; font-weight: 800; font-size: 17px; }
    .qty-in { color: #10b981; }
    .qty-out { color: #f43f5e; }
</style>

<div class="report-container">
    <div style="margin-bottom: 25px;">
        <a href="/lego_shop_php/adminreport" style="text-decoration: none; color: #3182ce; font-size: 14px; font-weight: 700;">
            <i class="fa-solid fa-arrow-left"></i> QUAY LẠI DANH SÁCH BÁO CÁO
        </a>
        <h2><?= $product['name'] ?></h2>
        <div style="display: flex; gap: 10px; align-items: center;">
            <span style="background: #1e293b; color: #fff; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 700;">ID: #<?= $product['id'] ?></span>
            <span style="background: #f1f5f9; color: #475569; padding: 3px 10px; border-radius: 4px; font-size: 11px; font-weight: 800; border: 1px solid #e2e8f0;">SKU: <?= $product['sku'] ?></span>
        </div>
    </div>

    <div class="report-stats-grid">
        <div class="report-card">
            <span class="card-label">Tồn đầu kỳ</span>
            <span class="card-date">Ngày <?= date('d/m/Y', strtotime($_GET['start'])) ?></span>
            <div class="card-value"><?= number_format($stats['opening_stock']) ?> <small style="font-size: 12px; color: #94a3b8; font-weight: normal;">cái</small></div>
        </div>
        <div class="report-card">
            <span class="card-label">Tổng Nhập (+)</span>
            <span class="card-date">Từ <?= date('d/m', strtotime($_GET['start'])) ?> - <?= date('d/m', strtotime($_GET['end'])) ?></span>
            <div class="card-value" style="color: #10b981;">+<?= number_format($stats['total_in']) ?></div>
        </div>
        <div class="report-card">
            <span class="card-label">Tổng Xuất (-)</span>
            <span class="card-date">Từ <?= date('d/m', strtotime($_GET['start'])) ?> - <?= date('d/m', strtotime($_GET['end'])) ?></span>
            <div class="card-value" style="color: #f43f5e;">-<?= number_format($stats['total_out']) ?></div>
        </div>
        <div class="report-card" style="background: #f0f7ff; border-color: #bfdbfe;">
            <span class="card-label" style="color: #1e40af;">Tồn cuối kỳ</span>
            <span class="card-date">Ngày <?= date('d/m/Y', strtotime($_GET['end'])) ?></span>
            <div class="card-value" style="color: #1e40af;"><?= number_format($stats['closing_stock']) ?> <small style="font-size: 12px; opacity: 0.7;">cái</small></div>
        </div>
        <div class="report-card card-profit-focus" style="cursor: pointer; transition: 0.2s;" onclick="openProfitModal()" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span class="card-label">Lợi nhuận gộp (Gross Profit)</span>
                    <span class="card-date">Nhấn vào để xem chi tiết từng đơn</span>
                </div>
                <i class="fa-solid fa-expand" style="color: #c4b5fd;"></i>
            </div>
            <div class="card-value"><?= number_format($stats['profit'], 0, ',', '.') ?>đ</div>
        </div>
    </div>

    <div class="report-main-content">
        <div class="analysis-side">
            <div class="chart-card">
                <span class="card-label" style="margin-bottom: 20px; display: block; font-size: 14px;">Biến động tồn kho hàng ngày</span>
                <canvas id="flowChart" height="135"></canvas>
            </div>

            <div class="insight-card">
                <span class="insight-title"><i class="fa-solid fa-microchip" style="color: #3b82f6;"></i> Phân tích thông minh</span>
                <div class="insight-grid">
                    <div class="insight-item">
                        Tốc độ tiêu thụ
                        <?php 
                            $days = (strtotime($_GET['end']) - strtotime($_GET['start'])) / (60 * 60 * 24) ?: 1;
                            $avg = round($stats['total_out'] / $days, 1);
                        ?>
                        <b><?= $avg ?> cái / ngày</b>
                    </div>
                    <div class="insight-item">
                        Dự kiến hết hàng sau
                        <?php $daysLeft = ($avg > 0) ? floor($stats['closing_stock'] / $avg) : 0; ?>
                        <b style="color: <?= ($daysLeft > 0 && $daysLeft < 7) ? '#fbbf24' : ($daysLeft == 0 ? '#9ca3af' : '#34d399') ?>">
                            <?= ($avg > 0) ? ($daysLeft . ' ngày') : '---' ?>
                        </b>
                    </div>
                </div>
                <div style="margin-top: 15px; font-size: 11px; color: #6b7280; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 10px;">
                    * Thuật toán tính toán dựa trên dữ liệu giao dịch trong kỳ.
                </div>
            </div>
        </div>

        <div class="timeline-side">
            <span class="card-label" style="margin-bottom: 15px; display: block; padding-left: 10px; font-size: 14px;">Lịch sử giao dịch (Cũ &rarr; Mới)</span>
            
            <?php 
            $history_asc = array_reverse($history); 
            if(!empty($history_asc)): ?>
                <?php foreach($history_asc as $h): 
                    $is_in = ($h['qty'] > 0);
                    $link = "/lego_shop_php/admin" . ($is_in ? 'import' : 'order') . "/detail/" . $h['ref'];
                ?>
                <a href="<?= $link ?>" class="timeline-row">
                    <div class="tm-date">
                        <?= date('d/m/Y', strtotime($h['dt'])) ?><br>
                        <span style="font-weight: 500; opacity: 0.6; font-size: 11px;"><?= date('H:i', strtotime($h['dt'])) ?></span>
                    </div>
                    <div class="tm-type">
                        <span style="color: <?= $is_in ? '#059669' : '#1e293b' ?>"><?= $h['type'] ?></span> 
                        <span class="tm-ref">#<?= $is_in ? 'PN' : 'DH' ?>-<?= $h['ref'] ?></span>
                    </div>
                    <div class="tm-qty <?= $is_in ? 'qty-in' : 'qty-out' ?>">
                        <?= $is_in ? '+'.number_format($h['qty']) : number_format($h['qty']) ?>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #94a3b8; font-size: 14px; padding: 40px; background: #fff; border-radius: 12px; border: 1px dashed #e2e8f0;">Chưa có biến động.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('flowChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_data['labels']) ?>,
            datasets: [
                {
                    label: 'Nhập kho',
                    data: <?= json_encode($chart_data['in']) ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true, tension: 0.4, pointRadius: 6, pointHoverRadius: 10, pointBackgroundColor: '#fff', pointBorderWidth: 3
                },
                {
                    label: 'Xuất bán',
                    data: <?= json_encode($chart_data['out']) ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true, tension: 0.4, pointRadius: 6, pointHoverRadius: 10, pointBackgroundColor: '#fff', pointBorderWidth: 3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true, position: 'top', align: 'end', labels: { usePointStyle: true, font: { size: 12, weight: '700' }, padding: 20 } },
                tooltip: {
                    padding: 15, backgroundColor: '#1e293b', titleFont: { size: 14, weight: 'bold' }, bodyFont: { size: 13 },
                    callbacks: { label: function(c) { return ` ${c.dataset.label}: ${c.parsed.y} cái`; } }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11, weight: '600' } } },
                x: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' } } }
            }
        }
    });
</script>
<style>
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; display: flex; justify-content: center; align-items: center; opacity: 0; visibility: hidden; transition: 0.3s; }
    .modal-overlay.active { opacity: 1; visibility: visible; }
    .modal-box { background: #fff; width: 850px; max-width: 95%; max-height: 85vh; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); display: flex; flex-direction: column; transform: translateY(20px); transition: 0.3s; }
    .modal-overlay.active .modal-box { transform: translateY(0); }
    .modal-header { padding: 20px 25px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; border-radius: 16px 16px 0 0; }
    .modal-header h3 { margin: 0; font-size: 18px; color: #1e293b; display: flex; align-items: center; gap: 10px; }
    .btn-close-modal { background: #f1f5f9; border: none; width: 32px; height: 32px; border-radius: 8px; color: #64748b; cursor: pointer; transition: 0.2s; display: flex; justify-content: center; align-items: center; }
    .btn-close-modal:hover { background: #e2e8f0; color: #ef4444; }
    .modal-body { padding: 0; overflow-y: auto; }
    
    .profit-table { width: 100%; border-collapse: collapse; }
    .profit-table th { position: sticky; top: 0; background: #f8fafc; padding: 15px; font-size: 12px; text-transform: uppercase; color: #64748b; text-align: left; border-bottom: 2px solid #e2e8f0; z-index: 10; }
    .profit-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
    .profit-table tr:hover { background: #f8fafc; }
</style>

<div class="modal-overlay" id="profitModalOverlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="fa-solid fa-file-invoice-dollar" style="color: #7c3aed;"></i> Phân tích lợi nhuận chi tiết</h3>
            <button class="btn-close-modal" onclick="closeProfitModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <table class="profit-table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Ngày xuất bán</th>
                        <th style="text-align: center;">SL</th>
                        <th style="text-align: right;">Tổng vốn (WAC)</th>
                        <th style="text-align: right;">Tổng thu (Bán ra)</th>
                        <th style="text-align: right;">Lợi nhuận</th>
                        <th style="text-align: right;">Tỷ lệ lãi (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $sum_qty = 0; $sum_cost = 0; $sum_revenue = 0; $sum_profit = 0;
                    ?>
                    <?php if(!empty($profit_details)): ?>
                        <?php foreach($profit_details as $pd): 
                            $total_cost = $pd['cost_price'] * $pd['quantity'];
                            $total_rev = $pd['price'] * $pd['quantity'];
                            
                            // Cộng dồn vào tổng
                            $sum_qty += $pd['quantity'];
                            $sum_cost += $total_cost;
                            $sum_revenue += $total_rev;
                            $sum_profit += $pd['order_profit'];

                            // SỬA Ở ĐÂY: Tính % Lãi dựa trên Giá Vốn (Markup on Cost)
                            $markup_percent = ($total_cost > 0) ? round(($pd['order_profit'] / $total_cost) * 100, 1) : 0;
                        ?>
                            <tr>
                                <td>
                                    <span style="font-family: monospace; font-weight: 700; color: #3b82f6; background: #eff6ff; padding: 4px 8px; border-radius: 6px;">#DH-<?= $pd['order_id'] ?></span>
                                </td>
                                <td style="color: #64748b; font-size: 13px;">
                                    <?= date('d/m/Y', strtotime($pd['created_at'])) ?> <br> 
                                    <small><?= date('H:i', strtotime($pd['created_at'])) ?></small>
                                </td>
                                <td style="text-align: center; font-weight: 800; color: #1e293b;"><?= number_format($pd['quantity']) ?></td>
                                <td style="text-align: right; color: #64748b;">
                                    <?= number_format($total_cost, 0, ',', '.') ?>đ
                                    <div style="font-size: 11px; opacity: 0.6;">(Đơn giá: <?= number_format($pd['cost_price'], 0, ',', '.') ?>đ)</div>
                                </td>
                                <td style="text-align: right; font-weight: 600; color: #1e293b;">
                                    <?= number_format($total_rev, 0, ',', '.') ?>đ
                                    <div style="font-size: 11px; opacity: 0.6;">(Đơn giá: <?= number_format($pd['price'], 0, ',', '.') ?>đ)</div>
                                </td>
                                <td style="text-align: right; font-weight: 800; color: <?= $pd['order_profit'] >= 0 ? '#10b981' : '#ef4444' ?>;">
                                    <?= $pd['order_profit'] >= 0 ? '+' : '' ?><?= number_format($pd['order_profit'], 0, ',', '.') ?>đ
                                </td>
                                <td style="text-align: right; font-weight: 800; color: <?= $markup_percent >= 0 ? '#059669' : '#e11d48' ?>;">
                                    <span style="background: <?= $markup_percent >= 0 ? '#ecfdf5' : '#fff1f2' ?>; padding: 4px 8px; border-radius: 6px;">
                                        <?= $markup_percent >= 0 ? '+' : '' ?><?= $markup_percent ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">Không có dữ liệu bán hàng trong kỳ này.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                
                <?php if(!empty($profit_details)): 
                    // TÍNH LẠI CHO DÒNG TỔNG: (Tổng lợi nhuận / Tổng vốn) * 100
                    $sum_markup_percent = ($sum_cost > 0) ? round(($sum_profit / $sum_cost) * 100, 1) : 0;
                ?>
                <tfoot>
                    <tr style="background: #fef08a; font-weight: 800; color: #1e293b;">
                        <td colspan="2" style="text-align: right; text-transform: uppercase; border-top: 2px solid #eab308;">Tổng cộng:</td>
                        <td style="text-align: center; border-top: 2px solid #eab308; font-size: 16px;"><?= number_format($sum_qty) ?></td>
                        <td style="text-align: right; border-top: 2px solid #eab308; color: #64748b;"><?= number_format($sum_cost, 0, ',', '.') ?>đ</td>
                        <td style="text-align: right; border-top: 2px solid #eab308; color: #1e293b;"><?= number_format($sum_revenue, 0, ',', '.') ?>đ</td>
                        <td style="text-align: right; border-top: 2px solid #eab308; font-size: 16px; color: <?= $sum_profit >= 0 ? '#166534' : '#991b1b' ?>;">
                            <?= $sum_profit >= 0 ? '+' : '' ?><?= number_format($sum_profit, 0, ',', '.') ?>đ
                        </td>
                        <td style="text-align: right; border-top: 2px solid #eab308; font-size: 16px; color: <?= $sum_markup_percent >= 0 ? '#166534' : '#991b1b' ?>;">
                            <?= $sum_markup_percent >= 0 ? '+' : '' ?><?= $sum_markup_percent ?>%
                        </td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<script>
    function openProfitModal() {
        document.getElementById('profitModalOverlay').classList.add('active');
        document.body.style.overflow = 'hidden'; // Khóa cuộn trang nền
    }
    
    function closeProfitModal() {
        document.getElementById('profitModalOverlay').classList.remove('active');
        document.body.style.overflow = 'auto'; // Mở lại cuộn trang nền
    }

    // Bấm ra ngoài vùng tối để đóng modal
    document.getElementById('profitModalOverlay').addEventListener('click', function(e) {
        if(e.target === this) closeProfitModal();
    });
</script>