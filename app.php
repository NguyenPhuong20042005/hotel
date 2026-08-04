<?php
// Global Application Configuration & Helper Functions

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'Grand Vista Hotel Chain');
define('BASE_URL', '/');

/**
 * HTML Output Sanitization Helper
 */
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Currency Formatter
 */
function formatCurrency(float $amount): string {
    return '$' . number_format($amount, 2);
}

/**
 * JSON API Response Helper
 */
function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Get logged-in user or null
 */
function getAuthUser(): ?array {
    return $_SESSION['user'] ?? null;
}

/**
 * Check if user is admin
 */
function isAdmin(): bool {
    $user = getAuthUser();
    return $user && ($user['role'] ?? '') === 'admin';
}

/**
 * Set Flash Message
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get Flash Message
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
