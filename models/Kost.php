<?php
/**
 * Kost Model
 * Handles kost data operations
 */

require_once __DIR__ . '/../utils/Database.php';

class Kost {
    
    /**
     * Get all kost with pagination and filtering
     */
    public static function getAll($params = []) {
        $page = $params['page'] ?? 1;
        $limit = min($params['limit'] ?? 10, 100);
        $offset = ($page - 1) * $limit;
        
        $where = ['k.is_active = 1'];
        $values = [];
        
        // Campus filter
        if (!empty($params['kampus_id'])) {
            $where[] = 'k.kampus_id = ?';
            $values[] = $params['kampus_id'];
        }
        
        // Search filter
        if (!empty($params['search'])) {
            $where[] = 'k.nama LIKE ?';
            $values[] = '%' . $params['search'] . '%';
        }
        
        // Price filter
        if (!empty($params['min_harga'])) {
            $where[] = 'k.harga >= ?';
            $values[] = $params['min_harga'];
        }
        if (!empty($params['max_harga'])) {
            $where[] = 'k.harga <= ?';
            $values[] = $params['max_harga'];
        }
        
        // Distance filter
        if (!empty($params['max_jarak_kampus'])) {
            $where[] = 'k.jarak_kampus <= ?';
            $values[] = $params['max_jarak_kampus'];
        }
        
        // Rating filters
        if (!empty($params['min_kebersihan'])) {
            $where[] = 'k.kebersihan >= ?';
            $values[] = $params['min_kebersihan'];
        }
        if (!empty($params['min_keamanan'])) {
            $where[] = 'k.keamanan >= ?';
            $values[] = $params['min_keamanan'];
        }
        if (!empty($params['min_fasilitas'])) {
            $where[] = 'k.fasilitas >= ?';
            $values[] = $params['min_fasilitas'];
        }
        
        $whereClause = implode(' AND ', $where);
        
        // Sorting
        $sortBy = $params['sort_by'] ?? 'id';
        $sortOrder = strtoupper($params['sort_order'] ?? 'ASC');
        $sortOrder = in_array($sortOrder, ['ASC', 'DESC']) ? $sortOrder : 'ASC';
        
        $validSortFields = ['id', 'nama', 'harga', 'jarak_kampus', 'kebersihan', 'keamanan', 'fasilitas', 'created_at'];
        $sortBy = in_array($sortBy, $validSortFields) ? 'k.' . $sortBy : 'k.id';
        
        // Get items with kampus join
        $sql = "SELECT k.*, kp.nama as kampus_nama, kp.kode as kampus_kode 
                FROM kost k 
                LEFT JOIN kampus kp ON k.kampus_id = kp.id 
                WHERE $whereClause 
                ORDER BY $sortBy $sortOrder 
                LIMIT ? OFFSET ?";
        $values[] = $limit;
        $values[] = $offset;
        
        $items = dbFetchAll($sql, $values);
        

        
        // Get total count (optional)
        $total = 0;
        if (empty($params['skip_count'])) {
            $countValues = array_slice($values, 0, -2);
            $totalResult = dbFetch("SELECT COUNT(*) as count FROM kost k WHERE $whereClause", $countValues);
            $total = $totalResult['count'];
        }
        
        return [
            'items' => $items,
            'pagination' => [
                'current_page' => (int)$page,
                'total_pages' => $total > 0 ? ceil($total / $limit) : 0,
                'total_items' => (int)$total,
                'items_per_page' => (int)$limit,
                'has_next' => $total > 0 && $page * $limit < $total,
                'has_prev' => $page > 1
            ]
        ];
    }
    
    /**
     * Get kost by ID
     */
    public static function getById($id) {
        return dbFetch(
            "SELECT k.*, kp.nama as kampus_nama, kp.kode as kampus_kode 
             FROM kost k 
             LEFT JOIN kampus kp ON k.kampus_id = kp.id 
             WHERE k.id = ?", 
            [$id]
        );
    }
    
    /**
     * Get all active kost (for TOPSIS calculation)
     */
    public static function getAllActive($kampusId = null) {
        if ($kampusId) {
            return dbFetchAll("SELECT * FROM kost WHERE is_active = 1 AND kampus_id = ? ORDER BY id", [$kampusId]);
        }
        return dbFetchAll("SELECT * FROM kost WHERE is_active = 1 ORDER BY id");
    }
    
    /**
     * Create new kost
     */
    public static function create($data) {
        dbQuery(
            "INSERT INTO kost (nama, jarak_kampus, jarak_market, harga, kebersihan, keamanan, fasilitas, deskripsi, alamat, foto_utama) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['nama'],
                $data['jarak_kampus'],
                $data['jarak_market'],
                $data['harga'],
                $data['kebersihan'],
                $data['keamanan'],
                $data['fasilitas'],
                $data['deskripsi'] ?? null,
                $data['alamat'] ?? null,
                $data['foto_utama'] ?? null
            ]
        );
        
        return dbLastId();
    }
    
    /**
     * Update kost
     */
    public static function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowed = ['nama', 'jarak_kampus', 'jarak_market', 'harga', 'kebersihan', 'keamanan', 'fasilitas', 'deskripsi', 'alamat', 'foto_utama', 'is_active', 'kampus_id'];
        
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        
        return dbQuery(
            "UPDATE kost SET " . implode(', ', $fields) . " WHERE id = ?",
            $values
        );
    }
    
    /**
     * Delete kost (soft delete)
     */
    public static function delete($id) {
        return dbQuery("UPDATE kost SET is_active = 0 WHERE id = ?", [$id]);
    }
    
    /**
     * Hard delete kost
     */
    public static function hardDelete($id) {
        return dbQuery("DELETE FROM kost WHERE id = ?", [$id]);
    }
    
    /**
     * Get total count
     */
    public static function count($activeOnly = true) {
        $sql = $activeOnly ? "SELECT COUNT(*) as count FROM kost WHERE is_active = 1" : "SELECT COUNT(*) as count FROM kost";
        $result = dbFetch($sql);
        return $result['count'];
    }
    
    /**
     * Get statistics
     */
    public static function getStats() {
        $total = self::count();
        $avgPrice = dbFetch("SELECT AVG(harga) as avg FROM kost WHERE is_active = 1");
        $priceRange = dbFetch("SELECT MIN(harga) as min, MAX(harga) as max FROM kost WHERE is_active = 1");
        
        return [
            'total' => (int)$total,
            'avg_price' => round($avgPrice['avg'], 0),
            'min_price' => (int)$priceRange['min'],
            'max_price' => (int)$priceRange['max']
        ];
    }
}
