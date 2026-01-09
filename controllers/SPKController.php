<?php
/**
 * SPK Controller
 * Handles AHP and TOPSIS endpoints
 */

require_once __DIR__ . '/../services/AHPService.php';
require_once __DIR__ . '/../services/TOPSISService.php';
require_once __DIR__ . '/../models/Kost.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class SPKController {
    
    // ==================== AHP Endpoints ====================
    
    /**
     * Get current AHP weights
     */
    public static function getWeights() {
        try {
            $ahp = new AHPService();
            $weights = $ahp->getWeights();
            
            Response::success([
                'weights' => $weights,
                'last_updated' => date('Y-m-d\TH:i:s\Z')
            ]);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * Configure AHP weights (Admin only)
     */
    public static function configureAHP($data) {
        AuthController::requireAdmin();
        
        if (!isset($data['pairwise_matrix'])) {
            Response::validationError(['pairwise_matrix' => 'Pairwise matrix is required']);
        }
        
        try {
            $ahp = new AHPService();
            $matrix = $ahp->parseMatrix($data['pairwise_matrix']);
            $result = $ahp->calculateWeights($matrix);
            
            if (!$result['is_consistent']) {
                Response::error(
                    'INCONSISTENT_MATRIX',
                    'Matrix is not consistent. Please revise your comparisons.',
                    ['consistency_ratio' => $result['consistency_ratio']],
                    400
                );
            }
            
            Response::success([
                'weights' => $result['weights'],
                'lambda_max' => $result['lambda_max'],
                'consistency_index' => $result['consistency_index'],
                'consistency_ratio' => $result['consistency_ratio'],
                'is_consistent' => $result['is_consistent']
            ], 'AHP weights configured successfully');
        } catch (Exception $e) {
            Response::error('CALCULATION_ERROR', $e->getMessage(), null, 500);
        }
    }
    
    /**
     * Get AHP calculation details
     */
    public static function getAHPDetails() {
        try {
            $ahp = new AHPService();
            
            $pairwiseMatrix = $ahp->getPairwiseMatrix();
            $normalizedCriteria = $ahp->getNormalizedCriteria();
            $weights = $ahp->getWeights();
            
            Response::success([
                'pairwise_matrix' => $pairwiseMatrix,
                'normalized_matrix' => $normalizedCriteria,
                'weights' => $weights
            ]);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * Calculate TOPSIS recommendations
     * Accepts direct weights from slider UI or uses saved AHP weights
     */
    public static function calculateTOPSIS($data) {
        try {
            $topsis = new TOPSISService();
            
            // Get kampus_id filter
            $kampusId = $data['kampus_id'] ?? null;
            
            // Get alternatives for specific campus or all
            $alternatives = Kost::getAllActive($kampusId);
            
            // Apply additional filters if provided
            if (!empty($data['filters'])) {
                $alternatives = array_filter($alternatives, function($k) use ($data) {
                    $f = $data['filters'];
                    if (isset($f['max_harga']) && $k['harga'] > $f['max_harga']) return false;
                    if (isset($f['max_jarak_kampus']) && $k['jarak_kampus'] > $f['max_jarak_kampus']) return false;
                    return true;
                });
                $alternatives = array_values($alternatives);
            }
            
            // Use direct weights from sliders if provided, normalize to sum=1
            $weights = null;
            if (!empty($data['weights'])) {
                $w = $data['weights'];
                $total = array_sum($w);
                if ($total > 0) {
                    $weights = [
                        'jarak_kampus' => ($w['jarak_kampus'] ?? 50) / $total,
                        'jarak_market' => ($w['jarak_market'] ?? 30) / $total,
                        'harga' => ($w['harga'] ?? 80) / $total,
                        'kebersihan' => ($w['kebersihan'] ?? 50) / $total,
                        'keamanan' => ($w['keamanan'] ?? 50) / $total,
                        'fasilitas' => ($w['fasilitas'] ?? 50) / $total
                    ];
                }
            }
            
            $result = $topsis->calculateRanking($alternatives, $weights);
            
            // Apply limit if specified
            $limit = $data['limit'] ?? 10;
            $result['recommendations'] = array_slice($result['recommendations'], 0, $limit);
            
            Response::success($result);
        } catch (Exception $e) {
            Response::error('CALCULATION_ERROR', $e->getMessage(), null, 500);
        }
    }
    
    /**
     * Get TOPSIS calculation details for specific kost
     */
    public static function getTOPSISDetails($kostId) {
        try {
            $topsis = new TOPSISService();
            $details = $topsis->getDetails($kostId);
            
            Response::success($details);
        } catch (Exception $e) {
            Response::error('CALCULATION_ERROR', $e->getMessage(), null, 500);
        }
    }
    
    /**
     * Get existing TOPSIS results
     */
    public static function getResults() {
        try {
            $topsis = new TOPSISService();
            $results = $topsis->getResults();
            
            // Add ranking
            $ranked = [];
            foreach ($results as $i => $result) {
                $result['rank'] = $i + 1;
                $ranked[] = $result;
            }
            
            Response::success(['recommendations' => $ranked]);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
}
