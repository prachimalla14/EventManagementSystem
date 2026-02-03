<?php
session_start(); // Session for auth & CSRF
require_once "../config/db.php"; // DB connection
require_once "../includes/functions.php"; // Helpers

requireLogin(); // Login required

// Redirect if event ID is missing
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Fetch event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) die("<p style='color:red;'>Event not found. <a href='index.php'>Back</a></p>");

// Owner or admin check
if ($_SESSION['user_id'] != $event['user_id'] && $_SESSION['role'] !== 'admin') {
    die("<p style='color:red;'>Unauthorized access.</p>");
}

// Generate CSRF token if missing
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF validation
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("<p style='color:red;'>CSRF validation failed!</p>");
    }

    // Get updated values
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $organizer = trim($_POST['organizer'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $max_participants = $_POST['max_participants'] ?? 50;

    // Basic validation
    if (!$title || !$category || !$organizer || !$event_date) {
        $error = "All fields are required.";
    }

    // Update event
    if (!$error) {
        $stmt = $pdo->prepare(
            "UPDATE events SET title=?, category=?, organizer=?, event_date=?, max_participants=? WHERE id=?"
        );

        if ($stmt->execute([$title, $category, $organizer, $event_date, $max_participants, $id])) {
            $success = "Event updated successfully!";

            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM events WHERE id=?");
            $stmt->execute([$id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = "Database error. Please try again.";
        }
    }
}

include "../includes/header.php"; // Header
?>
