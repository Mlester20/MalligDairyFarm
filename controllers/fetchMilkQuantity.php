<?php
include '../components/config.php';

$query = "SELECT SUM(quantity) AS total_quantity FROM milk_records";
$result = $con->query($query);

$total = 0;
if ($result && $row = $result->fetch_assoc()) {
    $total = $row['total_quantity'] ?? 0;
}

header('Content-Type: application/json');
echo json_encode(['total_quantity' => $total]);
?>