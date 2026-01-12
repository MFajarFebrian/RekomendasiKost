<?php
/**
 * Kampus Model
 * Handles campus/university data
 */

require_once __DIR__ . '/../utils/Database.php';

class Kampus {
    
    /**
     * Get all active campuses
     */
    public static function getAll() {
        return dbFetchAll(
            "SELECT * FROM kampus WHERE is_active = 1 ORDER BY kota, nama"
        );
    }
    
    /**
     * Get campus by ID
     */
    public static function getById($id) {
        return dbFetch("SELECT * FROM kampus WHERE id = ?", [$id]);
    }
    
    /**
     * Get campuses grouped by city
     */
    public static function getGroupedByCity() {
        $campuses = self::getAll();
        $grouped = [];
        
        foreach ($campuses as $campus) {
            $city = $campus['kota'] ?? 'Lainnya';
            if (!isset($grouped[$city])) {
                $grouped[$city] = [];
            }
            $grouped[$city][] = $campus;
        }
        
        return $grouped;
    }
    
    /**
     * Create new campus
     */
    public static function create($data) {
        dbQuery(
            "INSERT INTO kampus (nama, kode, alamat, kota, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)",
            [
                $data['nama'],
                $data['kode'] ?? null,
                $data['alamat'] ?? null,
                $data['kota'] ?? null,
                $data['latitude'] ?? null,
                $data['longitude'] ?? null
            ]
        );
        return dbLastId();
    }
    
    /**
     * Update campus
     */
    public static function update($id, $data) {
        $fields = [];
        $values = [];
        
        $allowed = ['nama', 'kode', 'alamat', 'kota', 'latitude', 'longitude', 'is_active'];
        
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        
        if (empty($fields)) return false;
        
        $values[] = $id;
        return dbQuery("UPDATE kampus SET " . implode(', ', $fields) . " WHERE id = ?", $values);
    }
    
    /**
     * Count total campuses
     */
    public static function count() {
        $result = dbFetch("SELECT COUNT(*) as count FROM kampus WHERE is_active = 1");
        return $result['count'];
    }
}
