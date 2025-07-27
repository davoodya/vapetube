<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=vape_tube', 'root', '12945');
    echo "اتصال موفقیت‌آمیز بود!";
} catch (PDOException $e) {
    echo "خطا در اتصال: " . $e->getMessage();
}
?>