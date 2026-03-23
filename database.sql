-- ==========================================
-- 1. CỤM TÀI KHOẢN & NGƯỜI DÙNG
-- ==========================================

CREATE TABLE accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) UNIQUE NOT NULL, -- Đã thêm để khớp với chức năng Login/Register PHP
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    status ENUM('active', 'locked') DEFAULT 'active', 
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE
);

CREATE TABLE user_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    receiver_name VARCHAR(100) NOT NULL, 
    receiver_phone VARCHAR(20) NOT NULL, 
    street VARCHAR(255) NOT NULL,
    ward VARCHAR(100) NOT NULL,
    district VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    is_default TINYINT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================================
-- 2. CỤM SẢN PHẨM & DANH MỤC
-- ==========================================

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT, 
    image_url VARCHAR(255),
    status VARCHAR(20) DEFAULT 'active', -- Đã thêm để khớp code Header PHP
    ordering INT DEFAULT 0               -- Đã thêm để sắp xếp Menu
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    sku VARCHAR(50) UNIQUE NOT NULL, 
    name VARCHAR(255) NOT NULL,
    description TEXT,
    unit VARCHAR(50) DEFAULT 'Hộp', 
    import_price INT DEFAULT 0, 
    profit_margin FLOAT DEFAULT 0, 
    selling_price INT DEFAULT 0, 
    stock_quantity INT DEFAULT 0, 
    min_stock_level INT DEFAULT 5, 
    status TINYINT DEFAULT 1, 
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    is_main TINYINT DEFAULT 0, 
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ==========================================
-- 3. CỤM QUẢN LÝ NHẬP KHO (BÌNH QUÂN GIA QUYỀN)
-- ==========================================

CREATE TABLE import_receipts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    total_amount INT DEFAULT 0,
    status ENUM('draft', 'completed') DEFAULT 'draft', 
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES accounts(id)
);

CREATE TABLE import_receipt_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price INT NOT NULL, 
    calculated_average_price INT DEFAULT 0, 
    calculated_selling_price INT DEFAULT 0, 
    FOREIGN KEY (receipt_id) REFERENCES import_receipts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ==========================================
-- 4. CỤM GIỎ HÀNG 
-- ==========================================

CREATE TABLE carts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE, 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ==========================================
-- 5. CỤM ĐƠN HÀNG
-- ==========================================

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    status ENUM('pending', 'confirmed', 'delivered', 'cancelled') DEFAULT 'pending', 
    payment_method ENUM('cash', 'transfer', 'online') NOT NULL,
    total_amount INT NOT NULL,
    shipping_fullname VARCHAR(100) NOT NULL,
    shipping_phone VARCHAR(20) NOT NULL,
    shipping_street VARCHAR(255) NOT NULL,
    shipping_ward VARCHAR(100) NOT NULL, 
    shipping_district VARCHAR(100) NOT NULL,
    shipping_city VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE order_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price INT NOT NULL, 
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ==========================================
-- 6. LỊCH SỬ XUẤT NHẬP
-- ==========================================

CREATE TABLE inventory_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    transaction_type ENUM('IN', 'OUT') NOT NULL, 
    quantity INT NOT NULL,
    reference_id INT, 
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);