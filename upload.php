<?php
include "connect.php";
try {

    $api_key = $_SERVER['HTTP_X_API_KEY'] ?? '';

    $stmt = $conn->prepare("SELECT d.device_id, d.device_name 
                          FROM device d 
                          WHERE d.device_key = ?");
    $stmt->execute([$api_key]);
    $device = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$device) {
        http_response_code(401);
        die(json_encode(['error' => 'Invalid API key']));
    }

    $device_id = $device['device_id'];
    $device_name = $device['device_name'];

    $stmt = $conn->prepare("SELECT chat_id FROM notify WHERE device_id = ?");
    $stmt->execute([$device_id]);
    $chat_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($chat_ids)) {
        http_response_code(404);
        die(json_encode(['error' => 'No chat_id found for the given device']));
    }

    $uploadDir = 'uploads';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $input = file_get_contents('php://input');
    $timestamp = time();
    $formattedTimestamp = date('Y-m-d H:i:s', $timestamp);
    $fileName = 'image_' . $timestamp . '.jpg';
    $filePath = $uploadDir . '/' . $fileName;
    $relativeFilePath = '/' . $uploadDir . '/' . $fileName;

    $text = "ตรวจสอบพบบุคคล " . $formattedTimestamp;

    if (file_put_contents($filePath, $input)) {
        $stmt = $conn->prepare("INSERT INTO imagedevice (img, device_id, text) VALUES (?, ?, ?)");
        $stmt->execute([$relativeFilePath, $device_id, "ตรวจสอบพบบุคคล"]);

        $telegram_api_url = "https://api.telegram.org/bot" . tokentelegram . "/sendPhoto";

        foreach ($chat_ids as $chat_id) {
            $post_fields = [
                'chat_id' => $chat_id,
                'photo' => new CURLFile($filePath),
                'caption' => $text . " - " . $device_name
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $telegram_api_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($response === FALSE) {
                throw new Exception("Failed to send image to Telegram for chat_id {$chat_id}: " . $curl_error);
            }
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Image processed and sent to Telegram successfully',
            'img_path' => $relativeFilePath
        ]);
    } else {
        throw new Exception("Failed to save the image file");
    }
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Database error: ' . $e->getMessage()]));
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode(['error' => $e->getMessage()]));
}
