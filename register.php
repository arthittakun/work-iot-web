<?php
session_start();
include "connect.php";
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;

$params = array(
    'client_id' => clientID,
    'redirect_uri' => redirectUri,
    'response_type' => 'code',
    'scope' => 'email profile',
    'state' => $state
);

$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
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

    <style>
        body {
            background-color: #DEF5E5;
        }

        .login-form {
            width: 100%;
            max-width: 380px;
            padding: 40px;
            margin: 100px auto 20px auto;
            background-color: #ffffff;
            border-radius: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #8EC3B0;
        }

        .form-control {
            border-radius: 50px;
            padding: 10px 20px;
        }

        .btn-custom {
            background-color: #8EC3B0;
            color: white;
            border-radius: 50px;
            width: 100%;
            padding: 10px;
        }

        .btn-custom:hover {
            background-color: #9ED5C5;
        }

        .valid {
            color: green;
        }

        .invalid {
            color: red;
        }
    </style>

    <div class="content">
        <div class="login-form">
            <h2>สมัครสมาชิก</h2>
            <form id="registerForm" action="register_int.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">ชื่อ-นามสกุล</label>
                    <input type="text" class="form-control" name="username" id="username" placeholder="กอรกชื่อเเละนามสกุล">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" class="form-control" name="email" id="email" placeholder="กรอกอีเมล">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="กรอกรหัสผ่าน">
                </div>
                <div class="mb-3">
                    <label for="password-verify" class="form-label">ยืนยันรหัสผ่าน</label>
                    <input type="password" class="form-control" name="password-verify" id="password-verify" placeholder="กอรกเพื่อยืนยันรหัสผ่าน">
                </div>
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']); 
                }
                ?>
                <div class="mb-3 d-flex justify-content-between">
                    <a href="index.php" class="text-end">เข้าสู่ระบบ</a>
                </div>
                <div class="regis">
                    <button type="submit" class="btn btn-custom">สมัครสมาชิก</button>
                </div>
                <div class="google">
                    <a href="<?php echo $auth_url; ?>" class="google-signin-button">
                        <i class="fab fa-google"></i> เข้าสู่ระบบด้วย Google
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>