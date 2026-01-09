<?php
/**
 * Response Utility Class
 * Standardized JSON responses for API
 */

class Response {
    /**
     * Send success response
     */
    public static function success($data = null, $message = null, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $response = ['success' => true];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        echo json_encode($response);
        exit;
    }
    
    /**
     * Send error response
     */
    public static function error($code, $message, $details = null, $statusCode = 400) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        
        $error = [
            'code' => $code,
            'message' => $message
        ];
        
        if ($details !== null) {
            $error['details'] = $details;
        }
        
        echo json_encode([
            'success' => false,
            'error' => $error
        ]);
        exit;
    }
    
    /**
     * Validation error helper
     */
    public static function validationError($details) {
        self::error('VALIDATION_ERROR', 'Invalid input data', $details, 400);
    }
    
    /**
     * Auth error helper
     */
    public static function authError($message = 'Authentication failed') {
        self::error('AUTH_ERROR', $message, null, 401);
    }
    
    /**
     * Permission error helper
     */
    public static function permissionError($message = 'Insufficient permissions') {
        self::error('PERMISSION_ERROR', $message, null, 403);
    }
    
    /**
     * Not found error helper
     */
    public static function notFound($message = 'Resource not found') {
        self::error('NOT_FOUND', $message, null, 404);
    }
    
    /**
     * Server error helper
     */
    public static function serverError($message = 'Internal server error') {
        self::error('SERVER_ERROR', $message, null, 500);
    }
}
