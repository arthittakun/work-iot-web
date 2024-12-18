<?php
date_default_timezone_set("Asia/Bangkok");
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include "connect.php";
$data = json_decode(file_get_contents("php://input"), true);
$otp = $data['otp'] ?? '';

if (empty($otp)) {
    echo json_encode(["success" => false, "message" => "OTP is required"]);
    exit;
}
try {
    $sql = "SELECT * FROM otp WHERE otp_code = :otp AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['otp' => $otp]);

    if ($stmt->rowCount() > 0) {
        $delete_sql = "DELETE FROM otp WHERE otp_code = :otp";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->execute(['otp' => $otp]);

        echo json_encode(["success" => true, "message" => "OTP is valid"]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid or expired OTP"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error", "error" => $e->getMessage()]);
}

$conn = null;
