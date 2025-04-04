<?php
include '../components/config.php';

// Fetch data with user name and live stock details
$query = "
    SELECT 
        r.id AS record_id,
        r.user_id,
        u.name, -- Fetch the name from the users table
        l.live_stock_name, -- Fetch the live stock name
        l.live_stock_code, -- Fetch the live stock code
        r.record_date,
        r.quantity,
        r.recorded_at
    FROM 
        milk_records r
    INNER JOIN 
        users u
    ON 
        r.user_id = u.user_id -- Join with users table using user_id
    INNER JOIN 
        live_stocks l -- Correct table name for live stocks
    ON 
        r.live_stock_id = l.id -- Join with live_stocks table using id
    ORDER BY 
        r.record_date DESC
";

$result = $con->query($query);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

?>