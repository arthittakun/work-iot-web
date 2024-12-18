<?php
include "connect.php";
session_start();
$clientID = clientID;
$redirectUri = redirectUri;
$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id' => $clientID,
    'redirect_uri' => $redirectUri,
    'response_type' => 'code',
    'scope' => 'email profile',
    'access_type' => 'offline',
]);

if(isset($_GET['token']) && isset($_GET['name']) ){
    $_SESSION['token'] = $_GET['token'];
    $_SESSION['fullname'] = $_GET['name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/asset/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sevillana&display=swap" rel="stylesheet">
    <title>shop</title>
</head>
<body>
    <div class="content">
        <div class="login-form">
            <h2>เข้าสู่ระบบ</h2>
            <form id="registerForm" action="login.php" method="POST">
                <div class="mb-3">
                    <label for="Email" class="form-label">อีเมล</label>
                    <input type="text" class="form-control" name="email" placeholder="กรอกอีเมล">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" class="form-control" name="password" placeholder="กรอกรหัสผ่าน">
                </div>
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']);  
                }
                ?>
                <div class="mb-3 d-flex justify-content-between">
                    <a href="register.php" class="text-start">สมัครสมาชิก</a>
                    <a href="check_otp.php" class="text-end">ลืมรหัสผ่านใช่ไหม?</a>
                </div>
                <div class="logib-web">
                    <button type="submit" class="btn btn-custom">เข้าสู่ระบบ</button>
                </div>
                <div class="google">
                    <a href="<?= $authUrl ?>" class="google-signin-button">
                        <i class="fab fa-google"></i> เข้าสู่ระบบด้วย Google
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
