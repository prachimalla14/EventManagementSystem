<?php
require_once "../config/db.php";
require_once "../includes/functions.php";

$q = $_GET['q'] ?? '';
if (strlen($q) < 2) exit;

$stmt = $pdo->prepare(
    "SELECT id, title, category FROM events
     WHERE title LIKE ? OR category LIKE ? OR organizer LIKE ?
     ORDER BY event_date ASC
     LIMIT 6"
);
$like = "%$q%";
$stmt->execute([$like, $like, $like]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($events as $event) {
    echo "<div class='search-item' data-id='{$event['id']}'>";
    echo htmlspecialchars($event['title']) . " <span class='category'>(" . htmlspecialchars($event['category']) . ")</span>";
    echo "</div>";
}
