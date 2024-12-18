<?php
include "connect.php";
session_start();
$email = $_POST['email'];
$password = $_POST['password'];
$query = $conn->prepare("SELECT roles, password, email ,name ,profile_picture FROM users WHERE email = ?");
$query->execute([$email]);
$user = $query->fetch();

if (!$user) {
    $_SESSION['error'] = "ไม่พบอีเมล";
    header("Location: index.php");
    exit;
}
if ($user['profile_picture'] != null && $user['profile_picture'] != '') {
    $_SESSION['picture'] = $user['profile_picture'];
}
$_SESSION['roles'] = $user['roles'];
$_SESSION['name'] = $user['name'];
if (password_verify($password, $user['password'])) {
    $_SESSION['email'] = $email;
    header("Location: dashboard.php");
} else {
    $_SESSION['error'] = "รหัสผ่านไม่ตรง";
    header("Location: index.php");
    exit;
}






