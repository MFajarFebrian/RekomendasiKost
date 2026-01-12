<?php
/**
 * SPK Model
 * Handles data access for AHP/TOPSIS tables
 */

require_once __DIR__ . '/../utils/Database.php';

class SPK {
    
    // ==================== AHP Methods ====================
    
    /**
     * Get pairwise comparison matrix
     */
    public static function getPairwiseMatrix() {
        return dbFetchAll("SELECT * FROM temp_bobot ORDER BY id");
    }
    
    /**
     * Save pairwise matrix
     */
    public static function savePairwiseMatrix($matrix) {
        // Clear existing data
        dbQuery("TRUNCATE TABLE temp_bobot");
        
        $criteria = ['Jarak Kampus', 'Jarak Market', 'Harga', 'Kebersihan', 'Keamanan', 'Fasilitas'];
        
        foreach ($criteria as $i => $kriteria) {
            dbQuery(
                "INSERT INTO temp_bobot (kriteria, jarak_kampus, jarak_market, harga, kebersihan, keamanan, fasilitas) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $kriteria,
                    $matrix[$i][0],
                    $matrix[$i][1],
                    $matrix[$i][2],
                    $matrix[$i][3],
                    $matrix[$i][4],
                    $matrix[$i][5]
                ]
            );
        }
        
        return true;
    }
    
    /**
     * Get normalized criteria weights
     */
    public static function getNormalizedCriteria() {
        return dbFetchAll("SELECT * FROM temp_normalisasi_kriteria ORDER BY id");
    }
    
    /**
     * Save normalized criteria and weights
     */
    public static function saveNormalizedCriteria($normalized, $weights, $matrixAW) {
        // Clear existing data
        dbQuery("TRUNCATE TABLE temp_normalisasi_kriteria");
        
        $criteria = ['Jarak Kampus', 'Jarak Market', 'Harga', 'Kebersihan', 'Keamanan', 'Fasilitas'];
        $criteriaKeys = ['jarak_kampus', 'jarak_market', 'harga', 'kebersihan', 'keamanan', 'fasilitas'];
        
        foreach ($criteria as $i => $kriteria) {
            dbQuery(
                "INSERT INTO temp_normalisasi_kriteria 
                 (kriteria, jarak_kampus, jarak_market, harga, kebersihan, keamanan, fasilitas, avg, matrix_aw) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $kriteria,
                    $normalized[$i][0],
                    $normalized[$i][1],
                    $normalized[$i][2],
                    $normalized[$i][3],
                    $normalized[$i][4],
                    $normalized[$i][5],
                    $weights[$criteriaKeys[$i]],
                    $matrixAW[$i]
                ]
            );
        }
        
        return true;
    }
    
    /**
     * Get current weights
     */
    public static function getWeights() {
        $data = self::getNormalizedCriteria();
        
        if (empty($data)) {
            // Return default weights
            return [
                'jarak_kampus' => 0.1,
                'jarak_market' => 0.05,
                'harga' => 0.4,
                'kebersihan' => 0.1,
                'keamanan' => 0.15,
                'fasilitas' => 0.2
            ];
        }
        
        $weights = [];
        $criteriaKeys = ['jarak_kampus', 'jarak_market', 'harga', 'kebersihan', 'keamanan', 'fasilitas'];
        
        foreach ($data as $i => $row) {
            $weights[$criteriaKeys[$i]] = $row['avg'];
        }
        
        return $weights;
    }
    
    // ==================== TOPSIS Methods ====================
    
    /**
     * Get normalized matrix
     */
    public static function getNormalisasi() {
        return dbFetchAll("SELECT * FROM temp_normalisasi ORDER BY id");
    }
    
    /**
     * Save normalization results
     */
    public static function saveNormalisasi($data) {
        // Clear existing data
        dbQuery("TRUNCATE TABLE temp_normalisasi");
        
        foreach ($data as $row) {
            dbQuery(
                "INSERT INTO temp_normalisasi (nama, jarak_kampus, jarak_market, harga, kebersihan, keamanan, fasilitas) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $row['nama'],
                    $row['jarak_kampus'],
                    $row['jarak_market'],
                    $row['harga'],
                    $row['kebersihan'],
                    $row['keamanan'],
                    $row['fasilitas']
                ]
            );
        }
        
        return true;
    }
    
    /**
     * Get D+ values
     */
    public static function getDPositif() {
        return dbFetchAll("SELECT * FROM temp_d_pos ORDER BY id");
    }
    
    /**
     * Save D+ values
     */
    public static function saveDPositif($data) {
        dbQuery("TRUNCATE TABLE temp_d_pos");
        
        foreach ($data as $row) {
            dbQuery(
                "INSERT INTO temp_d_pos (nama, dPositif) VALUES (?, ?)",
                [$row['nama'], $row['dPositif']]
            );
        }
        
        return true;
    }
    
    /**
     * Get D- values
     */
    public static function getDNegatif() {
        return dbFetchAll("SELECT * FROM temp_d_neg ORDER BY id");
    }
    
    /**
     * Save D- values
     */
    public static function saveDNegatif($data) {
        dbQuery("TRUNCATE TABLE temp_d_neg");
        
        foreach ($data as $row) {
            dbQuery(
                "INSERT INTO temp_d_neg (nama, dNegatif) VALUES (?, ?)",
                [$row['nama'], $row['dNegatif']]
            );
        }
        
        return true;
    }
    
    /**
     * Get preference values
     */
    public static function getNilaiPref() {
        return dbFetchAll("SELECT * FROM temp_nilai_pref ORDER BY val DESC");
    }
    
    /**
     * Save preference values
     */
    public static function saveNilaiPref($data) {
        dbQuery("TRUNCATE TABLE temp_nilai_pref");
        
        foreach ($data as $row) {
            dbQuery(
                "INSERT INTO temp_nilai_pref (nama, val) VALUES (?, ?)",
                [$row['nama'], $row['val']]
            );
        }
        
        return true;
    }
    
    /**
     * Get complete TOPSIS results with ranking
     */
    public static function getResults() {
        return dbFetchAll(
            "SELECT 
                p.nama, p.val as score,
                dp.dPositif as d_positive,
                dn.dNegatif as d_negative,
                k.id as kost_id, k.jarak_kampus, k.jarak_market, k.harga, 
                k.kebersihan, k.keamanan, k.fasilitas
             FROM temp_nilai_pref p
             LEFT JOIN temp_d_pos dp ON p.nama = dp.nama
             LEFT JOIN temp_d_neg dn ON p.nama = dn.nama
             LEFT JOIN kost k ON p.nama = k.nama
             ORDER BY p.val DESC"
        );
    }
}
