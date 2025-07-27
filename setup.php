<?php
require_once 'config.php';

echo "Setting up Vape Tube database...\n";

try {
    // Create database connection
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute SQL file
    $sql = file_get_contents('database_schema.sql');
    $pdo->exec($sql);
    
    echo "Database schema created successfully!\n";
    
    // Insert sample products
    $db = new Database();
    $product = new Product($db);
    
    $sampleProducts = [
        [
            'name' => 'SMOK Nord 4',
            'name_fa' => 'اسموک نورد ۴',
            'category_id' => 2, // Pod Systems
            'subcategory_id' => 4,
            'description_fa' => 'پاد سیستم قدرتمند و کاربردی با باتری ۲۰۰۰ میلی آمپر ساعت',
            'short_description_fa' => 'پاد سیستم اسموک نورد ۴',
            'price' => 1500000,
            'sale_price' => 1200000,
            'sku' => 'VT-SMOK-NORD4',
            'stock_quantity' => 25,
            'is_featured' => 1,
            'image_url' => 'assets/images/smok-nord4.jpg'
        ],
        [
            'name' => 'VOOPOO Drag X',
            'name_fa' => 'ووپو درگ ایکس',
            'category_id' => 1, // Vape Mod
            'subcategory_id' => 1,
            'description_fa' => 'مود ویپ حرفه‌ای با قدرت ۸۰ وات و طراحی زیبا',
            'short_description_fa' => 'مود ویپ ووپو درگ ایکس',
            'price' => 2500000,
            'sku' => 'VT-VOOPOO-DRAGX',
            'stock_quantity' => 15,
            'is_featured' => 1,
            'image_url' => 'assets/images/voopoo-dragx.jpg'
        ],
        [
            'name' => 'Nasty Juice - Mango',
            'name_fa' => 'نستی جوس - انبه',
            'category_id' => 4, // E-Juice
            'subcategory_id' => 10,
            'description_fa' => 'مایع ویپ با طعم انبه طبیعی و کیفیت بالا',
            'short_description_fa' => 'مایع ویپ طعم انبه',
            'price' => 350000,
            'sale_price' => 300000,
            'sku' => 'VT-NASTY-MANGO',
            'stock_quantity' => 50,
            'is_featured' => 1,
            'image_url' => 'assets/images/nasty-mango.jpg'
        ],
        [
            'name' => 'JUUL Pod - Mint',
            'name_fa' => 'جول پاد - نعنا',
            'category_id' => 3, // Salt Nicotine
            'subcategory_id' => 7,
            'description_fa' => 'پاد نیکوتین نمک با طعم نعنا خنک',
            'short_description_fa' => 'پاد نیکوتین نمک نعنا',
            'price' => 180000,
            'sku' => 'VT-JUUL-MINT',
            'stock_quantity' => 100,
            'image_url' => 'assets/images/juul-mint.jpg'
        ],
        [
            'name' => 'Puff Bar Disposable',
            'name_fa' => 'پاف بار یکبار مصرف',
            'category_id' => 5, // Disposable
            'subcategory_id' => 13,
            'description_fa' => 'ویپ یکبار مصرف با ۳۰۰ پاف',
            'short_description_fa' => 'ویپ یکبار مصرف',
            'price' => 120000,
            'sale_price' => 100000,
            'sku' => 'VT-PUFF-BAR',
            'stock_quantity' => 200,
            'is_featured' => 1,
            'image_url' => 'assets/images/puff-bar.jpg'
        ],
        [
            'name' => 'Mesh Coil 0.4ohm',
            'name_fa' => 'کویل مش ۰.۴ اهم',
            'category_id' => 6, // Coil & Cartridge
            'subcategory_id' => 16,
            'description_fa' => 'کویل مش با مقاومت ۰.۴ اهم برای طعم بهتر',
            'short_description_fa' => 'کویل مش ۰.۴ اهم',
            'price' => 80000,
            'sku' => 'VT-MESH-04',
            'stock_quantity' => 75,
            'image_url' => 'assets/images/mesh-coil.jpg'
        ],
        [
            'name' => 'Vape Carrying Case',
            'name_fa' => 'کیف حمل ویپ',
            'category_id' => 7, // Accessories
            'subcategory_id' => 19,
            'description_fa' => 'کیف حمل ویپ با جای مخصوص مایعات',
            'short_description_fa' => 'کیف حمل ویپ',
            'price' => 250000,
            'sku' => 'VT-CASE-01',
            'stock_quantity' => 30,
            'image_url' => 'assets/images/vape-case.jpg'
        ],
        [
            'name' => 'IQOS ILUMA',
            'name_fa' => 'آیکوس ایلوما',
            'category_id' => 8, // E-Cigarettes
            'subcategory_id' => 22,
            'description_fa' => 'سیگار الکترونیکی آیکوس نسل جدید',
            'short_description_fa' => 'سیگار الکترونیکی آیکوس',
            'price' => 4500000,
            'sale_price' => 4000000,
            'sku' => 'VT-IQOS-ILUMA',
            'stock_quantity' => 10,
            'is_featured' => 1,
            'image_url' => 'assets/images/iqos-iluma.jpg'
        ]
    ];
    
    foreach ($sampleProducts as $productData) {
        $product->addProduct($productData);
    }
    
    echo "Sample products added successfully!\n";
    echo "Setup completed!\n";
    echo "You can now access the website at: http://localhost/\n";
    echo "Admin login: admin / admin123\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

