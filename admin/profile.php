<?php
session_start();
include '../components/config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $current_password = md5(trim($_POST['current_password']));
    $new_password = !empty($_POST['new_password']) ? md5(trim($_POST['new_password'])) : null;
    $confirm_password = !empty($_POST['confirm_password']) ? md5(trim($_POST['confirm_password'])) : null;
    $user_id = $_SESSION['user_id'];

    // Verify current password
    $query = "SELECT password FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['password'] !== $current_password) {
            echo "<script>alert('Current password is incorrect.');</script>";
        } else {
            // Check if new password and confirm password match
            if ($new_password && $new_password !== $confirm_password) {
                echo "<script>alert('New password and confirm password do not match.');</script>";
            } else {
                // Update query
                $update_query = "UPDATE users SET name = ?, username = ?" . ($new_password ? ", password = ?" : "") . " WHERE user_id = ?";
                $update_stmt = mysqli_prepare($con, $update_query);

                if ($new_password) {
                    mysqli_stmt_bind_param($update_stmt, "sssi", $name, $username, $new_password, $user_id);
                } else {
                    mysqli_stmt_bind_param($update_stmt, "ssi", $name, $username, $user_id);
                }

                if (mysqli_stmt_execute($update_stmt)) {
                    // Update session variables
                    $_SESSION['name'] = $name;
                    $_SESSION['username'] = $username;

                    echo "<script>alert('Profile updated successfully!');</script>";
                } else {
                    echo "<script>alert('Failed to update profile. Please try again.');</script>";
                }
                mysqli_stmt_close($update_stmt);
            }
        }
    } else {
        echo "<script>alert('Database error. Please try again later.');</script>";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php include '../components/title.php'; ?> - Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../components/admin_header.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h3>Profile Information</h3>
                    </div>
                    <div class="card-body">
                        <form action="profile.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter current password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password (Optional)</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Enter new password">
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm new password">
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="home.php" class="btn btn-secondary">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>