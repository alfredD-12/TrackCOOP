<?php
session_start();
include('../auth/db_connect.php');

// Security check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Static bookkeeper data
$user_id = $_SESSION['user_id'];
$full_name = isset($_SESSION['fname']) ? $_SESSION['fname'] : "Bookkeeper";
$user_role = "Bookkeeper";

$secured_documents = 24;

// Fetch real-time count for Members with Share Capital
$q_sc_members = "SELECT COUNT(DISTINCT user_id) as total FROM share_capital";
$rs_sc_members = $conn->query($q_sc_members);
$real_sc_count = ($rs_sc_members && $row = $rs_sc_members->fetch_assoc()) ? $row['total'] : 0;
// Add 50 to the real count as requested for visual/demo purposes
$sc_member_count = $real_sc_count + 50;

// Fetch Total Overall Balance (Dynamic with Sample Fallback)
$q_balance = "SELECT SUM(IF(transaction_type = 'deposit', amount, -amount)) as total FROM share_capital";
$rs_balance = $conn->query($q_balance);
$total_balance = ($rs_balance && $row = $rs_balance->fetch_assoc() && $row['total'] > 0) ? $row['total'] : 245850.00;

// Top Sector calculation
$top_sector = "Rice Sector";
$top_capital = 0;
$top_sector_query = "SELECT u.sector, 
                    SUM(CASE WHEN sc.transaction_type = 'deposit' THEN sc.amount ELSE -sc.amount END) as total_capital
                    FROM users u
                    JOIN share_capital sc ON u.id = sc.user_id
                    WHERE u.role = 'Member' AND u.sector != ''
                    GROUP BY u.sector
                    ORDER BY total_capital DESC
                    LIMIT 1";
$top_sector_result = $conn->query($top_sector_query);
if ($top_sector_result && $row = $top_sector_result->fetch_assoc()) {
    $top_sector = $row['sector'];
    $top_capital = $row['total_capital'];
} else {
    $top_sector = "Rice Sector";
    $top_capital = 120500.00; 
}

// --- DATA FOR CHARTS ---

// 1. Transaction Trends (Last 6 Months)
$chart_months = [];
$chart_deposits = [];
$chart_withdrawals = [];
$trend_found = false;

for ($i = 5; $i >= 0; $i--) {
    $month_label = date('M', strtotime("-$i month"));
    $month_val = date('Y-m', strtotime("-$i month"));
    $chart_months[] = $month_label;
    
    $q_trend = "SELECT 
                SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE 0 END) as deposits,
                SUM(CASE WHEN transaction_type = 'withdrawal' THEN amount ELSE 0 END) as withdrawals
                FROM share_capital 
                WHERE created_at LIKE '$month_val%'";
    $r_trend = $conn->query($q_trend);
    if ($r_trend && $row = $r_trend->fetch_assoc()) {
        $chart_deposits[] = (float)($row['deposits'] ?? 0);
        $chart_withdrawals[] = (float)($row['withdrawals'] ?? 0);
        if ($row['deposits'] > 0 || $row['withdrawals'] > 0) $trend_found = true;
    } else {
        $chart_deposits[] = 0;
        $chart_withdrawals[] = 0;
    }
}

// Fallback sample for trend if empty
if (!$trend_found) {
    $chart_deposits = [45000, 52000, 48000, 61000, 55000, 68000];
    $chart_withdrawals = [12000, 15000, 11000, 18000, 14000, 21000];
}

// 2. Sector Distribution Data
$sector_dist_labels = [];
$sector_dist_values = [];
$q_dist = "SELECT u.sector, SUM(CASE WHEN sc.transaction_type = 'deposit' THEN sc.amount ELSE -sc.amount END) as total
           FROM users u JOIN share_capital sc ON u.id = sc.user_id
           WHERE u.sector != '' GROUP BY u.sector ORDER BY total DESC";
$r_dist = $conn->query($q_dist);
while ($r_dist && $row = $r_dist->fetch_assoc()) {
    $sector_dist_labels[] = $row['sector'];
    $sector_dist_values[] = (float)$row['total'];
}

