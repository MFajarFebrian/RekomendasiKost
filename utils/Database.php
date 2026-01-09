<?php
/**
 * Database Utility Class
 * Alternative to config/database.php for API usage
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Get database connection
 */
function db() {
    return Database::getInstance()->getConnection();
}

/**
 * Execute query with parameters
 */
function dbQuery($sql, $params = []) {
    return Database::getInstance()->query($sql, $params);
}

/**
 * Fetch all results
 */
function dbFetchAll($sql, $params = []) {
    return Database::getInstance()->fetchAll($sql, $params);
}

/**
 * Fetch single result
 */
function dbFetch($sql, $params = []) {
    return Database::getInstance()->fetch($sql, $params);
}

/**
 * Get last insert ID
 */
function dbLastId() {
    return Database::getInstance()->lastInsertId();
}
