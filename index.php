<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php include './components/title.php'; ?> - Login </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/login_style.css">
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="title">
            <span class="desktop-title d-none d-md-block">Mallig Dairy Cooperative Farm Record</span>
            <span class="mobile-title d-md-none">Dairy Farm Records</span>
        </div>
        <div class="date-time d-none d-md-block" id="currentDateTime"><span>Today is: </span></div>
    </div>

    <!-- Login Form -->
    <div class="container mt-4 mt-md-5">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-9 col-md-6 col-lg-4">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white text-center border-0">
                        <h5 class="mt-3 text-muted">Dairy Farm Record</h5>
                    </div>
                    
                    <div class="card-body p-3 p-md-4">
                        <form action="login.php" method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-3" id="username" name="username" placeholder="Username" required>
                                <label for="username">Username</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Password" required>
                                <label for="password">Password</label>
                            </div>
                            <div class="d-grid col-8 col-md-6 mx-auto mb-3">
                                <button type="submit" name="login" class="btn btn-primary rounded-pill">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/time.js"></script>
</body>
</html>