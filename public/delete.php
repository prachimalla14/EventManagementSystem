<?php
require_once "../config/db.php";

if (!isset($_GET['id'])) {
    die("Invalid Event ID");
}

$id = $_GET['id'];

// Delete the event
$stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>
