<?php
require_once "../config/db.php";

$event_id = $_GET['event_id'] ?? null;

if (!$event_id) {
    die("Invalid Event ID");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare("INSERT INTO participants (name, email, event_id) VALUES (?, ?, ?)");
    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $event_id
    ]);
    echo "Registered Successfully. <a href='index.php'>Back to Events</a>";
}
?>

<h2>Register for Event</h2>
<form method="POST" action="">
    <label>Name:</label><br>
    <input type="text" name="name" required><br>

    <label>Email:</label><br>
    <input type="email" name="email" required><br>

    <input type="submit" value="Register">
</form>