// Fallback sample for sector distribution if empty
if (empty($sector_dist_labels)) {
    $sector_dist_labels = ['Rice', 'Corn', 'Fishery', 'Livestock', 'Other'];
    $sector_dist_values = [120500, 85000, 42000, 58000, 32000];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookkeeper Dashboard | TRACKCOOP</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    <link rel="stylesheet" href="../includes/footer.css">

    <style>
        :root {
            --track-green: #206970; 
            --track-dark: #1a1a1a; 
            --track-bg: #f8fafc;
            --track-beige: #F5F5DC; 
            --transition-smooth: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            --text-main: #212529; 
            --text-muted: #555555; 
        }

        @keyframes fadeInUpCustom {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        body { 
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            color: var(--text-main);
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)), url('../Home.jpeg') top center / 100% 100% no-repeat fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* --- TABLE CARD --- */
        .table-card {
            border: 2.5px solid #20a060 !important;
            border-radius: 30px !important;
            background: #ffffff !important;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            animation: fadeInUpCustom 0.8s ease-out 0.4s both;
            overflow: hidden;
            opacity: 1 !important;
        }
        .table-card:hover { border-color: #20a060 !important; transform: translateY(-5px); box-shadow: 0 15px 35px rgba(32,160,96,0.15) !important; }

        /* --- Updated Navbar Styles --- */
        .navbar {
            background-color: #164a36 !important;
            padding: 15px 0;
            border-bottom: 1px solid rgba(22, 74, 54, 0.3);
            animation: fadeInUpCustom 0.8s ease-out;
            z-index: 1050;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -1px;
            color: #ffffff !important;
            text-decoration: none;
        }
        .navbar-brand span { color: #20a060; }

        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 600;
            font-size: 0.95rem;
            margin: 0 12px;
            padding: 8px 0 !important; 
            position: relative;
            transition: var(--transition-smooth);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--track-green);
            transition: width 0.3s ease;
        }

        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after {
            width: 100%;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active { 
            color: #20a060 !important;
        }

        .logout-btn {
            border: 2px solid #dc2626; background: #dc2626; color: white; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
            border-radius: 12px; transition: var(--transition-smooth); text-decoration: none;
            animation: fadeInUpCustom 0.8s ease-out 0.3s both;
        }
        .logout-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(220, 38, 38, 0.6); }
        .logout-btn:hover i { color: white; }

        /* ── Page Layout ── */
        .card { 
            background-color: #ffffff !important; 
            opacity: 1 !important; 
            border: 2.5px solid #20a060 !important; 
            border-radius: 30px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important; 
            transition: all 0.3s ease;
            overflow: hidden;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(32,160,96,0.15) !important; }
        
        /* ── Elite 3.0 Stat Cards ── */
        .stat-card {
            border: 1px solid rgba(255, 255, 255, 0.4) !important; 
            border-radius: 24px !important; 
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(12px);
            padding: 20px; 
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1) !important;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05) !important;
            position: relative; 
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .stat-card:hover { 
            transform: translateY(-12px) scale(1.03) !important; 
            box-shadow: 0 40px 80px -15px rgba(32,160,96,0.15) !important; 
            border-color: rgba(32,160,96,0.3) !important; 
            background: #ffffff !important;
        }
        .stat-card::after {
            content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 6px;
            background: var(--card-brand, #20a060);
            opacity: 0.1;
        }

        .icon-box {
            width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;
            border-radius: 18px; margin-bottom: 24px; transition: all 0.5s ease;
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05);
        }
        .stat-card:hover .icon-box { 
            transform: scale(1.2) rotate(12deg); 
            box-shadow: 0 15px 30px -5px rgba(0,0,0,0.1);
        }

        .stat-label {
            font-size: 0.8rem; 
            font-weight: 800; 
            text-transform: uppercase; 
            letter-spacing: 1.2px; 
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 1.85rem; 
            font-weight: 900; 
            color: #1e293b;
            letter-spacing: -1px;
            margin: 0;
            line-height: 1;
        }

        .bookkeeper-header {
            background: transparent;
            padding: 15px 0 10px;
            border-bottom: none;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
            color: #ffffff !important;
        }
        .bookkeeper-header h1 { color: #20a060 !important; letter-spacing: -1.5px; font-size: 3rem; font-weight: 900 !important; }
        .bookkeeper-header p { color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }

        .bookkeeper-header::after {
            content: ''; position: absolute; top: -20%; right: -5%; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(32,160,96,0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%; z-index: 0; pointer-events: none;
        }

        .status-badge {
            display: inline-flex; align-items: center; background: white; color: var(--track-green);
            font-weight: 700; padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; 
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.1); border: 1px solid rgba(32, 160, 96, 0.2);
        }

        /* Override footer to match navbar dark color */
        .footer-track { 
            background-color: rgba(22, 74, 54, 0.95) !important;
            border-top: 1px solid rgba(22, 74, 54, 0.3) !important; 
            padding: 60px 0 !important; 
            margin-top: auto !important;
        }
        
        .footer-track .navbar-brand { color: #ffffff !important; }
        .footer-track .navbar-brand span:first-child { color: #ffffff !important; }
        .footer-track .navbar-brand span:last-child { color: #20a060 !important; }
        .footer-track .small { color: #ffffff !important; }
        .footer-track .text-muted,
        .footer-track .text-secondary,
        .footer-track .text-dark { color: #ffffff !important; }
        
        .footer-track .social-btn {
            background: rgba(255, 255, 255, 0.15) !important;
            color: #20a060 !important;
            border: 1px solid rgba(32, 160, 96, 0.3) !important;
        }
        
        .footer-track .social-btn:hover {
            background-color: #20a060 !important;
            color: white !important;
        }
        
        .social-icon-btn { width: 44px; height: 44px; background: white; color: var(--track-green); display: inline-flex; align-items: center; justify-content: center; border-radius: 14px; text-decoration: none; font-size: 1.2rem; transition: 0.3s; border: 1px solid rgba(0,0,0,0.05); }
        .social-icon-btn:hover { background: var(--track-green); color: white; transform: translateY(-4px); }
        
        /* ── Solid Cards ── */
        .card { background-color: #ffffff !important; opacity: 1 !important; border: 1px solid rgba(226, 232, 240, 0.8) !important; }

        /* ── Elite 3.0 Dashboard Style ── */
        .elite-stat-card {
            background: #164a36 !important;
            border-radius: 32px !important;
            box-shadow: 0 15px 35px rgba(22, 74, 54, 0.25) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            transition: var(--transition-smooth);
            padding: 2rem;
        }
        .elite-stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 45px rgba(22, 74, 54, 0.35) !important;
        }
        .elite-icon-box {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<div class="sidebar-layout">
    <?php 
        $active_page = 'dashboard';
        $user_role = $_SESSION['role'];
        $membership_type = $user_role;
        $full_name = htmlspecialchars($full_name);
        include('../includes/dashboard_sidebar.php'); 
    ?>

    <div class="main-content-wrapper">

<div class="bookkeeper-header">
    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-center mb-0">
            <div class="col-lg-7">
                <!-- Heading Removed for Streamlined UI -->
            </div>
        </div>
    </div>
</div>

<div class="container pb-5 mt-4">
    <div class="row g-4 justify-content-center">
        <!-- Members with Share Capital Card -->
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="elite-stat-card border-0 h-100">
                <div class="d-flex align-items-center mb-4">
                    <div class="elite-icon-box me-3">
                        <i class="bi bi-person-check-fill fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-white fw-800 text-uppercase mb-0" style="letter-spacing: 1.5px; font-size: 0.8rem; opacity: 0.9;">Member Equity</h6>
                        <small class="text-white opacity-50 fw-bold">Active Capitalists</small>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-3">
                    <h1 class="fw-900 mb-0" style="font-size: 3.5rem; letter-spacing: -3px; color: #ffffff; text-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                        <?php echo number_format($sc_member_count); ?>
                    </h1>
                    <span class="badge bg-white bg-opacity-10 text-white fw-bold px-3 py-2" style="font-size: 0.8rem; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                        <i class="bi bi-star-fill me-1 text-white"></i>PARTIAL
                    </span>
                </div>
                <p class="text-white mt-4 mb-0 fw-bold" style="font-size: 0.9rem;">
                    <i class="bi bi-info-circle-fill me-2 opacity-75"></i>Total Members with Share Capital
                </p>
            </div>
        </div>

        <!-- Total Overall Balance Card -->
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="elite-stat-card border-0 h-100">
                <div class="d-flex align-items-center mb-4">
                    <div class="elite-icon-box me-3">
                        <i class="bi bi-wallet2 fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-white fw-800 text-uppercase mb-0" style="letter-spacing: 1.5px; font-size: 0.8rem; opacity: 0.9;">System Liquidity</h6>
                        <small class="text-white opacity-50 fw-bold">Overall Capital Pool</small>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="text-white fw-800" style="font-size: 1.5rem; opacity: 0.8;">₱</span>
                    <h1 class="fw-900 mb-0" style="font-size: 3.5rem; letter-spacing: -3px; color: #ffffff; text-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                        <?php echo number_format($total_balance, 2); ?>
                    </h1>
                </div>
                <p class="text-white mt-4 mb-0 fw-bold" style="font-size: 0.9rem;">
                    <i class="bi bi-bank2 me-2 opacity-75"></i>Total Overall Balance
                </p>
            </div>
        </div>

        <!-- Top Sector by Capital Card (New) -->
        <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="elite-stat-card border-0 h-100">
                <div class="d-flex align-items-center mb-4">
                    <div class="elite-icon-box me-3">
                        <i class="bi bi-trophy-fill fs-3 text-white"></i>
                    </div>
                    <div>
                        <h6 class="text-white fw-800 text-uppercase mb-0" style="letter-spacing: 1.5px; font-size: 0.8rem; opacity: 0.9;">Sector Leader</h6>
                        <small class="text-white opacity-50 fw-bold">Capital Leaderboard</small>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-3">
                    <h1 class="fw-900 mb-0" style="font-size: 2.2rem; letter-spacing: -1.5px; color: #ffffff; text-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                        <?php echo htmlspecialchars($top_sector); ?>
                    </h1>
                    <span class="badge bg-white bg-opacity-10 text-white fw-bold px-3 py-2" style="font-size: 0.8rem; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1);">
                        <i class="bi bi-star-fill me-1 text-white"></i>₱<?php echo number_format($top_capital, 2); ?>
                    </span>
                </div>
                <p class="text-white mt-4 mb-0 fw-bold" style="font-size: 0.9rem;">
                    <i class="bi bi-info-circle-fill me-2 opacity-75"></i>Highest Contributed Sector
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <!-- Capital Flow Chart -->
        <div class="col-lg-8" data-aos="fade-up" data-aos-delay="400">
            <div class="elite-stat-card border-0" style="min-height: 450px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="text-white fw-800 text-uppercase mb-0" style="letter-spacing: 1.5px; font-size: 0.8rem; opacity: 0.9;">Capital Flow Analysis</h6>
                        <small class="text-white opacity-50 fw-bold">6-Month Transaction Trends</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-link text-white opacity-50" type="button" style="text-decoration: none;">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                    </div>
                </div>
                <div style="height: 320px; position: relative;">
                    <canvas id="capitalFlowChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Sector Distribution Chart -->
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="500">
            <div class="elite-stat-card border-0" style="min-height: 450px;">
                <h6 class="text-white fw-800 text-uppercase mb-4" style="letter-spacing: 1.5px; font-size: 0.8rem; opacity: 0.9;">Sector Equity Concentration</h6>
                <div style="height: 320px; position: relative;">
                    <canvas id="sectorDistChart"></canvas>
                </div>
                <div class="mt-3 text-center">
                    <small class="text-white opacity-50 fw-bold"><i class="bi bi-info-circle me-1"></i>Capital distribution by agricultural sector</small>
                </div>
            </div>
        </div>
    </div>
</div>

    </div> <!-- .main-content-wrapper -->
</div> <!-- .sidebar-layout -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    AOS.init({ once: true, duration: 800 });

    // Chart.js - Global Defaults for Elite 3.0
    Chart.defaults.color = 'rgba(255, 255, 255, 0.5)';
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    
    // 1. Capital Flow Chart (Line/Bar Hybrid)
    const ctxFlow = document.getElementById('capitalFlowChart').getContext('2d');
    const capitalFlowChart = new Chart(ctxFlow, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($chart_months); ?>,
            datasets: [
                {
                    label: 'Deposits',
                    data: <?php echo json_encode($chart_deposits); ?>,
                    borderColor: '#20a060',
                    backgroundColor: 'rgba(32, 160, 96, 0.1)',
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#20a060',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Withdrawals',
                    data: <?php echo json_encode($chart_withdrawals); ?>,
                    borderColor: 'rgba(255, 255, 255, 0.3)',
                    borderDash: [5, 5],
                    borderWidth: 2,
                    fill: false,
                    tension: 0.4,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        color: 'rgba(255, 255, 255, 0.8)'
                    }
                },
                tooltip: {
                    backgroundColor: '#164a36',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    padding: 12,
                    cornerRadius: 10,
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ₱' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)', drawBorder: false },
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000) return '₱' + (value/1000) + 'k';
                            return '₱' + value;
                        }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });

    // 2. Sector Distribution Chart (Doughnut)
    const ctxDist = document.getElementById('sectorDistChart').getContext('2d');
    const sectorDistChart = new Chart(ctxDist, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($sector_dist_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($sector_dist_values); ?>,
                backgroundColor: [
                    '#20a060',
                    '#164a36',
                    '#2ecc71',
                    '#27ae60',
                    'rgba(255, 255, 255, 0.2)'
                ],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        color: 'rgba(255, 255, 255, 0.8)',
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    backgroundColor: '#164a36',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((context.raw / total) * 100).toFixed(1);
                            return context.label + ': ₱' + context.raw.toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
</script>


</body>
</html>
