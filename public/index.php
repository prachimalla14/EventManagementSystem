<?php
require_once "../config/db.php";

$where = [];
$params = [];

if (!empty($_GET['month'])) {
    $where[] = "MONTH(event_date) = ?";
    $params[] = $_GET['month'];
}

if (!empty($_GET['category'])) {
    $where[] = "category = ?";
    $params[] = $_GET['category'];
}

if (!empty($_GET['organizer'])) {
    $where[] = "organizer LIKE ?";
    $params[] = "%" . $_GET['organizer'] . "%";
}

$sql = "SELECT * FROM events";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php foreach ($events as $event): ?>
    <h3><?= htmlspecialchars($event['title']) ?></h3>
    <p>
        <?= htmlspecialchars($event['category']) ?> |
        <?= htmlspecialchars($event['organizer']) ?> |
        <?= htmlspecialchars($event['event_date']) ?>
    </p>

    <div id="count-<?= $event['id'] ?>"></div>
    <button onclick="loadCount(<?= $event['id'] ?>)">Refresh Count</button>
<?php endforeach; ?>

<script src="../assets/js/search.js"></script>
