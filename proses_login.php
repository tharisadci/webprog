<?php
session_start();

// Username & password sementara (bisa diganti dari database nanti)
$valid_username = "HRD001";
$valid_password = "171705";

$username = $_POST['username'];
$password = $_POST['password'];

if ($username == $valid_username && $password == $valid_password) {
    $_SESSION['username'] = $username;
    header("Location: index.php");
} else {
    header("Location: login.php?error=Username atau password salah");
}
?>
