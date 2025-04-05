<?php
session_start();
include '../components/config.php';

// Function to check if the user isn't logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php include '../components/title.php'; ?> - Home </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8d7xj1z2l4c5e5e5e5e5e5e5e5e5e5e5e5" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../style/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
            padding-left: 0;
        }
        .select2-dropdown {
            border: 1px solid #ced4da;
        }
    </style>
</head>
<body>

    <?php include '../components/header.php'; ?>    

    <div class="container mt-5">
        <h3 class="text-center mb-4 text-muted">Milk Records</h3>
        <div class="row mb-3">
            <div class="col text-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMilkRecordModal">Add Milk Record</button>
            </div>
        </div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Live Stock</th>
                    <th>Date</th>
                    <th>Quantity (Liters)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="milkRecordsTable">
                <!-- Milk records will be dynamically loaded -->
            </tbody>
        </table>
    </div>

    <!-- Add Milk Record Modal -->
    <div class="modal fade" id="addMilkRecordModal" tabindex="-1" aria-labelledby="addMilkRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="addMilkRecordForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addMilkRecordModalLabel">Add Milk Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="live_stock_id" class="form-label">Stocks Name</label>
                            <select class="form-select select2-dropdown" id="live_stock_id" name="live_stock_id" required>
                                <option value="" disabled selected>Select Stock Name</option>
                                <?php
                                $query = "SELECT * FROM live_stocks";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$row['id']}'>{$row['live_stock_name']} ({$row['live_stock_code']})</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="record_date" class="form-label">Record Date</label>
                            <input type="date" class="form-control" id="record_date" name="record_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity (Liters)</label>
                            <input type="number" step="0.01" class="form-control" id="quantity" name="quantity" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Milk Record Modal -->
    <div class="modal fade" id="editMilkRecordModal" tabindex="-1" aria-labelledby="editMilkRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editMilkRecordForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMilkRecordModalLabel">Edit Milk Record</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_record_id" name="id">
                        <div class="mb-3">
                            <label for="edit_live_stock_id" class="form-label">Stocks Name</label>
                            <select class="form-select select2-dropdown" id="edit_live_stock_id" name="live_stock_id" required>
                                <option value="" disabled>Select Stock Name</option>
                                <?php
                                $query = "SELECT * FROM live_stocks";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$row['id']}'>{$row['live_stock_name']} ({$row['live_stock_code']})</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_record_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="edit_record_date" name="record_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_quantity" class="form-label">Quantity (Liters)</label>
                            <input type="number" step="0.01" class="form-control" id="edit_quantity" name="quantity" required>
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

    <!-- footer -->
    <?php include '../components/footer.php'; ?>

    <!-- for scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize Select2 for add form dropdown
            $('.select2-dropdown').select2({
                dropdownParent: $('#addMilkRecordModal'),
                placeholder: "Search or select stock",
                allowClear: true
            });
            
            // Initialize Select2 for edit form dropdown
            $('#edit_live_stock_id').select2({
                dropdownParent: $('#editMilkRecordModal'),
                placeholder: "Search and select stock",
                allowClear: true
            });

            // Function to load milk records
            function loadMilkRecords() {
                $.ajax({
                    url: '../controllers/milk_recordsController.php',
                    method: 'GET',
                    data: { action: 'fetch' },
                    dataType: 'json',
                    success: function (data) {
                        let tableContent = '';
                        data.forEach(function (record) {
                            tableContent += `
                                <tr>
                                    <td>${record.live_stock_name} (${record.live_stock_code})</td>
                                    <td>${record.record_date}</td>
                                    <td>${record.quantity}</td>
                                    <td>
                                        <button class="btn btn-warning btn-sm edit-btn" data-id="${record.id}" data-bs-toggle="modal" data-bs-target="#editMilkRecordModal">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm delete-btn" data-id="${record.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                        $('#milkRecordsTable').html(tableContent);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching milk records:', error);
                    }
                });
            }

            // Load milk records on page load
            loadMilkRecords();

            // Handle Add Milk Record Form Submission
            $('#addMilkRecordForm').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.ajax({
                    url: '../controllers/milk_recordsController.php',
                    method: 'POST',
                    data: formData + '&action=add',
                    success: function (response) {
                        alert('Milk record added successfully!');
                        $('#addMilkRecordModal').modal('hide');
                        $('#addMilkRecordForm')[0].reset();
                        $('.select2-dropdown').val(null).trigger('change');
                        loadMilkRecords();
                    },
                    error: function (xhr, status, error) {
                        console.error('Error adding milk record:', error);
                        alert('Error adding milk record: ' + error);
                    }
                });
            });

            // Handle Edit Button Click - Load record data into the edit modal
            $(document).on('click', '.edit-btn', function () {
                const id = $(this).data('id');
                
                // Fetch the record details using AJAX
                $.ajax({
                    url: '../controllers/milk_recordsController.php',
                    method: 'GET',
                    data: { id: id, action: 'fetch_single' },
                    dataType: 'json',
                    success: function (record) {
                        // Populate the edit modal fields with the record data
                        $('#edit_record_id').val(record.id);
                        
                        // Set the live stock select value and trigger change for Select2
                        $('#edit_live_stock_id').val(record.live_stock_id).trigger('change');
                        
                        $('#edit_record_date').val(record.record_date);
                        $('#edit_quantity').val(record.quantity);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching record details:', error);
                        alert('Error fetching record details: ' + error);
                    }
                });
            });
            
            // Handle Edit Milk Record Form Submission
            $('#editMilkRecordForm').on('submit', function (e) {
                e.preventDefault();
                const formData = $(this).serialize();
                
                $.ajax({
                    url: '../controllers/milk_recordsController.php',
                    method: 'POST',
                    data: formData + '&action=edit',
                    success: function (response) {
                        if (response === 'success') {
                            alert('Milk record updated successfully!');
                            $('#editMilkRecordModal').modal('hide');
                            loadMilkRecords();
                        } else {
                            alert('Error updating record: ' + response);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error updating milk record:', error);
                        alert('Error updating milk record: ' + error);
                    }
                });
            });

            // Handle Delete Button Click
            $(document).on('click', '.delete-btn', function () {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this milk record?')) {
                    $.ajax({
                        url: '../controllers/milk_recordsController.php',
                        method: 'POST',
                        data: { id: id, action: 'delete' },
                        success: function (response) {
                            alert('Milk record deleted successfully!');
                            loadMilkRecords();
                        },
                        error: function (xhr, status, error) {
                            console.error('Error deleting milk record:', error);
                            alert('Error deleting milk record: ' + error);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>