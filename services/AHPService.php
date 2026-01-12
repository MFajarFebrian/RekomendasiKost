<?php
/**
 * AHP Service
 * Analytical Hierarchy Process calculations
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../models/SPK.php';

class AHPService {
    
    private $criteria = ['jarak_kampus', 'jarak_market', 'harga', 'kebersihan', 'keamanan', 'fasilitas'];
    private $n = 6; // Number of criteria
    
    /**
     * Calculate weights from pairwise comparison matrix
     */
    public function calculateWeights($pairwiseMatrix) {
        $n = $this->n;
        
        // Step 1: Calculate column sums
        $columnSums = array_fill(0, $n, 0);
        for ($j = 0; $j < $n; $j++) {
            for ($i = 0; $i < $n; $i++) {
                $columnSums[$j] += $pairwiseMatrix[$i][$j];
            }
        }
        
        // Step 2: Normalize matrix
        $normalized = [];
        for ($i = 0; $i < $n; $i++) {
            $normalized[$i] = [];
            for ($j = 0; $j < $n; $j++) {
                $normalized[$i][$j] = $pairwiseMatrix[$i][$j] / $columnSums[$j];
            }
        }
        
        // Step 3: Calculate weights (row averages)
        $weights = [];
        for ($i = 0; $i < $n; $i++) {
            $weights[$this->criteria[$i]] = array_sum($normalized[$i]) / $n;
        }
        
        // Step 4: Calculate lambda max
        $matrixAW = [];
        for ($i = 0; $i < $n; $i++) {
            $aw = 0;
            for ($j = 0; $j < $n; $j++) {
                $aw += $pairwiseMatrix[$i][$j] * $weights[$this->criteria[$j]];
            }
            $matrixAW[$i] = $aw;
        }
        
        $lambdaMax = 0;
        for ($i = 0; $i < $n; $i++) {
            $lambdaMax += $matrixAW[$i] / $weights[$this->criteria[$i]];
        }
        $lambdaMax /= $n;
        
        // Step 5: Calculate Consistency Index (CI)
        $CI = ($lambdaMax - $n) / ($n - 1);
        
        // Step 6: Calculate Consistency Ratio (CR)
        $RI = RANDOM_INDEX[$n];
        $CR = $CI / $RI;
        
        // Save to database
        SPK::savePairwiseMatrix($pairwiseMatrix);
        SPK::saveNormalizedCriteria($normalized, $weights, $matrixAW);
        
        return [
            'weights' => $weights,
            'normalized' => $normalized,
            'lambda_max' => $lambdaMax,
            'consistency_index' => $CI,
            'consistency_ratio' => $CR,
            'random_index' => $RI,
            'is_consistent' => $CR < 0.1
        ];
    }
    
    /**
     * Get current weights
     */
    public function getWeights() {
        return SPK::getWeights();
    }
    
    /**
     * Get pairwise matrix
     */
    public function getPairwiseMatrix() {
        return SPK::getPairwiseMatrix();
    }
    
    /**
     * Get normalized criteria
     */
    public function getNormalizedCriteria() {
        return SPK::getNormalizedCriteria();
    }
    
    /**
     * Convert input matrix to 2D array
     */
    public function parseMatrix($input) {
        $matrix = [];
        $criteriaKeys = ['jarak_kampus', 'jarak_market', 'harga', 'kebersihan', 'keamanan', 'fasilitas'];
        $criteriaNames = ['Jarak Kampus', 'Jarak Market', 'Harga', 'Kebersihan', 'Keamanan', 'Fasilitas'];
        
        foreach ($criteriaNames as $i => $name) {
            if (isset($input[$name])) {
                $matrix[$i] = array_values($input[$name]);
            } else {
                throw new Exception("Missing criteria: $name");
            }
        }
        
        return $matrix;
    }
    
    /**
     * Generate default consistent matrix
     */
    public function getDefaultMatrix() {
        return [
            [1, 2, 0.25, 1, 0.6667, 0.5],
            [0.5, 1, 0.125, 0.5, 0.3333, 0.25],
            [4, 8, 1, 4, 2.6667, 2],
            [1, 2, 0.25, 1, 0.6667, 0.5],
            [1.5, 3, 0.375, 1.5, 1, 0.75],
            [2, 4, 0.5, 2, 1.3333, 1]
        ];
    }
}
