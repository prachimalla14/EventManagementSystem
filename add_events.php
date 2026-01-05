<?php
require 'db.php';
if ($_POST) {
$stmt = $pdo->prepare("INSERT INTO events (title, category, organizer, event_date, description) VALUES (?,?,?,?,?)");
$stmt->execute([
$_POST['title'],
$_POST['category'],
$_POST['organizer'],
$_POST['event_date'],
$_POST['description']
]);
header("Location: events.php");
}
?>