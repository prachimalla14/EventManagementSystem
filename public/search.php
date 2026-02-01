<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

$keyword = $_GET['q'] ?? '';

$where = [];
$params = [];

if ($keyword) {
    $where[] = "(title LIKE ? OR category LIKE ? OR organizer LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

$sql = "SELECT * FROM events";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY event_date ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
?>

<div class="form-container">
<h2>Search Events</h2>

<form method="GET" autocomplete="off" style="position: relative; max-width: 500px;">
    <input type="text" name="q" id="search-box" placeholder="Start typing to search events..." value="<?= e($keyword) ?>" required>
    <input type="submit" value="Search">
    <div id="search-results" style="position: absolute; top: 38px; left: 0; width: 100%; z-index: 10;"></div>
</form>
</div>

<?php if ($events): ?>
    <?php foreach ($events as $event): ?>
        <div class="event-card">
            <h3><?= e($event['title']) ?></h3>
            <p>
                <strong>Category:</strong> <?= e($event['category']) ?><br>
                <strong>Organizer:</strong> <?= e($event['organizer']) ?><br>
                <strong>Date:</strong> <?= e($event['event_date']) ?>
            </p>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $event['user_id']): ?>
                <a href="edit.php?id=<?= $event['id'] ?>">Edit</a> |
                <a href="delete.php?id=<?= $event['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="GET" action="register.php" style="margin-top:5px;">
                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                    <input type="submit" value="Register for this Event">
                </form>
            <?php else: ?>
                <p><a href="login.php">Login</a> to register for this event.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="text-align:center;">No events found.</p>
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const input = document.getElementById("search-box");
    const results = document.getElementById("search-results");

    input.addEventListener("input", function() {
        const q = input.value.trim();
        if (q.length < 2) {
            results.innerHTML = "";
            return;
        }

        fetch(`search_ajax.php?q=${encodeURIComponent(q)}`)
            .then(res => res.text())  // HTML output from PHP
            .then(data => {
                results.innerHTML = data;
            });
    });

    results.addEventListener("click", function(e) {
        if (e.target.classList.contains("search-item")) {
            input.value = e.target.textContent;
            results.innerHTML = "";
        }
    });
});
</script>

<?php include "../includes/footer.php"; ?>
