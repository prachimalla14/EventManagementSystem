<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

if (!isset($_SESSION['user_id'])) {
    die("Please <a href='login.php'>login</a> to edit events.");
}

if (!isset($_GET['id'])) die("Invalid Event ID");

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) die("Event not found");
if ($event['user_id'] != $_SESSION['user_id']) die("You can only edit your own events");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE events SET title=?, category=?, organizer=?, event_date=?, description=? WHERE id=?");
    $stmt->execute([
        $_POST['title'],
        $_POST['category'],
        $_POST['organizer'],
        $_POST['event_date'],
        $_POST['description'],
        $id
    ]);
    header("Location: index.php");
    exit;
}
?>

<?php include "../includes/header.php"; ?>

<div class="form-container">
<h2>Edit Event</h2>
<form method="POST">
    Title: <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required><br>
    Category: <input type="text" name="category" value="<?= htmlspecialchars($event['category']) ?>" required><br>
    Organizer: <input type="text" name="organizer" value="<?= htmlspecialchars($event['organizer']) ?>" required><br>
    Event Date: <input type="date" name="event_date" value="<?= $event['event_date'] ?>" required><br>
    Description: <textarea name="description" required><?= htmlspecialchars($event['description']) ?></textarea><br>
    <input type="submit" value="Update Event">
</form>
</div>

<?php include "../includes/footer.php"; ?>
