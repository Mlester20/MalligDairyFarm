<?php
session_start();
include '../components/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Pagination setup
$records_per_page = 10; // Number of records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Fetch records with pagination
$query = "
    SELECT 
        r.id AS record_id,
        r.user_id,
        u.name,
        l.live_stock_name,
        l.live_stock_code,
        r.record_date,
        r.quantity,
        r.recorded_at
    FROM 
        milk_records r
    INNER JOIN 
        users u
    ON 
        r.user_id = u.user_id
    INNER JOIN 
        live_stocks l
    ON 
        r.live_stock_id = l.id
    ORDER BY 
        r.record_date DESC
    LIMIT $offset, $records_per_page
";

$result = $con->query($query);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Get total number of records for pagination
$total_records_query = "SELECT COUNT(*) AS total FROM milk_records";
$total_records_result = $con->query($total_records_query);
$total_records = $total_records_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dairy Farm Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8d7xj1z2l4c5e5e5e5e5e5e5e5e5e5e5e5" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/search.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../style/header.css">
    <link rel="icon" href="../images/favi.png" type="image/png">
</head>
<body>
    <?php include '../components/admin_header.php'; ?>

    <div class="container mt-5">
        <h3 class="mb-4 text-center text-muted">Dairy Farm Records</h3>

        <!-- Action Buttons -->
        <div class="mb-3 d-flex justify-content-end">
            <button id="exportExcel" class="btn btn-success me-2">
                <i class="fas fa-file-excel"></i> Export to Excel
            </button>
            <button id="printPage" class="btn btn-primary">
                <i class="fas fa-print"></i> Print
            </button>
        </div>

        <!-- Add Month and Year Filters -->
        <h5 class="mb-2 text-start text-muted">Filter Specific Records</h5>
        <div class="mb-3 d-flex justify-content-start">
            <select id="filterMonth" class="form-select me-2" style="width: auto;">
                <option value="">Select Month</option>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?php echo $m; ?>"><?php echo date('F', mktime(0, 0, 0, $m, 1)); ?></option>
                <?php endfor; ?>
            </select>
            <select id="filterYear" class="form-select me-2" style="width: auto;">
                <option value="">Select Year</option>
                <?php for ($y = date('Y'); $y >= 2000; $y--): ?>
                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            <button id="applyFilter" class="btn btn-secondary">Apply Filter</button>
        </div>

        <table class="table table-bordered table-striped" id="recordsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Recorded By</th>
                    <th>Stock Name</th>
                    <th>Stock Code</th>
                    <th>Milk Quantity(Liters)</th>
                    <th>Recorded Date</th> <!-- New column for date -->
                    <th>Recorded Time</th> <!-- New column for time -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data)): ?>
                    <?php foreach ($data as $index => $record): ?>
                        <tr>
                            <td><?php echo $offset + $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($record['name']); ?></td>
                            <td><?php echo htmlspecialchars($record['live_stock_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['live_stock_code']); ?></td>
                            <td><?php echo htmlspecialchars($record['quantity']); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($record['recorded_at'])); ?></td> 
                            <td><?php echo date('h:i A', strtotime($record['recorded_at'])); ?></td> 
                            <td>
                            <button class="btn btn-warning btn-sm edit-record" data-id="<?php echo $record['record_id']; ?>" data-bs-toggle="modal" data-bs-target="#editRecordModal">
                                <i class="fas fa-pen"></i>
                            </button>
                                <button class="btn btn-danger btn-sm delete-record" data-id="<?php echo $record['record_id']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Edit Record Modal -->
       
        <div class="modal fade" id="editRecordModal" tabindex="-1" aria-labelledby="editRecordModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editRecordForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editRecordModalLabel">Edit Milk Record</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit_record_id" name="record_id">
                            <div class="mb-3">
                                <label for="edit_live_stock_id" class="form-label">Stock Name</label>
                                <select class="form-select select2-dropdown" id="edit_live_stock_id" name="live_stock_id" required>
                                    <option value="" disabled>Select Stock Name</option>
                                    <?php
                                    // Fetch live stock names from the database
                                    $query = "SELECT * FROM live_stocks";
                                    $result = mysqli_query($con, $query);
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value='{$row['id']}'>{$row['live_stock_name']} ({$row['live_stock_code']})</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="edit_quantity" class="form-label">Quantity (Liters)</label>
                                <input type="number" step="0.01" class="form-control" id="edit_quantity" name="quantity" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_record_date" class="form-label">Recorded Date</label>
                                <input type="date" class="form-control" id="edit_record_date" name="record_date" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Pagination Links -->
        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <?php include '../components/footer.php'; ?>


    <!-- scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    <!-- Scripts for Export and Print -->
    <script>
        // Export to Excel
        document.getElementById('exportExcel').addEventListener('click', function () {
            const table = document.getElementById('recordsTable');
            let tableHTML = '<table border="1">';
            tableHTML += '<tr><th>Stock Name</th><th>Stock Code</th><th>Milk Quantity</th><th>Recorded Date</th></tr>';

            // Loop through table rows and extract relevant data
            Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 0) {
                    tableHTML += `<tr>
                        <td>${cells[2].innerText}</td>
                        <td>${cells[3].innerText}</td>
                        <td>${cells[4].innerText}</td>
                        <td>${cells[5].innerText}</td>
                    </tr>`;
                }
            });

            tableHTML += '</table>';
            const filename = 'milk_records.xls';

            const downloadLink = document.createElement('a');
            downloadLink.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tableHTML);
            downloadLink.download = filename;
            downloadLink.click();
        });

        // Print Page
        document.getElementById('applyFilter').addEventListener('click', function () {
            const month = document.getElementById('filterMonth').value;
            const year = document.getElementById('filterYear').value;

            const rows = document.querySelectorAll('#recordsTable tbody tr');
            rows.forEach(row => {
                const dateCell = row.querySelector('td:nth-child(6)');
                if (dateCell) {
                    const recordDate = new Date(dateCell.innerText);
                    const recordMonth = recordDate.getMonth() + 1; // Months are 0-based
                    const recordYear = recordDate.getFullYear();

                    if (
                        (month && recordMonth != month) ||
                        (year && recordYear != year)
                    ) {
                        row.style.display = 'none';
                    } else {
                        row.style.display = '';
                    }
                }
            });
        });

        //print function
        document.getElementById('printPage').addEventListener('click', function () {
            const table = document.getElementById('recordsTable');
            let printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Dairy Farm Records</title></head><body>');
            printWindow.document.write('<h2 style="text-align: center;">Dairy Farm Records</h2>');
            printWindow.document.write('<table border="1" style="width: 100%; border-collapse: collapse;">');
            printWindow.document.write('<tr><th>Stock Name</th><th>Stock Code</th><th>Milk Quantity</th><th>Recorded Date</th></tr>');

            // Loop through visible table rows and extract relevant data
            Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = row.querySelectorAll('td');
                    if (cells.length > 0) {
                        printWindow.document.write(`<tr>
                            <td>${cells[2].innerText}</td>
                            <td>${cells[3].innerText}</td>
                            <td>${cells[4].innerText}</td>
                            <td>${cells[5].innerText}</td>
                        </tr>`);
                    }
                }
            });

            printWindow.document.write('</table></body></html>');
            printWindow.document.close();
            printWindow.print();
        });
    </script>

    <!-- script for edit and delete -->
    <script>
            $(document).ready(function () {
                // Handle delete button click
                $('.delete-record').on('click', function () {
                    const recordId = $(this).data('id');
                    const row = $(`#record-${recordId}`);

                    if (confirm('Are you sure you want to delete this record?')) {
                        $.ajax({
                            url: '../controllers/recordsController.php', // Update with the correct path
                            type: 'POST',
                            data: { action: 'delete', id: recordId },
                            success: function (response) {
                                const res = JSON.parse(response);
                                if (res.success) {
                                    row.remove();
                                    alert('Record deleted successfully.');
                                } else {
                                    alert('Failed to delete the record.');
                                }
                            },
                            error: function () {
                                alert('An error occurred while deleting the record.');
                            }
                        });
                    }
                });
            });


            // Handle edit button click
            $(document).ready(function () {
                
            $(document).on('click', '.edit-record', function () {
                const recordId = $(this).data('id');

                // Fetch record details using AJAX
                $.ajax({
                    url: '../controllers/recordsController.php',
                    method: 'GET',
                    data: { id: recordId, action: 'fetch_single' },
                    dataType: 'json',
                    success: function (record) {
                        // Populate the modal fields with the record data
                        $('#edit_record_id').val(record.record_id);
                        $('#edit_live_stock_id').val(record.live_stock_id).trigger('change'); // Set dropdown value
                        $('#edit_quantity').val(record.quantity);
                        $('#edit_record_date').val(record.record_date);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching record details:', error);
                        alert('Error fetching record details.');
                    }
                });
            });

            // Handle Edit Form Submission
            $('#editRecordForm').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();

                // Submit the updated data using AJAX
                $.ajax({
                    url: '../controllers/recordsController.php',
                    method: 'POST',
                    data: formData + '&action=update',
                    success: function (response) {
                        const res = JSON.parse(response);
                        if (res.success) {
                            alert('Record updated successfully!');
                            $('#editRecordModal').modal('hide');
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert('Failed to update the record.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error updating record:', error);
                        alert('Error updating record.');
                    }
                });
            });
        });

    </script>

</body>
</html>