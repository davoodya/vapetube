<?php
// حذف تمام فاصله‌ها و خروجی‌های احتمالی قبل از این خط
header_remove(); // پاک کردن تمام هدرهای قبلی
header('Content-Type: application/json; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");

// اطلاعات اتصال به دیتابیس
define('DB_HOST', 'localhost');
define('DB_NAME', 'vape_tube');
define('DB_USER', 'root');
define('DB_PASS', '12945');

$response = [
    'success' => false,
    'message' => '',
    'results' => []
];

try {
    // دریافت عبارت جستجو
    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData, true);
    $searchTerm = $data['search_term'] ?? '';

    if (empty($searchTerm)) {
        throw new Exception('عبارت جستجو نباید خالی باشد');
    }

    // اتصال به دیتابیس
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // اجرای کوئری
    $stmt = $pdo->prepare("
        SELECT id, name, name_fa, price, image_url, category_id
        FROM products
        WHERE name LIKE :search OR name_fa LIKE :search
        LIMIT 10
    ");

    $searchParam = "%$searchTerm%";
    $stmt->bindParam(':search', $searchParam);
    $stmt->execute();

    $response['results'] = $stmt->fetchAll();
    $response['success'] = true;

} catch (PDOException $e) {
    $response['message'] = 'خطای دیتابیس: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// خروجی نهایی
ob_clean(); // پاک کردن بافرهای احتمالی
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit; // اطمینان از عدم خروجی اضافه
?>