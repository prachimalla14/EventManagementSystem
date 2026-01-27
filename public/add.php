<?php require_once "../config/db.php"; ?>

<h2>Add New Event</h2>

<form method="POST" action="">
    <label>Title:</label><br>
    <input type="text" name="title" required><br>

    <label>Category:</label><br>
    <input type="text" name="category" required><br>

    <label>Organizer:</label><br>
    <input type="text" name="organizer" required><br>

    <label>Event Date:</label><br>
    <input type="date" name="event_date" required><br>

    <label>Description:</label><br>
    <textarea name="description"></textarea><br>

    <input type="submit" value="Add Event">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare(
        "INSERT INTO events (title, category, organizer, event_date, description) VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $_POST['title'],
        $_POST['category'],
        $_POST['organizer'],
        $_POST['event_date'],
        $_POST['description']
    ]);

    echo "<p>Event added successfully! <a href='index.php'>View Events</a></p>";
}
?>
