<?php
require_once "../config/db.php";

if (!isset($_GET['id'])) {
    die("Invalid Event ID");
}

$id = $_GET['id'];

// Fetch existing event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Event not found");
}

// Handle form submission
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

<h2>Edit Event</h2>
<form method="POST">
    <label>Title:</label><br>
    <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>" required><br>

    <label>Category:</label><br>
    <input type="text" name="category" value="<?= htmlspecialchars($event['category']) ?>" required><br>

    <label>Organizer:</label><br>
    <input type="text" name="organizer" value="<?= htmlspecialchars($event['organizer']) ?>" required><br>

    <label>Event Date:</label><br>
    <input type="date" name="event_date" value="<?= $event['event_date'] ?>" required><br>

    <label>Description:</label><br>
    <textarea name="description"><?= htmlspecialchars($event['description']) ?></textarea><br>

    <input type="submit" value="Update Event">
</form>
