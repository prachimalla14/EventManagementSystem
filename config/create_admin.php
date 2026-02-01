<?php
require_once "db.php";

$adminName = 'Admin';
$adminEmail = 'admin@example.com';
$adminPassword = 'admin123'; 

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$adminEmail]);
if ($stmt->rowCount() > 0) {
    echo "Admin user already exists. Email: $adminEmail";
    exit;
}

$hash = password_hash($adminPassword, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$adminName, $adminEmail, $hash, 'admin']);

echo "Admin user created successfully! Email: $adminEmail, Password: $adminPassword";
?>
