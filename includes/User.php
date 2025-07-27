<?php
class User {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function register($username, $email, $password, $firstName, $lastName, $phone = '', $address = '') {
        // Check if username or email already exists
        if ($this->userExists($username, $email)) {
            return ['success' => false, 'message' => 'نام کاربری یا ایمیل قبلاً ثبت شده است'];
        }
        
        // Validate input
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'رمز عبور باید حداقل ۶ کاراکتر باشد'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'ایمیل معتبر نیست'];
        }
        
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $data = [
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'address' => $address
        ];
        
        $userId = $this->db->insert('users', $data);
        
        if ($userId) {
            return ['success' => true, 'message' => 'ثبت نام با موفقیت انجام شد', 'user_id' => $userId];
        } else {
            return ['success' => false, 'message' => 'خطا در ثبت نام'];
        }
    }
    
    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = :username OR email = :username";
        $user = $this->db->selectOne($sql, ['username' => $username]);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Update last login
            $this->db->update('users', ['updated_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $user['id']]);
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            return ['success' => true, 'message' => 'ورود موفقیت آمیز', 'user' => $user];
        } else {
            return ['success' => false, 'message' => 'نام کاربری یا رمز عبور اشتباه است'];
        }
    }
    
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'خروج موفقیت آمیز'];
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }
    
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return $this->getUserById($_SESSION['user_id']);
        }
        return false;
    }
    
    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        return $this->db->selectOne($sql, ['id' => $id]);
    }
    
    public function updateProfile($userId, $data) {
        // Remove password from data if empty
        if (isset($data['password']) && empty($data['password'])) {
            unset($data['password']);
        } else if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        $result = $this->db->update('users', $data, 'id = :id', ['id' => $userId]);
        
        if ($result) {
            return ['success' => true, 'message' => 'پروفایل با موفقیت به‌روزرسانی شد'];
        } else {
            return ['success' => false, 'message' => 'خطا در به‌روزرسانی پروفایل'];
        }
    }
    
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->getUserById($userId);
        
        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'رمز عبور فعلی اشتباه است'];
        }
        
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'رمز عبور جدید باید حداقل ۶ کاراکتر باشد'];
        }
        
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $this->db->update('users', ['password_hash' => $newPasswordHash], 'id = :id', ['id' => $userId]);
        
        if ($result) {
            return ['success' => true, 'message' => 'رمز عبور با موفقیت تغییر کرد'];
        } else {
            return ['success' => false, 'message' => 'خطا در تغییر رمز عبور'];
        }
    }
    
    private function userExists($username, $email) {
        $sql = "SELECT id FROM users WHERE username = :username OR email = :email";
        $result = $this->db->selectOne($sql, ['username' => $username, 'email' => $email]);
        return $result !== false;
    }
    
    public function getAllUsers($page = 1, $limit = ADMIN_ITEMS_PER_PAGE) {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT id, username, email, first_name, last_name, phone, is_admin, created_at 
                FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        return $this->db->select($sql, ['limit' => $limit, 'offset' => $offset]);
    }
    
    public function getTotalUsers() {
        return $this->db->count('users');
    }
    
    public function deleteUser($userId) {
        // Don't allow deleting admin users
        $user = $this->getUserById($userId);
        if ($user && $user['is_admin']) {
            return ['success' => false, 'message' => 'نمی‌توان کاربر مدیر را حذف کرد'];
        }
        
        $result = $this->db->delete('users', 'id = :id', ['id' => $userId]);
        
        if ($result) {
            return ['success' => true, 'message' => 'کاربر با موفقیت حذف شد'];
        } else {
            return ['success' => false, 'message' => 'خطا در حذف کاربر'];
        }
    }
}
?>

