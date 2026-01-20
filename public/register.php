<?php
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $pdo->prepare(
        "INSERT INTO participants (name, email, event_id)
         VALUES (?, ?, ?)"
    );

    $stmt->execute([
        $_POST['name'],
        $_POST['email'],
        $_POST['event_id']
    ]);

    echo "Registered Successfully";
}
?>
