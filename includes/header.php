<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header>
    <h1>Event Management System</h1>
    <nav>
        <a href="index.php">Home</a> |
        <a href="add.php">Add Event</a> |
        <a href="search.php">Search Events</a>

        <?php if(!isset($_SESSION['user_id'])): ?>
            | <a href="login.php">Login</a>
            | <a href="signup.php">Sign Up</a>
        <?php else: ?>
            | <a href="logout.php">Logout</a>
            | Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>! 
        <?php endif; ?>
    </nav>
</header>
<hr>
