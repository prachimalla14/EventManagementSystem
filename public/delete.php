<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user_id'])) die("Please login.");

if (!isset($_GET['id'])) die("Invalid Event ID");

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) die("Event not found");
if ($event['user_id'] != $_SESSION['user_id']) die("You can only delete your own events");

$stmt = $pdo->prepare("DELETE FROM events WHERE id=?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
