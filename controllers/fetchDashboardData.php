<?php
// Fetch total milk inventory
$totalMilkQuery = "SELECT SUM(quantity) AS total_quantity FROM milk_inventory";
$totalMilkResult = $con->query($totalMilkQuery);

$totalMilk = 0;
if ($totalMilkResult->num_rows > 0) {
    $row = $totalMilkResult->fetch_assoc();
    $totalMilk = $row['total_quantity'];
}

// Fetch monthly milk production data
$monthlyQuery = "
    SELECT 
        MONTHNAME(recorded_date) AS month, 
        SUM(quantity) AS total_quantity 
    FROM 
        milk_inventory 
    GROUP BY 
        MONTH(recorded_date)
    ORDER BY 
        MONTH(recorded_date)
";

$monthlyResult = $con->query($monthlyQuery);

$months = [];
$quantities = [];
if ($monthlyResult->num_rows > 0) {
    while ($row = $monthlyResult->fetch_assoc()) {
        $months[] = $row['month'];
        $quantities[] = $row['total_quantity'];
    }
}

// Fetch total users and admins
$userQuery = "SELECT role, COUNT(*) AS count FROM users GROUP BY role";
$userResult = $con->query($userQuery);

$userCounts = ['admin' => 0, 'user' => 0];
if ($userResult->num_rows > 0) {
    while ($row = $userResult->fetch_assoc()) {
        $userCounts[$row['role']] = $row['count'];
    }
}
?>