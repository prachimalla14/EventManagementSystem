<?php
	$host = "localhost";
	$db = "Event_Management_System";
	$username = "root";
	$password = "";


	try {
		$pdo = new PDO("mysql:host=$host;dbname=$db", $username, $password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		die("DB Error: " . $e->getMessage());
	}
?>