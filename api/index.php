<?php
/**
 * API Router
 * Main entry point for all API requests
 */

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Error handling
set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => [
            'code' => 'SERVER_ERROR',
            'message' => $e->getMessage()
        ]
    ]);
    exit;
});

// Get request info
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Parse URI - handle base path (local: /RekomendasiKost/api, Vercel: /api)
$path = parse_url($uri, PHP_URL_PATH);
$basePath = getenv('API_BASE_PATH') ?: '/RekomendasiKost/api';
$path = str_replace($basePath, '', $path);
$path = trim($path, '/');

// Get request body for POST/PUT
$input = json_decode(file_get_contents('php://input'), true) ?? [];

// Route the request
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/KostController.php';
require_once __DIR__ . '/../controllers/SPKController.php';
require_once __DIR__ . '/../models/Kampus.php';

// Route patterns
$routes = [
    // Authentication
    'POST auth/login' => fn() => AuthController::login($input),
    'POST auth/logout' => fn() => AuthController::logout(),
    'POST auth/register' => fn() => AuthController::register($input),
    'GET auth/me' => fn() => AuthController::me(),
    
    // Kampus
    'GET kampus' => fn() => Response::success(Kampus::getAll()),
    
    // Kost
    'GET kost' => fn() => KostController::index($_GET),
    'GET kost/stats' => fn() => KostController::stats(),
    'POST kost' => fn() => KostController::store($input),
    
    // SPK - AHP
    'GET spk/ahp/weights' => fn() => SPKController::getWeights(),
    'POST spk/ahp/configure' => fn() => SPKController::configureAHP($input),
    'GET spk/ahp/details' => fn() => SPKController::getAHPDetails(),
    
    // SPK - TOPSIS (accepts direct weights from sliders)
    'POST spk/topsis/calculate' => fn() => SPKController::calculateTOPSIS($input),
    'GET spk/topsis/results' => fn() => SPKController::getResults(),
];

// Check static routes first
$routeKey = "$method $path";
if (isset($routes[$routeKey])) {
    $routes[$routeKey]();
    exit;
}

// Check dynamic routes
$pathParts = explode('/', $path);

// GET/PUT/DELETE kost/{id}
if (count($pathParts) === 2 && $pathParts[0] === 'kost' && is_numeric($pathParts[1])) {
    $id = (int)$pathParts[1];
    switch ($method) {
        case 'GET':
            KostController::show($id);
            break;
        case 'PUT':
            KostController::update($id, $input);
            break;
        case 'DELETE':
            KostController::destroy($id);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => ['code' => 'METHOD_NOT_ALLOWED', 'message' => 'Method not allowed']]);
    }
    exit;
}

// GET spk/topsis/details/{id}
if (count($pathParts) === 4 && $pathParts[0] === 'spk' && $pathParts[1] === 'topsis' && $pathParts[2] === 'details' && is_numeric($pathParts[3])) {
    if ($method === 'GET') {
        SPKController::getTOPSISDetails((int)$pathParts[3]);
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => ['code' => 'METHOD_NOT_ALLOWED', 'message' => 'Method not allowed']]);
    }
    exit;
}

// 404 Not Found
http_response_code(404);
echo json_encode([
    'success' => false,
    'error' => [
        'code' => 'NOT_FOUND',
        'message' => 'Endpoint not found',
        'debug' => [
            'method' => $method,
            'path' => $path
        ]
    ]
]);
