<?php

/**
 * Configuration Constants
 * Portfolio Management System
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('SITE_URL', 'http://localhost/Portfolio');
define('SITE_TITLE', 'Arka Braja Prasad Nath - Portfolio');
define('ADMIN_URL', SITE_URL . '/admin');

// File Upload Configuration
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_FILE_TYPES', ['pdf', 'doc', 'docx']);

// Security Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour
define('BCRYPT_COST', 12);
define('CSRF_TOKEN_LENGTH', 32);

// Email Configuration (if needed)
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'noreply@portfolio.com');
define('FROM_NAME', 'Portfolio Contact');

// Pagination
define('ITEMS_PER_PAGE', 10);

// Theme Configuration
define('DEFAULT_THEME', 'light');
define('THEME_COOKIE_NAME', 'portfolio_theme');
define('THEME_COOKIE_DURATION', 30 * 24 * 60 * 60); // 30 days

// Error Reporting (set to false in production)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Asia/Dhaka');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/database.php';

// Helper Functions
function sanitize($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function redirect($url)
{
    header("Location: $url");
    exit();
}

function isLoggedIn()
{
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        redirect(ADMIN_URL . '/login.php');
    }
}

function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function formatDate($date, $format = 'M Y')
{
    if (!$date) return 'Present';
    return date($format, strtotime($date));
}

function timeAgo($datetime)
{
    $time = time() - strtotime($datetime);

    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time / 60) . ' minutes ago';
    if ($time < 86400) return floor($time / 3600) . ' hours ago';
    if ($time < 2592000) return floor($time / 86400) . ' days ago';
    if ($time < 31536000) return floor($time / 2592000) . ' months ago';
    return floor($time / 31536000) . ' years ago';
}

function uploadFile($file, $allowedTypes = [], $uploadPath = null)
{
    if (!$uploadPath) {
        $uploadPath = UPLOAD_PATH;
    }

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }

    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmp = $file['tmp_name'];
    $fileError = $file['error'];

    if ($fileError !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }

    if ($fileSize > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size too large'];
    }

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!empty($allowedTypes) && !in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }

    $newFileName = uniqid() . '.' . $fileExt;
    $destination = $uploadPath . $newFileName;

    if (move_uploaded_file($fileTmp, $destination)) {
        return ['success' => true, 'filename' => $newFileName];
    }

    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

function deleteFile($filename, $path = null)
{
    if (!$path) {
        $path = UPLOAD_PATH;
    }

    $filePath = $path . $filename;
    if (file_exists($filePath)) {
        return unlink($filePath);
    }

    return false;
}

function getImageUrl($filename)
{
    if (empty($filename)) {
        return SITE_URL . '/assets/images/default-avatar.png';
    }
    return UPLOAD_URL . $filename;
}

function truncateText($text, $length = 150, $suffix = '...')
{
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

// Auto-load classes if needed
spl_autoload_register(function ($className) {
    $classFile = __DIR__ . '/../classes/' . $className . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

/**
 * Get PDO database connection
 * @return PDO
 */
function getDBConnection()
{
    global $db;
    if (!isset($db)) {
        $db = new Database();
    }
    return $db->getConnection();
}
