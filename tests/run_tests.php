<?php
/**
 * KIỂM THỬ CÁC CHỨC NĂNG CƠ BẢN - LEGO SHOP
 *
 * Chạy:   php tests/run_tests.php            (xóa DB test sau khi chạy xong)
 *         php tests/run_tests.php --keep     (giữ lại DB test để xem dữ liệu)
 *
 * - Chạy trên database RIÊNG `lego_shop_test`, tạo mới từ lego_shop.sql mỗi lần,
 *   nên không ảnh hưởng dữ liệu thật trong `lego_shop`.
 * - Cần XAMPP đang bật MySQL. Phần "HTTP smoke test" cần thêm Apache (chỉ gửi GET, không ghi dữ liệu).
 */

date_default_timezone_set('Asia/Ho_Chi_Minh');
const TEST_DB = 'lego_shop_test';
putenv('DB_NAME=' . TEST_DB);   // phải đặt TRƯỚC khi nạp .env để không bị ghi đè

$ROOT = dirname(__DIR__);
require_once "$ROOT/app/core/Database.php";
foreach (['AccountModel', 'UserModel', 'CartModel', 'OrderModel', 'VoucherModel', 'PaymentModel', 'RefundModel'] as $m) {
    require_once "$ROOT/app/models/$m.php";
}

// =============================================================================
// MINI TEST FRAMEWORK
// =============================================================================
class AssertionFailed extends Exception {}

$PASS = 0;
$FAIL = 0;
$FAILURES = [];

function section($title) {
    echo "\n=== $title ===\n";
}

function test($name, callable $fn) {
    global $PASS, $FAIL, $FAILURES;
    try {
        $fn();
        $PASS++;
        echo "  [PASS] $name\n";
    } catch (Throwable $e) {
        $FAIL++;
        $where = $e instanceof AssertionFailed ? '' : ' (' . get_class($e) . ' tại ' . basename($e->getFile()) . ':' . $e->getLine() . ')';
        $FAILURES[] = "$name: " . $e->getMessage() . $where;
        echo "  [FAIL] $name\n         -> " . $e->getMessage() . "$where\n";
    }
}

function assertEq($expected, $actual, $msg = '') {
    if ($expected != $actual) {
        throw new AssertionFailed(($msg ? "$msg: " : '') . 'mong đợi ' . var_export($expected, true) . ', nhận được ' . var_export($actual, true));
    }
}

function assertTrue($cond, $msg = 'điều kiện sai') {
    if (!$cond) throw new AssertionFailed($msg);
}

function assertFalse($cond, $msg = 'điều kiện phải sai') {
    if ($cond) throw new AssertionFailed($msg);
}

function assertContains($needle, $haystack, $msg = '') {
    if (strpos((string)$haystack, (string)$needle) === false) {
        throw new AssertionFailed(($msg ? "$msg: " : '') . "không thấy '$needle' trong '" . mb_substr((string)$haystack, 0, 120) . "'");
    }
}

