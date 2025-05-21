<?php
session_start();
include '../components/config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch inventory data
$query = "
    SELECT m.milk_id, m.user_id, u.name AS recorded_by, m.quantity, m.recorded_date
    FROM milk_inventory m
    JOIN users u ON m.user_id = u.user_id
    ORDER BY m.recorded_date DESC
";
$result = $con->query($query);

// Calculate total milk quantity
$totalQuery = "SELECT SUM(quantity) AS total_quantity FROM milk_inventory";
$totalResult = $con->query($totalQuery);
$totalMilk = $totalResult->fetch_assoc()['total_quantity'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Milk Inventory - <?php include '../components/title.php'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../style/header.css">
    <link rel="icon" href="../images/favi.png" type="image/png">
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="container mt-4">
        <h3 class="card-title text-muted text-center">Milk Inventory</h3>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addMilkModal">Add Milk Record</button>
        <table class="table mt-4 table-bordered table-striped">
            <thead>
                <tr>
                    <th>Invent By</th>
                    <th>Milk Quantity</th>
                    <th>Recorded Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['recorded_by']) ?></td>
                        <td><?= htmlspecialchars($row['quantity']) ?> liters</td>
                        <td><?= htmlspecialchars($row['recorded_date']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editMilkModal<?= $row['milk_id'] ?>"> <i class="fas fa-pencil"></i> </button>
                            <!-- <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteMilkModal<?= $row['milk_id'] ?>"> <i class="fas fa-trash"></i> </button> -->
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editMilkModal<?= $row['milk_id'] ?>" tabindex="-1" aria-labelledby="editMilkModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="../controllers/inventoryController.php" method="POST">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="milk_id" value="<?= $row['milk_id'] ?>">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">Milk Quantity (liters)</label>
                                            <input type="number" name="quantity" class="form-control" value="<?= $row['quantity'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="recorded_date" class="form-label">Recorded Date</label>
                                            <input type="date" name="recorded_date" class="form-control" value="<?= $row['recorded_date'] ?>" required>
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
                    <div class="modal fade" id="deleteMilkModal<?= $row['milk_id'] ?>" tabindex="-1" aria-labelledby="deleteMilkModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="../controllers/inventoryController.php" method="POST">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="milk_id" value="<?= $row['milk_id'] ?>">
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete this record?</p>
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
            </tbody>
        </table>
        <h5 class="text-end text-muted mt-3">Total Milk: <?= $totalMilk ?> liters</h5>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addMilkModal" tabindex="-1" aria-labelledby="addMilkModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="../controllers/inventoryController.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMilkModalLabel">Add Milk Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Milk Quantity (liters)</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="recorded_date" class="form-label">Recorded Date</label>
                            <input type="date" name="recorded_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Record</button>
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
</body>
</html>