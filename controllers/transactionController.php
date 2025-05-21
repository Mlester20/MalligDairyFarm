<?php
include '../components/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $milk_id = $_POST['milk_id'];
            $name = $_POST['name'];
            $transaction_type = $_POST['transaction_type'];
            $quantity = $_POST['quantity'];
            $transaction_date = $_POST['transaction_date'];

            // Start a transaction
            $con->begin_transaction();

            try {
                // Check if there's enough milk for order/distributed transactions
                if ($transaction_type === 'order' || $transaction_type === 'distributed') {
                    $checkStmt = $con->prepare("SELECT quantity FROM milk_inventory WHERE milk_id = ?");
                    $checkStmt->bind_param("i", $milk_id);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();
                    $row = $result->fetch_assoc();
                    $currentQuantity = $row['quantity'];
                    $checkStmt->close();

                    if ($currentQuantity < $quantity) {
                        // Not enough milk, rollback and return error
                        $con->rollback();
                        header("Location: ../admin/transaction.php?error=Not enough milk available. Available: $currentQuantity liters");
                        exit;
                    }
                }

                // Insert the transaction
                $stmt = $con->prepare("INSERT INTO transaction (milk_id, name, transaction_type, quantity, transaction_date) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issds", $milk_id, $name, $transaction_type, $quantity, $transaction_date);
                $stmt->execute();
                $stmt->close();

                // Update the milk inventory based on the transaction type
                if ($transaction_type === 'order' || $transaction_type === 'distributed') {
                    $updateStmt = $con->prepare("UPDATE milk_inventory SET quantity = quantity - ? WHERE milk_id = ?");
                    $updateStmt->bind_param("di", $quantity, $milk_id);
                } elseif ($transaction_type === 'restock') {
                    $updateStmt = $con->prepare("UPDATE milk_inventory SET quantity = quantity + ? WHERE milk_id = ?");
                    $updateStmt->bind_param("di", $quantity, $milk_id);
                }
                $updateStmt->execute();
                $updateStmt->close();

                // Commit the transaction
                $con->commit();
                header("Location: ../admin/transaction.php?success=Transaction added successfully");
            } catch (Exception $e) {
                // Rollback the transaction on error
                $con->rollback();
                header("Location: ../admin/transaction.php?error=Failed to add transaction: " . $e->getMessage());
            }
            break;

        case 'edit':
            $transaction_id = $_POST['transaction_id'];
            $original_milk_id = $_POST['original_milk_id'];
            $original_quantity = $_POST['original_quantity'];
            $original_type = $_POST['original_type'];
            
            $milk_id = $_POST['milk_id'];
            $name = $_POST['name'];
            $transaction_type = $_POST['transaction_type'];
            $quantity = $_POST['quantity'];
            $transaction_date = $_POST['transaction_date'];

            // Start a transaction
            $con->begin_transaction();

            try {
                // Reverse the effect of the original transaction
                if ($original_type === 'order' || $original_type === 'distributed') {
                    $reverseStmt = $con->prepare("UPDATE milk_inventory SET quantity = quantity + ? WHERE milk_id = ?");
                    $reverseStmt->bind_param("di", $original_quantity, $original_milk_id);
                } else { // restock
                    $reverseStmt = $con->prepare("UPDATE milk_inventory SET quantity = quantity - ? WHERE milk_id = ?");
                    $reverseStmt->bind_param("di", $original_quantity, $original_milk_id);
                }
                $reverseStmt->execute();
                $reverseStmt->close();

                // Check if there's enough milk for the new transaction if it's an order/distributed
                if ($transaction_type === 'order' || $transaction_type === 'distributed') {
                    $checkStmt = $con->prepare("SELECT quantity FROM milk_inventory WHERE milk_id = ?");
                    $checkStmt->bind_param("i", $milk_id);
                    $checkStmt->execute();
                    $result = $checkStmt->get_result();
                    $row = $result->fetch_assoc();
                    $currentQuantity = $row['quantity'];
                    $checkStmt->close();

                    if ($currentQuantity < $quantity) {
                        // Restore original transaction effect and return error
                        if ($original_type === 'order' || $original_type === 'distributed') {
                            $restoreStmt = $con->prepare("UPDATE milk_inventory SET quantity = quantity - ? WHERE milk_id = ?");
                            $restoreStmt->bind_param("di", $original_quantity, $original_milk_id);
                        } else { // restock
                            $restoreStmt = $con->prepare("UPDATE milk_inventory SET quantity = quantity + ? WHERE milk_id = ?");
                            $restoreStmt->bind_param("di", $original_quantity, $original_milk_id);
                        }
                        $restoreStmt->execute();
                        $restoreStmt->close();
                        
                        $con->rollback();
                        header("Location: ../admin/transaction.php?error=Not enough milk available. Available: $currentQuantity liters");
                        exit;
                    }
                }

                // Update the transaction record
                $stmt = $con->prepare("UPDATE transaction SET milk_id = ?, name = ?, transaction_type = ?, quantity = ?, transaction_date = ? WHERE transaction_id = ?");
                $stmt->bind_param("issdsi", $milk_id, $name, $transaction_type, $quantity, $transaction_date, $transaction_id);
                $stmt->execute();
                $stmt->close();

                // Apply the effect of the new transaction
                if ($transaction_type === 'order' || $transaction_type === 'distributed') {
                    $applyStmt = $con->prepare("UPDATE milk_inventory SET quantity = quantity - ? WHERE milk_id = ?");
                    $applyStmt->bind_param("di", $quantity, $milk_id);
                } else { // restock
                    $applyStmt = $con->prepare("UPDATE milk_inventory SET quantity = quantity + ? WHERE milk_id = ?");
                    $applyStmt->bind_param("di", $quantity, $milk_id);
                }
                $applyStmt->execute();
                $applyStmt->close();

                // Commit the transaction
                $con->commit();
                header("Location: ../admin/transaction.php?success=Transaction updated successfully");
            } catch (Exception $e) {
                // Rollback the transaction on error
                $con->rollback();
                header("Location: ../admin/transaction.php?error=Failed to update transaction: " . $e->getMessage());
            }
            break;

        case 'delete':
            $transaction_id = $_POST['transaction_id'];
            $milk_id = $_POST['milk_id'];
            $quantity = $_POST['quantity'];
            $transaction_type = $_POST['transaction_type'];

            // Start a transaction
            $con->begin_transaction();

            try {
                // Reverse the effect of the transaction on milk inventory
                if ($transaction_type === 'order' || $transaction_type === 'distributed') {
                    // If it was an order/distributed, add the quantity back to inventory
                    $updateStmt = $con->prepare("UPDATE milk_inventory SET quantity = quantity + ? WHERE milk_id = ?");
                } else { // restock
                    // If it was a restock, subtract the quantity from inventory
                    $updateStmt = $con->prepare("UPDATE milk_inventory SET quantity = quantity - ? WHERE milk_id = ?");
                }
                $updateStmt->bind_param("di", $quantity, $milk_id);
                $updateStmt->execute();
                $updateStmt->close();

                // Delete the transaction
                $stmt = $con->prepare("DELETE FROM transaction WHERE transaction_id = ?");
                $stmt->bind_param("i", $transaction_id);
                $stmt->execute();
                $stmt->close();

                // Commit the transaction
                $con->commit();
                header("Location: ../admin/transaction.php?success=Transaction deleted successfully");
            } catch (Exception $e) {
                // Rollback the transaction on error
                $con->rollback();
                header("Location: ../admin/transaction.php?error=Failed to delete transaction: " . $e->getMessage());
            }
            break;

        default:
            header("Location: ../admin/transaction.php?error=Invalid action");
            break;
    }
} else {
    header("Location: ../admin/transaction.php?error=Invalid request method");
}
?>