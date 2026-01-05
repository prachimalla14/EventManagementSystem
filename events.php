<?php
require 'db.php';

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
$params[] = "%".$_GET['organizer']."%";
}


$sql = "SELECT * FROM events";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);


$stmt = $pdo->prepare($sql);
$stmt->execute($params);
events = $stmt->fetchAll();
?>

<?php foreach ($events as $event): ?>
<h3><?= $event['title'] ?></h3>
<p><?= $event['category'] ?> | <?= $event['organizer'] ?> | <?= $event['event_date'] ?></p>
<div id="count-<?= $event['id'] ?>"></div>
<button onclick="loadCount(<?= $event['id'] ?>)">Refresh Count</button>
<?php endforeach; ?>


<script>
function loadCount(eventId) {
fetch('registration_count.php?event_id=' + eventId)
.then(res => res.text())
.then(data => document.getElementById('count-' + eventId).innerHTML = data);
}
</script>