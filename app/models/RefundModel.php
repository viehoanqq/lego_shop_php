<?php
class RefundModel extends Database {

    // Danh sách ngân hàng phổ biến để khách chọn khi nhận tiền hoàn
    const BANKS = [
        'Vietcombank', 'VietinBank', 'BIDV', 'Agribank', 'MB Bank', 'Techcombank', 'ACB', 'VPBank',
        'TPBank', 'Sacombank', 'VIB', 'SHB', 'HDBank', 'OCB', 'MSB', 'SeABank', 'Eximbank',
        'LPBank', 'Nam A Bank', 'Bac A Bank', 'ABBANK', 'PVcomBank', 'Cake by VPBank', 'Timo',
    ];

    // Mã ngân hàng theo chuẩn VietQR (api.vietqr.io/v2/banks) để admin quét QR chuyển tiền hoàn
    const BANK_CODES = [
        'Vietcombank' => 'VCB', 'VietinBank' => 'ICB', 'BIDV' => 'BIDV', 'Agribank' => 'VBA', 'MB Bank' => 'MB',
        'Techcombank' => 'TCB', 'ACB' => 'ACB', 'VPBank' => 'VPB', 'TPBank' => 'TPB', 'Sacombank' => 'STB',
        'VIB' => 'VIB', 'SHB' => 'SHB', 'HDBank' => 'HDB', 'OCB' => 'OCB', 'MSB' => 'MSB', 'SeABank' => 'SEAB',
        'Eximbank' => 'EIB', 'LPBank' => 'LPB', 'Nam A Bank' => 'NAB', 'Bac A Bank' => 'BAB', 'ABBANK' => 'ABB',
        'PVcomBank' => 'PVCB', 'Cake by VPBank' => 'CAKE', 'Timo' => 'TIMO',
    ];

    const STATUS_LABELS = [
        'pending'   => 'Chờ xử lý',
        'approved'  => 'Đã duyệt - chờ chuyển tiền',
        'rejected'  => 'Bị từ chối',
        'completed' => 'Đã hoàn tiền',
    ];

    const TYPE_LABELS = [
        'cancel' => 'Hủy đơn đã thanh toán',
        'return' => 'Trả hàng / hoàn tiền',
    ];

    public function create($data) {
        $db = $this->getConnection();
        $sql = "INSERT INTO refund_requests
                (order_id, user_id, type, amount, reason, evidence_image, bank_name, bank_account_number, bank_account_name)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("iisisssss",
            $data['order_id'], $data['user_id'], $data['type'], $data['amount'], $data['reason'],
            $data['evidence_image'], $data['bank_name'], $data['bank_account_number'], $data['bank_account_name']
        );
        $stmt->execute();
        return $db->insert_id;
    }

