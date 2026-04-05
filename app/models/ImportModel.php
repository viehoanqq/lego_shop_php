<?php
class ImportModel extends Database {
    
    // 1. Lấy danh sách Nhà cung cấp cho form nhập hàng
    public function getAllSuppliers() {
        $db = $this->getConnection();
        $sql = "SELECT * FROM suppliers WHERE status = 'active'";
        $result = $db->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { $data[] = $row; }
        }
        return $data;
    }

    // 2. Lấy danh sách Lịch sử phiếu nhập
    // --- Lấy danh sách Lịch sử phiếu nhập (CÓ TÌM KIẾM & LỌC) ---
    public function getAllImports($filters = [], $offset = null, $limit = null) {
        $db = $this->getConnection();
        $where = ["1=1"];

        if (!empty($filters['supplier_id'])) {
            $where[] = "r.supplier_id = " . intval($filters['supplier_id']);
        }
        if (!empty($filters['status'])) {
            $where[] = "r.status = '" . $db->real_escape_string($filters['status']) . "'";
        }
        if (!empty($filters['start_date'])) {
            $where[] = "r.created_at >= '" . $db->real_escape_string($filters['start_date']) . " 00:00:00'";
        }
        if (!empty($filters['end_date'])) {
            $where[] = "r.created_at <= '" . $db->real_escape_string($filters['end_date']) . " 23:59:59'";
        }
        if (!empty($filters['keyword'])) {
            $keyword = $db->real_escape_string($filters['keyword']);
            $clean_id = preg_replace('/[^0-9]/', '', $keyword);
            if (!empty($clean_id)) {
                $where[] = "r.id = " . intval($clean_id);
            }
        }

        $whereSql = implode(' AND ', $where);

        $sql = "SELECT r.*, 
                       COALESCE(NULLIF(u.fullname, ''), acc.email, 'Quản trị viên') as admin_name, 
                       s.name as supplier_name 
                FROM import_receipts r
                LEFT JOIN accounts acc ON r.admin_id = acc.id
                LEFT JOIN users u ON acc.id = u.account_id
                LEFT JOIN suppliers s ON r.supplier_id = s.id
                WHERE $whereSql
                ORDER BY r.created_at DESC";

        // Xử lý LIMIT
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT " . intval($offset) . ", " . intval($limit);
        }

        $result = $db->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { $data[] = $row; }
        }
        return $data;
    }
    public function countAllImports($filters = []) {
        $db = $this->getConnection();
        $where = ["1=1"];

        if (!empty($filters['supplier_id'])) {
            $where[] = "r.supplier_id = " . intval($filters['supplier_id']);
        }
        if (!empty($filters['status'])) {
            $where[] = "r.status = '" . $db->real_escape_string($filters['status']) . "'";
        }
        if (!empty($filters['start_date'])) {
            $where[] = "r.created_at >= '" . $db->real_escape_string($filters['start_date']) . " 00:00:00'";
        }
        if (!empty($filters['end_date'])) {
            $where[] = "r.created_at <= '" . $db->real_escape_string($filters['end_date']) . " 23:59:59'";
        }
        if (!empty($filters['keyword'])) {
            $keyword = $db->real_escape_string($filters['keyword']);
            $clean_id = preg_replace('/[^0-9]/', '', $keyword);
            if (!empty($clean_id)) {
                $where[] = "r.id = " . intval($clean_id);
            }
        }

        $whereSql = implode(' AND ', $where);

        $sql = "SELECT COUNT(*) as total FROM import_receipts r WHERE $whereSql";
        $result = $db->query($sql);
        return (int)$result->fetch_assoc()['total'];
    }

    // 3. Xử lý lưu Phiếu nhập và Tính Giá Bình Quân Gia Quyền (WAC)
    public function createImportTransaction($admin_id, $supplier_id, $products_list, $status = 'draft', $import_date) {
        $db = $this->getConnection();
        $db->begin_transaction(); 

        try {
            // 1. Tính tổng tiền
            $total_amount = 0;
            foreach ($products_list as $item) {
                $total_amount += intval($item['quantity']) * intval($item['price']);
            }

            // 2. Lưu Phiếu nhập chung với biến $status
            $sqlReceipt = "INSERT INTO import_receipts (admin_id, supplier_id, total_amount, status, created_at) VALUES (?, ?, ?, ?, ?)";
            $stmtR = $db->prepare($sqlReceipt);
            $stmtR->bind_param("iiiss", $admin_id, $supplier_id, $total_amount, $status, $import_date);
            $stmtR->execute();
            $receipt_id = $db->insert_id;

            // 3. Xử lý từng sản phẩm
            foreach ($products_list as $item) {
                $p_id = intval($item['product_id']);
                $qty_in = intval($item['quantity']);
                $price_in = intval($item['price']); 
                
                $new_wac = 0;
                $new_selling_price = 0;

                // CHỈ TÍNH GIÁ VÀ TĂNG KHO KHI TRẠNG THÁI LÀ 'completed'
                if ($status === 'completed') {
                    $res = $db->query("SELECT stock_quantity, import_price, profit_margin FROM products WHERE id = $p_id");
                    $prod = $res->fetch_assoc();

                    $old_stock = intval($prod['stock_quantity']);
                    $old_wac = intval($prod['import_price']);
                    $margin = floatval($prod['profit_margin']);

                    // TÍNH BÌNH QUÂN GIA QUYỀN (WAC)
                    $new_stock = $old_stock + $qty_in;
                    $new_wac = round((($old_stock * $old_wac) + ($qty_in * $price_in)) / $new_stock);

                    // Tính giá bán mới
                    $new_selling_price = round($new_wac * (1 + $margin));

                    // Cập nhật lại kho và giá vào bảng products
                    $sqlUpdate = "UPDATE products SET stock_quantity = ?, import_price = ?, selling_price = ? WHERE id = ?";
                    $stmtU = $db->prepare($sqlUpdate);
                    $stmtU->bind_param("iiii", $new_stock, $new_wac, $new_selling_price, $p_id);
                    $stmtU->execute();
                }

                // 4. Lưu chi tiết phiếu nhập (Nếu là draft thì WAC và giá bán lưu = 0)
                $sqlDetail = "INSERT INTO import_receipt_details (receipt_id, product_id, quantity, price, calculated_average_price, calculated_selling_price) VALUES (?, ?, ?, ?, ?, ?)";
                $stmtD = $db->prepare($sqlDetail);
                $stmtD->bind_param("iiiiii", $receipt_id, $p_id, $qty_in, $price_in, $new_wac, $new_selling_price);
                $stmtD->execute();
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }
    // --- Lấy thông tin chung của 1 phiếu nhập ---
    public function getImportById($id) {
        $db = $this->getConnection();
        $sql = "SELECT r.*, u.fullname as admin_name, s.name as supplier_name 
                FROM import_receipts r
                LEFT JOIN accounts acc ON r.admin_id = acc.id
                LEFT JOIN users u ON acc.id = u.account_id
                LEFT JOIN suppliers s ON r.supplier_id = s.id
                WHERE r.id = " . intval($id);
        $result = $db->query($sql);
        return $result ? $result->fetch_assoc() : false;
    }

    // --- Lấy danh sách sản phẩm chi tiết của phiếu nhập đó ---
    public function getImportDetails($receipt_id) {
        $db = $this->getConnection();
        
        $sql = "SELECT d.*, p.name as product_name, p.sku, p.stock_quantity as current_stock,
                       (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM import_receipt_details d
                JOIN products p ON d.product_id = p.id
                WHERE d.receipt_id = " . intval($receipt_id);
                
        $result = $db->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { $data[] = $row; }
        }
        return $data;
    }

    // --- LOGIC QUAN TRỌNG: Chuyển Bản nháp -> Hoàn tất (Tính WAC và Tăng kho) ---
    public function completeImport($receipt_id) {
        $db = $this->getConnection();
        $receipt_id = intval($receipt_id);

        // Kiểm tra xem phiếu này có tồn tại và đang là draft không
        $receipt = $this->getImportById($receipt_id);
        if (!$receipt || $receipt['status'] === 'completed') return false;

        $db->begin_transaction();
        try {
            // 1. Cập nhật trạng thái phiếu thành completed
            $db->query("UPDATE import_receipts SET status = 'completed' WHERE id = $receipt_id");

            // 2. Lấy chi tiết để tính toán
            $details = $this->getImportDetails($receipt_id);
            foreach ($details as $item) {
                $p_id = intval($item['product_id']);
                $qty_in = intval($item['quantity']);
                $price_in = intval($item['price']);

                // Lấy thông tin tồn kho hiện tại
                $res = $db->query("SELECT stock_quantity, import_price, profit_margin FROM products WHERE id = $p_id");
                $prod = $res->fetch_assoc();

                $old_stock = intval($prod['stock_quantity']);
                $old_wac = intval($prod['import_price']);
                $margin = floatval($prod['profit_margin']);

                // TÍNH BÌNH QUÂN GIA QUYỀN (WAC)
                $new_stock = $old_stock + $qty_in;
                $new_wac = round((($old_stock * $old_wac) + ($qty_in * $price_in)) / $new_stock);

                // Tính giá bán mới
                $new_selling_price = round($new_wac * (1 + $margin));

                // 3. Cập nhật vào bảng Products
                $stmtU = $db->prepare("UPDATE products SET stock_quantity = ?, import_price = ?, selling_price = ? WHERE id = ?");
                $stmtU->bind_param("iiii", $new_stock, $new_wac, $new_selling_price, $p_id);
                $stmtU->execute();

                // 4. Cập nhật lại giá đã tính vào chi tiết phiếu (để lưu lịch sử)
                $stmtD = $db->prepare("UPDATE import_receipt_details SET calculated_average_price = ?, calculated_selling_price = ? WHERE id = ?");
                $stmtD->bind_param("iii", $new_wac, $new_selling_price, $item['id']);
                $stmtD->execute();
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }

    // --- LƯU CHỈNH SỬA PHIẾU NHÁP ---
    public function updateDraftTransaction($receipt_id, $supplier_id, $products_list, $import_date) {
        $db = $this->getConnection();
        $receipt_id = intval($receipt_id);

        // Kiểm tra xem phiếu có tồn tại và đúng là bản nháp không
        $receipt = $this->getImportById($receipt_id);
        if (!$receipt || $receipt['status'] !== 'draft') return false;

        $db->begin_transaction();
        try {
            // 1. Tính tổng tiền mới
            $total_amount = 0;
            foreach ($products_list as $item) {
                $total_amount += intval($item['quantity']) * intval($item['price']);
            }

            // 2. Cập nhật Nhà cung cấp và Tổng tiền ở bảng phiếu nhập
            $stmtR = $db->prepare("UPDATE import_receipts SET supplier_id = ?, total_amount = ?, created_at = ? WHERE id = ?");
            $stmtR->bind_param("iisi", $supplier_id, $total_amount, $import_date, $receipt_id);
            $stmtR->execute();

            // 3. XÓA TOÀN BỘ chi tiết cũ của phiếu này
            $db->query("DELETE FROM import_receipt_details WHERE receipt_id = $receipt_id");

            // 4. THÊM LẠI chi tiết mới (từ mảng người dùng gửi lên)
            foreach ($products_list as $item) {
                $p_id = intval($item['product_id']);
                $qty_in = intval($item['quantity']);
                $price_in = intval($item['price']);
                
                // Vì là draft nên WAC và Giá bán vẫn lưu = 0
                $sqlDetail = "INSERT INTO import_receipt_details (receipt_id, product_id, quantity, price, calculated_average_price, calculated_selling_price) VALUES (?, ?, ?, ?, 0, 0)";
                $stmtD = $db->prepare($sqlDetail);
                $stmtD->bind_param("iiii", $receipt_id, $p_id, $qty_in, $price_in);
                $stmtD->execute();
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }
}