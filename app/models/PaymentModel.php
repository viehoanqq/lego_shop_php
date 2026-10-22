<?php
class PaymentModel extends Database {

    const CODE_PREFIX = 'LEGO';

    public function getSettings() {
        $db = $this->getConnection();
        $result = $db->query("SELECT * FROM shop_settings WHERE id = 1");
        return $result ? $result->fetch_assoc() : [];
    }

    public static function makePaymentCode($order_id) {
        return self::CODE_PREFIX . (int)$order_id;
    }

    // Tách mã đơn từ nội dung chuyển khoản (ngân hàng có thể thêm ký tự, viết hoa/thường lung tung)
    public static function extractOrderId($content) {
        if (preg_match('/' . self::CODE_PREFIX . '\s*(\d+)/i', (string)$content, $m)) {
            return (int)$m[1];
        }
        return 0;
    }

    // Link ảnh QR động theo chuẩn VietQR (img.vietqr.io) - quét bằng mọi app ngân hàng
    public function buildQrUrl($order, $settings = null) {
        $settings = $settings ?: $this->getSettings();
        $bank = rawurlencode($settings['bank_code'] ?? 'MB');
        $acc = rawurlencode($settings['bank_account_no'] ?? '');
        $query = http_build_query([
            'amount' => (int)$order['total_amount'],
            'addInfo' => $order['payment_code'],
            'accountName' => $settings['bank_account_name'] ?? '',
        ]);
        return "https://img.vietqr.io/image/{$bank}-{$acc}-compact2.png?{$query}";
    }

    // Thời điểm hết hạn thanh toán của đơn VietQR
    public function getExpireTime($order, $settings = null) {
        $settings = $settings ?: $this->getSettings();
        $minutes = (int)($settings['payment_timeout_minutes'] ?? 30);
        return strtotime($order['created_at']) + $minutes * 60;
    }

    private function logTransaction($order_id, $provider, $ref, $amount, $content, $status, $raw) {
        $db = $this->getConnection();
        $sql = "INSERT INTO payment_transactions (order_id, provider, transaction_ref, amount, content, status, raw_data)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $order_id = $order_id ?: null;
        $stmt->bind_param("ississs", $order_id, $provider, $ref, $amount, $content, $status, $raw);
        $stmt->execute();
    }

