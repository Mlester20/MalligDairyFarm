<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php include './components/title.php'; ?> - Login </title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/login.css">
</head>
<body style="background-color: #f8f9fa;">

    <!-- Header -->
    <div class="header">
        <div class="title">Mallig Dairy Cooperative Farm Record</div>
        <div class="date-time" id="currentDateTime"><span>Today is: </span></div>
    </div>

    <!-- Login Form -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4"> <!-- made slimmer -->
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white text-center border-0">
                        <h5 class="mt-4 text-muted">Dairy Farm Record</h5>
                    </div>
                    
                    <div class="card-body p-4">
                        <form action="login.php" method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control rounded-3" id="username" name="username" placeholder="Username" required>
                                <label for="username">Username</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Password" required>
                                <label for="password">Password</label>
                            </div>
                            <div class="d-grid col-md-4 mx-auto mb-3">
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
