<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = "Email already registered";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)");
        $stmt->execute([$name,$email,$password,'user']);
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['user_name'] = $name;
        $_SESSION['role'] = 'user';
        header("Location: index.php");
        exit;
    }
}
?>

<?php include "../includes/header.php"; ?>

<div class="form-container">
<h2>Sign Up</h2>
<form method="POST">
    Name: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required minlength="6"><br>
    <input type="submit" value="Sign Up">
</form>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</div>

<?php include "../includes/footer.php"; ?>
