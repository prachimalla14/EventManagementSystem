<?php
session_start(); // Start session
require_once "../config/db.php";       // DB connection
require_once "../includes/functions.php"; 

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$errors = [];   // Store validation errors
$success = '';  // Success message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (!$name) $errors[] = "Name is required.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required.";
    if (!$password) $errors[] = "Password is required.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";

    // Password strength check
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/';
    if ($password && !preg_match($pattern, $password)) {
        $errors[] = "Password must be at least 6 characters long, include uppercase, lowercase, number, and special character.";
    }

    // Check if email already exists
    if (!$errors) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) $errors[] = "Email is already registered.";
    }

    // Insert new user
    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT); // Hash password
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        if ($stmt->execute([$name, $email, $hash])) {
            $success = "Registration successful! <a href='login.php'>Login here</a>.";
            $_POST = []; // Clear form
        } else {
            $errors[] = "Database error. Please try again.";
        }
    }
}
?>

<?php include "../includes/header.php"; ?>

<div class="form-container">
    <h2>Sign Up</h2>

    <!-- Display errors -->
    <?php if ($errors): ?>
        <div style="color:red; margin-bottom:10px;">
            <?php foreach ($errors as $err) echo "<p>- " . e($err) . "</p>"; ?>
        </div>
    <?php endif; ?>

    <!-- Display success -->
    <?php if ($success): ?>
        <div style="color:green; margin-bottom:10px;"><?= $success ?></div>
    <?php endif; ?>

    <!-- Sign-up form -->
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
// Front-end password validation
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
