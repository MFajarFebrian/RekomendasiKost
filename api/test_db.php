<?php
// Display errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Vercel PHP Debug (API Folder)</h1>";

echo "<h2>Environment Variables</h2>";
$vars = ['DB_CONNECTION', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PORT'];
foreach ($vars as $var) {
    echo "$var: " . (getenv($var) ?: '(not set)') . "<br>";
}
$host = getenv('DB_HOST');
$dns_a = dns_get_record($host, DNS_A);
echo "DNS_A Records: <pre>" . print_r($dns_a, true) . "</pre>";
echo "Resolved IP: " . ($dns_a[0]['ip'] ?? 'Failed to resolve') . "<br>";
echo "DB_PASSWORD: " . (getenv('DB_PASSWORD') ? '******' : '(not set)') . "<br>";

echo "<h2>extensions</h2>";
echo "PDO drivers: " . implode(', ', PDO::getAvailableDrivers()) . "<br>";

echo "<h2>Database Connection Test</h2>";
// Note: Adjusted path for api folder
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "Connection successful!<br>";
    echo "<h3>Query Test</h3>";
    $stmt = $db->query("SELECT count(*) as count FROM kampus");
    $result = $stmt->fetch();
    echo "Count from 'kampus' table: " . $result['count'] . "<br>";

    echo "<h3>Model Loading Test</h3>";
    require_once __DIR__ . '/../models/Kampus.php';
    echo "Kampus model loaded.<br>";
    $kampusList = Kampus::getAll();
    echo "Kampus::getAll() returned " . count($kampusList) . " items.<br>";
    echo "First item name: " . ($kampusList[0]['nama'] ?? 'N/A') . "<br>";

    echo "<h3>Kost Model Test (Join Query)</h3>";
    require_once __DIR__ . '/../models/Kost.php';
    $kostList = Kost::getAll(['limit' => 5]);
    echo "Kost::getAll() returned " . count($kostList['items']) . " items.<br>";
    if (count($kostList['items']) > 0) {
        echo "First kost name: " . ($kostList['items'][0]['nama'] ?? 'N/A') . "<br>";
        echo "Kampus name (joined): " . ($kostList['items'][0]['kampus_nama'] ?? 'N/A') . "<br>";
    } else {
        echo "No kost data found.<br>";
    }
    
} catch (Exception $e) {
    echo "Connection Failed: " . $e->getMessage();
}
