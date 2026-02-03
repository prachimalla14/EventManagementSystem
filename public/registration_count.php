<?php
require_once "../config/db.php"; // DB connection

$event_id = $_GET['event_id'] ?? 0; // Get event ID from query

// Count participants for the event
$stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM participants WHERE event_id = ?");
$stmt->execute([$event_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo $row['total']; // Return total count
