<?php
require_once 'connect.php';

header('Content-Type: application/json');

$days = isset($_GET['days']) ? intval($_GET['days']) : 7;
$startDate = date('Y-m-d', strtotime("-$days days"));

$query = $conn->prepare("SELECT DATE(timestamp) as date, COUNT(*) as count FROM imagedevice 
                         WHERE timestamp >= :startDate 
                         GROUP BY DATE(timestamp)");
$query->bindValue(':startDate', $startDate, PDO::PARAM_STR);
$query->execute();

$data = $query->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($data);
?>
