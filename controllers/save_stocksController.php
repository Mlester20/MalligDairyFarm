<?php
include '../components/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'fetch') {
    $query = "SELECT * FROM live_stocks";
    $result = mysqli_query($con, $query);
    $stocks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stocks[] = $row;
    }
    echo json_encode($stocks);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $live_stock_name = $_POST['live_stock_name'];
    $live_stock_code = $_POST['live_stock_code'];

    $query = "INSERT INTO live_stocks (live_stock_name, live_stock_code, created_at) VALUES (?, ?, NOW())";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ss", $live_stock_name, $live_stock_code);
    mysqli_stmt_execute($stmt);
    echo 'success';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $id = $_POST['id'];
    $live_stock_name = $_POST['live_stock_name'];
    $live_stock_code = $_POST['live_stock_code'];

    $query = "UPDATE live_stocks SET live_stock_name = ?, live_stock_code = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssi", $live_stock_name, $live_stock_code, $id);
    mysqli_stmt_execute($stmt);
    echo 'success';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $id = $_POST['id'];

    $query = "DELETE FROM live_stocks WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    echo 'success';
    exit;
}
?>