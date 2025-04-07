<?php
session_start();
include '../components/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location: ../index.php');
    exit;
}

$limit = 5; // Number of entries per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get the total number of records
$query = "SELECT COUNT(*) FROM live_stocks";
$result = mysqli_query($con, $query);
$total_records = mysqli_fetch_array($result)[0];
$total_pages = ceil($total_records / $limit);

// Get the live stock records with pagination
$query = "SELECT * FROM live_stocks LIMIT $limit OFFSET $offset";
$result = mysqli_query($con, $query);
$live_stocks = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Live Stocks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../style/header.css">
    <link rel="stylesheet" href="../style/dashboard_responsive.css">
    <link rel="icon" href="../images/favi.png" type="image/png">
</head>
<body>

    <?php include '../components/admin_header.php'; ?>

    <div class="container mt-5">
        <h3 class="text-center mb-4 text-muted"><i class="fas fa-magnifying-glass">Manage Live Stocks</i></h3>
        <div class="row mb-3">
            <div class="col-md-2">
                <input type="text" id="searchInput" class="form-control" placeholder="Search Live Stocks...">
            </div>
            <div class="col text-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLiveStockModal">Add Live Stock</button>
            </div>
        </div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Live Stock Name</th>
                    <th>Live Stock Code</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="liveStockTable">
                <?php foreach ($live_stocks as $stock): ?>
                    <tr>
                        <td><?php echo $stock['id']; ?></td>
                        <td><?php echo $stock['live_stock_name']; ?></td>
                        <td><?php echo $stock['live_stock_code']; ?></td>
                        <td><?php echo $stock['created_at']; ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-btn" data-id="<?php echo $stock['id']; ?>" data-name="<?php echo $stock['live_stock_name']; ?>" data-code="<?php echo $stock['live_stock_code']; ?>"><i class="fas fa-pencil"></i></button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $stock['id']; ?>"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Add Live Stock Modal -->
    <div class="modal fade" id="addLiveStockModal" tabindex="-1" aria-labelledby="addLiveStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addLiveStockForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLiveStockModalLabel">Add Live Stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="live_stock_name" class="form-label">Live Stock Name</label>
                            <input type="text" class="form-control" id="live_stock_name" name="live_stock_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="live_stock_code" class="form-label">Live Stock Code</label>
                            <input type="text" class="form-control" id="live_stock_code" name="live_stock_code" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Live Stock</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Live Stock Modal -->
    <div class="modal fade" id="editLiveStockModal" tabindex="-1" aria-labelledby="editLiveStockModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editLiveStockForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editLiveStockModalLabel">Edit Live Stock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_live_stock_id" name="id">
                        <div class="mb-3">
                            <label for="edit_live_stock_name" class="form-label">Live Stock Name</label>
                            <input type="text" class="form-control" id="edit_live_stock_name" name="live_stock_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_live_stock_code" class="form-label">Live Stock Code</label>
                            <input type="text" class="form-control" id="edit_live_stock_code" name="live_stock_code" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/search_stocks.js"></script>
    <script>
        $(document).ready(function () {
            // Handle Add Live Stock Form Submission
            $('#addLiveStockForm').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: '../controllers/save_stocksController.php',
                    method: 'POST',
                    data: formData + '&action=add',
                    success: function (response) {
                        alert('Live stock added successfully!');
                        $('#addLiveStockModal').modal('hide');
                        $('#addLiveStockForm')[0].reset();
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        console.error('Error adding live stock:', error);
                    }
                });
            });

            // Handle Edit Button Click
            $(document).on('click', '.edit-btn', function () {
                const id = $(this).data('id');
                const name = $(this).data('name');
                const code = $(this).data('code');

                $('#edit_live_stock_id').val(id);
                $('#edit_live_stock_name').val(name);
                $('#edit_live_stock_code').val(code);

                $('#editLiveStockModal').modal('show');
            });

            // Handle Edit Live Stock Form Submission
            $('#editLiveStockForm').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: '../controllers/save_stocksController.php',
                    method: 'POST',
                    data: formData + '&action=edit',
                    success: function (response) {
                        alert('Live stock updated successfully!');
                        $('#editLiveStockModal').modal('hide');
                        location.reload();
                    },
                    error: function (xhr, status, error) {
                        console.error('Error updating live stock:', error);
                    }
                });
            });

            // Handle Delete Button Click
            $(document).on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this live stock?')) {
                    $.ajax({
                        url: '../controllers/save_stocksController.php',
                        method: 'POST',
                        data: { id: id, action: 'delete' },
                        success: function (response) {
                            alert('Live stock deleted successfully!');
                            location.reload();
                        },
                        error: function (xhr, status, error) {
                            console.error('Error deleting live stock:', error);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
