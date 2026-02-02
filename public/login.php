<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

// If already logged in, go to index
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = "All fields are required.";
    } else {

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            // Regenerate session ID for security
            session_regenerate_id(true);

            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role']      = $user['role'];

            header("Location: index.php");
            exit;

        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<?php include "../includes/header.php"; ?>

<div class="form-container">
    <h2>Login</h2>

    <form method="POST" autocomplete="off">
        Email:<br>
        <input type="email" name="email" required><br><br>

        Password:<br>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>

    <?php if ($error): ?>
        <p style="color:red;"><?= e($error) ?></p>
    <?php endif; ?>
</div>

<?php include "../includes/footer.php"; ?>
