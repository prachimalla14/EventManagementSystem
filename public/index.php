<?php
require_once "../config/db.php";

// ===========================
// 1️⃣ Handle Ajax request for participant count
// ===========================
if (isset($_GET['ajax_count'])) {
    $event_id = $_GET['ajax_count'];
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM participants WHERE event_id = ?");
    $stmt->execute([$event_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $row['total'];
    exit; // stop executing the rest of index.php for Ajax
}

// ===========================
// 2️⃣ Fetch Events (with optional filters)
// ===========================
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

<?php include "../includes/header.php"; ?>

<h2>All Events</h2>

<?php if ($events): ?>
    <?php foreach ($events as $event): ?>
        <div class="event-card">
            <h3><?= htmlspecialchars($event['title']) ?></h3>
            <p>
                <strong>Category:</strong> <?= htmlspecialchars($event['category']) ?><br>
                <strong>Organizer:</strong> <?= htmlspecialchars($event['organizer']) ?><br>
                <strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?>
            </p>

            <!-- Register button -->
            <form method="GET" action="register.php">
                <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                <input type="submit" value="Register for this Event">
            </form>

            <!-- Ajax participant count -->
            <div id="count-<?= $event['id'] ?>">Registered: 0</div>
            <button onclick="loadCount(<?= $event['id'] ?>)">Refresh Count</button>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No events found.</p>
<?php endif; ?>

<script src="../assets/js/search.js"></script>
<script>
    // Automatically load participant counts on page load
    <?php foreach ($events as $event): ?>
        loadCount(<?= $event['id'] ?>);
    <?php endforeach; ?>
</script>

<?php include "../includes/footer.php"; ?>
