<?php
/**
 * Router script for PHP built-in server
 * Serves static files directly, routes everything else to Laravel
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the file exists in the public directory, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // Let PHP server handle it
}

// Otherwise, route to Laravel
require __DIR__ . '/index.php';

