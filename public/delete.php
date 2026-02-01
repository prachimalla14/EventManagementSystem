<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<p style='color:red;'>Invalid request method.</p>");
}

$id = $_POST['id'] ?? '';
if (!$id) die("<p style='color:red;'>Event ID missing.</p>");

if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("<p style='color:red;'>CSRF validation failed.</p>");
}

$stmt = $pdo->prepare("SELECT * FROM events WHERE id=?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) die("<p style='color:red;'>Event not found.</p>");

if ($_SESSION['user_id'] != $event['user_id'] && $_SESSION['role'] !== 'admin') {
    die("<p style='color:red;'>Unauthorized access.</p>");
}

$stmt = $pdo->prepare("DELETE FROM events WHERE id=?");
$stmt->execute([$id]);
header("Location: index.php");
exit;
