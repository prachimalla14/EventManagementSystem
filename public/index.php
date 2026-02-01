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
    <h2>All Events</h2>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p>
            Logged in as: <strong><?= e($_SESSION['user_name']) ?></strong>
            (<?= e($_SESSION['role']) ?>)
            | <a href="add.php">Add Event</a>
            | <a href="logout.php">Logout</a>
        </p>
    <?php else: ?>
        <p><a href="login.php">Login</a> to add or register for events.</p>
    <?php endif; ?>

    <form method="GET" autocomplete="off" style="position: relative; max-width: 500px;">
        <input type="text" name="q" id="search-box"
               placeholder="Search by title, category, or organizer"
               value="<?= e($keyword) ?>">
        <input type="submit" value="Search">
        <div id="search-results"></div>
    </form>
</div>

<div class="events-container">
<?php if ($events): ?>
    <?php foreach ($events as $event): ?>
        <div class="event-card">
            <h3><?= e($event['title']) ?></h3>
            <p>
                <strong>Category:</strong> <?= e($event['category']) ?><br>
                <strong>Organizer:</strong> <?= e($event['organizer']) ?><br>
                <strong>Date:</strong> <?= e($event['event_date']) ?>
            </p>

            <?php
            $currentCount = participantCount($pdo, $event['id']);
            $max = $event['max_participants'] ?? null;
            ?>
            <p>Registered: <strong><?= $currentCount ?></strong>
                <?php if ($max): ?> / <strong><?= e($max) ?></strong><?php endif; ?>
            </p>

            <?php
            $isOwner = isset($_SESSION['user_id']) && $_SESSION['user_id'] == $event['user_id'];
            $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
            ?>

            <?php if ($isOwner || $isAdmin): ?>
                <div style="margin-bottom:5px;">
                    <a href="edit.php?id=<?= $event['id'] ?>">Edit</a>

                    <!-- CSRF-protected Delete Form -->
                    <form method="POST" action="delete.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $event['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to delete this event?')">Delete</button>
                    </form>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (!$max || $currentCount < $max): ?>
                    <form method="POST" action="register.php" style="margin-top:5px;">
                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                        <input type="submit" value="Register for this Event">
                    </form>
                <?php else: ?>
                    <p style="color:red;">Event full. Registration closed.</p>
                <?php endif; ?>
            <?php else: ?>
                <p><a href="login.php">Login</a> to register.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p style="text-align:center;">No events found.</p>
<?php endif; ?>
</div>

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
            .then(res => res.text())
            .then(data => results.innerHTML = data);
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
