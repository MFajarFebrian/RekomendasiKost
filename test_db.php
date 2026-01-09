<?php
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Connection successful!";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
