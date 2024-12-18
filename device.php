<?php
include "navbar.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO device (device_name, device_key) VALUES (:device_name, :device_key)");
        $stmt->execute(['device_name' => $_POST['device_name'], 'device_key' => $_POST['device_key']]);
    } elseif (isset($_POST['edit'])) {
        $stmt = $conn->prepare("UPDATE device SET device_name = :device_name, device_key = :device_key WHERE device_id = :device_id");
        $stmt->execute(['device_name' => $_POST['device_name'], 'device_key' => $_POST['device_key'], 'device_id' => $_POST['device_id']]);
    } elseif (isset($_POST['delete'])) {
        $stmt = $conn->prepare("DELETE FROM device WHERE device_id = :device_id");
        $stmt->execute(['device_id' => $_POST['device_id']]);
    }
}


$stmt = $conn->prepare("SELECT * FROM device ORDER BY timestamp DESC");
$stmt->execute();
$devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    .container {
        margin-top: 40px;
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .table th,
    .table td {
        vertical-align: middle;
    }
</style>
</head>
<script>
    function generateKey() {
        const characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let key = '';
        for (let i = 0; i < 20; i++) {
            key += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        document.getElementById('deviceKey').value = key;
    }

    function generateKeyedit(id) {
        const characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let key = '';
        for (let i = 0; i < 20; i++) {
            key += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        const inputField = document.getElementById(`editDeviceKey${id}`);
        if (inputField) {
            inputField.value = key; 
        } else {
            console.error(`Input field with ID "editDeviceKey${id}" not found.`);
        }
    }
</script>

<body>
    <div class="container">
        <h1 class="mb-4 text-center">จัดการอุปกรณ์</h1>

        <button class="btn btn-success mb-4" data-bs-toggle="modal" data-bs-target="#addModal">เพิ่มอุปกรณ์</button>

        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">เพิ่มอุปกรณ์ใหม่</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">ชื่ออุปกรณ์</label>
                                <input type="text" class="form-control" name="device_name" placeholder="Enter device name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">รหัสการเชื่อมต่อ</label>
                                <div class="input-group">
                                    <input type="text" id="deviceKey" class="form-control" name="device_key" readonly required>
                                    <button type="button" class="btn btn-secondary" onclick="generateKey()">สร้างรหัส</button>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="add" class="btn btn-success">บันทึก</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>ชื่ออุปกรณ์</th>
                    <th>รหัสการเชื่อมต่อ</th>

                    <?php
                    if (isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin') {
                    ?>
                        <th>Actions</th>
                    <?php
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($devices as $device): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($device['device_name']); ?></td>
                        <td><?php echo htmlspecialchars($device['device_key'] ?? ''); ?></td>
                        <?php
                        if (isset($_SESSION['roles']) && $_SESSION['roles'] === 'admin') {
                        ?>

                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="device_id" value="<?php echo $device['device_id']; ?>">
                                    <button type="submit" name="delete" class="btn btn-danger btn-sm">ลบ</button>
                                </form>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $device['device_id']; ?>">แก้ไข</button>
                                <div class="modal fade" id="editModal<?php echo $device['device_id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $device['device_id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">แก้ไขอุปกรณ์</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="device_id" value="<?php echo $device['device_id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">ชื่ออุปกรณ์</label>
                                                        <input type="text" class="form-control" name="device_name" value="<?php echo htmlspecialchars($device['device_name']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">รหัสการเชื่อมต่อ</label>
                                                        <div class="input-group">
                                                            <input type="text" id="editDeviceKey<?php echo $device['device_id']; ?>" class="form-control" name="device_key" value="<?php echo htmlspecialchars($device['device_key']); ?>" readonly required>
                                                            <button type="button" class="btn btn-secondary" onclick="generateKeyedit('<?php echo $device['device_id']; ?>')">สร้างรหัส</button>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="edit" class="btn btn-primary">บันทึก</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        <?php
                        }

                        ?>
                    </tr>
                <?php endforeach; ?>


            </tbody>
        </table>
    </div>




    <?php
    include "flooter.php";
    $conn = null;
    ?>