<?php
require_once '../config.php';

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    exit(0);
}

$db = new Database();
$cart = new Cart($db);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        switch ($action) {
            case 'get':
                handleGetCart($cart);
                break;
            case 'count':
                handleGetCartCount($cart);
                break;
            case 'total':
                handleGetCartTotal($cart);
                break;
            default:
                sendJsonResponse(['success' => false, 'message' => 'عملیات نامعتبر'], 400);
        }
        break;
    
    case 'POST':
        switch ($action) {
            case 'add':
                handleAddToCart($cart);
                break;
            case 'update':
                handleUpdateCartItem($cart);
                break;
            case 'remove':
                handleRemoveFromCart($cart);
                break;
            case 'clear':
                handleClearCart($cart);
                break;
            case 'checkout':
                handleCheckout($cart);
                break;
            default:
                sendJsonResponse(['success' => false, 'message' => 'عملیات نامعتبر'], 400);
        }
        break;
    
    default:
        sendJsonResponse(['success' => false, 'message' => 'متد نامعتبر'], 405);
}

function handleGetCart($cart) {
    if (!isset($_SESSION['user_id'])) {
        // Return session cart for non-logged-in users
        $sessionCart = $cart->getSessionCartItems();
        sendJsonResponse(['success' => true, 'items' => $sessionCart]);
        return;
    }
    
    $items = $cart->getCartItems($_SESSION['user_id']);
    $total = $cart->getCartTotal($_SESSION['user_id']);
    $count = $cart->getCartItemCount($_SESSION['user_id']);
    
    sendJsonResponse([
        'success' => true,
        'items' => $items,
        'total' => $total,
        'count' => $count
    ]);
}

function handleGetCartCount($cart) {
    if (!isset($_SESSION['user_id'])) {
        $sessionCart = $cart->getSessionCartItems();
        $count = array_sum(array_column($sessionCart, 'quantity'));
        sendJsonResponse(['success' => true, 'count' => $count]);
        return;
    }
    
    $count = $cart->getCartItemCount($_SESSION['user_id']);
    sendJsonResponse(['success' => true, 'count' => $count]);
}

function handleGetCartTotal($cart) {
    if (!isset($_SESSION['user_id'])) {
        $sessionCart = $cart->getSessionCartItems();
        $total = array_sum(array_column($sessionCart, 'total_price'));
        sendJsonResponse(['success' => true, 'total' => $total]);
        return;
    }
    
    $total = $cart->getCartTotal($_SESSION['user_id']);
    sendJsonResponse(['success' => true, 'total' => $total]);
}

function handleAddToCart($cart) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJsonResponse(['success' => false, 'message' => 'داده‌های ورودی نامعتبر'], 400);
    }
    
    $productId = $input['product_id'] ?? null;
    $quantity = $input['quantity'] ?? 1;
    
    if (!$productId) {
        sendJsonResponse(['success' => false, 'message' => 'شناسه محصول ضروری است'], 400);
    }
    
    if ($quantity <= 0) {
        sendJsonResponse(['success' => false, 'message' => 'تعداد باید بیشتر از صفر باشد'], 400);
    }
    
    // Rate limiting
    if (!rateLimit('add_to_cart_' . getClientIP(), 30, 60)) {
        sendJsonResponse(['success' => false, 'message' => 'تعداد درخواست‌ها بیش از حد مجاز'], 429);
    }
    
    if (!isset($_SESSION['user_id'])) {
        $result = $cart->addToSessionCart($productId, $quantity);
        sendJsonResponse($result);
        return;
    }
    
    $result = $cart->addToCart($_SESSION['user_id'], $productId, $quantity);
    sendJsonResponse($result);
}

function handleUpdateCartItem($cart) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJsonResponse(['success' => false, 'message' => 'داده‌های ورودی نامعتبر'], 400);
    }
    
    $productId = $input['product_id'] ?? null;
    $quantity = $input['quantity'] ?? null;
    
    if (!$productId || $quantity === null) {
        sendJsonResponse(['success' => false, 'message' => 'شناسه محصول و تعداد ضروری است'], 400);
    }
    
    if ($quantity < 0) {
        sendJsonResponse(['success' => false, 'message' => 'تعداد نمی‌تواند منفی باشد'], 400);
    }
    
    if (!isset($_SESSION['user_id'])) {
        // Handle session cart update
        if ($quantity == 0) {
            $result = $cart->removeFromSessionCart($productId);
        } else {
            $result = $cart->updateSessionCartItem($productId, $quantity);
        }
        sendJsonResponse($result);
        return;
    }
    
    $result = $cart->updateCartItem($_SESSION['user_id'], $productId, $quantity);
    sendJsonResponse($result);
}

function handleRemoveFromCart($cart) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJsonResponse(['success' => false, 'message' => 'داده‌های ورودی نامعتبر'], 400);
    }
    
    $productId = $input['product_id'] ?? null;
    
    if (!$productId) {
        sendJsonResponse(['success' => false, 'message' => 'شناسه محصول ضروری است'], 400);
    }
    
    if (!isset($_SESSION['user_id'])) {
        $result = $cart->removeFromSessionCart($productId);
        sendJsonResponse($result);
        return;
    }
    
    $result = $cart->removeFromCart($_SESSION['user_id'], $productId);
    sendJsonResponse($result);
}

function handleClearCart($cart) {
    if (!isset($_SESSION['user_id'])) {
        unset($_SESSION['cart']);
        sendJsonResponse(['success' => true, 'message' => 'سبد خرید خالی شد']);
        return;
    }
    
    $result = $cart->clearCart($_SESSION['user_id']);
    sendJsonResponse($result);
}

function handleCheckout($cart) {
    requireLogin();
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJsonResponse(['success' => false, 'message' => 'داده‌های ورودی نامعتبر'], 400);
    }
    
    $shippingAddress = $input['shipping_address'] ?? '';
    $paymentMethod = $input['payment_method'] ?? 'cash';
    
    if (empty($shippingAddress)) {
        sendJsonResponse(['success' => false, 'message' => 'آدرس ارسال ضروری است'], 400);
    }
    
    // Rate limiting for checkout
    if (!rateLimit('checkout_' . $_SESSION['user_id'], 5, 3600)) {
        sendJsonResponse(['success' => false, 'message' => 'تعداد درخواست‌های تسویه حساب بیش از حد مجاز'], 429);
    }
    
    $result = $cart->processCheckout($_SESSION['user_id'], $shippingAddress, $paymentMethod);
    sendJsonResponse($result);
}
?>

