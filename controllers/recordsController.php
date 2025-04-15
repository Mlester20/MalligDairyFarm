<?php
include '../components/config.php';


//update record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $recordId = intval($_POST['record_id']);
    $quantity = floatval($_POST['quantity']);
    $recordDate = $_POST['record_date'];

    $query = "UPDATE milk_records SET quantity = ?, record_date = ? WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('dsi', $quantity, $recordDate, $recordId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'fetch_single') {
    $recordId = intval($_GET['id']);

    $query = "
        SELECT 
            r.id AS record_id,
            r.live_stock_id,
            l.live_stock_name,
            r.quantity,
            r.record_date
        FROM 
            milk_records r
        INNER JOIN 
            live_stocks l
        ON 
            r.live_stock_id = l.id
        WHERE 
            r.id = ?
    ";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $recordId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    exit;
}


//Handle the delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $recordId = intval($_POST['id']);

    $query = "DELETE FROM milk_records WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $recordId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    exit;
}
?>