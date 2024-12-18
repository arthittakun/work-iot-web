<?php
session_start();
include "connect.php";
require 'vendor/autoload.php';
date_default_timezone_set("Asia/Bangkok");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


$data = json_decode(file_get_contents("php://input"), true);
$email = $data['email'] ?? '';
$_SESSION['otp_email'] = $email;
if (empty($email)) {
    echo json_encode(["success" => false, "message" => "Email is required"]);
    exit;
}
function generateOTP($length = 6)
{
    return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
}

$otp_code = generateOTP();
$expires_at = date("Y-m-d H:i:s", strtotime("+1 minute"));

$_SESSION['otp'] = $otp_code;

$email = "example@example.com";
try {
    $sql = "INSERT INTO otp (email, otp_code, expires_at) VALUES (:email, :otp_code, :expires_at)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':email' => $email,
        ':otp_code' => $otp_code,
        ':expires_at' => $expires_at
    ]);

    echo json_encode(["success" => true, "message" => "OTP generated successfully", "otp" => $otp_code]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Failed to insert OTP", "error" => $e->getMessage()]);
}



$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'meomiwon@gmail.com';
    $mail->Password = 'ozcu dydr jzwd sxbv';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('meomiwon@gmail.com', 'meomiw');
    $mail->addAddress($email);



    $mail->isHTML(true);
    $mail->Subject = "Your OTP Code";

    $mail->Body = "
    <div style='font-family: Arial, sans-serif; max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9;'>
        <h2 style='color: #333; text-align: center;'>Your OTP Code</h2>
        <p style='text-align: center; font-size: 16px; color: #555;'>
            Your one-time password (OTP) for verification is:
        </p>
        <div style='margin: 20px auto; text-align: center; font-size: 24px; font-weight: bold; color: #4CAF50; padding: 10px; border: 2px dashed #4CAF50; border-radius: 5px; width: fit-content;'>
            $otp_code
        </div>
        <p style='text-align: center; font-size: 14px; color: #777;'>
            This code will expire in 1 minute. Please do not share this code with anyone.
        </p>
        <p style='text-align: center; font-size: 12px; color: #aaa;'>
            If you did not request this code, please ignore this email.
        </p>
    </div>";

    $mail->send();
    echo json_encode(["success" => true, "message" => "OTP sent to email"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
}

$conn = null;
