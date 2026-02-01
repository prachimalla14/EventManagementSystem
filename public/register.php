<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

requireLogin();

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<p style='color:red;'>Invalid request method. <a href='index.php'>Back to Events</a></p>");
}

if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    die("<p style='color:red;'>Invalid CSRF token. <a href='index.php'>Back to Events</a></p>");
}

$event_id = $_POST['event_id'] ?? null;
if (!$event_id) die("<p style='color:red;'>Invalid Event ID. <a href='index.php'>Back to Events</a></p>");

$stmt = $pdo->prepare("SELECT * FROM events WHERE id=?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$event) die("<p style='color:red;'>Event not found. <a href='index.php'>Back</a></p>");

$registered = participantCount($pdo, $event_id);
if ($registered >= $event['max_participants']) {
    die("<p style='color:red;'>Event is full. <a href='index.php'>Back to Events</a></p>");
}

$stmt = $pdo->prepare("SELECT * FROM participants WHERE user_id=? AND event_id=?");
$stmt->execute([$user_id, $event_id]);
if ($stmt->rowCount() > 0) {
    die("<p style='color:red;'>Already registered. <a href='index.php'>Back</a></p>");
}

$stmt = $pdo->prepare("INSERT INTO participants (user_id, event_id, name, email) VALUES (?, ?, ?, ?)");
$stmt->execute([$user_id, $event_id, $_SESSION['user_name'], $event['organizer']]);

include "../includes/header.php";
?>
<div class="form-container">
    <p style="color:green;">Registered successfully! <a href="index.php">Back to Events</a></p>
</div>
<?php include "../includes/footer.php"; ?>
