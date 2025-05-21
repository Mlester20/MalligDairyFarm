<?php
// Fetch data for milk records by livestock
$query = "
    SELECT 
        l.live_stock_name,
        SUM(r.quantity) AS total_quantity 
    FROM 
        milk_records r
    INNER JOIN 
        live_stocks l 
    ON 
        r.live_stock_id = l.id
    GROUP BY 
        l.live_stock_name
    ORDER BY 
        total_quantity DESC
    LIMIT 5
";

$result = $con->query($query);

$pieData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pieData[$row['live_stock_name']] = $row['total_quantity'];
    }
}

// Fetch monthly milk production data
$monthlyQuery = "
    SELECT 
        MONTHNAME(record_date) AS month, 
        SUM(quantity) AS total_quantity 
    FROM 
        milk_records 
    GROUP BY 
        MONTH(record_date)
    ORDER BY 
        MONTH(record_date)
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