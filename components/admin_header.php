<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container-fluid">
        <img src="../images/remove.png" alt="Mallig Dairy Logo" class="me-3" style="height: 50px;">
        <a class="navbar-brand" id="navbarTitle" href="home.php">
            Mallig Dairy Cooperative Farm Records
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="home.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="entriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-folder"></i> Entries
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="entriesDropdown">
                        <li><a class="dropdown-item" href="manage_users.php"><i class="fas fa-users me-2"></i> Manage Users</a></li>
                        <li><a class="dropdown-item" href="records.php"><i class="fas fa-book me-2"></i> Manage Records</a></li>
                        <li><a class="dropdown-item" href="manage_stocks.php"><i class="fas fa-cow me-2"></i> Manage Stocks</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><a class="dropdown-item text-danger" href="logout.php" onclick="return confirm('Are you sure you want to logout?')">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    // Function to update the navbar title based on screen size
    function updateNavbarTitle() {
        const navbarTitle = document.getElementById('navbarTitle');
        if (window.innerWidth <= 768) {
            navbarTitle.textContent = 'Dairy Farm'; // Mobile view title
        } else {
            navbarTitle.textContent = 'Mallig Dairy Cooperative Farm Records'; 
        }
    }
    updateNavbarTitle();

    window.addEventListener('resize', updateNavbarTitle);
</script>