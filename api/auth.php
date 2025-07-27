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
$user = new User($db);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'POST':
        switch ($action) {
            case 'register':
                handleRegister($user);
                break;
            case 'login':
                handleLogin($user);
                break;
            case 'logout':
                handleLogout($user);
                break;
            default:
                sendJsonResponse(['success' => false, 'message' => 'عملیات نامعتبر'], 400);
        }
        break;
    
    case 'GET':
        switch ($action) {
            case 'profile':
                handleGetProfile($user);
                break;
            case 'check':
                handleCheckAuth($user);
                break;
            default:
                sendJsonResponse(['success' => false, 'message' => 'عملیات نامعتبر'], 400);
        }
        break;
    
    case 'PUT':
        switch ($action) {
            case 'profile':
                handleUpdateProfile($user);
                break;
            case 'password':
                handleChangePassword($user);
                break;
            default:
                sendJsonResponse(['success' => false, 'message' => 'عملیات نامعتبر'], 400);
        }
        break;
    
    default:
        sendJsonResponse(['success' => false, 'message' => 'متد نامعتبر'], 405);
}

function handleRegister($user) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJsonResponse(['success' => false, 'message' => 'داده‌های ورودی نامعتبر'], 400);
    }
    
    $username = sanitizeInput($input['username'] ?? '');
    $email = sanitizeInput($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $firstName = sanitizeInput($input['first_name'] ?? '');
    $lastName = sanitizeInput($input['last_name'] ?? '');
    $phone = sanitizeInput($input['phone'] ?? '');
    $address = sanitizeInput($input['address'] ?? '');
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
        sendJsonResponse(['success' => false, 'message' => 'لطفا تمام فیلدهای ضروری را پر کنید'], 400);
    }
    
    if (!validateEmail($email)) {
        sendJsonResponse(['success' => false, 'message' => 'ایمیل معتبر نیست'], 400);
    }
    
    // Rate limiting
    if (!rateLimit('register_' . getClientIP(), 5, 3600)) {
        sendJsonResponse(['success' => false, 'message' => 'تعداد درخواست‌های ثبت نام بیش از حد مجاز'], 429);
    }
    
    $result = $user->register($username, $email, $password, $firstName, $lastName, $phone, $address);
    sendJsonResponse($result);
}

function handleLogin($user) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJsonResponse(['success' => false, 'message' => 'داده‌های ورودی نامعتبر'], 400);
    }
    
    $username = sanitizeInput($input['username'] ?? '');
    $password = $input['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        sendJsonResponse(['success' => false, 'message' => 'نام کاربری و رمز عبور ضروری است'], 400);
    }
    
    // Rate limiting
    if (!rateLimit('login_' . getClientIP(), 10, 3600)) {
        sendJsonResponse(['success' => false, 'message' => 'تعداد درخواست‌های ورود بیش از حد مجاز'], 429);
    }
    
    $result = $user->login($username, $password);
    
    if ($result['success']) {
        // Merge session cart if exists
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            $cart = new Cart(new Database());
            $cart->mergeSessionCartToUser($_SESSION['user_id']);
        }
    }
    
    sendJsonResponse($result);
}

function handleLogout($user) {
    $result = $user->logout();
    sendJsonResponse($result);
}

function handleGetProfile($user) {
    requireLogin();
    
    $currentUser = $user->getCurrentUser();
    if ($currentUser) {
        unset($currentUser['password_hash']); // Remove sensitive data
        sendJsonResponse(['success' => true, 'user' => $currentUser]);
    } else {
        sendJsonResponse(['success' => false, 'message' => 'کاربر یافت نشد'], 404);
    }
}

function handleCheckAuth($user) {
    $isLoggedIn = $user->isLoggedIn();
    $isAdmin = $user->isAdmin();
    
    $response = [
        'success' => true,
        'logged_in' => $isLoggedIn,
        'is_admin' => $isAdmin
    ];
    
    if ($isLoggedIn) {
        $response['user'] = [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'name' => $_SESSION['user_name']
        ];
    }
    
    sendJsonResponse($response);
}

function handleUpdateProfile($user) {
    requireLogin();
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJsonResponse(['success' => false, 'message' => 'داده‌های ورودی نامعتبر'], 400);
    }
    
    $allowedFields = ['first_name', 'last_name', 'phone', 'address'];
    $updateData = [];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $updateData[$field] = sanitizeInput($input[$field]);
        }
    }
    
    if (empty($updateData)) {
        sendJsonResponse(['success' => false, 'message' => 'هیچ داده‌ای برای به‌روزرسانی ارسال نشده'], 400);
    }
    
    $result = $user->updateProfile($_SESSION['user_id'], $updateData);
    sendJsonResponse($result);
}

function handleChangePassword($user) {
    requireLogin();
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJsonResponse(['success' => false, 'message' => 'داده‌های ورودی نامعتبر'], 400);
    }
    
    $currentPassword = $input['current_password'] ?? '';
    $newPassword = $input['new_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword)) {
        sendJsonResponse(['success' => false, 'message' => 'رمز عبور فعلی و جدید ضروری است'], 400);
    }
    
    $result = $user->changePassword($_SESSION['user_id'], $currentPassword, $newPassword);
    sendJsonResponse($result);
}
?>

