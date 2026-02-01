<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

if (!isset($_SESSION['user_id'])) {
    die("Please <a href='login.php'>login</a> to create events.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare(
        "INSERT INTO events (title, category, organizer, event_date, description, user_id) VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $_POST['title'],
        $_POST['category'],
        $_POST['organizer'],
        $_POST['event_date'],
        $_POST['description'],
        $_SESSION['user_id']
    ]);
    echo "<p style='color:green;'>Event added successfully! <a href='index.php'>View Events</a></p>";
}
?>

<?php include "../includes/header.php"; ?>

<div class="form-container">
<h2>Add New Event</h2>
<form method="POST">
    Title: <input type="text" name="title" required><br>
    Category: <input type="text" name="category" required><br>
    Organizer: <input type="text" name="organizer" required><br>
    Event Date: <input type="date" name="event_date" required><br>
    Description: <textarea name="description" required></textarea><br>
    <input type="submit" value="Add Event">
</form>
</div>

<?php include "../includes/footer.php"; ?>
