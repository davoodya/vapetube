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
$product = new Product($db);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        switch ($action) {
            case 'categories':
                handleGetCategories($product);
                break;
            case 'featured':
                handleGetFeaturedProducts($product);
                break;
            case 'all':
                handleGetAllProducts($product);
                break;
            case 'search':
                handleSearchProducts($product);
                break;
            case 'category':
                handleGetProductsByCategory($product);
                break;
            case 'single':
                handleGetSingleProduct($product);
                break;
            default:
                sendJsonResponse(['success' => false, 'message' => 'عملیات نامعتبر'], 400);
        }
        break;
    
    case 'POST':
        requireAdmin();
        switch ($action) {
            case 'add':
                handleAddProduct($product);
                break;
            default:
                sendJsonResponse(['success' => false, 'message' => 'عملیات نامعتبر'], 400);
        }
        break;
    
    case 'PUT':
        requireAdmin();
        switch ($action) {
            case 'update':
                handleUpdateProduct($product);
                break;
            default:
                sendJsonResponse(['success' => false, 'message' => 'عملیات نامعتبر'], 400);
        }
        break;
    
    case 'DELETE':
        requireAdmin();
        switch ($action) {
            case 'delete':
                handleDeleteProduct($product);
                break;
            default:
                sendJsonResponse(['success' => false, 'message' => 'عملیات نامعتبر'], 400);
        }
        break;
    
    default:
        sendJsonResponse(['success' => false, 'message' => 'متد نامعتبر'], 405);
}

function handleGetCategories($product) {
    $categories = $product->getAllCategories();
    
    // Get subcategories for each category
    foreach ($categories as &$category) {
        $category['subcategories'] = $product->getSubcategoriesByCategory($category['id']);
    }
    
    sendJsonResponse(['success' => true, 'categories' => $categories]);
}

function handleGetFeaturedProducts($product) {
    $limit = $_GET['limit'] ?? 8;
    $products = $product->getFeaturedProducts($limit);
    
    sendJsonResponse(['success' => true, 'products' => $products]);
}

function handleGetAllProducts($product) {
    $page = $_GET['page'] ?? 1;
    $categoryId = $_GET['category_id'] ?? null;
    $subcategoryId = $_GET['subcategory_id'] ?? null;
    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? '';
    
    // Apply sorting
    $orderBy = 'p.created_at DESC';
    switch ($sort) {
        case 'price_low':
            $orderBy = '(CASE WHEN p.sale_price > 0 THEN p.sale_price ELSE p.price END) ASC';
            break;
        case 'price_high':
            $orderBy = '(CASE WHEN p.sale_price > 0 THEN p.sale_price ELSE p.price END) DESC';
            break;
        case 'newest':
        default:
            $orderBy = 'p.created_at DESC';
            break;
    }
    
    $products = $product->getAllProducts($page, PRODUCTS_PER_PAGE, $categoryId, $subcategoryId, $search);
    $totalProducts = $product->getTotalProducts($categoryId, $subcategoryId, $search);
    
    $pagination = getPagination($page, $totalProducts, PRODUCTS_PER_PAGE);
    
    sendJsonResponse([
        'success' => true,
        'products' => $products,
        'pagination' => $pagination
    ]);
}

function handleSearchProducts($product) {
    $query = $_GET['q'] ?? '';
    $page = $_GET['page'] ?? 1;
    
    if (empty($query)) {
        sendJsonResponse(['success' => false, 'message' => 'کلمه جستجو ضروری است'], 400);
    }
    
    $products = $product->searchProducts($query, $page);
    $totalProducts = $product->getTotalProducts(null, null, $query);
    
    $pagination = getPagination($page, $totalProducts, PRODUCTS_PER_PAGE);
    
    sendJsonResponse([
        'success' => true,
        'products' => $products,
        'pagination' => $pagination,
        'query' => $query
    ]);
}

