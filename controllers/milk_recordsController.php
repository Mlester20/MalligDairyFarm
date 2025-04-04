<?php
include '../components/config.php';
session_start(); // Ensure session is started to access $_SESSION['user_id']

// Fetch milk records
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'fetch') {
    $query = "SELECT mr.id, mr.record_date, mr.quantity, ls.live_stock_name, ls.live_stock_code, u.username 
              FROM milk_records mr 
              JOIN live_stocks ls ON mr.live_stock_id = ls.id
              JOIN users u ON mr.user_id = u.user_id"; // Join with users table to get the username
    $result = mysqli_query($con, $query);
    $records = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $records[] = $row;
    }
    echo json_encode($records);
    exit;
}

// Add a new milk record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $live_stock_id = $_POST['live_stock_id'];
    $record_date = $_POST['record_date'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID

    $query = "INSERT INTO milk_records (user_id, live_stock_id, record_date, quantity, recorded_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        echo "Prepare failed: (" . mysqli_errno($con) . ") " . mysqli_error($con);
        exit;
    }
    
    mysqli_stmt_bind_param($stmt, "iisd", $user_id, $live_stock_id, $record_date, $quantity);
    
    if (!mysqli_stmt_execute($stmt)) {
        echo "Execute failed: (" . mysqli_stmt_errno($stmt) . ") " . mysqli_stmt_error($stmt);
        exit;
    }
    
    echo 'success';
    exit;
}

// Delete a milk record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $id = $_POST['id'];

    $query = "DELETE FROM milk_records WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    echo 'success';
    exit;
}

// Edit an existing milk record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $live_stock_id = $_POST['live_stock_id'];
    $record_date = $_POST['record_date'];
    $quantity = $_POST['quantity'];

    $query = "UPDATE milk_records SET live_stock_id = ?, record_date = ?, quantity = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "isdi", $live_stock_id, $record_date, $quantity, $id);
    mysqli_stmt_execute($stmt);
    echo 'success';
    exit;
}
?>