<?php
class Product {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    // Category methods
    public function getAllCategories() {
        $sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order, name_fa";
        return $this->db->select($sql);
    }
    
    public function getCategoryById($id) {
        $sql = "SELECT * FROM categories WHERE id = :id";
        return $this->db->selectOne($sql, ['id' => $id]);
    }
    
    public function getCategoryBySlug($slug) {
        $sql = "SELECT * FROM categories WHERE slug = :slug AND is_active = 1";
        return $this->db->selectOne($sql, ['slug' => $slug]);
    }
    
    // Subcategory methods
    public function getSubcategoriesByCategory($categoryId) {
        $sql = "SELECT * FROM subcategories WHERE category_id = :category_id AND is_active = 1 ORDER BY sort_order, name_fa";
        return $this->db->select($sql, ['category_id' => $categoryId]);
    }
    
    public function getSubcategoryById($id) {
        $sql = "SELECT * FROM subcategories WHERE id = :id";
        return $this->db->selectOne($sql, ['id' => $id]);
    }
    
    public function getSubcategoryBySlug($slug) {
        $sql = "SELECT * FROM subcategories WHERE slug = :slug AND is_active = 1";
        return $this->db->selectOne($sql, ['slug' => $slug]);
    }
    
    // Product methods
    public function getAllProducts($page = 1, $limit = PRODUCTS_PER_PAGE, $categoryId = null, $subcategoryId = null, $search = '') {
        $offset = ($page - 1) * $limit;
        $where = "p.is_active = 1";
        $params = ['limit' => $limit, 'offset' => $offset];
        
        if ($categoryId) {
            $where .= " AND p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }
        
        if ($subcategoryId) {
            $where .= " AND p.subcategory_id = :subcategory_id";
            $params['subcategory_id'] = $subcategoryId;
        }
        
        if ($search) {
            $where .= " AND (p.name_fa LIKE :search OR p.description_fa LIKE :search OR p.sku LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }
        
        $sql = "SELECT p.*, c.name_fa as category_name, s.name_fa as subcategory_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN subcategories s ON p.subcategory_id = s.id 
                WHERE {$where} 
                ORDER BY p.is_featured DESC, p.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        return $this->db->select($sql, $params);
    }
    
    public function getProductById($id) {
        $sql = "SELECT p.*, c.name_fa as category_name, s.name_fa as subcategory_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN subcategories s ON p.subcategory_id = s.id 
                WHERE p.id = :id";
        return $this->db->selectOne($sql, ['id' => $id]);
    }
    
    public function getProductBySlug($slug) {
        $sql = "SELECT p.*, c.name_fa as category_name, s.name_fa as subcategory_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN subcategories s ON p.subcategory_id = s.id 
                WHERE p.slug = :slug AND p.is_active = 1";
        return $this->db->selectOne($sql, ['slug' => $slug]);
    }
    
    public function getFeaturedProducts($limit = 8) {
        $sql = "SELECT p.*, c.name_fa as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.is_featured = 1 AND p.is_active = 1 
                ORDER BY p.created_at DESC 
                LIMIT :limit";
        return $this->db->select($sql, ['limit' => $limit]);
    }
    
    public function getRelatedProducts($categoryId, $currentProductId, $limit = 4) {
        $sql = "SELECT p.*, c.name_fa as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = :category_id AND p.id != :current_id AND p.is_active = 1 
                ORDER BY RAND() 
                LIMIT :limit";
        return $this->db->select($sql, [
            'category_id' => $categoryId,
            'current_id' => $currentProductId,
            'limit' => $limit
        ]);
    }
    
    public function searchProducts($query, $page = 1, $limit = PRODUCTS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        $searchTerm = '%' . $query . '%';
        
        $sql = "SELECT p.*, c.name_fa as category_name, s.name_fa as subcategory_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN subcategories s ON p.subcategory_id = s.id 
                WHERE p.is_active = 1 AND (
                    p.name_fa LIKE :search OR 
                    p.description_fa LIKE :search OR 
                    p.short_description_fa LIKE :search OR 
                    p.sku LIKE :search OR
                    c.name_fa LIKE :search OR
                    s.name_fa LIKE :search
                ) 
                ORDER BY p.is_featured DESC, p.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        return $this->db->select($sql, [
            'search' => $searchTerm,
            'limit' => $limit,
            'offset' => $offset
        ]);
    }
    
    public function getTotalProducts($categoryId = null, $subcategoryId = null, $search = '') {
        $where = "is_active = 1";
        $params = [];
        
        if ($categoryId) {
            $where .= " AND category_id = :category_id";
            $params['category_id'] = $categoryId;
        }
        
        if ($subcategoryId) {
            $where .= " AND subcategory_id = :subcategory_id";
            $params['subcategory_id'] = $subcategoryId;
        }
        
        if ($search) {
            $where .= " AND (name_fa LIKE :search OR description_fa LIKE :search OR sku LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }
        
        return $this->db->count('products', $where, $params);
    }
    
    public function addProduct($data) {
        // Generate slug from name
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name_fa']);
        }
        
        // Generate SKU if not provided
        if (!isset($data['sku']) || empty($data['sku'])) {
            $data['sku'] = $this->generateSKU();
        }
        
        $productId = $this->db->insert('products', $data);
        
        if ($productId) {
            return ['success' => true, 'message' => 'محصول با موفقیت اضافه شد', 'product_id' => $productId];
        } else {
            return ['success' => false, 'message' => 'خطا در افزودن محصول'];
        }
    }
    
    public function updateProduct($id, $data) {
        $result = $this->db->update('products', $data, 'id = :id', ['id' => $id]);
        
        if ($result) {
            return ['success' => true, 'message' => 'محصول با موفقیت به‌روزرسانی شد'];
        } else {
            return ['success' => false, 'message' => 'خطا در به‌روزرسانی محصول'];
        }
    }
    
    public function deleteProduct($id) {
        $result = $this->db->delete('products', 'id = :id', ['id' => $id]);
        
        if ($result) {
            return ['success' => true, 'message' => 'محصول با موفقیت حذف شد'];
        } else {
            return ['success' => false, 'message' => 'خطا در حذف محصول'];
        }
    }
    
    public function updateStock($productId, $quantity) {
        $product = $this->getProductById($productId);
        if (!$product) {
            return false;
        }
        
        $newStock = $product['stock_quantity'] - $quantity;
        if ($newStock < 0) {
            return false; // Not enough stock
        }
        
        $result = $this->db->update('products', ['stock_quantity' => $newStock], 'id = :id', ['id' => $productId]);
        return $result > 0;
    }
    
    public function checkStock($productId, $quantity) {
        $product = $this->getProductById($productId);
        return $product && $product['stock_quantity'] >= $quantity;
    }
    
    private function generateSlug($name) {
        // Simple slug generation - in production, use a more robust solution
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        
        // Check if slug exists and make it unique
        $originalSlug = $slug;
        $counter = 1;
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function generateSKU() {
        return 'VT-' . strtoupper(uniqid());
    }
    
    private function slugExists($slug) {
        $sql = "SELECT id FROM products WHERE slug = :slug";
        $result = $this->db->selectOne($sql, ['slug' => $slug]);
        return $result !== false;
    }
    
    // Admin methods
    public function getAllProductsAdmin($page = 1, $limit = ADMIN_ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT p.*, c.name_fa as category_name, s.name_fa as subcategory_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN subcategories s ON p.subcategory_id = s.id 
                ORDER BY p.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        return $this->db->select($sql, ['limit' => $limit, 'offset' => $offset]);
    }
    
    public function getTotalProductsAdmin() {
        return $this->db->count('products');
    }
}
?>