    public function transactionExists($provider, $ref) {
        if ($ref === null || $ref === '') return false;
        $db = $this->getConnection();
        $stmt = $db->prepare("SELECT id FROM payment_transactions WHERE provider = ? AND transaction_ref = ?");
        $stmt->bind_param("ss", $provider, $ref);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Ghi nhận 1 giao dịch tiền vào cho đơn hàng.
     * Trả về: 'paid' | 'already_paid' | 'duplicate' | 'not_found' | 'amount_mismatch' | 'order_cancelled'
     */
    public function confirmPayment($order_id, $amount, $provider, $ref = null, $content = '', $raw = '') {
        $db = $this->getConnection();

        if ($this->transactionExists($provider, $ref)) return 'duplicate';

        $db->begin_transaction();
        try {
            $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? FOR UPDATE");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $order = $stmt->get_result()->fetch_assoc();

            if (!$order) {
                $this->logTransaction(null, $provider, $ref, $amount, $content, 'unmatched', $raw);
                $db->commit();
                return 'not_found';
            }

            if ($order['payment_status'] !== 'unpaid') {
                $this->logTransaction($order_id, $provider, $ref, $amount, $content, 'mismatch', $raw);
                $db->commit();
                return 'already_paid';
            }

            // Khách chuyển tiền sau khi đơn đã bị hủy -> lưu lại để admin hoàn tiền thủ công
            if ($order['status'] === 'cancelled') {
                $this->logTransaction($order_id, $provider, $ref, $amount, $content, 'mismatch', $raw);
                $db->commit();
                return 'order_cancelled';
            }

            if ((int)$amount < (int)$order['total_amount']) {
                $this->logTransaction($order_id, $provider, $ref, $amount, $content, 'mismatch', $raw);
                $db->commit();
                return 'amount_mismatch';
            }

            $stmtUpd = $db->prepare("UPDATE orders SET payment_status = 'paid', paid_at = NOW() WHERE id = ?");
            $stmtUpd->bind_param("i", $order_id);
            $stmtUpd->execute();

            if ($provider === 'manual') {
                $note = 'Admin xác nhận đã nhận ' . number_format($amount, 0, ',', '.') . 'đ' . ($content ? " - $content" : '');
            } else {
                $note = 'Đã nhận thanh toán ' . number_format($amount, 0, ',', '.') . 'đ qua VietQR'
                      . ($provider === 'sandbox' ? ' (giả lập)' : '') . ($ref ? " - Mã GD: $ref" : '');
            }
            $stmtHis = $db->prepare("INSERT INTO order_history (order_id, status, note) VALUES (?, 'paid', ?)");
            $stmtHis->bind_param("is", $order_id, $note);
            $stmtHis->execute();

            $this->logTransaction($order_id, $provider, $ref, $amount, $content, 'success', $raw);

            $db->commit();
            return 'paid';
        } catch (Exception $e) {
            $db->rollback();
            error_log('confirmPayment error: ' . $e->getMessage());
            throw $e;
        }
    }

    // =====================================================================
    // [ADMIN] TRA CỨU GIAO DỊCH
    // =====================================================================
    public function getTransactionsByOrder($order_id) {
        $db = $this->getConnection();
        $stmt = $db->prepare("SELECT * FROM payment_transactions WHERE order_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    private function buildTxWhere($filters) {
        $db = $this->getConnection();
        $where = "WHERE 1=1";
        if (($filters['status'] ?? '') === 'review') {
            $where .= " AND t.status IN ('mismatch','unmatched')";
        } elseif (!empty($filters['status']) && $filters['status'] !== 'all') {
            $where .= " AND t.status = '" . $db->real_escape_string($filters['status']) . "'";
        }
        if (!empty($filters['keyword'])) {
            $k = $db->real_escape_string(trim($filters['keyword']));
            $where .= " AND (t.content LIKE '%$k%' OR t.transaction_ref LIKE '%$k%' OR t.order_id = " . (int)preg_replace('/[^0-9]/', '', $k) . ")";
        }
        return $where;
    }

    public function getTransactions($filters, $limit = 10, $offset = 0) {
        $db = $this->getConnection();
        $sql = "SELECT t.*, o.total_amount as order_total, o.status as order_status, o.payment_status
                FROM payment_transactions t LEFT JOIN orders o ON t.order_id = o.id
                " . $this->buildTxWhere($filters) . "
                ORDER BY t.created_at DESC LIMIT " . (int)$offset . ", " . (int)$limit;
        $result = $db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countTransactions($filters) {
        $db = $this->getConnection();
        $sql = "SELECT COUNT(*) as total FROM payment_transactions t " . $this->buildTxWhere($filters);
        return (int)$db->query($sql)->fetch_assoc()['total'];
    }

    // Tổng quan: tiền đã nhận online + số giao dịch cần admin xem lại
    public function getTransactionStats() {
        $db = $this->getConnection();
        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN status = 'success' THEN amount END), 0) as received,
                    SUM(status = 'success') as success_count,
                    SUM(status IN ('mismatch','unmatched')) as review_count
                FROM payment_transactions";
        return $db->query($sql)->fetch_assoc();
    }

    // Tự động hủy các đơn VietQR quá hạn chưa thanh toán (giải phóng tồn kho + hoàn lượt voucher)
    public function expireUnpaidOrders($user_id = null) {
        $db = $this->getConnection();
        $settings = $this->getSettings();
        $minutes = max(1, (int)($settings['payment_timeout_minutes'] ?? 30));

        $sql = "SELECT id FROM orders
                WHERE payment_method = 'vietqr' AND payment_status = 'unpaid' AND status = 'pending'
                  AND created_at < (NOW() - INTERVAL $minutes MINUTE)";
        if ($user_id) $sql .= " AND user_id = " . (int)$user_id;

        $result = $db->query($sql);
        if (!$result || $result->num_rows === 0) return 0;

        require_once __DIR__ . '/VoucherModel.php';
        $voucherModel = new VoucherModel();
        $count = 0;

        while ($row = $result->fetch_assoc()) {
            $id = (int)$row['id'];
            $db->begin_transaction();
            try {
                $db->query("UPDATE orders SET status = 'cancelled' WHERE id = $id AND status = 'pending' AND payment_status = 'unpaid'");
                if ($db->affected_rows > 0) {
                    $note = "Tự động hủy do quá $minutes phút chưa thanh toán";
                    $stmt = $db->prepare("INSERT INTO order_history (order_id, status, note) VALUES (?, 'cancelled', ?)");
                    $stmt->bind_param("is", $id, $note);
                    $stmt->execute();
                    $voucherModel->releaseUsage($id);
                    $count++;
                }
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
            }
        }
        return $count;
    }
}
