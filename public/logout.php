<?php
session_start();      // Start session
session_destroy();    // Destroy session (logout)
header("Location: ../public/login.php"); // Redirect to login
exit;

