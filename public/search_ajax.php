<?php
require_once "../config/db.php";       // DB connection
require_once "../includes/functions.php"; // Helper functions

$q = $_GET['q'] ?? '';
if (strlen($q) < 2) exit; // Ignore short queries

// Fetch matching events (limit 6)
$stmt = $pdo->prepare(
    "SELECT id, title, category, max_participants
     FROM events
     WHERE title LIKE ? OR category LIKE ? OR organizer LIKE ?
     ORDER BY event_date ASC
     LIMIT 6"
);
$like = "%$q%";
$stmt->execute([$like, $like, $like]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output results
foreach ($events as $event) {
    // Get current registrations
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM participants WHERE event_id=?");
    $stmtCount->execute([$event['id']]);
    $regCount = $stmtCount->fetchColumn();
    $spotsLeft = max(0, $event['max_participants'] - $regCount);

    echo "<div class='search-item' data-id='{$event['id']}' data-title='" . htmlspecialchars($event['title'], ENT_QUOTES) . "'>";
    echo htmlspecialchars($event['title']) . " <span class='category'>(" . htmlspecialchars($event['category']) . ")</span>";
    
    // Show availability
    if ($spotsLeft == 0) {
        echo " <span style='color:red;'>[Full]</span>";
    } else {
        echo " <span style='color:green;'>[{$spotsLeft} spots left]</span>";
    }
    echo "</div>";
}
