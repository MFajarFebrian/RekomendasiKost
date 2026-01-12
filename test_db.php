<?php
// Display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Vercel PHP Debug</h1>";

echo "<h2>Environment Variables</h2>";
$vars = ['DB_CONNECTION', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PORT'];
foreach ($vars as $var) {
    echo "$var: " . (getenv($var) ?: '(not set)') . "<br>";
}
echo "DB_PASSWORD: " . (getenv('DB_PASSWORD') ? '******' : '(not set)') . "<br>";

echo "<h2>extensions</h2>";
echo "PDO drivers: " . implode(', ', PDO::getAvailableDrivers()) . "<br>";

echo "<h2>Database Connection Test</h2>";
require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "Connection successful!<br>";
    
    echo "<h3>Query Test</h3>";
    $stmt = $db->query("SELECT count(*) as count FROM kampus");
    $result = $stmt->fetch();
    echo "Count from 'kampus' table: " . $result['count'];
    
} catch (Exception $e) {
    echo "Connection Failed: " . $e->getMessage();
}