    // Yêu cầu mới nhất của 1 đơn hàng
    public function getLatestByOrder($order_id) {
        $db = $this->getConnection();
        $stmt = $db->prepare("SELECT * FROM refund_requests WHERE order_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Đơn đang có yêu cầu chưa kết thúc (pending/approved) hoặc đã hoàn xong
    public function hasOpenOrCompleted($order_id) {
        $db = $this->getConnection();
        $stmt = $db->prepare("SELECT id FROM refund_requests WHERE order_id = ? AND status IN ('pending','approved','completed')");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function getByUser($user_id) {
        $db = $this->getConnection();
        $sql = "SELECT r.*, o.total_amount, o.created_at as order_created_at, o.payment_method
                FROM refund_requests r
                JOIN orders o ON r.order_id = o.id
                WHERE r.user_id = ?
                ORDER BY r.created_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Thời điểm đơn được giao thành công (lấy từ lịch sử), dùng để tính hạn trả hàng
    public function getDeliveredAt($order_id) {
        $db = $this->getConnection();
        $stmt = $db->prepare("SELECT MAX(changed_at) as delivered_at FROM order_history WHERE order_id = ? AND status = 'delivered'");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['delivered_at'] ?? null;
    }

    /**
     * Kiểm tra đơn có được yêu cầu trả hàng/hoàn tiền không.
     * Trả về ['ok' => bool, 'message' => string, 'deadline' => timestamp|null]
     */
    public function checkReturnEligibility($order, $refund_days) {
        if ($order['status'] !== 'delivered') {
            return ['ok' => false, 'message' => 'Chỉ đơn đã giao thành công mới được yêu cầu trả hàng!', 'deadline' => null];
        }
        if (in_array($order['payment_status'], ['refund_pending', 'refunded'])) {
            return ['ok' => false, 'message' => 'Đơn hàng này đã được hoàn tiền hoặc đang xử lý hoàn tiền!', 'deadline' => null];
        }
        if ($this->hasOpenOrCompleted($order['id'])) {
            return ['ok' => false, 'message' => 'Đơn hàng này đã có yêu cầu hoàn tiền!', 'deadline' => null];
        }

        $delivered_at = $this->getDeliveredAt($order['id']) ?: $order['created_at'];
        $deadline = strtotime($delivered_at) + (int)$refund_days * 86400;
        if (time() > $deadline) {
            return ['ok' => false, 'message' => "Đã quá hạn $refund_days ngày kể từ khi nhận hàng!", 'deadline' => $deadline];
        }
        return ['ok' => true, 'message' => '', 'deadline' => $deadline];
    }

    public static function hasBankInfo($refund) {
        return !empty($refund['bank_name']) && !empty($refund['bank_account_number']) && !empty($refund['bank_account_name']);
    }

    public function updateBankInfo($id, $bank_name, $account_number, $account_name) {
        $db = $this->getConnection();
        $stmt = $db->prepare("UPDATE refund_requests SET bank_name = ?, bank_account_number = ?, bank_account_name = ?
                              WHERE id = ? AND status IN ('pending','approved')");
        $stmt->bind_param("sssi", $bank_name, $account_number, $account_name, $id);
        $stmt->execute();
        return $stmt->affected_rows >= 0;
    }

    // QR chuyển tiền hoàn về tài khoản khách (admin quét bằng app ngân hàng của shop)
    public static function buildRefundQrUrl($refund) {
        $code = self::BANK_CODES[$refund['bank_name']] ?? null;
        if (!$code || !self::hasBankInfo($refund)) return null;
        $query = http_build_query([
            'amount' => (int)$refund['amount'],
            'addInfo' => 'HOAN TIEN DON LEGO' . (int)$refund['order_id'],
            'accountName' => $refund['bank_account_name'],
        ]);
        return "https://img.vietqr.io/image/{$code}-" . rawurlencode($refund['bank_account_number']) . "-compact2.png?{$query}";
    }

    // =====================================================================
    // [ADMIN] DANH SÁCH & XỬ LÝ YÊU CẦU HOÀN TIỀN
    // =====================================================================
    private function buildAdminWhere($filters) {
        $db = $this->getConnection();
        $where = "WHERE 1=1";
        if (!empty($filters['keyword'])) {
            $k = $db->real_escape_string(trim($filters['keyword']));
            $id = (int)preg_replace('/[^0-9]/', '', $k);
            $where .= " AND (u.fullname LIKE '%$k%' OR o.shipping_phone LIKE '%$k%'" . ($id ? " OR r.order_id = $id OR r.id = $id" : "") . ")";
        }
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $where .= " AND r.status = '" . $db->real_escape_string($filters['status']) . "'";
        }
        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $where .= " AND r.type = '" . $db->real_escape_string($filters['type']) . "'";
        }
        return $where;
    }

    public function getAdminList($filters, $limit = 8, $offset = 0) {
        $db = $this->getConnection();
        $sql = "SELECT r.*, u.fullname, o.shipping_phone, o.payment_method, o.status as order_status
                FROM refund_requests r
                JOIN users u ON r.user_id = u.id
                JOIN orders o ON r.order_id = o.id
                " . $this->buildAdminWhere($filters) . "
                ORDER BY FIELD(r.status, 'pending', 'approved', 'rejected', 'completed'), r.created_at DESC
                LIMIT " . (int)$offset . ", " . (int)$limit;
        $result = $db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countAdminList($filters) {
        $db = $this->getConnection();
        $sql = "SELECT COUNT(*) as total FROM refund_requests r
                JOIN users u ON r.user_id = u.id JOIN orders o ON r.order_id = o.id " . $this->buildAdminWhere($filters);
        return (int)$db->query($sql)->fetch_assoc()['total'];
    }

    public function countByStatus() {
        $db = $this->getConnection();
        $rows = $db->query("SELECT status, COUNT(*) as total FROM refund_requests GROUP BY status")->fetch_all(MYSQLI_ASSOC);
        $out = ['pending' => 0, 'approved' => 0, 'rejected' => 0, 'completed' => 0];
        foreach ($rows as $r) $out[$r['status']] = (int)$r['total'];
        return $out;
    }

    public function getDetail($id) {
        $db = $this->getConnection();
        $sql = "SELECT r.*, u.fullname, a.email, a.phone as account_phone, ad.fullname as admin_name
                FROM refund_requests r
                JOIN users u ON r.user_id = u.id
                JOIN accounts a ON u.account_id = a.id
                LEFT JOIN users ad ON ad.account_id = r.processed_by
                WHERE r.id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    private function addHistory($order_id, $status, $note) {
        $db = $this->getConnection();
        $stmt = $db->prepare("INSERT INTO order_history (order_id, status, note) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $order_id, $status, $note);
        $stmt->execute();
    }

    // Đổi trạng thái có điều kiện trạng thái cũ (chống 2 admin xử lý cùng lúc)
    private function transition($id, $from, $to, $admin_id, $note) {
        $db = $this->getConnection();
        $in = "'" . implode("','", $from) . "'";
        $stmt = $db->prepare("UPDATE refund_requests SET status = ?, admin_note = ?, processed_by = ?, processed_at = NOW()
                              WHERE id = ? AND status IN ($in)");
        $stmt->bind_param("ssii", $to, $note, $admin_id, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    public function approve($refund, $admin_id, $note) {
        if (!$this->transition($refund['id'], ['pending'], 'approved', $admin_id, $note)) return false;
        $this->addHistory($refund['order_id'], 'refund_approved', $note ?: 'Cửa hàng đã duyệt yêu cầu hoàn tiền');
        return true;
    }

    // Chỉ từ chối được yêu cầu trả hàng. Đơn hủy đã thanh toán thì bắt buộc phải hoàn tiền.
    public function reject($refund, $admin_id, $note) {
        if ($refund['type'] !== 'return') return false;
        if (!$this->transition($refund['id'], ['pending', 'approved'], 'rejected', $admin_id, $note)) return false;
        $this->addHistory($refund['order_id'], 'refund_rejected', $note);
        return true;
    }

    /**
     * Xác nhận đã chuyển tiền hoàn cho khách.
     * - Đơn chuyển payment_status = 'refunded'
     * - Trả hàng: đơn chuyển status = 'returned' và cộng lại tồn kho (vì kho đã bị trừ khi giao thành công)
     */
    public function complete($refund, $admin_id, $note) {
        $db = $this->getConnection();
        $db->begin_transaction();
        try {
            if (!$this->transition($refund['id'], ['pending', 'approved'], 'completed', $admin_id, $note)) {
                $db->rollback();
                return false;
            }

            $order_id = (int)$refund['order_id'];
            if ($refund['type'] === 'return') {
                $db->query("UPDATE orders SET payment_status = 'refunded', status = 'returned' WHERE id = $order_id AND status = 'delivered'");
                if ($db->affected_rows === 0) {
                    throw new RuntimeException('Đơn hàng không còn ở trạng thái đã giao!');
                }
                $items = $db->query("SELECT product_id, SUM(quantity) as qty FROM order_details WHERE order_id = $order_id GROUP BY product_id");
                while ($it = $items->fetch_assoc()) {
                    $db->query("UPDATE products SET stock_quantity = stock_quantity + " . (int)$it['qty'] . " WHERE id = " . (int)$it['product_id']);
                }
                $this->addHistory($order_id, 'returned', 'Đã nhận lại hàng và nhập lại kho');
            } else {
                $db->query("UPDATE orders SET payment_status = 'refunded' WHERE id = $order_id");
            }

            $msg = 'Đã hoàn ' . number_format($refund['amount'], 0, ',', '.') . 'đ về ' . $refund['bank_name'] . ' - ' . $refund['bank_account_number'];
            $this->addHistory($order_id, 'refunded', $note ? "$msg ($note)" : $msg);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            error_log('Refund complete error: ' . $e->getMessage());
            return false;
        }
    }

    // Kiểm tra thông tin tài khoản nhận tiền hoàn. Trả về chuỗi lỗi hoặc null nếu hợp lệ
    public static function validateBankInfo($bank_name, $account_number, $account_name) {
        if ($bank_name === '' || $account_number === '' || $account_name === '') {
            return 'Vui lòng nhập đầy đủ thông tin tài khoản nhận tiền hoàn!';
        }
        if (!in_array($bank_name, self::BANKS, true)) {
            return 'Ngân hàng không hợp lệ!';
        }
        if (!preg_match('/^[0-9]{6,20}$/', $account_number)) {
            return 'Số tài khoản chỉ gồm 6-20 chữ số!';
        }
        if (mb_strlen($account_name) < 3 || mb_strlen($account_name) > 100) {
            return 'Tên chủ tài khoản không hợp lệ!';
        }
        return null;
    }
}
