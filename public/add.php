<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die("<p style='color:red;'>CSRF validation failed! <a href='index.php'>Back</a></p>");
    }

    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $organizer = trim($_POST['organizer'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $max_participants = intval($_POST['max_participants'] ?? 50);

    if (!$title || !$category || !$organizer || !$event_date || !$max_participants) {
        $error = "All fields are required.";
    }

    if (!$error) {
        $stmt = $pdo->prepare("INSERT INTO events (title, category, organizer, event_date, user_id, max_participants) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $category, $organizer, $event_date, $_SESSION['user_id'], $max_participants])) {
            $success = "Event added successfully!";
        } else {
            $error = "Database error. Please try again.";
        }
    }
}

include "../includes/header.php";
?>

<div class="form-container">
    <h2>Add Event</h2>

    <?php if ($error): ?><p style="color:red;"><?= e($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color:green;"><?= e($success) ?></p><?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <label>Title:</label>
        <input type="text" name="title" required>

        <label>Category:</label>
        <input type="text" name="category" required>

        <label>Organizer:</label>
        <input type="text" name="organizer" required>

        <label>Date:</label>
        <input type="date" name="event_date" required>

        <label>Max Participants:</label>
        <input type="number" name="max_participants" min="1" value="50" required>

        <input type="submit" value="Add Event">
    </form>
</div>

<?php include "../includes/footer.php"; ?>
