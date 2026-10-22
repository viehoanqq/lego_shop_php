<?php
class VoucherModel extends Database {

    public function getByCode($code, $forUpdate = false) {
        $db = $this->getConnection();
        $sql = "SELECT * FROM vouchers WHERE code = ?" . ($forUpdate ? " FOR UPDATE" : "");
        $stmt = $db->prepare($sql);
        $code = strtoupper(trim($code));
        $stmt->bind_param("s", $code);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Số lần user đã dùng voucher (không tính đơn đã hủy vì đã được hoàn lượt)
    public function countUserUsage($voucher_id, $user_id) {
        $db = $this->getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM voucher_usages WHERE voucher_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $voucher_id, $user_id);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'];
    }

    // Tính số tiền được giảm dựa trên loại voucher
    public function calculateDiscount($voucher, $subtotal) {
        if ($voucher['discount_type'] === 'percent') {
            $discount = (int)floor($subtotal * $voucher['discount_value'] / 100);
            if (!empty($voucher['max_discount'])) {
                $discount = min($discount, (int)$voucher['max_discount']);
            }
        } else {
            $discount = (int)$voucher['discount_value'];
        }
        // Không giảm quá tổng tiền hàng
        return max(0, min($discount, (int)$subtotal));
    }

    /**
     * Kiểm tra voucher có dùng được không.
     * Trả về ['ok' => bool, 'message' => string, 'voucher' => array|null, 'discount' => int]
     * $forUpdate = true khi gọi bên trong transaction lúc tạo đơn (khóa dòng để chống vượt lượt dùng)
     */
    public function validate($code, $user_id, $subtotal, $forUpdate = false) {
        $fail = function ($msg) { return ['ok' => false, 'message' => $msg, 'voucher' => null, 'discount' => 0]; };

        if (trim($code) === '') return $fail('Vui lòng nhập mã giảm giá!');

        $voucher = $this->getByCode($code, $forUpdate);
        if (!$voucher || $voucher['status'] !== 'active') return $fail('Mã giảm giá không tồn tại hoặc đã ngừng áp dụng!');

        $now = date('Y-m-d H:i:s');
        if (!empty($voucher['start_date']) && $now < $voucher['start_date']) return $fail('Mã giảm giá chưa đến thời gian áp dụng!');
        if (!empty($voucher['end_date']) && $now > $voucher['end_date']) return $fail('Mã giảm giá đã hết hạn!');

        if ($voucher['usage_limit'] !== null && (int)$voucher['used_count'] >= (int)$voucher['usage_limit']) {
            return $fail('Mã giảm giá đã hết lượt sử dụng!');
        }

        if ($this->countUserUsage($voucher['id'], $user_id) >= (int)$voucher['per_user_limit']) {
            return $fail('Bạn đã sử dụng hết lượt cho mã giảm giá này!');
        }

        if ($subtotal < (int)$voucher['min_order_value']) {
            return $fail('Đơn hàng tối thiểu ' . number_format($voucher['min_order_value'], 0, ',', '.') . 'đ để dùng mã này!');
        }

        $discount = $this->calculateDiscount($voucher, $subtotal);
        return ['ok' => true, 'message' => 'Áp dụng mã giảm giá thành công!', 'voucher' => $voucher, 'discount' => $discount];
    }

