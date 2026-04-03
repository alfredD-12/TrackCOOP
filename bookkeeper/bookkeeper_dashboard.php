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

// Static financial data
$total_capital = 125000;
$pending_audits = 12;
$secured_documents = 24;

// Static sector capital data
$sector_capital = [
    ['sector' => 'Rice', 'amount' => 50000, 'percentage' => 40],
    ['sector' => 'Corn', 'amount' => 31250, 'percentage' => 25],
    ['sector' => 'Fishery', 'amount' => 25000, 'percentage' => 20],
    ['sector' => 'Livestock', 'amount' => 12500, 'percentage' => 10],
    ['sector' => 'High Value Crops', 'amount' => 6250, 'percentage' => 5],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookkeeper Dashboard | TrackCOOP</title>
    
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
            background: var(--track-bg);
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* --- Updated Navbar Styles --- */
        .navbar {
            background-color: rgba(22, 74, 54, 0.95) !important;
            backdrop-filter: blur(10px);
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

        /* Dashboard Header */
        .bookkeeper-header {
            background: linear-gradient(135deg, var(--track-bg) 0%, var(--track-beige) 100%);
            padding: 70px 0 50px;
            border-bottom: 1px solid rgba(229, 229, 192, 0.4);
            margin-bottom: 50px;
            position: relative;
            overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .bookkeeper-header h1 { color: var(--track-dark); letter-spacing: -1.5px; font-size: 3rem; }

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
    </style>
</head>
<body>

<?php 
    $active_page = 'dashboard';
    $membership_type = $user_role;
    include('../includes/dashboard_navbar.php'); 
?>

<div class="bookkeeper-header">
    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-center mb-0">
            <div class="col-lg-7">
                <div class="status-badge">
                    <span class="spinner-grow spinner-grow-sm me-2 text-success" role="status" style="width: 10px; height: 10px;"></span>
                    System Live
                </div>
                <h1 class="fw-800 display-3 mb-2">Financial Overview.</h1>
                <p class="fs-5 text-muted mb-0" style="max-width: 600px;">Monitor share capital and manage audited financial records.</p>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5 mt-5">
    <!-- 3 Stat Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-cash-stack fs-5 text-success"></i>
                        </div>
                        <span class="badge bg-success">+8%</span>
                    </div>
                    <h6 class="text-muted text-uppercase fw-bold small mb-2" style="letter-spacing: 0.5px;">Total Capital</h6>
                    <h3 class="fw-800 text-dark">₱<?php echo number_format($total_capital, 0); ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-clock-history fs-5 text-warning"></i>
                        </div>
                        <span class="badge bg-warning">!</span>
                    </div>
                    <h6 class="text-muted text-uppercase fw-bold small mb-2" style="letter-spacing: 0.5px;">Pending Audit</h6>
                    <h3 class="fw-800 text-dark"><?php echo $pending_audits; ?> Files</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-file-earmark-check fs-5 text-info"></i>
                        </div>
                        <span class="badge bg-success">100%</span>
                    </div>
                    <h6 class="text-muted text-uppercase fw-bold small mb-2" style="letter-spacing: 0.5px;">Secured Documents</h6>
                    <h3 class="fw-800 text-dark"><?php echo $secured_documents; ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Capital by Sector -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-diagram-3 text-success me-2"></i>Share Capital by Sector</h5>
                    
                    <?php foreach ($sector_capital as $sector): ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold"><?php echo $sector['sector']; ?></span>
                            <span class="text-success fw-bold"><?php echo $sector['percentage']; ?>%</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar bg-success" style="width: <?php echo $sector['percentage']; ?>%;"></div>
                        </div>
                        <small class="text-muted">₱<?php echo number_format($sector['amount'], 0); ?></small>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-bullseye text-danger me-2"></i>Monthly Goal</h5>
                    
                    <div class="text-center mb-4">
                        <div style="width: 120px; height: 120px; margin: 0 auto; border-radius: 50%; background: linear-gradient(135deg, #20a060 0%, #206970 100%); display: flex; align-items: center; justify-content: center; color: white;">
                            <div>
                                <div class="fw-800" style="font-size: 28px;">84%</div>
                                <small>Reached</small>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-2 text-center">
                        <small class="text-muted d-block mb-2">Current Pool</small>
                        <h5 class="fw-bold text-success mb-3">₱125,000</h5>
                        <small class="text-muted d-block mb-2">Goal Target</small>
                        <h5 class="fw-bold mb-0">₱150,000</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 800 });
</script>
<?php include('../includes/footer.php'); ?>

</body>
</html>
