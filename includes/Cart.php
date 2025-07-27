<?php
class Cart {
    private $db;
    private $product;
    
    public function __construct($database) {
        $this->db = $database;
        $this->product = new Product($database);
    }
    
    public function addToCart($userId, $productId, $quantity = 1) {
        // Check if product exists and has enough stock
        if (!$this->product->checkStock($productId, $quantity)) {
            return ['success' => false, 'message' => 'موجودی کافی نیست'];
        }
        
        // Check if item already exists in cart
        $existingItem = $this->getCartItem($userId, $productId);
        
        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            
            // Check stock for new quantity
            if (!$this->product->checkStock($productId, $newQuantity)) {
                return ['success' => false, 'message' => 'موجودی کافی نیست'];
            }
            
            $result = $this->updateCartItem($userId, $productId, $newQuantity);
        } else {
            // Add new item
            $data = [
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity
            ];
            
            $result = $this->db->insert('cart', $data);
        }
        
        if ($result) {
            return ['success' => true, 'message' => 'محصول به سبد خرید اضافه شد'];
        } else {
            return ['success' => false, 'message' => 'خطا در افزودن به سبد خرید'];
        }
    }
    
    public function updateCartItem($userId, $productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeFromCart($userId, $productId);
        }
        
        // Check stock
        if (!$this->product->checkStock($productId, $quantity)) {
            return ['success' => false, 'message' => 'موجودی کافی نیست'];
        }
        
        $result = $this->db->update(
            'cart',
            ['quantity' => $quantity],
            'user_id = :user_id AND product_id = :product_id',
            ['user_id' => $userId, 'product_id' => $productId]
        );
        
        if ($result) {
            return ['success' => true, 'message' => 'سبد خرید به‌روزرسانی شد'];
        } else {
            return ['success' => false, 'message' => 'خطا در به‌روزرسانی سبد خرید'];
        }
    }
    
    public function removeFromCart($userId, $productId) {
        $result = $this->db->delete(
            'cart',
            'user_id = :user_id AND product_id = :product_id',
            ['user_id' => $userId, 'product_id' => $productId]
        );
        
        if ($result) {
            return ['success' => true, 'message' => 'محصول از سبد خرید حذف شد'];
        } else {
            return ['success' => false, 'message' => 'خطا در حذف از سبد خرید'];
        }
    }
    
    public function getCartItems($userId) {
        $sql = "SELECT c.*, p.name_fa, p.price, p.sale_price, p.image_url, p.stock_quantity,
                       (CASE WHEN p.sale_price > 0 THEN p.sale_price ELSE p.price END) as current_price,
                       (CASE WHEN p.sale_price > 0 THEN p.sale_price ELSE p.price END) * c.quantity as total_price
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = :user_id AND p.is_active = 1
                ORDER BY c.added_at DESC";
        
        return $this->db->select($sql, ['user_id' => $userId]);
    }
    
    public function getCartItem($userId, $productId) {
        $sql = "SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id";
        return $this->db->selectOne($sql, ['user_id' => $userId, 'product_id' => $productId]);
    }
    
    public function getCartTotal($userId) {
        $sql = "SELECT SUM((CASE WHEN p.sale_price > 0 THEN p.sale_price ELSE p.price END) * c.quantity) as total
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = :user_id AND p.is_active = 1";
        
        $result = $this->db->selectOne($sql, ['user_id' => $userId]);
        return $result ? $result['total'] : 0;
    }
    
    public function getCartItemCount($userId) {
        $sql = "SELECT SUM(quantity) as count FROM cart WHERE user_id = :user_id";
        $result = $this->db->selectOne($sql, ['user_id' => $userId]);
        return $result ? $result['count'] : 0;
    }
    
    public function clearCart($userId) {
        $result = $this->db->delete('cart', 'user_id = :user_id', ['user_id' => $userId]);
        
        if ($result) {
            return ['success' => true, 'message' => 'سبد خرید خالی شد'];
        } else {
            return ['success' => false, 'message' => 'خطا در خالی کردن سبد خرید'];
        }
    }
    
    public function validateCart($userId) {
        $cartItems = $this->getCartItems($userId);
        $errors = [];
        
        foreach ($cartItems as $item) {
            if ($item['stock_quantity'] < $item['quantity']) {
                $errors[] = "موجودی محصول {$item['name_fa']} کافی نیست";
            }
        }
        
        return $errors;
    }
    
    public function processCheckout($userId, $shippingAddress, $paymentMethod = 'cash') {
        // Validate cart
        $errors = $this->validateCart($userId);
        if (!empty($errors)) {
            return ['success' => false, 'message' => implode(', ', $errors)];
        }
        
        $cartItems = $this->getCartItems($userId);
        if (empty($cartItems)) {
            return ['success' => false, 'message' => 'سبد خرید خالی است'];
        }
        
        $this->db->beginTransaction();
        
        try {
            // Create order
            $orderNumber = 'VT-' . date('Ymd') . '-' . strtoupper(uniqid());
            $totalAmount = $this->getCartTotal($userId);
            
            $orderData = [
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'shipping_address' => $shippingAddress,
                'payment_method' => $paymentMethod,
                'status' => 'pending'
            ];
            
            $orderId = $this->db->insert('orders', $orderData);
            
            if (!$orderId) {
                throw new Exception('خطا در ایجاد سفارش');
            }
            
            // Add order items and update stock
            foreach ($cartItems as $item) {
                $orderItemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['current_price'],
                    'total' => $item['total_price']
                ];
                
                $this->db->insert('order_items', $orderItemData);
                
                // Update product stock
                if (!$this->product->updateStock($item['product_id'], $item['quantity'])) {
                    throw new Exception('خطا در به‌روزرسانی موجودی');
                }
            }
            
            // Clear cart
            $this->clearCart($userId);
            
            $this->db->commit();
            
            return [
                'success' => true,
                'message' => 'سفارش با موفقیت ثبت شد',
                'order_id' => $orderId,
                'order_number' => $orderNumber
            ];
            
        } catch (Exception $e) {
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    // Session-based cart for non-logged-in users
    public function addToSessionCart($productId, $quantity = 1) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        
        return ['success' => true, 'message' => 'محصول به سبد خرید اضافه شد'];
    }
    
    public function getSessionCartItems() {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return [];
        }
        
        $productIds = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
        
        $sql = "SELECT id, name_fa, price, sale_price, image_url, stock_quantity
                FROM products 
                WHERE id IN ({$placeholders}) AND is_active = 1";
        
        $products = $this->db->select($sql, $productIds);
        
        $cartItems = [];
        foreach ($products as $product) {
            $quantity = $_SESSION['cart'][$product['id']];
            $currentPrice = $product['sale_price'] > 0 ? $product['sale_price'] : $product['price'];
            
            $cartItems[] = [
                'product_id' => $product['id'],
                'name_fa' => $product['name_fa'],
                'price' => $product['price'],
                'sale_price' => $product['sale_price'],
                'current_price' => $currentPrice,
                'image_url' => $product['image_url'],
                'stock_quantity' => $product['stock_quantity'],
                'quantity' => $quantity,
                'total_price' => $currentPrice * $quantity
            ];
        }
        
        return $cartItems;
    }
    
    public function mergeSessionCartToUser($userId) {
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            return;
        }
        
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $this->addToCart($userId, $productId, $quantity);
        }
        
        unset($_SESSION['cart']);
    }
}
?>

