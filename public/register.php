<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

if (!isset($_SESSION['user_id'])) {
    die("You must <a href='login.php'>login</a> to register for events.");
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) die("User not found.");

$event_id = $_GET['event_id'] ?? null;
if (!$event_id) die("Invalid Event ID");

$stmt = $pdo->prepare("SELECT * FROM participants WHERE event_id = ? AND user_id = ?");
$stmt->execute([$event_id, $user_id]);
if ($stmt->rowCount() > 0) {
    die("You are already registered for this event. <a href='index.php'>Back to Events</a>");
}

$stmt = $pdo->prepare("INSERT INTO participants (user_id, event_id, name, email) VALUES (?, ?, ?, ?)");
$stmt->execute([$user_id, $event_id, $user['name'], $user['email']]);
?>

<?php include "../includes/header.php"; ?>

<div class="form-container">
<p style="color:green;">Registered successfully! <a href="index.php">Back to Events</a></p>
</div>

<?php include "../includes/footer.php"; ?>
