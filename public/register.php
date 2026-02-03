<?php
session_start(); // Start session
require_once "../config/db.php"; // DB connection
require_once "../includes/functions.php"; // Helpers

requireLogin(); // Login required

// Logged-in user details
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'] ?? $_SESSION['user_name'] . "@example.com";

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<p style='color:red;'>Invalid request method. <a href='index.php'>Back to Events</a></p>");
}

// CSRF validation
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    die("<p style='color:red;'>Invalid CSRF token. <a href='index.php'>Back to Events</a></p>");
}

// Get event ID
$event_id = $_POST['event_id'] ?? null;
if (!$event_id) {
    die("<p style='color:red;'>Invalid Event ID. <a href='index.php'>Back to Events</a></p>");
}

// Fetch event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("<p style='color:red;'>Event not found. <a href='index.php'>Back to Events</a></p>");
}

// Prevent owner from registering
if ($event['user_id'] == $user_id) {
    die("<p style='color:red;'>You cannot register for your own event. <a href='index.php'>Back to Events</a></p>");
}

// Check capacity
$registered = participantCount($pdo, $event_id);
$max = $event['max_participants'] ?? 50;
if ($registered >= $max) {
    die("<p style='color:red;'>Event is full. <a href='index.php'>Back to Events</a></p>");
}

// Prevent duplicate registration
$stmt = $pdo->prepare("SELECT * FROM participants WHERE user_id = ? AND event_id = ?");
$stmt->execute([$user_id, $event_id]);
if ($stmt->rowCount() > 0) {
    die("<p style='color:red;'>You are already registered for this event. <a href='index.php'>Back</a></p>");
}

// Register participant
$stmt = $pdo->prepare(
    "INSERT INTO participants (user_id, event_id, name, email) VALUES (?, ?, ?, ?)"
);
$stmt->execute([$user_id, $event_id, $user_name, $user_email]);

include "../includes/header.php";
?>

<div class="form-container">
    <h2>Registration Successful</h2>
    <p style="color:green;">
        You have successfully registered for the event:
        <strong><?= e($event['title']) ?></strong>.<br>
        Event Date: <?= e($event['event_date']) ?><br>
        <a href="index.php">Back to Events</a>
    </p>
</div>

<?php include "../includes/footer.php"; ?>