    // Ghi nhận lượt dùng (gọi trong transaction tạo đơn)
    public function recordUsage($voucher_id, $user_id, $order_id, $discount) {
        $db = $this->getConnection();
        $stmt = $db->prepare("INSERT INTO voucher_usages (voucher_id, user_id, order_id, discount_amount) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $voucher_id, $user_id, $order_id, $discount);
        $stmt->execute();

        $stmt2 = $db->prepare("UPDATE vouchers SET used_count = used_count + 1 WHERE id = ?");
        $stmt2->bind_param("i", $voucher_id);
        return $stmt2->execute();
    }

    // Hoàn lại lượt dùng khi đơn bị hủy (gọi trong transaction hủy đơn)
    public function releaseUsage($order_id) {
        $db = $this->getConnection();
        $stmt = $db->prepare("SELECT voucher_id FROM voucher_usages WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) return false;

        $stmtDel = $db->prepare("DELETE FROM voucher_usages WHERE order_id = ?");
        $stmtDel->bind_param("i", $order_id);
        $stmtDel->execute();

        $stmtUpd = $db->prepare("UPDATE vouchers SET used_count = GREATEST(used_count - 1, 0) WHERE id = ?");
        $stmtUpd->bind_param("i", $row['voucher_id']);
        return $stmtUpd->execute();
    }

    // Danh sách voucher còn hiệu lực để khách chọn, kèm số lượt khách còn lại
    public function getAvailableVouchers($user_id) {
        $db = $this->getConnection();
        $sql = "SELECT v.*,
                    (SELECT COUNT(*) FROM voucher_usages u WHERE u.voucher_id = v.id AND u.user_id = ?) as user_used
                FROM vouchers v
                WHERE v.status = 'active'
                  AND (v.start_date IS NULL OR v.start_date <= NOW())
                  AND (v.end_date IS NULL OR v.end_date >= NOW())
                  AND (v.usage_limit IS NULL OR v.used_count < v.usage_limit)
                ORDER BY v.min_order_value ASC, v.id DESC";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as &$v) {
            $v['remaining_for_user'] = max(0, (int)$v['per_user_limit'] - (int)$v['user_used']);
        }
        return $rows;
    }

    // =====================================================================
    // [ADMIN] QUẢN LÝ VOUCHER
    // =====================================================================

    // Trạng thái hiển thị thực tế (kết hợp status + thời hạn + lượt dùng)
    public static function computeState($v) {
        $now = date('Y-m-d H:i:s');
        if ($v['status'] !== 'active') return 'inactive';
        if (!empty($v['end_date']) && $now > $v['end_date']) return 'expired';
        if (!empty($v['start_date']) && $now < $v['start_date']) return 'upcoming';
        if ($v['usage_limit'] !== null && (int)$v['used_count'] >= (int)$v['usage_limit']) return 'exhausted';
        return 'running';
    }

    private function buildAdminWhere($keyword, $state) {
        $db = $this->getConnection();
        $where = "WHERE 1=1";
        if ($keyword !== '') {
            $k = $db->real_escape_string($keyword);
            $where .= " AND (v.code LIKE '%$k%' OR v.description LIKE '%$k%')";
        }
        switch ($state) {
            case 'running':
                $where .= " AND v.status = 'active' AND (v.start_date IS NULL OR v.start_date <= NOW())
                            AND (v.end_date IS NULL OR v.end_date >= NOW())
                            AND (v.usage_limit IS NULL OR v.used_count < v.usage_limit)";
                break;
            case 'inactive': $where .= " AND v.status = 'inactive'"; break;
            case 'expired':  $where .= " AND v.status = 'active' AND v.end_date IS NOT NULL AND v.end_date < NOW()"; break;
            case 'upcoming': $where .= " AND v.status = 'active' AND v.start_date IS NOT NULL AND v.start_date > NOW()"; break;
            case 'exhausted': $where .= " AND v.usage_limit IS NOT NULL AND v.used_count >= v.usage_limit"; break;
            case 'ended': $where .= " AND v.status = 'active' AND ((v.end_date IS NOT NULL AND v.end_date < NOW()) OR (v.usage_limit IS NOT NULL AND v.used_count >= v.usage_limit))"; break;
        }
        return $where;
    }

    public function getAdminVouchers($keyword = '', $state = 'all', $limit = 8, $offset = 0) {
        $db = $this->getConnection();
        $where = $this->buildAdminWhere($keyword, $state);
        $sql = "SELECT v.*,
                    (SELECT COALESCE(SUM(u.discount_amount), 0) FROM voucher_usages u WHERE u.voucher_id = v.id) as total_discount
                FROM vouchers v $where
                ORDER BY v.id DESC
                LIMIT " . (int)$offset . ", " . (int)$limit;
        $result = $db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countAdminVouchers($keyword = '', $state = 'all') {
        $db = $this->getConnection();
        $result = $db->query("SELECT COUNT(*) as total FROM vouchers v " . $this->buildAdminWhere($keyword, $state));
        return (int)$result->fetch_assoc()['total'];
    }

    public function getById($id) {
        $db = $this->getConnection();
        $stmt = $db->prepare("SELECT * FROM vouchers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function codeExists($code, $exclude_id = null) {
        $db = $this->getConnection();
        $exclude_id = (int)$exclude_id;
        $stmt = $db->prepare("SELECT id FROM vouchers WHERE code = ? AND id != ?");
        $stmt->bind_param("si", $code, $exclude_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    public function insert($d) {
        $db = $this->getConnection();
        $sql = "INSERT INTO vouchers (code, description, discount_type, discount_value, max_discount, min_order_value,
                    usage_limit, per_user_limit, start_date, end_date, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssiiiiisss", $d['code'], $d['description'], $d['discount_type'], $d['discount_value'],
            $d['max_discount'], $d['min_order_value'], $d['usage_limit'], $d['per_user_limit'],
            $d['start_date'], $d['end_date'], $d['status']);
        return $stmt->execute();
    }

    public function update($id, $d) {
        $db = $this->getConnection();
        $sql = "UPDATE vouchers SET code = ?, description = ?, discount_type = ?, discount_value = ?, max_discount = ?,
                    min_order_value = ?, usage_limit = ?, per_user_limit = ?, start_date = ?, end_date = ?, status = ?
                WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssiiiiisssi", $d['code'], $d['description'], $d['discount_type'], $d['discount_value'],
            $d['max_discount'], $d['min_order_value'], $d['usage_limit'], $d['per_user_limit'],
            $d['start_date'], $d['end_date'], $d['status'], $id);
        return $stmt->execute();
    }

    public function toggleStatus($id) {
        $db = $this->getConnection();
        $stmt = $db->prepare("UPDATE vouchers SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Đã có đơn dùng mã -> không xóa (giữ lịch sử), chỉ ngừng áp dụng
    public function isUsed($id) {
        $db = $this->getConnection();
        $stmt = $db->prepare("SELECT (SELECT COUNT(*) FROM voucher_usages WHERE voucher_id = ?) + (SELECT COUNT(*) FROM orders WHERE voucher_id = ?) as total");
        $stmt->bind_param("ii", $id, $id);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['total'] > 0;
    }

    public function deleteForever($id) {
        $db = $this->getConnection();
        $stmt = $db->prepare("DELETE FROM vouchers WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function deactivate($id) {
        $db = $this->getConnection();
        $stmt = $db->prepare("UPDATE vouchers SET status = 'inactive' WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Lịch sử các đơn đã dùng mã
    public function getUsages($voucher_id) {
        $db = $this->getConnection();
        $sql = "SELECT u.*, us.fullname, o.total_amount, o.status as order_status
                FROM voucher_usages u
                JOIN users us ON u.user_id = us.id
                JOIN orders o ON u.order_id = o.id
                WHERE u.voucher_id = ?
                ORDER BY u.used_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $voucher_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Mô tả ngắn gọn mức giảm, VD: "Giảm 10% (tối đa 200.000đ)"
    public static function describe($v) {
        if ($v['discount_type'] === 'percent') {
            $text = 'Giảm ' . (int)$v['discount_value'] . '%';
            if (!empty($v['max_discount'])) $text .= ' (tối đa ' . number_format($v['max_discount'], 0, ',', '.') . 'đ)';
            return $text;
        }
        return 'Giảm ' . number_format($v['discount_value'], 0, ',', '.') . 'đ';
    }
}
