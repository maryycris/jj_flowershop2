<?php
/**
 * Router script for PHP built-in server
 * Serves static files directly, routes everything else to Laravel
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Handle storage files - check if symlink exists and file is accessible
if (strpos($uri, '/storage/') === 0) {
    $filePath = __DIR__ . $uri;
    // Check if file exists (following symlinks)
    if (file_exists($filePath) && is_file($filePath)) {
        // Let PHP server serve the file directly
        return false;
    }
    // If file doesn't exist, try to find it in backend storage
    $storagePath = str_replace('/storage/', '', $uri);
    $backendStoragePath = __DIR__ . '/../backend/storage/app/public/' . $storagePath;
    if (file_exists($backendStoragePath) && is_file($backendStoragePath)) {
        // Serve the file with correct MIME type
        $mimeType = mime_content_type($backendStoragePath) ?: 'application/octet-stream';
        header('Content-Type: ' . $mimeType);
        readfile($backendStoragePath);
        exit;
    }
}

// If the file exists in the public directory, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . $uri) && is_file(__DIR__ . $uri)) {
    return false; // Let PHP server handle it
}

// Otherwise, route to Laravel
require __DIR__ . '/index.php';

