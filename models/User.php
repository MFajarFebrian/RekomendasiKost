<?php
/**
 * User Model
 * Handles user authentication and management
 */

require_once __DIR__ . '/../utils/Database.php';

class User {
    
    /**
     * Find user by email
     */
    public static function findByEmail($email) {
        return dbFetch(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        );
    }
    
    /**
     * Find user by ID
     */
    public static function findById($id) {
        return dbFetch(
            "SELECT id, email, nama, telepon, role, foto_profil, is_active, created_at FROM users WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Authenticate user
     */
    public static function authenticate($email, $password) {
        $user = self::findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        
        if (!$user['is_active']) {
            return false;
        }
        
        // Remove password from result
        unset($user['password']);
        return $user;
    }
    
    /**
     * Create new user
     */
    public static function create($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        
        dbQuery(
            "INSERT INTO users (email, password, nama, telepon, role) VALUES (?, ?, ?, ?, ?)",
            [
                $data['email'],
                $hashedPassword,
                $data['nama'],
                $data['telepon'] ?? null,
                $data['role'] ?? 'user'
            ]
        );
        
        return dbLastId();
    }
    
    /**
     * Update user
     */
    public static function update($id, $data) {
        $fields = [];
        $values = [];
        
        if (isset($data['nama'])) {
            $fields[] = 'nama = ?';
            $values[] = $data['nama'];
        }
        
        if (isset($data['telepon'])) {
            $fields[] = 'telepon = ?';
            $values[] = $data['telepon'];
        }
        
        if (isset($data['foto_profil'])) {
            $fields[] = 'foto_profil = ?';
            $values[] = $data['foto_profil'];
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        
        return dbQuery(
            "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?",
            $values
        );
    }
    
    /**
     * Check if email exists
     */
    public static function emailExists($email) {
        $result = dbFetch(
            "SELECT COUNT(*) as count FROM users WHERE email = ?",
            [$email]
        );
        return $result['count'] > 0;
    }
    
    /**
     * Get all users (admin)
     */
    public static function getAll($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;
        
        return dbFetchAll(
            "SELECT id, email, nama, telepon, role, is_active, created_at 
             FROM users 
             ORDER BY created_at DESC 
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
    }
    
    /**
     * Count total users
     */
    public static function count() {
        $result = dbFetch("SELECT COUNT(*) as count FROM users");
        return $result['count'];
    }
}