// =============================================================================
// CHUẨN BỊ DATABASE TEST
// =============================================================================
function setupTestDatabase($root) {
    // Thông số kết nối lấy từ .env (giống app/core/Database.php)
    $conn = new mysqli(env('DB_HOST', 'localhost'), env('DB_USER', 'root'), (string)env('DB_PASS', ''), null, (int)env('DB_PORT', 3306));
    $conn->set_charset('utf8mb4');
    $conn->query("DROP DATABASE IF EXISTS `" . TEST_DB . "`");
    $conn->query("CREATE DATABASE `" . TEST_DB . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $conn->select_db(TEST_DB);

    $sql = file_get_contents("$root/lego_shop.sql");
    $conn->multi_query($sql);
    do {
        if ($r = $conn->store_result()) $r->free();
    } while ($conn->more_results() && $conn->next_result());

    // Cấu hình cố định cho test
    $conn->query("UPDATE shop_settings SET bank_code = 'MB', bank_account_no = '0123456789', bank_account_name = 'LEGO TEST',
                  payment_webhook_key = 'test-key', payment_sandbox = 1, payment_timeout_minutes = 30, refund_days = 7");
    $conn->close();
}

function dropTestDatabase() {
    $conn = new mysqli(env('DB_HOST', 'localhost'), env('DB_USER', 'root'), (string)env('DB_PASS', ''), null, (int)env('DB_PORT', 3306));
    $conn->query("DROP DATABASE IF EXISTS `" . TEST_DB . "`");
    $conn->close();
}

// =============================================================================
// HÀM TẠO DỮ LIỆU MẪU (FIXTURE)
// =============================================================================
function db() {
    return (new Database())->getConnection();
}

function scalar($sql) {
    $row = db()->query($sql)->fetch_row();
    return $row ? $row[0] : null;
}

function row($sql) {
    return db()->query($sql)->fetch_assoc();
}

// Tạo khách hàng mới, trả về ['user_id', 'account_id', 'email']
function makeUser($status = 'active') {
    static $n = 0;
    $n++;
    $email = "tester{$n}_" . uniqid() . "@example.com";
    $phone = '09' . str_pad((string)mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    $result = (new AccountModel())->registerFull([
        'phone' => $phone, 'email' => $email, 'password' => 'matkhau123', 'fullname' => "Tester $n",
        'street' => '1 Test', 'ward' => 'P1', 'district' => 'Q1', 'city' => 'HCM',
    ]);
    if ($result !== true) throw new Exception("Không tạo được user: $result");
    $account_id = (int)scalar("SELECT id FROM accounts WHERE email = '$email'");
    if ($status !== 'active') db()->query("UPDATE accounts SET status = '$status' WHERE id = $account_id");
    return [
        'user_id' => (int)scalar("SELECT id FROM users WHERE account_id = $account_id"),
        'account_id' => $account_id,
        'email' => $email,
        'phone' => $phone,
    ];
}

// Tạo sản phẩm đang bán, trả về product_id
function makeProduct($stock = 10, $price = 100000) {
    $cat = scalar("SELECT id FROM categories LIMIT 1");
    $sku = 'T-' . strtoupper(uniqid());
    db()->query("INSERT INTO products (category_id, sku, name, import_price, selling_price, stock_quantity, status)
                 VALUES ($cat, '$sku', 'SP test $sku', " . (int)($price * 0.7) . ", $price, $stock, 1)");
    return db()->insert_id;
}

// Tạo đơn hàng, $items = [[product_id, qty, price], ...]
function makeOrder($user_id, $items, $opts = []) {
    $subtotal = 0;
    foreach ($items as [$pid, $qty, $price]) $subtotal += $qty * $price;
    $discount = $opts['discount'] ?? 0;

    $orderModel = new OrderModel();
    $order_id = $orderModel->createOrder([
        'user_id' => $user_id,
        'payment_method' => $opts['method'] ?? 'cash',
        'subtotal_amount' => $subtotal,
        'discount_amount' => $discount,
        'voucher_id' => $opts['voucher_id'] ?? null,
        'voucher_code' => $opts['voucher_code'] ?? null,
        'total_amount' => $subtotal - $discount,
        'shipping_fullname' => 'Tester', 'shipping_phone' => '0900000000', 'shipping_street' => '1 Test',
        'shipping_ward' => 'P1', 'shipping_district' => 'Q1', 'shipping_city' => 'HCM',
    ]);
    foreach ($items as [$pid, $qty, $price]) $orderModel->addOrderItem($order_id, $pid, $qty, $price);
    if (($opts['method'] ?? '') === 'vietqr') $orderModel->setPaymentCode($order_id, PaymentModel::makePaymentCode($order_id));

    $sets = [];
    if (isset($opts['status'])) $sets[] = "status = '{$opts['status']}'";
    if (isset($opts['payment_status'])) $sets[] = "payment_status = '{$opts['payment_status']}'";
    if (isset($opts['created_ago_min'])) $sets[] = "created_at = NOW() - INTERVAL {$opts['created_ago_min']} MINUTE";
    if ($sets) db()->query("UPDATE orders SET " . implode(', ', $sets) . " WHERE id = $order_id");
    return $order_id;
}

// Đơn đã giao: trừ kho thật như lúc admin chuyển "Giao thành công" + ghi mốc delivered
function makeDeliveredOrder($user_id, $product_id, $qty, $price, $delivered_days_ago = 1) {
    $order_id = makeOrder($user_id, [[$product_id, $qty, $price]], ['status' => 'delivered']);
    db()->query("UPDATE products SET stock_quantity = stock_quantity - $qty WHERE id = $product_id");
    db()->query("INSERT INTO order_history (order_id, status, note, changed_at)
                 VALUES ($order_id, 'delivered', 'test', NOW() - INTERVAL $delivered_days_ago DAY)");
    return $order_id;
}

function makeVoucher($fields = []) {
    $v = array_merge([
        'code' => 'T' . strtoupper(substr(uniqid(), -8)), 'description' => 'test', 'discount_type' => 'fixed',
        'discount_value' => 50000, 'max_discount' => null, 'min_order_value' => 0, 'usage_limit' => null,
        'per_user_limit' => 1, 'start_date' => null, 'end_date' => null, 'status' => 'active',
    ], $fields);
    (new VoucherModel())->insert($v);
    return (new VoucherModel())->getByCode($v['code']);
}

function makeRefund($order_id, $user_id, $type, $amount, $bank = ['MB Bank', '0123456789', 'TESTER']) {
    return (new RefundModel())->create([
        'order_id' => $order_id, 'user_id' => $user_id, 'type' => $type, 'amount' => $amount, 'reason' => 'test reason',
        'evidence_image' => null, 'bank_name' => $bank[0], 'bank_account_number' => $bank[1], 'bank_account_name' => $bank[2],
    ]);
}

// =============================================================================
// BẮT ĐẦU
// =============================================================================
echo "Chuẩn bị database test `" . TEST_DB . "`...\n";
try {
    setupTestDatabase($ROOT);
} catch (Throwable $e) {
    echo "KHÔNG TẠO ĐƯỢC DB TEST: " . $e->getMessage() . "\nHãy bật MySQL trong XAMPP.\n";
    exit(1);
}

$admin_id = (int)scalar("SELECT id FROM accounts WHERE role = 'admin' LIMIT 1");

// -----------------------------------------------------------------------------
section('1. Tài khoản');
// -----------------------------------------------------------------------------
test('Đăng ký thành công, mật khẩu được băm', function () {
    $u = makeUser();
    $hash = scalar("SELECT password FROM accounts WHERE id = {$u['account_id']}");
    assertTrue($hash !== 'matkhau123', 'mật khẩu không được lưu dạng thô');
    assertTrue(password_verify('matkhau123', $hash), 'hash không khớp mật khẩu');
    assertEq(1, scalar("SELECT COUNT(*) FROM user_addresses WHERE user_id = {$u['user_id']} AND is_default = 1"), 'địa chỉ mặc định');
});

test('Đăng ký trùng email bị từ chối', function () {
    $u = makeUser();
    $result = (new AccountModel())->registerFull([
        'phone' => '0911111111', 'email' => $u['email'], 'password' => 'x', 'fullname' => 'X',
        'street' => 'a', 'ward' => 'b', 'district' => 'c', 'city' => 'd',
    ]);
    assertTrue(is_string($result), 'phải trả về thông báo lỗi');
});

test('Đăng nhập đúng mật khẩu (bằng email hoặc SĐT)', function () {
    $u = makeUser();
    $acc = (new AccountModel())->login($u['email'], 'matkhau123');
    assertEq($u['user_id'], $acc['user_id']);
    assertFalse(isset($acc['password']), 'không được trả password ra ngoài');
    assertTrue((new AccountModel())->login($u['phone'], 'matkhau123') !== false, 'đăng nhập bằng SĐT');
});

test('Sai mật khẩu hoặc tài khoản bị khóa thì không đăng nhập được', function () {
    $u = makeUser();
    assertFalse((new AccountModel())->login($u['email'], 'sai-mat-khau'));
    $locked = makeUser('locked');
    assertFalse((new AccountModel())->login($locked['email'], 'matkhau123'));
});

test('Admin đăng nhập được bằng mật khẩu đã băm', function () {
    assertTrue((new AccountModel())->checkAdminLogin('admin', '123456') !== false);
    assertFalse((new AccountModel())->checkAdminLogin('admin', 'sai'));
});

// -----------------------------------------------------------------------------
section('2. Giỏ hàng & tồn kho');
// -----------------------------------------------------------------------------
test('Thêm vào giỏ trong giới hạn tồn kho', function () {
    $u = makeUser();
    $p = makeProduct(5);
    $cart = new CartModel();
    assertTrue($cart->addProductToCart($u['user_id'], $p, 3) === true);
    assertTrue($cart->addProductToCart($u['user_id'], $p, 2) === true, 'cộng dồn đến đúng tồn kho');
    assertEq(5, $cart->countCartItems($u['user_id']));
});

test('Thêm vượt tồn kho bị chặn', function () {
    $u = makeUser();
    $p = makeProduct(2);
    $cart = new CartModel();
    assertEq('out_of_stock', $cart->addProductToCart($u['user_id'], $p, 3));
    $cart->addProductToCart($u['user_id'], $p, 2);
    assertEq('out_of_stock', $cart->addProductToCart($u['user_id'], $p, 1), 'cộng dồn vượt kho');
    assertEq(2, $cart->countCartItems($u['user_id']));
});

test('Tồn khả dụng trừ hàng đang giữ cho đơn chưa giao, không trừ đơn đã hủy', function () {
    $u = makeUser();
    $p = makeProduct(10);
    $cart = new CartModel();
    makeOrder($u['user_id'], [[$p, 3, 100000]]);                              // pending -> giữ 3
    makeOrder($u['user_id'], [[$p, 2, 100000]], ['status' => 'shipping']);   // đang giao -> giữ 2
    makeOrder($u['user_id'], [[$p, 4, 100000]], ['status' => 'cancelled']);  // đã hủy -> không giữ
    assertEq(5, $cart->getAvailableStock($p));
});

test('Sản phẩm ngừng bán có tồn khả dụng = 0', function () {
    $p = makeProduct(10);
    db()->query("UPDATE products SET status = 2 WHERE id = $p");
    assertEq(0, (new CartModel())->getAvailableStock($p));
});

test('Tăng/giảm số lượng trong giỏ', function () {
    $u = makeUser();
    $p = makeProduct(2);
    $cart = new CartModel();
    $cart->addProductToCart($u['user_id'], $p, 1);
    $item_id = $cart->getCartItems($u['user_id'])[0]['cart_item_id'];
    assertTrue($cart->updateQuantity($item_id, $u['user_id'], 'increase') === true);
    assertEq('out_of_stock', $cart->updateQuantity($item_id, $u['user_id'], 'increase'), 'tăng vượt kho');
    assertTrue($cart->updateQuantity($item_id, $u['user_id'], 'decrease') === true);
    assertFalse($cart->updateQuantity($item_id, $u['user_id'], 'decrease'), 'không giảm dưới 1');
});

test('Không sửa được giỏ hàng của người khác', function () {
    $a = makeUser();
    $b = makeUser();
    $p = makeProduct(5);
    $cart = new CartModel();
    $cart->addProductToCart($a['user_id'], $p, 1);
    $item_id = $cart->getCartItems($a['user_id'])[0]['cart_item_id'];
    assertFalse($cart->updateQuantity($item_id, $b['user_id'], 'increase'));
    $cart->removeCartItem($item_id, $b['user_id']);
    assertEq(1, $cart->countCartItems($a['user_id']), 'B không xóa được món của A');
});

// -----------------------------------------------------------------------------
section('3. Đơn hàng');
// -----------------------------------------------------------------------------
test('Tạo đơn lưu đủ tạm tính, giảm giá, tổng tiền và giá vốn', function () {
    $u = makeUser();
    $p1 = makeProduct(10, 100000);
    $p2 = makeProduct(10, 50000);
    $id = makeOrder($u['user_id'], [[$p1, 2, 100000], [$p2, 1, 50000]], ['discount' => 30000, 'voucher_code' => 'X']);
    $o = (new OrderModel())->getOrderById($id);
    assertEq(250000, $o['subtotal_amount']);
    assertEq(30000, $o['discount_amount']);
    assertEq(220000, $o['total_amount']);
    assertEq('pending', $o['status']);
    assertEq('unpaid', $o['payment_status']);
    assertEq(2, count((new OrderModel())->getOrderItems($id)));
    assertEq(70000, scalar("SELECT cost_price FROM order_details WHERE order_id = $id AND product_id = $p1"), 'giá vốn lấy từ import_price');
});

test('Đơn VietQR có nội dung chuyển khoản LEGO<mã đơn>', function () {
    $u = makeUser();
    $id = makeOrder($u['user_id'], [[makeProduct(), 1, 100000]], ['method' => 'vietqr']);
    assertEq("LEGO$id", (new OrderModel())->getOrderById($id)['payment_code']);
});

// -----------------------------------------------------------------------------
section('4. Mã giảm giá');
// -----------------------------------------------------------------------------
test('Tính tiền giảm: cố định, phần trăm, giới hạn tối đa, không vượt tổng tiền', function () {
    $vm = new VoucherModel();
    assertEq(50000, $vm->calculateDiscount(['discount_type' => 'fixed', 'discount_value' => 50000, 'max_discount' => null], 300000));
    assertEq(30000, $vm->calculateDiscount(['discount_type' => 'percent', 'discount_value' => 10, 'max_discount' => null], 300000));
    assertEq(20000, $vm->calculateDiscount(['discount_type' => 'percent', 'discount_value' => 10, 'max_discount' => 20000], 300000), 'chặn mức tối đa');
    assertEq(40000, $vm->calculateDiscount(['discount_type' => 'fixed', 'discount_value' => 50000, 'max_discount' => null], 40000), 'không giảm quá tổng đơn');
});

test('Áp dụng mã hợp lệ (không phân biệt hoa thường)', function () {
    $u = makeUser();
    $v = makeVoucher(['discount_type' => 'percent', 'discount_value' => 10, 'min_order_value' => 100000]);
    $r = (new VoucherModel())->validate(strtolower($v['code']), $u['user_id'], 200000);
    assertTrue($r['ok'], $r['message']);
    assertEq(20000, $r['discount']);
});

test('Từ chối mã không tồn tại / ngừng áp dụng / hết hạn / chưa đến ngày / đơn chưa đủ tối thiểu', function () {
    $u = makeUser();
    $vm = new VoucherModel();
    assertFalse($vm->validate('KHONGCO', $u['user_id'], 500000)['ok'], 'mã không tồn tại');
    assertFalse($vm->validate(makeVoucher(['status' => 'inactive'])['code'], $u['user_id'], 500000)['ok'], 'mã ngừng áp dụng');
    assertFalse($vm->validate(makeVoucher(['end_date' => date('Y-m-d H:i:s', strtotime('-1 day'))])['code'], $u['user_id'], 500000)['ok'], 'mã hết hạn');
    assertFalse($vm->validate(makeVoucher(['start_date' => date('Y-m-d H:i:s', strtotime('+1 day'))])['code'], $u['user_id'], 500000)['ok'], 'mã chưa bắt đầu');
    $r = $vm->validate(makeVoucher(['min_order_value' => 300000])['code'], $u['user_id'], 299000);
    assertFalse($r['ok'], 'chưa đủ đơn tối thiểu');
    assertContains('300.000', $r['message']);
});

test('Giới hạn lượt mỗi khách + hoàn lượt khi hủy đơn', function () {
    $u = makeUser();
    $other = makeUser();
    $vm = new VoucherModel();
    $v = makeVoucher(['per_user_limit' => 1]);
    $order = makeOrder($u['user_id'], [[makeProduct(), 1, 100000]]);

    $vm->recordUsage($v['id'], $u['user_id'], $order, 50000);
    assertEq(1, $vm->getById($v['id'])['used_count']);
    assertFalse($vm->validate($v['code'], $u['user_id'], 100000)['ok'], 'đã dùng hết lượt');
    assertTrue($vm->validate($v['code'], $other['user_id'], 100000)['ok'], 'khách khác vẫn dùng được');

    $vm->releaseUsage($order);
    assertEq(0, $vm->getById($v['id'])['used_count']);
    assertTrue($vm->validate($v['code'], $u['user_id'], 100000)['ok'], 'được dùng lại sau khi hủy đơn');
});

test('Hết tổng lượt dùng', function () {
    $u = makeUser();
    $v = makeVoucher(['usage_limit' => 1]);
    db()->query("UPDATE vouchers SET used_count = 1 WHERE id = {$v['id']}");
    assertFalse((new VoucherModel())->validate($v['code'], $u['user_id'], 100000)['ok']);
    assertEq('exhausted', VoucherModel::computeState((new VoucherModel())->getById($v['id'])));
});

test('Admin: không tạo được mã trùng, mã đã dùng chỉ được ngừng áp dụng', function () {
    $vm = new VoucherModel();
    $v = makeVoucher();
    assertTrue($vm->codeExists($v['code']));
    assertFalse($vm->codeExists($v['code'], $v['id']), 'bỏ qua chính nó khi sửa');
    assertFalse($vm->isUsed($v['id']));
    $u = makeUser();
    makeOrder($u['user_id'], [[makeProduct(), 1, 100000]], ['voucher_id' => $v['id'], 'voucher_code' => $v['code']]);
    assertTrue($vm->isUsed($v['id']), 'đã có đơn dùng mã');
});

// -----------------------------------------------------------------------------
section('5. Thanh toán VietQR');
// -----------------------------------------------------------------------------
test('Đọc mã đơn từ nội dung chuyển khoản', function () {
    assertEq(25, PaymentModel::extractOrderId('NGUYEN VAN A chuyen tien LEGO25 FT123'));
    assertEq(7, PaymentModel::extractOrderId('lego7'));
    assertEq(12, PaymentModel::extractOrderId('MBVCB.123.LEGO 12.CT tu 0123'));
    assertEq(0, PaymentModel::extractOrderId('tien an trua'));
    assertEq('LEGO99', PaymentModel::makePaymentCode(99));
});

test('Link QR có đúng ngân hàng, số tiền, nội dung', function () {
    $url = (new PaymentModel())->buildQrUrl(['total_amount' => 615600, 'payment_code' => 'LEGO24']);
    assertContains('img.vietqr.io/image/MB-0123456789', $url);
    assertContains('amount=615600', $url);
    assertContains('addInfo=LEGO24', $url);
});

test('Nhận tiền: thiếu tiền -> chưa thanh toán; đủ tiền -> đã thanh toán', function () {
    $u = makeUser();
    $pm = new PaymentModel();
    $id = makeOrder($u['user_id'], [[makeProduct(), 1, 200000]], ['method' => 'vietqr']);

    assertEq('amount_mismatch', $pm->confirmPayment($id, 100000, 'sepay', 'TX-A', "LEGO$id"));
    assertEq('unpaid', (new OrderModel())->getOrderById($id)['payment_status']);

    assertEq('paid', $pm->confirmPayment($id, 200000, 'sepay', 'TX-B', "LEGO$id"));
    $o = (new OrderModel())->getOrderById($id);
    assertEq('paid', $o['payment_status']);
    assertTrue(!empty($o['paid_at']), 'phải lưu thời điểm thanh toán');
    assertEq(1, scalar("SELECT COUNT(*) FROM order_history WHERE order_id = $id AND status = 'paid'"));
    assertEq(2, scalar("SELECT COUNT(*) FROM payment_transactions WHERE order_id = $id"), 'ghi log cả 2 giao dịch');
});

test('Webhook gửi trùng / thanh toán 2 lần không bị cộng lại', function () {
    $u = makeUser();
    $pm = new PaymentModel();
    $id = makeOrder($u['user_id'], [[makeProduct(), 1, 100000]], ['method' => 'vietqr']);
    assertEq('paid', $pm->confirmPayment($id, 100000, 'sepay', 'TX-DUP', "LEGO$id"));
    assertEq('duplicate', $pm->confirmPayment($id, 100000, 'sepay', 'TX-DUP', "LEGO$id"));
    assertEq('already_paid', $pm->confirmPayment($id, 100000, 'sepay', 'TX-OTHER', "LEGO$id"));
});

test('Tiền vào cho đơn không tồn tại hoặc đơn đã hủy', function () {
    $u = makeUser();
    $pm = new PaymentModel();
    assertEq('not_found', $pm->confirmPayment(999999, 100000, 'casso', 'TX-NF', 'LEGO999999'));
    $id = makeOrder($u['user_id'], [[makeProduct(), 1, 100000]], ['method' => 'vietqr', 'status' => 'cancelled']);
    assertEq('order_cancelled', $pm->confirmPayment($id, 100000, 'sepay', 'TX-CXL', "LEGO$id"));
    assertEq('unpaid', (new OrderModel())->getOrderById($id)['payment_status']);
});

test('Tự hủy đơn VietQR quá hạn chưa thanh toán + hoàn lượt voucher', function () {
    $u = makeUser();
    $v = makeVoucher();
    $vm = new VoucherModel();
    $p = makeProduct();
    $old = makeOrder($u['user_id'], [[$p, 1, 100000]], ['method' => 'vietqr', 'created_ago_min' => 45, 'voucher_id' => $v['id']]);
    $vm->recordUsage($v['id'], $u['user_id'], $old, 50000);
    $fresh = makeOrder($u['user_id'], [[$p, 1, 100000]], ['method' => 'vietqr', 'created_ago_min' => 5]);
    $paid = makeOrder($u['user_id'], [[$p, 1, 100000]], ['method' => 'vietqr', 'created_ago_min' => 45, 'payment_status' => 'paid']);
    $cod = makeOrder($u['user_id'], [[$p, 1, 100000]], ['method' => 'cash', 'created_ago_min' => 45]);

    (new PaymentModel())->expireUnpaidOrders($u['user_id']);
    $om = new OrderModel();
    assertEq('cancelled', $om->getOrderById($old)['status'], 'đơn quá hạn');
    assertEq('pending', $om->getOrderById($fresh)['status'], 'đơn còn hạn');
    assertEq('pending', $om->getOrderById($paid)['status'], 'đơn đã thanh toán');
    assertEq('pending', $om->getOrderById($cod)['status'], 'đơn COD');
    assertEq(0, $vm->getById($v['id'])['used_count'], 'hoàn lượt voucher');
});

// -----------------------------------------------------------------------------
section('6. Hoàn tiền / Trả hàng');
// -----------------------------------------------------------------------------
test('Kiểm tra tài khoản nhận tiền hoàn', function () {
    assertEq(null, RefundModel::validateBankInfo('MB Bank', '0123456789', 'NGUYEN VAN A'));
    assertTrue(RefundModel::validateBankInfo('', '0123456789', 'A B C') !== null, 'thiếu ngân hàng');
    assertTrue(RefundModel::validateBankInfo('Ngan hang ma', '0123456789', 'A B C') !== null, 'ngân hàng không có trong danh sách');
    assertTrue(RefundModel::validateBankInfo('MB Bank', '12ab', 'A B C') !== null, 'số tài khoản sai');
});

test('Điều kiện trả hàng: phải đã giao và còn trong hạn 7 ngày', function () {
    $u = makeUser();
    $rm = new RefundModel();
    $p = makeProduct(20);
    $pending = (new OrderModel())->getOrderById(makeOrder($u['user_id'], [[$p, 1, 100000]]));
    assertFalse($rm->checkReturnEligibility($pending, 7)['ok'], 'đơn chưa giao');

    $recent = (new OrderModel())->getOrderById(makeDeliveredOrder($u['user_id'], $p, 1, 100000, 2));
    assertTrue($rm->checkReturnEligibility($recent, 7)['ok'], 'giao 2 ngày trước');

    $late = (new OrderModel())->getOrderById(makeDeliveredOrder($u['user_id'], $p, 1, 100000, 10));
    assertFalse($rm->checkReturnEligibility($late, 7)['ok'], 'giao 10 ngày trước');

    makeRefund($recent['id'], $u['user_id'], 'return', 100000);
    assertFalse($rm->checkReturnEligibility($recent, 7)['ok'], 'đã có yêu cầu đang xử lý');
});

test('Hoàn tất trả hàng: đơn -> returned, đã hoàn tiền, cộng lại kho', function () {
    global $admin_id;
    $u = makeUser();
    $rm = new RefundModel();
    $p = makeProduct(10, 150000);
    $order_id = makeDeliveredOrder($u['user_id'], $p, 3, 150000);
    assertEq(7, scalar("SELECT stock_quantity FROM products WHERE id = $p"), 'kho sau khi giao');

    $rid = makeRefund($order_id, $u['user_id'], 'return', 450000);
    $refund = $rm->getDetail($rid);
    assertTrue($rm->approve($refund, $admin_id, 'ok'));
    assertTrue($rm->complete($rm->getDetail($rid), $admin_id, 'FT1'));

    $o = (new OrderModel())->getOrderById($order_id);
    assertEq('returned', $o['status']);
    assertEq('refunded', $o['payment_status']);
    assertEq(10, scalar("SELECT stock_quantity FROM products WHERE id = $p"), 'kho được cộng lại');
    assertEq('completed', $rm->getDetail($rid)['status']);
    assertFalse($rm->complete($rm->getDetail($rid), $admin_id, ''), 'không hoàn tất được lần 2');
});

test('Hoàn tiền đơn hủy: đã hoàn tiền nhưng không đụng kho, không được từ chối', function () {
    global $admin_id;
    $u = makeUser();
    $rm = new RefundModel();
    $p = makeProduct(10);
    $order_id = makeOrder($u['user_id'], [[$p, 2, 100000]], ['method' => 'vietqr', 'status' => 'cancelled', 'payment_status' => 'refund_pending']);
    $rid = makeRefund($order_id, $u['user_id'], 'cancel', 200000);

    assertFalse($rm->reject($rm->getDetail($rid), $admin_id, 'khong'), 'đơn hủy đã thanh toán bắt buộc hoàn tiền');
    assertTrue($rm->complete($rm->getDetail($rid), $admin_id, ''));
    $o = (new OrderModel())->getOrderById($order_id);
    assertEq('cancelled', $o['status']);
    assertEq('refunded', $o['payment_status']);
    assertEq(10, scalar("SELECT stock_quantity FROM products WHERE id = $p"), 'kho giữ nguyên');
});

test('Từ chối trả hàng thì khách được gửi lại yêu cầu', function () {
    global $admin_id;
    $u = makeUser();
    $rm = new RefundModel();
    $order_id = makeDeliveredOrder($u['user_id'], makeProduct(10), 1, 100000);
    $rid = makeRefund($order_id, $u['user_id'], 'return', 100000);
    assertTrue($rm->reject($rm->getDetail($rid), $admin_id, 'Hàng không lỗi'));
    assertEq('rejected', $rm->getDetail($rid)['status']);
    $order = (new OrderModel())->getOrderById($order_id);
    assertEq('delivered', $order['status'], 'đơn vẫn là đã giao');
    assertTrue($rm->checkReturnEligibility($order, 7)['ok'], 'được gửi lại');
});

test('QR hoàn tiền tạo đúng mã ngân hàng của khách', function () {
    $url = RefundModel::buildRefundQrUrl(['order_id' => 5, 'amount' => 120000, 'bank_name' => 'Vietcombank',
        'bank_account_number' => '1234567890', 'bank_account_name' => 'NGUYEN A']);
    assertContains('image/VCB-1234567890', $url);
    assertContains('amount=120000', $url);
    assertEq(null, RefundModel::buildRefundQrUrl(['order_id' => 5, 'amount' => 1, 'bank_name' => '', 'bank_account_number' => '', 'bank_account_name' => '']));
});

// -----------------------------------------------------------------------------
section('7. HTTP smoke test (cần Apache, chỉ gửi GET)');
// -----------------------------------------------------------------------------
$BASE = 'http://localhost/lego_shop_php';
$http = function ($path, $method = 'GET', $headers = []) use ($BASE) {
    $ctx = stream_context_create(['http' => ['method' => $method, 'header' => implode("\r\n", $headers), 'ignore_errors' => true, 'timeout' => 10, 'follow_location' => 0]]);
    $body = @file_get_contents($BASE . $path, false, $ctx);
    $status = isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m) ? (int)$m[1] : 0;
    return [$status, (string)$body];
};

if ($http('/')[0] === 0) {
    echo "  [SKIP] Không kết nối được Apache tại $BASE\n";
} else {
    foreach (['/home' => 'Trang chủ', '/product' => 'Danh sách sản phẩm', '/account/login' => 'Trang đăng nhập', '/account/register' => 'Trang đăng ký'] as $path => $name) {
        test("$name ($path) trả về 200, không lỗi PHP", function () use ($http, $path) {
            [$status, $body] = $http($path);
            assertEq(200, $status, 'HTTP status');
            assertFalse(preg_match('/Fatal error|Warning:|Uncaught/', $body), 'trang có lỗi PHP');
        });
    }
    test('Vào giỏ hàng / thanh toán khi chưa đăng nhập -> chuyển về trang đăng nhập', function () use ($http) {
        [$status] = $http('/checkout');
        assertEq(302, $status);
    });
    test('Webhook sai API key -> 401', function () use ($http) {
        [$status] = $http('/payment/webhook', 'POST', ['Authorization: Apikey sai-key', 'Content-Type: application/json']);
        assertEq(401, $status);
    });
    test('Trang admin khi chưa đăng nhập -> chuyển về trang đăng nhập admin', function () use ($http) {
        [$status] = $http('/admin/refund');
        assertEq(302, $status);
    });
}

// =============================================================================
// KẾT QUẢ
// =============================================================================
if (in_array('--keep', $argv ?? [])) {
    echo "\n(Giữ lại database `" . TEST_DB . "` để kiểm tra)\n";
} else {
    dropTestDatabase();
}

echo "\n" . str_repeat('-', 60) . "\n";
echo "KẾT QUẢ: $PASS passed, $FAIL failed\n";
if ($FAIL > 0) {
    echo "\nCác test lỗi:\n";
    foreach ($FAILURES as $f) echo "  - $f\n";
    exit(1);
}
exit(0);
