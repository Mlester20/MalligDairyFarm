<?php
session_start();
include '../components/config.php';

// Fetch transactions data
$query = "
    SELECT t.transaction_id, t.milk_id, m.quantity as inventory_quantity, t.name, t.transaction_type, t.quantity, t.transaction_date
    FROM transaction t
    LEFT JOIN milk_inventory m ON t.milk_id = m.milk_id
    ORDER BY t.transaction_date DESC
";
$result = $con->query($query);

// Fetch available milk inventory options
$milkInventoryQuery = "SELECT milk_id, quantity FROM milk_inventory";
$milkInventoryResult = $con->query($milkInventoryQuery);
$milkInventories = [];
$totalMilk = 0;

if ($milkInventoryResult && $milkInventoryResult->num_rows > 0) {
    while ($row = $milkInventoryResult->fetch_assoc()) {
        $milkInventories[$row['milk_id']] = $row;
        $totalMilk += floatval($row['quantity']);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - <?php include '../components/title.php'; ?> </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../style/header.css">
    <link rel="icon" href="../images/favi.png" type="image/png">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="container mt-4">
        <h3 class="text-center text-muted">Milk Orders Transaction</h3>
        <h5 class="text-end text-muted">Total Milk Available: <?= number_format($totalMilk, 2) ?> liters</h5>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTransactionModal">Add Transaction</button>
        <table class="table mt-4 table-bordered table-striped">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Milk Inventory</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['transaction_id']) ?></td>
                            <td>Inventory #<?= htmlspecialchars($row['milk_id']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td class="<?= $row['transaction_type'] === 'restock' ? 'text-success' : ($row['transaction_type'] === 'order' || $row['transaction_type'] === 'distributed' ? 'text-danger' : '') ?>">
                                <?= ucfirst(htmlspecialchars($row['transaction_type'])) ?>
                            </td>
                            <td><?= htmlspecialchars($row['quantity']) ?> liters</td>
                            <td><?= htmlspecialchars($row['transaction_date']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTransactionModal<?= $row['transaction_id'] ?>">Edit</button>
                                <!-- <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteTransactionModal<?= $row['transaction_id'] ?>">Delete</button> -->
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editTransactionModal<?= $row['transaction_id'] ?>" tabindex="-1" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editTransactionModalLabel">Edit Transaction</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="../controllers/transactionController.php" method="POST">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="transaction_id" value="<?= $row['transaction_id'] ?>">
                                        <input type="hidden" name="original_milk_id" value="<?= $row['milk_id'] ?>">
                                        <input type="hidden" name="original_quantity" value="<?= $row['quantity'] ?>">
                                        <input type="hidden" name="original_type" value="<?= $row['transaction_type'] ?>">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="milk_id" class="form-label">Milk Inventory</label>
                                                <select name="milk_id" class="form-select" required>
                                                    <?php foreach ($milkInventories as $milk_id => $inventory): ?>
                                                        <option value="<?= $milk_id ?>" <?= $row['milk_id'] == $milk_id ? 'selected' : '' ?>>
                                                            Inventory #<?= $milk_id ?> (Available: <?= $inventory['quantity'] ?> liters)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name']) ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="transaction_type" class="form-label">Transaction Type</label>
                                                <select name="transaction_type" class="form-select" required>
                                                    <option value="order" <?= $row['transaction_type'] === 'order' ? 'selected' : '' ?>>Order</option>
                                                    <option value="restock" <?= $row['transaction_type'] === 'restock' ? 'selected' : '' ?>>Restock</option>
                                                    <option value="distributed" <?= $row['transaction_type'] === 'distributed' ? 'selected' : '' ?>>Distributed</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="quantity" class="form-label">Quantity (liters)</label>
                                                <input type="number" name="quantity" class="form-control" value="<?= $row['quantity'] ?>" min="0.01" step="0.01" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="transaction_date" class="form-label">Transaction Date</label>
                                                <input type="datetime-local" name="transaction_date" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($row['transaction_date'])) ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteTransactionModal<?= $row['transaction_id'] ?>" tabindex="-1" aria-labelledby="deleteTransactionModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteTransactionModalLabel">Delete Transaction</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="../controllers/transactionController.php" method="POST">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="transaction_id" value="<?= $row['transaction_id'] ?>">
                                        <input type="hidden" name="milk_id" value="<?= $row['milk_id'] ?>">
                                        <input type="hidden" name="quantity" value="<?= $row['quantity'] ?>">
                                        <input type="hidden" name="transaction_type" value="<?= $row['transaction_type'] ?>">
                                        <div class="modal-body">
                                            <p>Are you sure you want to delete this transaction?</p>
                                            <p class="text-danger">This will also update the milk inventory accordingly.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No transactions found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTransactionModalLabel">Add New Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../controllers/transactionController.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="milk_id" class="form-label">Milk Inventory</label>
                            <select name="milk_id" class="form-select" required>
                                <?php foreach ($milkInventories as $milk_id => $inventory): ?>
                                                                            <option value="<?= $milk_id ?>">
                                            Inventory #<?= $milk_id ?> (Available: <?= number_format($inventory['quantity'], 2) ?> liters)
                                        </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Customer's Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="transaction_type" class="form-label">Transaction Type: </label>
                            <select name="transaction_type" class="form-select" style="width: 40%;" required>
                                <option value="order">Order</option>
                                <option value="restock">Restock</option>
                                <option value="distributed">Distributed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity (liters)</label>
                            <input type="number" name="quantity" class="form-control" min="0.01" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="transaction_date" class="form-label">Transaction Date</label>
                            <input type="datetime-local" name="transaction_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Transaction</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 properly with correct parent for each select
            $('.modal select').each(function() {
                $(this).select2({
                    dropdownParent: $(this).closest('.modal')
                });
            });
        });
    </script>
</body>
</html>