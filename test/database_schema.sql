-- Vape Tube Database Schema
-- MySQL Database for Vape Pod Store Website

CREATE DATABASE IF NOT EXISTS vape_tube CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vape_tube;

-- Users table for authentication and user management
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Product categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    name_fa VARCHAR(100) NOT NULL, -- Persian name
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    description_fa TEXT, -- Persian description
    image_url VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Product subcategories table
CREATE TABLE subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    name_fa VARCHAR(100) NOT NULL, -- Persian name
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    description_fa TEXT, -- Persian description
    image_url VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    subcategory_id INT,
    name VARCHAR(200) NOT NULL,
    name_fa VARCHAR(200) NOT NULL, -- Persian name
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    description_fa TEXT, -- Persian description
    short_description TEXT,
    short_description_fa TEXT, -- Persian short description
    price DECIMAL(10, 2) NOT NULL,
    sale_price DECIMAL(10, 2),
    sku VARCHAR(100) UNIQUE NOT NULL,
    stock_quantity INT DEFAULT 0,
    low_stock_threshold INT DEFAULT 5,
    weight DECIMAL(8, 2),
    dimensions VARCHAR(100),
    image_url VARCHAR(255),
    gallery_images JSON, -- Array of image URLs
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    meta_title VARCHAR(200),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id)
);

-- Shopping cart table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    total_amount DECIMAL(10, 2) NOT NULL,
    shipping_address TEXT NOT NULL,
    billing_address TEXT,
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Product reviews table
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(200),
    comment TEXT,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Website content pages table (for About Us, FAQ, Articles, etc.)
CREATE TABLE pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    title_fa VARCHAR(200) NOT NULL, -- Persian title
    slug VARCHAR(200) UNIQUE NOT NULL,
    content TEXT,
    content_fa TEXT, -- Persian content
    page_type ENUM('about', 'faq', 'article', 'contact', 'other') DEFAULT 'other',
    is_published BOOLEAN DEFAULT TRUE,
    meta_title VARCHAR(200),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact form submissions table
CREATE TABLE contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default categories
INSERT INTO categories (name, name_fa, slug, description_fa) VALUES
('Vape Mod', 'مود ویپ', 'vape-mod', 'انواع مود های ویپ با کیفیت بالا'),
('Pod Systems', 'سیستم های پاد', 'pod-systems', 'سیستم های پاد مدرن و کاربردی'),
('Salt Nicotine', 'نیکوتین نمک', 'salt-nicotine', 'انواع نیکوتین نمک با طعم های مختلف'),
('E-Juice', 'مایع ویپ', 'e-juice', 'مایعات ویپ با طعم های متنوع'),
('Disposable', 'یکبار مصرف', 'disposable', 'ویپ های یکبار مصرف'),
('Coil & Cartridge', 'کویل و کارتریج', 'coil-cartridge', 'کویل ها و کارتریج های یدکی'),
('Accessories', 'لوازم جانبی', 'accessories', 'لوازم جانبی و تکمیلی ویپ'),
('E-Cigarettes', 'سیگار الکترونیکی', 'e-cigarettes', 'انواع سیگار الکترونیکی');

-- Insert default subcategories
INSERT INTO subcategories (category_id, name, name_fa, slug, description_fa) VALUES
-- Vape Mod subcategories
(1, 'Item 1', 'آیتم ۱', 'vape-mod-item-1', 'زیر دسته اول مود ویپ'),
(1, 'Item 2', 'آیتم ۲', 'vape-mod-item-2', 'زیر دسته دوم مود ویپ'),
(1, 'Item 3', 'آیتم ۳', 'vape-mod-item-3', 'زیر دسته سوم مود ویپ'),

-- Pod Systems subcategories
(2, 'Item 1', 'آیتم ۱', 'pod-systems-item-1', 'زیر دسته اول سیستم های پاد'),
(2, 'Item 2', 'آیتم ۲', 'pod-systems-item-2', 'زیر دسته دوم سیستم های پاد'),
(2, 'Item 3', 'آیتم ۳', 'pod-systems-item-3', 'زیر دسته سوم سیستم های پاد'),

-- Salt Nicotine subcategories
(3, 'Item 1', 'آیتم ۱', 'salt-nicotine-item-1', 'زیر دسته اول نیکوتین نمک'),
(3, 'Item 2', 'آیتم ۲', 'salt-nicotine-item-2', 'زیر دسته دوم نیکوتین نمک'),
(3, 'Item 3', 'آیتم ۳', 'salt-nicotine-item-3', 'زیر دسته سوم نیکوتین نمک'),

-- E-Juice subcategories
(4, 'Item 1', 'آیتم ۱', 'e-juice-item-1', 'زیر دسته اول مایع ویپ'),
(4, 'Item 2', 'آیتم ۲', 'e-juice-item-2', 'زیر دسته دوم مایع ویپ'),
(4, 'Item 3', 'آیتم ۳', 'e-juice-item-3', 'زیر دسته سوم مایع ویپ'),

-- Disposable subcategories
(5, 'Item 1', 'آیتم ۱', 'disposable-item-1', 'زیر دسته اول یکبار مصرف'),
(5, 'Item 2', 'آیتم ۲', 'disposable-item-2', 'زیر دسته دوم یکبار مصرف'),
(5, 'Item 3', 'آیتم ۳', 'disposable-item-3', 'زیر دسته سوم یکبار مصرف'),

-- Coil & Cartridge subcategories
(6, 'Item 1', 'آیتم ۱', 'coil-cartridge-item-1', 'زیر دسته اول کویل و کارتریج'),
(6, 'Item 2', 'آیتم ۲', 'coil-cartridge-item-2', 'زیر دسته دوم کویل و کارتریج'),
(6, 'Item 3', 'آیتم ۳', 'coil-cartridge-item-3', 'زیر دسته سوم کویل و کارتریج'),

-- Accessories subcategories
(7, 'Item 1', 'آیتم ۱', 'accessories-item-1', 'زیر دسته اول لوازم جانبی'),
(7, 'Item 2', 'آیتم ۲', 'accessories-item-2', 'زیر دسته دوم لوازم جانبی'),
(7, 'Item 3', 'آیتم ۳', 'accessories-item-3', 'زیر دسته سوم لوازم جانبی'),

-- E-Cigarettes subcategories
(8, 'Item 1', 'آیتم ۱', 'e-cigarettes-item-1', 'زیر دسته اول سیگار الکترونیکی'),
(8, 'Item 2', 'آیتم ۲', 'e-cigarettes-item-2', 'زیر دسته دوم سیگار الکترونیکی'),
(8, 'Item 3', 'آیتم ۳', 'e-cigarettes-item-3', 'زیر دسته سوم سیگار الکترونیکی');

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password_hash, first_name, last_name, is_admin) VALUES
('admin', 'admin@vapetube.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدیر', 'سایت', TRUE);

-- Insert default pages
INSERT INTO pages (title, title_fa, slug, content_fa, page_type) VALUES
('About Us', 'درباره ما', 'about-us', 'محتوای صفحه درباره ما', 'about'),
('Contact Us', 'تماس با ما', 'contact-us', 'محتوای صفحه تماس با ما', 'contact'),
('FAQ', 'سوالات متداول', 'faq', 'محتوای صفحه سوالات متداول', 'faq');