function handleGetProductsByCategory($product) {
    $categoryId = $_GET['category_id'] ?? null;
    $subcategoryId = $_GET['subcategory_id'] ?? null;
    $page = $_GET['page'] ?? 1;
    
    if (!$categoryId) {
        sendJsonResponse(['success' => false, 'message' => 'شناسه دسته‌بندی ضروری است'], 400);
    }
    
    $products = $product->getAllProducts($page, PRODUCTS_PER_PAGE, $categoryId, $subcategoryId);
    $totalProducts = $product->getTotalProducts($categoryId, $subcategoryId);
    
    $pagination = getPagination($page, $totalProducts, PRODUCTS_PER_PAGE);
    
    sendJsonResponse([
        'success' => true,
        'products' => $products,
        'pagination' => $pagination
    ]);
}

function handleGetSingleProduct($product) {
    $productId = $_GET['id'] ?? null;
    $slug = $_GET['slug'] ?? null;
    
    if (!$productId && !$slug) {
        sendJsonResponse(['success' => false, 'message' => 'شناسه یا نامک محصول ضروری است'], 400);
    }
    
    if ($productId) {
        $productData = $product->getProductById($productId);
    } else {
        $productData = $product->getProductBySlug($slug);
    }
    
    if (!$productData) {
        sendJsonResponse(['success' => false, 'message' => 'محصول یافت نشد'], 404);
    }
    
    // Get related products
    $relatedProducts = $product->getRelatedProducts($productData['category_id'], $productData['id']);
    
    sendJsonResponse([
        'success' => true,
        'product' => $productData,
        'related_products' => $relatedProducts
    ]);
}

function handleAddProduct($product) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJsonResponse(['success' => false, 'message' => 'داده‌های ورودی نامعتبر'], 400);
    }
    
    // Validate required fields
    $requiredFields = ['name_fa', 'category_id', 'price', 'stock_quantity'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || empty($input[$field])) {
            sendJsonResponse(['success' => false, 'message' => "فیلد {$field} ضروری است"], 400);
        }
    }
    
    // Sanitize input
    $data = [];
    $allowedFields = [
        'name', 'name_fa', 'category_id', 'subcategory_id', 'description', 'description_fa',
        'short_description', 'short_description_fa', 'price', 'sale_price', 'sku',
        'stock_quantity', 'weight', 'dimensions', 'image_url', 'is_featured', 'is_active'
    ];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $data[$field] = sanitizeInput($input[$field]);
        }
    }
    
    $result = $product->addProduct($data);
    sendJsonResponse($result);
}

function handleUpdateProduct($product) {
    $productId = $_GET['id'] ?? null;
    
    if (!$productId) {
        sendJsonResponse(['success' => false, 'message' => 'شناسه محصول ضروری است'], 400);
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJsonResponse(['success' => false, 'message' => 'داده‌های ورودی نامعتبر'], 400);
    }
    
    // Sanitize input
    $data = [];
    $allowedFields = [
        'name', 'name_fa', 'category_id', 'subcategory_id', 'description', 'description_fa',
        'short_description', 'short_description_fa', 'price', 'sale_price', 'sku',
        'stock_quantity', 'weight', 'dimensions', 'image_url', 'is_featured', 'is_active'
    ];
    
    foreach ($allowedFields as $field) {
        if (isset($input[$field])) {
            $data[$field] = sanitizeInput($input[$field]);
        }
    }
    
    if (empty($data)) {
        sendJsonResponse(['success' => false, 'message' => 'هیچ داده‌ای برای به‌روزرسانی ارسال نشده'], 400);
    }
    
    $result = $product->updateProduct($productId, $data);
    sendJsonResponse($result);
}

function handleDeleteProduct($product) {
    $productId = $_GET['id'] ?? null;
    
    if (!$productId) {
        sendJsonResponse(['success' => false, 'message' => 'شناسه محصول ضروری است'], 400);
    }
    
    $result = $product->deleteProduct($productId);
    sendJsonResponse($result);
}
?>

