<?php
session_start();
include('../auth/db_connect.php');

// Security check: Admin and Bookkeeper only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

/** ── DATA FETCHING ── **/

// Static demo data for analytics
$total_members = 16;
$approved_members = 13;
$pending_members = 3;
$total_capital = 185750.00;

// Sector distribution (static for demo)
$sector_labels = ['Rice', 'Corn', 'Fishery', 'Livestock', 'High Value Crops'];
$sector_data = [5, 8, 3];

// 4. Monthly Growth (Line Chart - Last 6 Months) - Static demo data
$growth_labels = ['October', 'November', 'December', 'January', 'February', 'March'];
$growth_data = [2, 3, 4, 5, 7, 13];

// 5. Capital Over Time (Bar Chart) - Static demo data
$capital_labels = ['October', 'November', 'December', 'January', 'February', 'March'];
$capital_data = [15000, 22500, 31250, 45000, 62500, 185750];

// Unified Nav Vars
$full_name = isset($_SESSION['fname']) ? $_SESSION['fname'] : "Administrator";
@$q_u = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
if ($q_u) {
    @$q_u->bind_param("i", $_SESSION['user_id']);
    @$q_u->execute();
    if ($u_inf = @$q_u->get_result()->fetch_assoc()) $full_name = $u_inf['first_name'].' '.$u_inf['last_name'];
}
$active_page = 'analytics';
$user_role = $_SESSION['role'];
$membership_type = $user_role;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard | TrackCOOP</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <?php if (isset($_GET['view']) && $_GET['view'] === 'modal'): ?>
    <style>
        body { background: white !important; padding: 0 !important; overflow-x: hidden; }
        .analytics-header { padding: 30px 0 20px !important; margin-bottom: 20px !important; background: white !important; border-bottom: none !important; }
        .analytics-header h1 { font-size: 2.5rem !important; }
        .container { max-width: 100% !important; padding: 0 20px !important; }
        .navbar, footer { display: none !important; }
    </style>
    <?php endif; ?>
    
    <style>
        :root {
            --track-green: #206970;
            --track-dark: #1a1a1a;
            --track-bg: #f8fafc;
            --track-beige: #F5F5DC;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --text-main: #1a202c;
            --text-muted: #718096;
        }

        body {
            background-color: var(--track-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
        }

        .navbar {
            background-color: rgba(245, 245, 220, 0.95) !important;
            backdrop-filter: blur(10px);
            padding: 15px 0;
            border-bottom: 1px solid rgba(229, 229, 192, 0.5);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .navbar-brand { font-weight: 800; font-size: 1.5rem; letter-spacing: -0.8px; color: var(--track-dark) !important; }
        .navbar-brand span { color: var(--track-green); }

        .analytics-header {
            background: linear-gradient(135deg, #fff 0%, var(--track-beige) 100%);
            padding: 50px 0 40px;
            border-bottom: 1px solid rgba(229, 229, 192, 0.4);
            margin-bottom: 40px;
        }

        .stat-card {
            background: white; border-radius: 24px; padding: 25px;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            transition: var(--transition-smooth);
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px rgba(32, 160, 96, 0.08); border-color: rgba(32, 160, 96, 0.1); }

        .icon-box {
            width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;
            border-radius: 12px; margin-bottom: 15px; font-size: 1.5rem;
        }

        .chart-container {
            background: white; border-radius: 24px; padding: 30px;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            height: 100%;
        }

        .chart-title { font-weight: 700; color: var(--track-dark); margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }

        .btn-back { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 12px; border: 1.5px solid #e5e5c0; background: white; color: var(--text-muted); text-decoration: none; transition: 0.3s; margin-right: 15px; }
        .btn-back:hover { border-color: var(--track-green); color: var(--track-green); background: rgba(32, 160, 96, 0.05); }

    </style>
</head>
<?php 
if (!(isset($_GET['view']) && $_GET['view'] === 'modal')) {
    include('../includes/dashboard_navbar.php'); 
}
?>

<div class="analytics-header">
    <div class="container">
        <div class="d-flex align-items-center mb-2">
            <span class="text-success fw-bold text-uppercase small" style="letter-spacing: 1px;"><i class="bi bi-cpu-fill me-1"></i> Cooperative Insights</span>
        </div>
        <h1 class="fw-800 display-5" style="letter-spacing: -2px;">System <span class="text-success">Analytics</span></h1>
    </div>
</div>

<div class="container pb-5">
    <!-- Charts Row 1 -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="chart-container">
                <h5 class="chart-title"><i class="bi bi-graph-up text-success"></i> Monthly Membership Growth</h5>
                <canvas id="growthChart" height="300"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="chart-container">
                <h5 class="chart-title"><i class="bi bi-pie-chart-fill text-success"></i> Sector Distribution</h5>
                <canvas id="sectorChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row g-4">
        <div class="col-12">
            <div class="chart-container">
                <h5 class="chart-title"><i class="bi bi-currency-dollar text-success"></i> Monthly Share Capital Contributions</h5>
                <canvas id="capitalChart" height="150"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Membership Growth Line Chart
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    new Chart(growthCtx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($growth_labels); ?>,
            datasets: [{
                label: 'New Members',
                data: <?php echo json_encode($growth_data); ?>,
                borderColor: '#20a060',
                backgroundColor: 'rgba(32, 160, 96, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#20a060',
                pointRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Sector Distribution Pie Chart
    const sectorCtx = document.getElementById('sectorChart').getContext('2d');
    new Chart(sectorCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($sector_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($sector_data); ?>,
                backgroundColor: ['#20a060', '#f1c40f', '#3498db', '#e74c3c', '#9b59b6', '#34495e'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
            },
            cutout: '70%'
        }
    });

    // 3. Share Capital Bar Chart
    const capitalCtx = document.getElementById('capitalChart').getContext('2d');
    new Chart(capitalCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($capital_labels); ?>,
            datasets: [{
                label: 'Contribution Amount',
                data: <?php echo json_encode($capital_data); ?>,
                backgroundColor: '#20a060',
                borderRadius: 8,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: value => '₱' + value.toLocaleString() } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
