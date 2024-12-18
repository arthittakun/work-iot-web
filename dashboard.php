<?php 
include "navbar.php";
if (isset($_SESSION['token']) && $_SESSION['token'] !== null && $_SESSION['token'] !== "") {
  $chat_id = $_SESSION['token'];
  $chat_name = $_SESSION['fullname'];
  $email = $_SESSION['email'];
  $sql = "UPDATE users 
          SET 
              chat_name = :chat_name, 
              chat_id = :chat_id, 
              updated_at = CURRENT_TIMESTAMP 
          WHERE email = :email";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':chat_name', $chat_name);
  $stmt->bindParam(':chat_id', $chat_id);
  $stmt->bindParam(':email', $email);
  
  if($stmt->execute()){
    $response = [];
    $chat_id = $_SESSION['token'];
    $response = [
      "chat_id" => $chat_id,
      "text" => "เข้าสู่ระบบเรียบร้อย!"
  ];
  file_get_contents("https://api.telegram.org/bot".tokentelegram."/sendMessage?" . http_build_query($response));
  unset($_SESSION['token']);
  }
}
?>
<style>
.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.filter-section {
    margin-bottom: 30px;
    padding: 20px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.filter-section label {
    font-weight: bold;
    margin-right: 10px;
    color: #333;
}

.filter-section select {
    padding: 8px 15px;
    border: 2px solid #ddd;
    border-radius: 6px;
    background-color: white;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-section select:hover {
    border-color: #4a90e2;
}

.filter-section select:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 2px rgba(74,144,226,0.2);
}

.date-inputs {
    display: inline-block;
    margin-left: 15px;
}

.date-inputs input[type="date"] {
    padding: 8px 12px;
    border: 2px solid #ddd;
    border-radius: 6px;
    margin: 0 5px;
}

.charts-container {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.chart-wrapper {
    flex: 1;
    min-width: 300px;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

canvas {
    width: 100% !important;
    height: 300px !important;
    margin: auto;
    display: block;
}

@media (max-width: 768px) {
    .charts-container {
        flex-direction: column;
    }
    
    .chart-wrapper {
        width: 100%;
    }
}
</style>

<div class="container">
    <h3>การตรวจจับการเคลื่อนไหว</h3>
    <div class="filter-section">
        <label for="days">Select Days:</label>
        <select id="days" onchange="fetchData()">
            <option value="1">1 Day</option>
            <option value="7" selected>7 Days</option>
            <option value="30">30 Days</option>
            <option value="custom">Custom Range</option>
        </select>
        <div class="date-inputs">
            <input type="date" id="custom-start" style="display: none;" onchange="fetchData()">
            <input type="date" id="custom-end" style="display: none;" onchange="fetchData()">
        </div>
    </div>
    
    <div class="charts-container">
        <div class="chart-wrapper">
            <canvas id="barChart"></canvas>
        </div>
        <div class="chart-wrapper">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>

  <script>
    let barChart, pieChart;

    async function fetchData() {
      const daysSelector = document.getElementById('days');
      const customStart = document.getElementById('custom-start');
      const customEnd = document.getElementById('custom-end');
      let days = daysSelector.value;
      
      let url = 'chart.php'; // URL ของ API ที่เขียนไว้ใน PHP

      if (days === 'custom') {
        customStart.style.display = 'inline';
        customEnd.style.display = 'inline';
        if (customStart.value && customEnd.value) {
          url += `?start=${customStart.value}&end=${customEnd.value}`;
        } else {
          return;
        }
      } else {
        customStart.style.display = 'none';
        customEnd.style.display = 'none';
        url += `?days=${days}`;
      }

      const response = await fetch(url);
      const data = await response.json();
      updateCharts(data);
    }

    function updateCharts(data) {
      const labels = data.map(item => item.date);
      const counts = data.map(item => item.count);

      // Update Bar Chart
      if (barChart) barChart.destroy();
      const ctxBar = document.getElementById('barChart').getContext('2d');
      barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Images per Day',
            data: counts,
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });

      // Update Pie Chart
      if (pieChart) pieChart.destroy();
      const ctxPie = document.getElementById('pieChart').getContext('2d');
      pieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
          labels: labels,
          datasets: [{
            label: 'Images Distribution',
            data: counts,
            backgroundColor: labels.map(() => `hsl(${Math.random() * 360}, 70%, 70%)`)
          }]
        }
      });
    }

    // Initial Fetch
    fetchData();
  </script>

<?php 
include "flooter.php";
?>