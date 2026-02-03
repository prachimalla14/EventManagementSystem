<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

requireLogin();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) die("<p style='color:red;'>Event not found. <a href='index.php'>Back</a></p>");

if ($_SESSION['user_id'] != $event['user_id'] && $_SESSION['role'] !== 'admin') {
    die("<p style='color:red;'>Unauthorized access.</p>");
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("<p style='color:red;'>CSRF validation failed!</p>");
    }

    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $organizer = trim($_POST['organizer'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $max_participants = $_POST['max_participants'] ?? 50;

    if (!$title || !$category || !$organizer || !$event_date) {
        $error = "All fields are required.";
    }

    if (!$error) {
        $stmt = $pdo->prepare("UPDATE events SET title=?, category=?, organizer=?, event_date=?, max_participants=? WHERE id=?");
        if ($stmt->execute([$title, $category, $organizer, $event_date, $max_participants, $id])) {
            $success = "Event updated successfully!";
            // Refresh event data
            $stmt = $pdo->prepare("SELECT * FROM events WHERE id=?");
            $stmt->execute([$id]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = "Database error. Please try again.";
        }
    }
}

include "../includes/header.php";
?>

<div class="form-container">
    <h2>Edit Event</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?= e($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green;"><?= e($success) ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

        <label>Title:</label>
        <input type="text" name="title" value="<?= e($event['title']) ?>" required>

        <label>Category:</label>
        <input type="text" name="category" value="<?= e($event['category']) ?>" required>

        <label>Organizer:</label>
        <input type="text" name="organizer" value="<?= e($event['organizer']) ?>" required>

        <label>Date:</label>
        <input type="date" name="event_date" value="<?= e($event['event_date']) ?>" required>

        <label>Max Participants:</label>
        <input type="number" name="max_participants" value="<?= e($event['max_participants'] ?? 50) ?>" min="1" required>

        <input type="submit" value="Update Event">
    </form>

    <div style="margin-top: 15px;">
        <a href="index.php">
            <button type="button" style="
                background:#c2f0c2;
                color:#4b3e4d;
                border:none;
                padding:10px 15px;
                border-radius:8px;
                cursor:pointer;">
                &larr; Back to Events
            </button>
        </a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
