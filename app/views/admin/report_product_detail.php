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
        <div class="report-card card-profit-focus">
            <span class="card-label">Lợi nhuận gộp</span>
            <span class="card-date">Ước tính doanh thu</span>
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