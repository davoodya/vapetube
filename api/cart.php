<?php
session_start();

// نمونه‌ای از محصولات برای تست
$products = [
    1 => ['name' => 'Vape Mod', 'price' => 250000],
    2 => ['name' => 'Nicotine Salt', 'price' => 50000]
];

// بر اساس اکشن دریافتی، عملیات مناسب را انجام می‌دهیم
$action = $_GET['action'] ?? '';

// گرفتن سبد خرید
if ($action === 'get') {
    $cart = $_SESSION['cart'] ?? [];
    echo json_encode(['success' => true, 'items' => $cart]);
}

// افزودن آیتم به سبد خرید
elseif ($action === 'add') {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = $input['product_id'];
    $quantity = $input['quantity'];

    // بررسی موجود بودن محصول
    if (isset($products[$productId])) {
        $cart = $_SESSION['cart'] ?? [];
        $cart[$productId] = [
            'product_id' => $productId,
            'name' => $products[$productId]['name'],
            'price' => $products[$productId]['price'],
            'quantity' => $quantity,
            'total_price' => $products[$productId]['price'] * $quantity
        ];
        $_SESSION['cart'] = $cart;
        echo json_encode(['success' => true, 'item' => $cart[$productId]]);
    } else {
        echo json_encode(['success' => false, 'message' => 'محصول یافت نشد']);
    }
}

// حذف آیتم از سبد خرید
elseif ($action === 'remove') {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = $input['product_id'];

    $cart = $_SESSION['cart'] ?? [];
    unset($cart[$productId]);
    $_SESSION['cart'] = $cart;

    echo json_encode(['success' => true]);
}

// به‌روز رسانی تعداد آیتم در سبد خرید
elseif ($action === 'update') {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = $input['product_id'];
    $quantity = $input['quantity'];

    $cart = $_SESSION['cart'] ?? [];
    if (isset($cart[$productId])) {
        $cart[$productId]['quantity'] = $quantity;
        $cart[$productId]['total_price'] = $cart[$productId]['price'] * $quantity;
        $_SESSION['cart'] = $cart;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'آیتم یافت نشد']);
    }
}
?>
