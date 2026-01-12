<?php
/**
 * TOPSIS Service
 * Technique for Order of Preference by Similarity to Ideal Solution
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../models/SPK.php';
require_once __DIR__ . '/../models/Kost.php';

class TOPSISService {
    
    private $criteria = ['jarak_kampus', 'jarak_market', 'harga', 'kebersihan', 'keamanan', 'fasilitas'];
    private $costCriteria = ['jarak_kampus', 'jarak_market', 'harga'];
    private $benefitCriteria = ['kebersihan', 'keamanan', 'fasilitas'];
    
    /**
     * Calculate TOPSIS ranking
     */
    public function calculateRanking($alternatives = null, $weights = null) {
        // Get alternatives if not provided
        if ($alternatives === null) {
            $alternatives = Kost::getAllActive();
        }
        
        if (empty($alternatives)) {
            throw new Exception("No alternatives available for calculation");
        }
        
        // Get weights if not provided
        if ($weights === null) {
            $weights = SPK::getWeights();
        }
        
        $startTime = microtime(true);
        
        // Step 1: Normalization
        $normalized = $this->normalize($alternatives);
        
        // Step 2: Apply weights
        $weighted = $this->applyWeights($normalized, $weights);
        
        // Step 3: Get ideal solutions
        $idealSolutions = $this->getIdealSolutions($weighted);
        
        // Step 4: Calculate distances
        $distances = $this->calculateDistances($weighted, $idealSolutions);
        
        // Step 5: Calculate preference values
        $preferenceValues = $this->calculatePreferenceValues($distances);
        
        // Step 6: Prepare and save results
        $results = $this->prepareResults($alternatives, $normalized, $weighted, $distances, $preferenceValues, $idealSolutions);
        
        $executionTime = (microtime(true) - $startTime) * 1000;
        
        return [
            'recommendations' => $results,
            'calculation_metadata' => [
                'total_alternatives' => count($alternatives),
                'weights_used' => $weights,
                'calculated_at' => date('Y-m-d\TH:i:s\Z'),
                'execution_time_ms' => round($executionTime, 2)
            ],
            'ideal_solutions' => $idealSolutions
        ];
    }
    
    /**
     * Step 1: Normalize decision matrix
     * r_ij = x_ij / sqrt(sum(x_ij^2))
     */
    private function normalize($alternatives) {
        $sqrtSums = [];
        
        // Calculate sqrt of sum of squares for each criterion
        foreach ($this->criteria as $criterion) {
            $sumSquares = 0;
            foreach ($alternatives as $alt) {
                $sumSquares += pow($alt[$criterion], 2);
            }
            $sqrtSums[$criterion] = sqrt($sumSquares);
        }
        
        // Normalize each value
        $normalized = [];
        foreach ($alternatives as $alt) {
            $normAlt = ['nama' => $alt['nama']];
            foreach ($this->criteria as $criterion) {
                $normAlt[$criterion] = $alt[$criterion] / $sqrtSums[$criterion];
            }
            $normalized[] = $normAlt;
        }
        
        // Save to database
        SPK::saveNormalisasi($normalized);
        
        return $normalized;
    }
    
    /**
     * Step 2: Apply weights to normalized matrix
     * y_ij = r_ij * w_j
     */
    private function applyWeights($normalized, $weights) {
        $weighted = [];
        
        foreach ($normalized as $alt) {
            $weightedAlt = ['nama' => $alt['nama']];
            foreach ($this->criteria as $criterion) {
                $weightedAlt[$criterion] = $alt[$criterion] * $weights[$criterion];
            }
            $weighted[] = $weightedAlt;
        }
        
        return $weighted;
    }
    
    /**
     * Step 3: Determine ideal positive and negative solutions
     * A+ (positive): max for benefit, min for cost
     * A- (negative): min for benefit, max for cost
     */
    private function getIdealSolutions($weighted) {
        $idealPositive = [];
        $idealNegative = [];
        
        foreach ($this->criteria as $criterion) {
            $values = array_column($weighted, $criterion);
            
            if (in_array($criterion, $this->costCriteria)) {
                // Cost criteria: lower is better
                $idealPositive[$criterion] = min($values);
                $idealNegative[$criterion] = max($values);
            } else {
                // Benefit criteria: higher is better
                $idealPositive[$criterion] = max($values);
                $idealNegative[$criterion] = min($values);
            }
        }
        
        return [
            'positive' => $idealPositive,
            'negative' => $idealNegative
        ];
    }
    
    /**
     * Step 4: Calculate distances to ideal solutions
     * D+ = sqrt(sum((y_ij - A+_j)^2))
     * D- = sqrt(sum((y_ij - A-_j)^2))
     */
    private function calculateDistances($weighted, $idealSolutions) {
        $dPositive = [];
        $dNegative = [];
        
        foreach ($weighted as $alt) {
            $dPlus = 0;
            $dMinus = 0;
            
            foreach ($this->criteria as $criterion) {
                $dPlus += pow($alt[$criterion] - $idealSolutions['positive'][$criterion], 2);
                $dMinus += pow($alt[$criterion] - $idealSolutions['negative'][$criterion], 2);
            }
            
            $dPositive[] = ['nama' => $alt['nama'], 'dPositif' => sqrt($dPlus)];
            $dNegative[] = ['nama' => $alt['nama'], 'dNegatif' => sqrt($dMinus)];
        }
        
        // Save to database
        SPK::saveDPositif($dPositive);
        SPK::saveDNegatif($dNegative);
        
        return [
            'positive' => $dPositive,
            'negative' => $dNegative
        ];
    }
    
    /**
     * Step 5: Calculate preference values
     * V = D- / (D+ + D-)
     */
    private function calculatePreferenceValues($distances) {
        $preferenceValues = [];
        
        for ($i = 0; $i < count($distances['positive']); $i++) {
            $dPlus = $distances['positive'][$i]['dPositif'];
            $dMinus = $distances['negative'][$i]['dNegatif'];
            
            $preferenceValues[] = [
                'nama' => $distances['positive'][$i]['nama'],
                'val' => $dMinus / ($dPlus + $dMinus)
            ];
        }
        
        // Sort by preference value descending
        usort($preferenceValues, function($a, $b) {
            return $b['val'] <=> $a['val'];
        });
        
        // Save to database
        SPK::saveNilaiPref($preferenceValues);
        
        return $preferenceValues;
    }
    
    /**
     * Prepare final results with ranking
     */
    private function prepareResults($alternatives, $normalized, $weighted, $distances, $preferenceValues, $idealSolutions) {
        $results = [];
        
        foreach ($preferenceValues as $rank => $pv) {
            $nama = $pv['nama'];
            
            // Find corresponding data
            $altIndex = array_search($nama, array_column($alternatives, 'nama'));
            $normIndex = array_search($nama, array_column($normalized, 'nama'));
            $distPosIndex = array_search($nama, array_column($distances['positive'], 'nama'));
            $distNegIndex = array_search($nama, array_column($distances['negative'], 'nama'));
            
            $alt = $alternatives[$altIndex];
            $norm = $normalized[$normIndex];
            $weight = $weighted[$normIndex];
            
            $results[] = [
                'rank' => $rank + 1,
                'kost_id' => $alt['id'],
                'nama' => $nama,
                'score' => round($pv['val'], 6),
                'd_positive' => round($distances['positive'][$distPosIndex]['dPositif'], 6),
                'd_negative' => round($distances['negative'][$distNegIndex]['dNegatif'], 6),
                'details' => [
                    'jarak_kampus' => $alt['jarak_kampus'],
                    'jarak_market' => $alt['jarak_market'],
                    'harga' => $alt['harga'],
                    'kebersihan' => $alt['kebersihan'],
                    'keamanan' => $alt['keamanan'],
                    'fasilitas' => $alt['fasilitas']
                ],
                'normalized_values' => [
                    'jarak_kampus' => round($norm['jarak_kampus'], 6),
                    'jarak_market' => round($norm['jarak_market'], 6),
                    'harga' => round($norm['harga'], 6),
                    'kebersihan' => round($norm['kebersihan'], 6),
                    'keamanan' => round($norm['keamanan'], 6),
                    'fasilitas' => round($norm['fasilitas'], 6)
                ],
                'weighted_values' => [
                    'jarak_kampus' => round($weight['jarak_kampus'], 6),
                    'jarak_market' => round($weight['jarak_market'], 6),
                    'harga' => round($weight['harga'], 6),
                    'kebersihan' => round($weight['kebersihan'], 6),
                    'keamanan' => round($weight['keamanan'], 6),
                    'fasilitas' => round($weight['fasilitas'], 6)
                ]
            ];
        }
        
        return $results;
    }
    
    /**
     * Get calculation details for specific kost
     */
    public function getDetails($kostId) {
        $kost = Kost::getById($kostId);
        
        if (!$kost) {
            throw new Exception("Kost not found");
        }
        
        $normalized = SPK::getNormalisasi();
        $dPositive = SPK::getDPositif();
        $dNegative = SPK::getDNegatif();
        $preferences = SPK::getNilaiPref();
        $weights = SPK::getWeights();
        
        // Find by name
        $nama = $kost['nama'];
        
        $normData = null;
        foreach ($normalized as $n) {
            if ($n['nama'] === $nama) {
                $normData = $n;
                break;
            }
        }
        
        $dPosData = null;
        foreach ($dPositive as $dp) {
            if ($dp['nama'] === $nama) {
                $dPosData = $dp;
                break;
            }
        }
        
        $dNegData = null;
        foreach ($dNegative as $dn) {
            if ($dn['nama'] === $nama) {
                $dNegData = $dn;
                break;
            }
        }
        
        $prefData = null;
        $rank = 0;
        foreach ($preferences as $i => $p) {
            if ($p['nama'] === $nama) {
                $prefData = $p;
                $rank = $i + 1;
                break;
            }
        }
        
        if (!$normData || !$dPosData || !$dNegData || !$prefData) {
            throw new Exception("Calculation data not found. Please run TOPSIS calculation first.");
        }
        
        return [
            'kost_id' => $kost['id'],
            'kost_nama' => $nama,
            'original_values' => [
                'jarak_kampus' => $kost['jarak_kampus'],
                'jarak_market' => $kost['jarak_market'],
                'harga' => $kost['harga'],
                'kebersihan' => $kost['kebersihan'],
                'keamanan' => $kost['keamanan'],
                'fasilitas' => $kost['fasilitas']
            ],
            'normalized_values' => [
                'jarak_kampus' => $normData['jarak_kampus'],
                'jarak_market' => $normData['jarak_market'],
                'harga' => $normData['harga'],
                'kebersihan' => $normData['kebersihan'],
                'keamanan' => $normData['keamanan'],
                'fasilitas' => $normData['fasilitas']
            ],
            'weighted_normalized_values' => [
                'jarak_kampus' => $normData['jarak_kampus'] * $weights['jarak_kampus'],
                'jarak_market' => $normData['jarak_market'] * $weights['jarak_market'],
                'harga' => $normData['harga'] * $weights['harga'],
                'kebersihan' => $normData['kebersihan'] * $weights['kebersihan'],
                'keamanan' => $normData['keamanan'] * $weights['keamanan'],
                'fasilitas' => $normData['fasilitas'] * $weights['fasilitas']
            ],
            'd_positive' => $dPosData['dPositif'],
            'd_negative' => $dNegData['dNegatif'],
            'preference_value' => $prefData['val'],
            'rank' => $rank
        ];
    }
    
    /**
     * Get existing results from database
     */
    public function getResults() {
        return SPK::getResults();
    }
}
