<?php
include "navbar.php";
$sql = "SELECT img_id, img, imagedevice.device_id, imagedevice.timestamp AS img_timestamp, text, device.device_name
        FROM imagedevice
        JOIN device ON imagedevice.device_id = device.device_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
?>

<style>
   .container ul {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        padding: 0;
        list-style: none;
    }
   .container li {
        border: 1px solid #ccc;
        border-radius: 8px;
        overflow: hidden;
        background-color: #f9f9f9;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
   .container img {
        width: 100%;
        height: auto;
        cursor: pointer;
    }
   .container .content {
        padding: 12px;
    }
    .container {
        margin-top: 40px;
    }
</style>

<div class="container">
    <ul>
        <?php if ($stmt->rowCount() > 0): ?>
            <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <li>
                    <img src="<?php echo htmlspecialchars($row['img']); ?>" alt="Image <?php echo $row['img_id']; ?>" 
                         data-bs-toggle="modal" data-bs-target="#imageModal" 
                         data-bs-img="<?php echo htmlspecialchars($row['img']); ?>" 
                         data-bs-caption="<?php echo htmlspecialchars($row['text']); ?>" 
                         data-bs-timestamp="<?php echo $row['img_timestamp']; ?>" 
                         data-bs-name="<?php echo htmlspecialchars($row['device_name']); ?>">
                    <div class="content">
                        <b>ชื่ออุปกรณ์: <?php echo $row['device_name']; ?></b>
                        <p><?php echo htmlspecialchars($row['text']); ?></p>
                        <small><?php echo $row['img_timestamp']; ?></small>
                    </div>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li>No data available</li>
        <?php endif; ?>
    </ul>
</div>

<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" alt="Large Image" class="img-fluid">
        <p id="modalCaption"></p>
        <small id="modalTimestamp"></small>
      </div>
    </div>
  </div>
</div>

<script>
    var imageModal = document.getElementById('imageModal');
    imageModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; 
        var imgSrc = button.getAttribute('data-bs-img'); 
        var caption = button.getAttribute('data-bs-caption'); 
        var timestamp = button.getAttribute('data-bs-timestamp'); 
        var deviceName = button.getAttribute('data-bs-name'); 

        var modalImage = imageModal.querySelector('#modalImage');
        var modalCaption = imageModal.querySelector('#modalCaption');
        var modalTimestamp = imageModal.querySelector('#modalTimestamp');
        var imageModalLabel = imageModal.querySelector('#imageModalLabel');
        modalImage.src = imgSrc;
        modalCaption.textContent = caption;
        modalTimestamp.textContent = timestamp;
        imageModalLabel.textContent = deviceName;
    });
</script>

<?php
include "flooter.php";
$conn = null;
?>
