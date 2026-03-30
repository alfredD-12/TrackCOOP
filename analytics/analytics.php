<?php
session_start();
include('../auth/db_connect.php');

// Security check: Admin and Bookkeeper only
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Bookkeeper'])) {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

/** ── DATA FETCHING ── **/

// 1. Total Members and Pending
$total_members = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role='Member'")->fetch_assoc()['cnt'];
$approved_members = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role='Member' AND status='Approved'")->fetch_assoc()['cnt'];
$pending_members = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role='Member' AND status='Pending'")->fetch_assoc()['cnt'];

// 2. Total Share Capital
$total_capital_res = $conn->query("SELECT SUM(amount) as total FROM share_capital");
$total_capital = $total_capital_res ? ($total_capital_res->fetch_assoc()['total'] ?? 0) : 0;

// 3. Sector Distribution (Pie Chart)
$sector_dist = $conn->query("SELECT sector, COUNT(*) as count FROM users WHERE role='Member' AND status='Approved' GROUP BY sector");
$sector_labels = [];
$sector_data = [];
while($row = $sector_dist->fetch_assoc()) {
    $sector_labels[] = $row['sector'] ?: 'Not Assigned';
    $sector_data[] = $row['count'];
}

// 4. Monthly Growth (Line Chart - Last 6 Months)
$growth_labels = [];
$growth_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $month_display = date('M Y', strtotime("-$i months"));
    $res = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role='Member' AND created_at LIKE '$month%'");
    $growth_labels[] = $month_display;
    $growth_data[] = $res->fetch_assoc()['cnt'];
}

// 5. Capital Over Time (Bar Chart)
$capital_labels = [];
$capital_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $month_display = date('M Y', strtotime("-$i months"));
    $res = $conn->query("SELECT SUM(amount) as total FROM share_capital WHERE created_at LIKE '$month%'");
    $capital_labels[] = $month_display;
    $capital_data[] = $res ? ($res->fetch_assoc()['total'] ?? 0) : 0;
}

// Unified Nav Vars
$full_name = "User";
$q_u = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$q_u->bind_param("i", $_SESSION['user_id']);
$q_u->execute();
if ($u_inf = $q_u->get_result()->fetch_assoc()) $full_name = $u_inf['first_name'].' '.$u_inf['last_name'];
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
            --track-green: #20a060;
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
    <!-- Stat Highlights -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-people-fill"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase">Total Members</h6>
                <h2 class="fw-800 m-0"><?php echo number_format($total_members); ?></h2>
                <div class="mt-2 small text-success">Approved: <?php echo $approved_members; ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="bi bi-hourglass-split"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase">Pending Apps</h6>
                <h2 class="fw-800 m-0"><?php echo number_format($pending_members); ?></h2>
                <div class="mt-2 small text-warning">Awaiting Approval</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-cash-stack"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase">Total Capital</h6>
                <h2 class="fw-800 m-0">₱<?php echo number_format($total_capital, 2); ?></h2>
                <div class="mt-2 small text-primary">Consolidated Funds</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="icon-box bg-info bg-opacity-10 text-info"><i class="bi bi-diagram-3-fill"></i></div>
                <h6 class="text-muted small fw-bold text-uppercase">Active Sectors</h6>
                <h2 class="fw-800 m-0"><?php echo count($sector_labels); ?></h2>
                <div class="mt-2 small text-info">Registered Agri Sectors</div>
            </div>
        </div>
    </div>

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
<?php include('../includes/footer.php'); ?>
</body>
</html>
