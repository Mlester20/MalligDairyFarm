<?php
include '../components/config.php';

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