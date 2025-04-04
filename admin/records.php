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

        <table class="table table-bordered table-striped" id="recordsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Recorded By</th>
                    <th>Stock Name</th>
                    <th>Stock Code</th>
                    <th>Milk Quantity</th>
                    <th>Recorded Date</th>
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
                            <td><?php echo htmlspecialchars($record['record_date']); ?></td>
                            <td>
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
        document.getElementById('printPage').addEventListener('click', function () {
            const table = document.getElementById('recordsTable');
            let printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Dairy Farm Records</title></head><body>');
            printWindow.document.write('<h3 style="text-align: center;">Dairy Farm Records</h3>');
            printWindow.document.write('<table border="1" style="width: 100%; border-collapse: collapse;">');
            printWindow.document.write('<tr><th>Stock Name</th><th>Stock Code</th><th>Milk Quantity</th><th>Recorded Date</th></tr>');

            // Loop through table rows and extract relevant data
            Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 0) {
                    printWindow.document.write(`<tr>
                        <td>${cells[2].innerText}</td>
                        <td>${cells[3].innerText}</td>
                        <td>${cells[4].innerText}</td>
                        <td>${cells[5].innerText}</td>
                    </tr>`);
                }
            });

            printWindow.document.write('</table></body></html>');
            printWindow.document.close();
            printWindow.print();
        });
    </script>

    <!-- script for delete -->
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
    </script>

</body>
</html>