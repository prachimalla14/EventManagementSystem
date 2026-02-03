<?php
session_start(); // Session for auth & CSRF
require_once "../config/db.php"; // DB connection
require_once "../includes/functions.php"; // Helpers

requireLogin(); // Only loggedin users allowed

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<p style='color:red;'>Invalid request method.</p>");
}

// Get event ID
$id = $_POST['id'] ?? '';
if (!$id) die("<p style='color:red;'>Event ID missing.</p>");

// CSRF protection
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("<p style='color:red;'>CSRF validation failed.</p>");
}

// Fetch event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id=?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) die("<p style='color:red;'>Event not found.</p>");

// admin only
if ($_SESSION['user_id'] != $event['user_id'] && $_SESSION['role'] !== 'admin') {
    die("<p style='color:red;'>Unauthorized access.</p>");
}

// Delete event
$stmt = $pdo->prepare("DELETE FROM events WHERE id=?");
$stmt->execute([$id]);

header("Location: index.php"); // Redirect after delete
exit;
