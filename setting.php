<?php
include "navbar.php";
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    $stmt = $conn->prepare("
    SELECT 
        n.chat_id, 
        n.chat_name, 
        n.device_id, 
        d.device_name,       -- เพิ่ม device_name จากตาราง device
        n.timestamp, 
        u.name, 
        u.email
    FROM notify n
    JOIN users u ON n.user_id = u.user_id
    JOIN device d ON n.device_id = d.device_id  -- เพิ่มการ JOIN กับตาราง device
    WHERE u.email = :email
    ");

    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email ");
    $stmt->bindParam(':email', $email);
$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "ไม่มีการตั้งค่า email ใน session.";
}

if (isset($_POST['delete'])) {
    $chat_id_to_delete = $_POST['chat_id'];

    $delete_stmt = $conn->prepare("DELETE FROM notify WHERE chat_id = :chat_id");
    $delete_stmt->bindParam(':chat_id', $chat_id_to_delete);
    if ($delete_stmt->execute()) {
        echo "<script>alert('ลบข้อมูลแชทสำเร็จ'); window.location = window.location.href;</script>";
        exit;
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการลบข้อมูล'); window.location = window.location.href;</script>";
    }
}


?>

<div class="container mt-5">
    <h2 class = "text-center">ข้อมูลรับแจ้งเตือน</h2>

    <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $notify): ?>
            <div class="notify-setting">
                <div class="mb-3">
                    <label class="form-label">รหัสแชทเเจ้งเตือน</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="chat_id" value="<?php echo htmlspecialchars($notify['chat_id']); ?>" readonly required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">ชื่อ Telegram</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="chat_name" value="<?php echo htmlspecialchars($notify['chat_name']); ?>" readonly required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">อุปกรณ์รับการแจ้งเตือน</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="device_name" value="<?php echo htmlspecialchars($notify['device_name']); ?>" readonly required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">อีเมลล์</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="email" value="<?php echo htmlspecialchars($notify['email']); ?>" readonly required>
                    </div>
                </div>
                <div class="mb-3">
                    <form action="" method="POST">
                        <input type="hidden" name="chat_id" value="<?php echo htmlspecialchars($notify['chat_id']); ?>"> <!-- ใช้ chat_id แท้จริง -->
                        <button type="submit" name="delete" class="btn btn-danger">ลบ</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center">ไม่มีข้อมูลแจ้งเตือนสำหรับผู้ใช้ที่เลือก</p>
    <?php endif; ?>

</div>

<?php
include "flooter.php";
?>
