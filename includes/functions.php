<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Escape output
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
// Count participants in an event
function participantCount($pdo, $event_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM participants WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['total'];
}

// Check login
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Require login for pages
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Get current user role
function currentUserRole() {
    return $_SESSION['role'] ?? 'guest';
}

// Generate CSRF token
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
