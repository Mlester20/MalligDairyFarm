<?php
session_start();
include '../components/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Fetch all users from the database
$query = "SELECT * FROM users";
$result = mysqli_query($con, $query);
?>

<?php
// Handle Add User
if (isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = md5(trim($_POST['password']));
    $role = trim($_POST['role']);

    $query = "INSERT INTO users (name, username, password, role, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $name, $username, $password, $role);
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('User added successfully!'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Failed to add user. Please try again.');</script>";
    }
    mysqli_stmt_close($stmt);
}

// Handle Edit User
if (isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $role = trim($_POST['role']);
    $password = !empty($_POST['password']) ? md5(trim($_POST['password'])) : null;

    if ($password) {
        $query = "UPDATE users SET name = ?, username = ?, password = ?, role = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "ssssi", $name, $username, $password, $role, $user_id);
    } else {
        $query = "UPDATE users SET name = ?, username = ?, role = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "sssi", $name, $username, $role, $user_id);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('User updated successfully!'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Failed to update user. Please try again.');</script>";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php include '../components/title.php' ?> - Users</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8d7xj1z2l4c5e5e5e5e5e5e5e5e5e5e5e5" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/fontawesome.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8d7xj1z2l4c5e5e5e5e5e5e5e5e5e5e5e5" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8d7xj1z2l4c5e5e5e5e5e5e5e5e5e5e5e5" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../style/header.css">
    <link rel="icon" href="../images/favi.png" type="image/png">
</head>
<body>
    <?php include '../components/admin_header.php'; ?>

    <div class="container mt-5">
        <h3 class="text-center">Manage Users</h3>
        <div class="text-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
        </div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <!-- <th>Password (MD5)</th> -->
                    <th>Created At</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <!-- <td><?php echo htmlspecialchars($row['password']); ?></td> -->
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td class="d-flex justify-content-center align-items-center gap-2">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $row['user_id']; ?>">
                                <i class="fas fa-pencil"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteUser(<?php echo $row['user_id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal<?php echo $row['user_id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="manage_users.php" method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password (Optional)</label>
                                            <input type="password" class="form-control" name="password" placeholder="Enter new password">
                                        </div>
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role</label>
                                            <select class="form-select" name="role" required>
                                                <option value="admin" <?php echo $row['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                <option value="user" <?php echo $row['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" name="edit_user" class="btn btn-warning">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="manage_users.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>

    <script>
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                window.location.href = `delete_user.php?id=${userId}`;
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

