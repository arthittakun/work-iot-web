<?php

include "connect.php";
function sendTelegram($response)
{
    $url = "https://api.telegram.org/bot" . tokentelegram . "/sendMessage";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($response));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}
$update = file_get_contents("php://input");
$updateArray = json_decode($update, TRUE);

if (!isset($updateArray["message"]["text"]) || !isset($updateArray["message"]["chat"]["id"])) {
    die("ข้อมูลที่ส่งมาจาก Telegram ไม่ครบ");
}
$message = $updateArray["message"]["text"];
$chat_id = $updateArray["message"]["chat"]["id"];
$first_name = $updateArray["message"]["chat"]["first_name"] ?? '';
$last_name = $updateArray["message"]["chat"]["last_name"] ?? '';
$full_name = trim($first_name . ' ' . $last_name);
if (!$conn) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว");
}
$query = $conn->prepare("SELECT user_id, password, email, name, profile_picture FROM users WHERE chat_id = ?");
$query->execute([$chat_id]);
$user = $query->fetch();

if ($message === "/start") {
    $response = [
        "chat_id" => $chat_id,
        "text" => "ยินดีต้อนรับสู่บอทแจ้งเตือนการเคลื่อนไหว! นี่คือคำสั่งที่คุณสามารถใช้ได้:

/login - เข้าสู่ระบบเพื่อเชื่อมต่อบัญชี Telegram
/logout - ออกจากระบบและยกเลิกการแจ้งเตือน

/view - ดูรายการอุปกรณ์ที่มี
/notify - เลือกอุปกรณ์เพื่อเปิดการแจ้งเตือน
/adddeviceall - เปิดการแจ้งเตือนสำหรับทุกอุปกรณ์

หากต้องการข้อมูลเพิ่มเติม กรุณาไปที่หน้าการตั้งค่าผู้ใช้หรือติดต่อฝ่ายสนับสนุน!"
    ];
    sendTelegram($response);
    exit;
} else if (!$user) {
    if ($message === "/login") {
        $response = [
            "chat_id" => $chat_id,
            "text" => "คุณสามารถคลิกลิงก์นี้เพื่อเข้าสู่ระบบ หรือคัดลอก URL ด้านล่างได้:\n\n[เข้าสู่ระบบคลิก](https:\/\/meo-mil.xyz\/index.php?token=" . $chat_id . "&name=" . urlencode($full_name) . ")\n\nลิงก์: `https://meo-mil.xyz/index.php?token=" . $chat_id . "&name=" . urlencode($full_name) . "`",
            "parse_mode" => "MarkdownV2"
        ];
    } else {
        $response = [
            "chat_id" => $chat_id,
            "text" => "คุณยังไม่ได้เข้าสู่ระบบ! กรุณาล็อกอินก่อน \n<a href='https://meo-mil.xyz/index.php?token=" . $chat_id . "&name=" . urlencode($full_name) . "'>เข้าสู่ระบบคลิก</a>",
            "parse_mode" => "HTML"
        ];
    }

    sendTelegram($response);
    exit;
}

$sql = "SELECT * FROM device ORDER BY timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$devices = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [];
$add = "";

