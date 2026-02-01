<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$name) $errors[] = "Name is required.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (!$password) $errors[] = "Password is required.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/';
    if ($password && !preg_match($pattern, $password)) {
        $errors[] = "Password must be at least 6 characters long, include uppercase, lowercase, number, and special character.";
    }

    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) $errors[] = "Email is already registered.";
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        if ($stmt->execute([$name, $email, $hash])) {
            $success = "Registration successful! <a href='login.php'>Login here</a>.";
            $_POST = [];
        } else {
            $errors[] = "Database error. Please try again.";
        }
    }
}
?>

<?php include "../includes/header.php"; ?>

<div class="form-container">
    <h2>Sign Up</h2>

    <?php if ($errors): ?>
        <div style="color:red; margin-bottom:10px;">
            <?php foreach ($errors as $err) echo "<p>- " . e($err) . "</p>"; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="color:green; margin-bottom:10px;"><?= $success ?></div>
    <?php endif; ?>

    <form id="signup-form" method="POST" autocomplete="off">
        <label>Name:</label>
        <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>

        <label>Password:</label>
        <input type="password" name="password" id="password" required>

        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <small id="password-help" style="color:#555;">
            Password must be at least 6 characters, include uppercase, lowercase, number, and special character.
        </small>

        <input type="submit" value="Sign Up">
    </form>
</div>

<script>
document.getElementById('signup-form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;
    const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/;

    if (!pattern.test(password)) {
        alert("Password must be at least 6 characters long, include uppercase, lowercase, number, and special character.");
        e.preventDefault();
        return false;
    }

    if (password !== confirm) {
        alert("Passwords do not match.");
        e.preventDefault();
        return false;
    }
});
</script>

<?php include "../includes/footer.php"; ?>
