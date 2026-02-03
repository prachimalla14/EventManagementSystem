<?php
session_start(); // Start session for login & user data
require_once "../config/db.php"; // Database connection
require_once "../includes/functions.php"; 
requireLogin(); // Restrict access to logged-in users only

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF protection check
    if (empty($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die("<p style='color:red;'>CSRF validation failed! <a href='index.php'>Back</a></p>");
    }

    // Fetch and sanitize form inputs
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $organizer = trim($_POST['organizer'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $max_participants = intval($_POST['max_participants'] ?? 50);

    // Basic validation
    if (!$title || !$category || !$organizer || !$event_date || !$max_participants) {
        $error = "All fields are required.";
    }

    // Insert event if no validation errors
    if (!$error) {
        $stmt = $pdo->prepare(
            "INSERT INTO events (title, category, organizer, event_date, user_id, max_participants)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

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

    <!-- Error message -->
    <?php if ($error): ?>
        <p style="color:red;"><?= e($error) ?></p>
    <?php endif; ?>

    <!-- Success message -->
    <?php if ($success): ?>
        <p style="color:green;"><?= e($success) ?></p>
    <?php endif; ?>

    <!-- Event creation form -->
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

    <!-- Back navigation -->
    <div style="margin-top: 15px;">
        <a href="index.php">
            <button type="button" style="
                background:#c2f0c2;
                color:#4b3e4d;
                border:none;
                padding:10px 15px;
                border-radius:8px;
                cursor:pointer;">
                &larr; Back to Events
            </button>
        </a>
    </div>
</div>

<?php include "../includes/footer.php";  ?>
