<?php
$host = "localhost";
$db = "NP03CS4A240273";
$username = "NP03CS4A240273";
$password = "NiBxSb6vt2";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
