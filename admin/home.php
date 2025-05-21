<?php
session_start();
include '../components/config.php';
include '../controllers/fetchDashboardData.php';

// Check if the user is not logged in
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
    <title> <?php include '../components/title.php'; ?> - Dashboard </title>
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

    <div class="container mt-4">
        <h4 class="text-center mb-4">Milk Records Analysis</h4>

        <div class="dashboard-container">
            <!-- Left panel with graph -->
            <div class="left-panel">
                <div class="graph-container">
                    <h5 class="mb-4 text-center">Milk Production Graph</h5>
                    <canvas id="lineChart" style="max-height: 300px;"></canvas>
                </div>
            </div>

            <!-- Right panel with pie chart and cards -->
            <div class="right-panel">
                <!-- Pie Chart -->
                <div class="pie-chart-container">
                    <h5 class="mb-4 text-muted text-center">Total Invent Milk: <?php echo $totalMilk . " Liters"; ?> </h5>
                    <canvas id="pieChart" style="max-height: 200px;"></canvas>
                </div>

                <!-- Cards Section -->
                <div class="cards-container">
                    <!-- Total Users -->
                    <div class="stats-card bg-primary">
                        <div class="stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5>Total Users</h5>
                        <div class="stats-value">
                            <?php echo $userCounts['user']; ?>
                        </div>
                    </div>

                    <!-- Total Admins -->
                    <div class="stats-card bg-success">
                        <div class="stats-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h5>Total Admins</h5>
                        <div class="stats-value">
                            <?php echo $userCounts['admin']; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- footer -->
    <?php include '../components/footer.php'; ?>

    <!-- for scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Line Chart Data
        const lineChartData = {
            labels: <?php echo json_encode($months); ?>, // Dynamic months
            datasets: [{
                label: 'Milk Production (Liters)',
                data: <?php echo json_encode($quantities); ?>, // Dynamic quantities
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderWidth: 2,
                tension: 0.4
            }]
        };

        // Render Line Chart
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: lineChartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Months'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Liters'
                        }
                    }
                }
            }
        });

        // Pie Chart Data
        const pieChartData = {
            labels: ['Total Milk Inventory'],
            datasets: [{
                data: [<?php echo $totalMilk; ?>],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)'
                ],
                borderWidth: 1
            }]
        };

        // Render Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: pieChartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    </script>
</body>
</html>