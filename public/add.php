<?php
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare(
        "INSERT INTO events (title, category, organizer, event_date, description)
         VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $_POST['title'],
        $_POST['category'],
        $_POST['organizer'],
        $_POST['event_date'],
        $_POST['description']
    ]);

    header("Location: index.php");
    exit;
}
?>
