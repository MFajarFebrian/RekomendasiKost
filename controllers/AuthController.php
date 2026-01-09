<?php
/**
 * Authentication Controller
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';

class AuthController {
    
    /**
     * Login user
     */
    public static function login($data) {
        $validator = new Validator($data);
        $validator
            ->required('email')
            ->email('email')
            ->required('password');
        
        if (!$validator->isValid()) {
            Response::validationError($validator->getErrors());
        }
        
        $user = User::authenticate($data['email'], $data['password']);
        
        if (!$user) {
            Response::authError('Invalid email or password');
        }
        
        // Start session and store user data
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        
        Response::success([
            'user' => $user,
            'session_id' => session_id()
        ], 'Login successful');
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        session_start();
        
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        Response::success(null, 'Logged out successfully');
    }
    
    /**
     * Get current user
     */
    public static function me() {
        session_start();
        
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            Response::authError('Not authenticated');
        }
        
        $user = User::findById($_SESSION['user_id']);
        
        if (!$user) {
            Response::authError('User not found');
        }
        
        Response::success($user);
    }
    
    /**
     * Register new user
     */
    public static function register($data) {
        $validator = new Validator($data);
        $validator
            ->required('email')
            ->email('email')
            ->required('password')
            ->minLength('password', 8)
            ->required('nama');
        
        if (!$validator->isValid()) {
            Response::validationError($validator->getErrors());
        }
        
        // Check if email exists
        if (User::emailExists($data['email'])) {
            Response::error('CONFLICT', 'Email already registered', null, 409);
        }
        
        $userId = User::create([
            'email' => $data['email'],
            'password' => $data['password'],
            'nama' => $data['nama'],
            'telepon' => $data['telepon'] ?? null
        ]);
        
        Response::success([
            'user_id' => $userId,
            'email' => $data['email'],
            'nama' => $data['nama'],
            'role' => 'user'
        ], 'Registration successful', 201);
    }
    
    /**
     * Check if user is authenticated
     */
    public static function checkAuth() {
        session_start();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
    }
    
    /**
     * Check if user is admin
     */
    public static function checkAdmin() {
        session_start();
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * Require authentication
     */
    public static function requireAuth() {
        if (!self::checkAuth()) {
            Response::authError('Authentication required');
        }
    }
    
    /**
     * Require admin role
     */
    public static function requireAdmin() {
        self::requireAuth();
        if (!self::checkAdmin()) {
            Response::permissionError('Admin access required');
        }
    }
}
