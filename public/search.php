<?php
require_once "../config/db.php";

$keyword = $_GET['q'] ?? '';
$events = [];

if ($keyword) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE title LIKE ? OR category LIKE ? OR organizer LIKE ?");
    $stmt->execute(["%$keyword%", "%$keyword%", "%$keyword%"]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h2>Search Events</h2>
<form method="GET">
    <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Search by title, category, or organizer">
    <input type="submit" value="Search">
</form>

<?php if ($events): ?>
    <?php foreach ($events as $event): ?>
        <div style="border:1px solid #ccc; padding:5px; margin:5px;">
            <h3><?= htmlspecialchars($event['title']) ?></h3>
            <p><?= htmlspecialchars($event['category']) ?> | <?= htmlspecialchars($event['organizer']) ?> | <?= $event['event_date'] ?></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No events found.</p>
<?php endif; ?>
 