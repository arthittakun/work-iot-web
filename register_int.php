<?php
include "connect.php";
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passwordVerify = $_POST['password-verify'];

    $_SESSION['name'] = $username;
    if ($password !== $passwordVerify) {
        $_SESSION['error'] = "รหัสผ่านไม่ตรงกัน";
        header("Location: register.php");
        exit;
    }


    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] ="อีเมลนี้ถูกใช้แล้ว";
        header("Location: register.php");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
    $stmt->bindParam(':name', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashedPassword);

    if ($stmt->execute()) {
        $_SESSION['email'] = $email;
        header("Location: dashboard.php");
    } else {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลงทะเบียน";
    header("Location: register.php");
    exit;
    }
}
?>
