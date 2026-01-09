<?php
/**
 * Kost Controller
 */

require_once __DIR__ . '/../models/Kost.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class KostController {
    
    /**
     * Get all kost with pagination and filtering
     */
    public static function index($params = []) {
        try {
            $result = Kost::getAll($params);
            Response::success($result);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * Get kost by ID
     */
    public static function show($id) {
        try {
            $kost = Kost::getById($id);
            
            if (!$kost) {
                Response::notFound('Kost not found');
            }
            
            Response::success($kost);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * Create new kost (Admin only)
     */
    public static function store($data) {
        AuthController::requireAdmin();
        
        $validator = new Validator($data);
        $validator
            ->required('nama')
            ->required('jarak_kampus')
            ->numeric('jarak_kampus')
            ->min('jarak_kampus', 0)
            ->required('jarak_market')
            ->numeric('jarak_market')
            ->min('jarak_market', 0)
            ->required('harga')
            ->numeric('harga')
            ->min('harga', 1)
            ->required('kebersihan')
            ->between('kebersihan', 1, 5)
            ->required('keamanan')
            ->between('keamanan', 1, 5)
            ->required('fasilitas')
            ->between('fasilitas', 1, 5);
        
        if (!$validator->isValid()) {
            Response::validationError($validator->getErrors());
        }
        
        try {
            $kostId = Kost::create($data);
            $kost = Kost::getById($kostId);
            
            Response::success($kost, 'Kost created successfully', 201);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * Update kost (Admin only)
     */
    public static function update($id, $data) {
        AuthController::requireAdmin();
        
        $kost = Kost::getById($id);
        if (!$kost) {
            Response::notFound('Kost not found');
        }
        
        $validator = new Validator($data);
        
        if (isset($data['jarak_kampus'])) {
            $validator->numeric('jarak_kampus')->min('jarak_kampus', 0);
        }
        if (isset($data['jarak_market'])) {
            $validator->numeric('jarak_market')->min('jarak_market', 0);
        }
        if (isset($data['harga'])) {
            $validator->numeric('harga')->min('harga', 1);
        }
        if (isset($data['kebersihan'])) {
            $validator->between('kebersihan', 1, 5);
        }
        if (isset($data['keamanan'])) {
            $validator->between('keamanan', 1, 5);
        }
        if (isset($data['fasilitas'])) {
            $validator->between('fasilitas', 1, 5);
        }
        
        if (!$validator->isValid()) {
            Response::validationError($validator->getErrors());
        }
        
        try {
            Kost::update($id, $data);
            $kost = Kost::getById($id);
            
            Response::success($kost, 'Kost updated successfully');
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * Delete kost (Admin only)
     */
    public static function destroy($id) {
        AuthController::requireAdmin();
        
        $kost = Kost::getById($id);
        if (!$kost) {
            Response::notFound('Kost not found');
        }
        
        try {
            Kost::delete($id);
            Response::success(null, 'Kost deleted successfully');
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
    
    /**
     * Get kost statistics (Admin)
     */
    public static function stats() {
        AuthController::requireAdmin();
        
        try {
            $stats = Kost::getStats();
            Response::success($stats);
        } catch (Exception $e) {
            Response::serverError($e->getMessage());
        }
    }
}
