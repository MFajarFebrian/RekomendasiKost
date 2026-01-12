<?php
/**
 * Application Constants
 * Sistem Rekomendasi Kost
 */

// Kriteria names
define('CRITERIA', [
    'jarak_kampus' => 'Jarak Kampus',
    'jarak_market' => 'Jarak Market',
    'harga' => 'Harga',
    'kebersihan' => 'Kebersihan',
    'keamanan' => 'Keamanan',
    'fasilitas' => 'Fasilitas'
]);

// Kriteria types (cost = lower is better, benefit = higher is better)
define('COST_CRITERIA', ['jarak_kampus', 'jarak_market', 'harga']);
define('BENEFIT_CRITERIA', ['kebersihan', 'keamanan', 'fasilitas']);

// AHP Saaty Scale
define('AHP_SCALE', [
    1 => 'Equal importance',
    2 => 'Weak or slight',
    3 => 'Moderate importance',
    4 => 'Moderate plus',
    5 => 'Strong importance',
    6 => 'Strong plus',
    7 => 'Very strong',
    8 => 'Very, very strong',
    9 => 'Extreme importance'
]);

// Random Index for Consistency Ratio (n = 1 to 10)
define('RANDOM_INDEX', [
    1 => 0,
    2 => 0,
    3 => 0.58,
    4 => 0.90,
    5 => 1.12,
    6 => 1.24,
    7 => 1.32,
    8 => 1.41,
    9 => 1.45,
    10 => 1.49
]);

// Pagination defaults
define('DEFAULT_PAGE_SIZE', 10);
define('MAX_PAGE_SIZE', 100);

// Session configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes

// API Response codes
define('ERROR_CODES', [
    'VALIDATION_ERROR' => 400,
    'AUTH_ERROR' => 401,
    'PERMISSION_ERROR' => 403,
    'NOT_FOUND' => 404,
    'CONFLICT' => 409,
    'INCONSISTENT_MATRIX' => 400,
    'DATABASE_ERROR' => 500,
    'CALCULATION_ERROR' => 500,
    'SERVER_ERROR' => 500
]);
