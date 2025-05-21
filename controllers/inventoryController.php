<?php
session_start();
include '../components/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':

            $user_id = $_SESSION['user_id'];
            $quantity = $_POST['quantity'];
            $recorded_date = $_POST['recorded_date'];

            $stmt = $con->prepare("INSERT INTO milk_inventory (user_id, quantity, recorded_date) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $user_id, $quantity, $recorded_date);

            if ($stmt->execute()) {
                header("Location: ../admin/inventory.php?success=Record added successfully");
            } else {
                header("Location: ../admin/inventory.php?error=Failed to add record");
            }
            $stmt->close();
            break;

        case 'edit':
            $milk_id = $_POST['milk_id'];
            $user_id = $_SESSION['user_id'];
            $quantity = $_POST['quantity'];
            $recorded_date = $_POST['recorded_date'];

            $stmt = $con->prepare("UPDATE milk_inventory SET user_id = ?, quantity = ?, recorded_date = ? WHERE milk_id = ?");
            $stmt->bind_param("iisi", $user_id, $quantity, $recorded_date, $milk_id);

            if ($stmt->execute()) {
                header("Location: ../admin/inventory.php?success=Record updated successfully");
            } else {
                header("Location: ../admin/inventory.php?error=Failed to update record");
            }
            $stmt->close();
            break;

        case 'delete':
            $milk_id = $_POST['milk_id'];

            $stmt = $con->prepare("DELETE FROM milk_inventory WHERE milk_id = ?");
            $stmt->bind_param("i", $milk_id);

            if ($stmt->execute()) {
                header("Location: ../admin/inventory.php?success=Record deleted successfully");
            } else {
                header("Location: ../admin/inventory.php?error=Failed to delete record");
            }
            $stmt->close();
            break;

        default:
            header("Location: ../admin/inventory.php?error=Invalid action");
            break;
    }
}
?>