switch ($message) {

    case "/login":
        $response = [
            "chat_id" => $chat_id,
            "text" => "คุณได้เข้าสู่ระบบแล้ว! หากต้องการออกจากระบบการเเจ้งเตือนให้ไปที่ เว็บการตั้งค่าผู้ใช้ เเละกดลบการเเจ้งเตือน \n
                หรือคุณสามารถใช้คำสั่ง /logout เพื่อออกจากระบบ
                "
        ];
        break;


    case "/logout":
        if ($user) {
            try {
                $conn->beginTransaction();
                $sqlUpdateUser = "UPDATE users SET chat_id = NULL, name = NULL WHERE chat_id = ?";
                $stmtUser = $conn->prepare($sqlUpdateUser);
                $stmtUser->execute([$chat_id]);
                $sqlDeleteNotify = "DELETE FROM notify WHERE chat_id = ?";
                $stmtNotify = $conn->prepare($sqlDeleteNotify);
                $stmtNotify->execute([$chat_id]);
                $conn->commit();

                $response = [
                    "chat_id" => $chat_id,
                    "text" => "คุณได้ออกจากระบบเรียบร้อยแล้ว! การแจ้งเตือนทั้งหมดของคุณถูกลบแล้ว หากต้องการเข้าสู่ระบบอีกครั้ง ให้พิมพ์ /login"
                ];
            } catch (Exception $e) {
                $conn->rollBack();
                $response = [
                    "chat_id" => $chat_id,
                    "text" => "เกิดข้อผิดพลาดในการออกจากระบบ: " . $e->getMessage()
                ];
            }
        } else {
            $response = [
                "chat_id" => $chat_id,
                "text" => "คุณยังไม่ได้เข้าสู่ระบบ!"
            ];
        }
        break;


    case "/view":
        $text = "";
        foreach ($devices as $device) {
            $text .= "ชื่ออุปกรณ์ : " . $device['device_name'] . "\n";
        }
        $response = [
            "chat_id" => $chat_id,
            "text" => "เลือกอุปกรณ์:\n" . $text
        ];
        break;

    case "/notify":
        $text = "";
        foreach ($devices as $device) {
            $text .= "ชื่ออุปกรณ์ : " . $device['device_name'] . "\n";
            $add .= "/" . $device['device_name'] . "\n";
        }
        $response = [
            "chat_id" => $chat_id,
            "text" => "เลือกอุปกรณ์:\n" . $text . "\n\nคุณสามารถพิมพ์ / ตามด้วยชื่ออุปกรณ์เพื่อทำการเชื่อมต่อได้ \n" . $add . "\n หรือเพิ่มทั้งหมด \n /adddeviceall"
        ];
        break;

    case "/adddeviceall":
        $added = 0;
        $skipped = 0;
        foreach ($devices as $device) {
            $checkSql = "SELECT * FROM notify WHERE chat_id = :chat_id AND device_id = :device_id";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);
            $checkStmt->bindParam(':device_id', $device['device_id'], PDO::PARAM_INT);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                $skipped++;
            } else {
                $sql = "INSERT INTO notify (chat_id, chat_name, device_id, user_id) 
                        VALUES (:chat_id, :chat_name, :device_id, :user_id)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);
                $stmt->bindParam(':chat_name', $full_name, PDO::PARAM_STR);
                $stmt->bindParam(':device_id', $device['device_id'], PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $added++;
                }
            }
        }

        $response = [
            "chat_id" => $chat_id,
            "text" => "เพิ่มอุปกรณ์สำเร็จ: $added รายการ\nข้ามอุปกรณ์ที่มีอยู่แล้ว: $skipped รายการ"
        ];
        break;

    default:
        foreach ($devices as $device) {
            if ($message === "/" . $device['device_name']) {
                $checkSql = "SELECT * FROM notify WHERE chat_id = :chat_id AND device_id = :device_id";
                $checkStmt = $conn->prepare($checkSql);
                $checkStmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);
                $checkStmt->bindParam(':device_id', $device['device_id'], PDO::PARAM_INT);
                $checkStmt->execute();

                if ($checkStmt->rowCount() > 0) {
                    $response = [
                        "chat_id" => $chat_id,
                        "text" => "อุปกรณ์นี้ถูกเพิ่มไปแล้ว!"
                    ];
                } else {
                    $sql = "INSERT INTO notify (chat_id, chat_name, device_id, user_id) 
                            VALUES (:chat_id, :chat_name, :device_id, :user_id)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':chat_id', $chat_id, PDO::PARAM_STR);
                    $stmt->bindParam(':chat_name', $full_name, PDO::PARAM_STR);
                    $stmt->bindParam(':device_id', $device['device_id'], PDO::PARAM_INT);
                    $stmt->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $response = [
                            "chat_id" => $chat_id,
                            "text" => "ข้อมูลถูกแทรกสำเร็จ!"
                        ];
                    } else {
                        $response = [
                            "chat_id" => $chat_id,
                            "text" => "ข้อมูลถูกแทรกไม่สำเร็จ! โปรดตรวจสอบข้อมูลที่ส่ง"
                        ];
                    }
                }
                break;
            }
        }

        if (empty($response)) {
            $response = [
                "chat_id" => $chat_id,
                "text" => "ไม่พบคำสั่งหรือข้อมูลไม่ตรงกับอุปกรณ์ที่มีอยู่"
            ];
        }
        break;
}
sendTelegram($response);
