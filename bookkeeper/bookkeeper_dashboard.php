<?php
session_start();
include('../auth/db_connect.php');

/** * SECURITY CHECK */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT first_name, last_name, role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $full_name = $user['first_name'] . " " . $user['last_name'];
    $user_role = $user['role'];
} else {
    $full_name = "User Account";
    $user_role = "Bookkeeper";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookkeeper Dashboard | TrackCOOP</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../includes/footer.css">

    <style>
        :root {
            --track-green: #20a060; 
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
            background-color: rgba(245, 245, 220, 0.9) !important;
            backdrop-filter: blur(10px);
            padding: 15px 0;
            border-bottom: 1px solid rgba(229, 229, 192, 0.5);
            animation: fadeInUpCustom 0.8s ease-out;
            z-index: 1050;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -1px;
            color: var(--track-dark) !important;
            text-decoration: none;
        }
        .navbar-brand span { color: var(--track-green); }

        .status-badge {
            display: inline-flex; align-items: center; background: white; color: var(--track-green);
            font-weight: 700; padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; 
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.1); border: 1px solid rgba(32, 160, 96, 0.2);
        }

        .navbar-nav .nav-link {
            color: var(--text-muted) !important;
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
            color: var(--track-dark) !important;
            background: transparent !important; 
        }

        .logout-btn {
            border: 2px solid #dc2626; color: #dc2626; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
            border-radius: 12px; transition: var(--transition-smooth); text-decoration: none;
            animation: fadeInUpCustom 0.8s ease-out 0.3s both;
        }
        .logout-btn:hover { background: #dc2626; color: white; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(220, 38, 38, 0.4); }
        .logout-btn:hover i { color: white; }

        /* Dashboard Header */
        .bookkeeper-header {
            background: linear-gradient(135deg, var(--track-bg) 0%, var(--track-beige) 100%);
            padding: 60px 0 40px;
            border-bottom: 1px solid rgba(229, 229, 192, 0.4);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .bookkeeper-header h1 { color: var(--track-dark); letter-spacing: -1.5px; }

        .bookkeeper-header::after {
            content: ''; position: absolute; top: -20%; right: -5%; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(32,160,96,0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%; z-index: 0; pointer-events: none;
        }

        .badge-platform {
            display: none;
        }

        .btn-success-coop {
            background: var(--track-green); color: white; border-radius: 12px; padding: 14px 28px;
            font-weight: 700; border: none; box-shadow: 0 8px 20px rgba(32, 160, 96, 0.2);
            transition: var(--transition-smooth);
        }
        .btn-success-coop:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(32, 160, 96, 0.3); color: white; }

        .btn-portal {
            background: var(--track-green); color: white; border-radius: 12px; padding: 14px 28px;
            font-weight: 700; border: none; box-shadow: 0 8px 20px rgba(32, 160, 96, 0.2);
            transition: var(--transition-smooth);
        }
        .btn-portal:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(32, 160, 96, 0.3); color: white; }

        /* Stat Cards */
        .stat-card {
            border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 20px; background: white;
            padding: 24px; transition: var(--transition-smooth);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03); position: relative; overflow: hidden; z-index: 1; height: 100%;
        }
        .stat-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(32, 160, 96, 0.08); border-color: rgba(32, 160, 96, 0.3); z-index: 2; }
        
        .icon-wrap { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 14px; margin-bottom: 16px; transition: 0.3s; }
        .stat-card:hover .icon-wrap { transform: scale(1.1) rotate(5deg); }

        .quick-tool-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 18px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
        }
        .quick-tool-card:hover {
            transform: translateY(-8px); border-color: rgba(32, 160, 96, 0.3) !important;
            box-shadow: 0 20px 40px rgba(32, 160, 96, 0.08); z-index: 10;
        }

        .table-card { background: white; border-radius: 20px; padding: 35px; border: 1px solid rgba(226, 232, 240, 0.8); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03); transition: var(--transition-smooth); }
        .table-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(32, 160, 96, 0.08); border-color: rgba(32, 160, 96, 0.2); }
        
        /* Premium Modal Ecosystem */
        .modal-glass { backdrop-filter: blur(15px); background: rgba(255, 255, 255, 0.8) !important; }
        .modal-header-gradient { background: linear-gradient(90deg, #f5f5dc 0%, #ffffff 100%); border-bottom: 1px solid rgba(32, 160, 96, 0.1); }
        
        @keyframes staggeredPop {
            0% { opacity: 0; transform: translateY(10px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        .stagger-1 { animation: staggeredPop 0.4s ease forwards 0.1s; opacity: 0; }
        .stagger-2 { animation: staggeredPop 0.4s ease forwards 0.2s; opacity: 0; }
        
        .report-item { transition: all 0.3s ease; border: 1.5px solid #f1f5f9 !important; }
        .report-item:hover { transform: translateY(-4px); border-color: var(--track-green) !important; box-shadow: 0 10px 20px rgba(32, 160, 96, 0.08) !important; background-color: #fff !important; }
        .search-wrapper { background: #f8f9fa; border-radius: 12px; padding: 8px 16px; display: flex; align-items: center; width: 280px; transition: 0.3s; }
        .search-wrapper:focus-within { background: #fff; box-shadow: 0 0 0 2px var(--track-green); }
        .search-wrapper input { background: transparent; border: none; outline: none; font-size: 14px; width: 100%; margin-left: 10px; }

        .custom-table { border-collapse: separate; border-spacing: 0 12px; width: 100%; }
        .custom-table thead th { font-size: 11px; text-transform: uppercase; letter-spacing: 1.2px; color: #a0aec0; font-weight: 700; border: none; padding: 0 20px 10px 20px; }
        .custom-table tbody tr { background: #fff; transition: 0.3s; cursor: pointer; }
        .custom-table tbody tr:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.05); }
        .custom-table tbody td { padding: 20px; border-top: 1px solid #f1f1f1; border-bottom: 1px solid #f1f1f1; vertical-align: middle; }
        .custom-table tbody td:first-child { border-left: 1px solid #f1f1f1; border-top-left-radius: 16px; border-bottom-left-radius: 16px; }
        .custom-table tbody td:last-child { border-right: 1px solid #f1f1f1; border-top-right-radius: 16px; border-bottom-right-radius: 16px; }

        .avatar-circle { width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: white; font-size: 14px; }
        .badge-status { padding: 8px 16px; border-radius: 50px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
        .badge-verified { background: #eefdf5; color: #20a060; }
        .badge-pending { background: #fff9e6; color: #f1c40f; }

        .sector-tag { background: #f7f9fc; color: #4a5568; padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; }

        /* Staggered Animations */
        .content-container { animation: fadeInUpCustom 0.8s ease-out 0.2s both; flex: 1; }
        .fade-in-up { opacity: 0; animation: fadeInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        .footer-track { background-color: var(--track-beige); padding: 50px 0; border-top: 1px solid rgba(0,0,0,0.03); margin-top: auto; }
        .social-icon-btn { width: 44px; height: 44px; background: white; color: var(--track-green); display: inline-flex; align-items: center; justify-content: center; border-radius: 14px; text-decoration: none; font-size: 1.2rem; transition: 0.3s; border: 1px solid rgba(0,0,0,0.05); }
        .social-icon-btn:hover { background: var(--track-green); color: white; transform: translateY(-4px); }
    </style>
    <link rel="stylesheet" href="../includes/footer.css">
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
            <div class="col-lg-7 fade-in-up">
                <div class="status-badge stagger-1">
                    <span class="spinner-grow spinner-grow-sm me-2 text-success" role="status" style="width: 10px; height: 10px;"></span>
                    System Live
                </div>
                <h1 class="fw-800 display-4 mb-2 stagger-2">Financial Overview.</h1>
                <p class="fs-5 text-muted mb-0 stagger-3" style="max-width: 700px;">Monitor share capital and manage audited financial records for NFFAC.</p>
            </div>
            <div class="col-lg-5 text-lg-end mt-4 mt-lg-0 fade-in-up d-flex flex-wrap justify-content-lg-end gap-2 text-center">
                <button class="btn-portal d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#dataCenterModal">
                    Predictive Insights
                </button>
            </div>
        </div>
    </div>
</div>

<div class="content-container pb-5" data-aos="fade-up" data-aos-duration="1000">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-md-3 fade-in-up delay-1">
                <div class="stat-card">
                    <div class="icon-wrap bg-success bg-opacity-10 text-success" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Total Share Capital</small>
                    <h3 class="fw-bold mt-1">₱125,000.0</h3>
                    <div class="progress mt-3" style="height: 4px; background: #f0f0f0;"><div class="progress-bar bg-success" style="width: 70%"></div></div>
                </div>
            </div>
            <div class="col-md-3 fade-in-up delay-2">
                <div class="stat-card">
                    <div class="icon-wrap bg-warning bg-opacity-10 text-warning" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Pending Audit</small>
                    <h3 class="fw-bold mt-1">12 Files</h3>
                    <span class="badge bg-warning-subtle text-warning mt-2">Action Required</span>
                </div>
            </div>
            <div class="col-md-3 fade-in-up delay-3">
                <div class="stat-card">
                    <div class="icon-wrap bg-primary bg-opacity-10 text-primary" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <i class="bi bi-person-check fs-4"></i>
                    </div>
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Active Members</small>
                    <h3 class="fw-bold mt-1">153</h3>
                    <small class="text-success"><i class="bi bi-caret-up-fill"></i> +5 this month</small>
                </div>
            </div>
            <div class="col-md-3 fade-in-up delay-4">
                <div class="stat-card">
                    <div class="icon-wrap bg-danger bg-opacity-10 text-danger" style="width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <i class="bi bi-file-earmark-lock fs-4"></i>
                    </div>
                    <small class="text-muted fw-bold text-uppercase" style="font-size: 10px;">Tax Documents</small>
                    <h3 class="fw-bold mt-1">Secured</h3>
                    <small class="text-muted">Last updated: 2 days ago</small>
                </div>
            </div>
        </div>

        <!-- Quick Access Tools -->
        <div class="row g-4 mb-5 mt-2">
            <div class="col-lg-3 col-md-6 fade-in-up delay-1">
                <div class="quick-tool-card p-4 rounded-3" onclick="showToolModal('dashboard')" style="background: white; border: 2px solid rgba(32, 160, 96, 0.1); cursor: pointer; height: 100%;">
                    <div class="text-primary fw-bold mb-3" style="font-size: 2rem;"><i class="bi bi-speedometer2"></i></div>
                    <div class="fw-bold text-dark mb-2">Access Dashboard</div>
                    <small class="text-muted">View overview metrics</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 fade-in-up delay-2">
                <div class="quick-tool-card p-4 rounded-3" onclick="showToolModal('engagement')" style="background: white; border: 2px solid rgba(32, 160, 96, 0.1); cursor: pointer; height: 100%;">
                    <div class="text-success fw-bold mb-3" style="font-size: 2rem;"><i class="bi bi-bar-chart-line"></i></div>
                    <div class="fw-bold text-dark mb-2">Interpret Engagement Graphs</div>
                    <small class="text-muted">Member activity trends</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 fade-in-up delay-3">
                <div class="quick-tool-card p-4 rounded-3" onclick="showToolModal('members')" style="background: white; border: 2px solid rgba(32, 160, 96, 0.1); cursor: pointer; height: 100%;">
                    <div class="text-warning fw-bold mb-3" style="font-size: 2rem;"><i class="bi bi-people-fill"></i></div>
                    <div class="fw-bold text-dark mb-2">Identify Active/At-Risk Members</div>
                    <small class="text-muted">Member status analysis</small>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 fade-in-up delay-4">
                <div class="quick-tool-card p-4 rounded-3" onclick="showToolModal('heatmap')" style="background: white; border: 2px solid rgba(32, 160, 96, 0.1); cursor: pointer; height: 100%;">
                    <div class="text-danger fw-bold mb-3" style="font-size: 2rem;"><i class="bi bi-diagram-3"></i></div>
                    <div class="fw-bold text-dark mb-2">View Sector Engagement Heatmap</div>
                    <small class="text-muted">Sector performance heat map</small>
                </div>
            </div>
        </div>

        <!-- Sample Charts Section -->
        <div class="row g-4 mb-5">
            <div class="col-lg-6 fade-in-up delay-1">
                <div class="stat-card h-100">
                    <h6 class="fw-bold text-dark mb-4"><i class="bi bi-graph-up text-success me-2"></i> Capital Growth</h6>
                    <div style="position: relative; height: 300px;">
                        <canvas id="capitalGrowthChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 fade-in-up delay-2">
                <div class="stat-card h-100">
                    <h6 class="fw-bold text-dark mb-4"><i class="bi bi-check-circle text-primary me-2"></i> Audit Completion</h6>
                    <div style="position: relative; height: 300px;">
                        <canvas id="auditChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-6 fade-in-up delay-3">
                <div class="stat-card h-100">
                    <h6 class="fw-bold text-dark mb-4"><i class="bi bi-pie-chart text-warning me-2"></i> Fund Distribution</h6>
                    <div style="position: relative; height: 300px;">
                        <canvas id="fundChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 fade-in-up delay-4">
                <div class="stat-card h-100">
                    <h6 class="fw-bold text-dark mb-4"><i class="bi bi-graph-up-arrow text-info me-2"></i> Transaction Timeline</h6>
                    <div style="position: relative; height: 300px;">
                        <canvas id="transactionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ===== TOOL MODALS ===== -->
<!-- Dashboard Tool Modal -->
<div class="modal fade" id="toolDashboardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;border:none;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: #F5F5DC; border-bottom: 1px solid rgba(229, 229, 192, 0.5); padding: 24px;">
                <h5 class="modal-title fw-bold text-dark mb-0"><i class="bi bi-speedometer2 text-primary me-2"></i> Access Dashboard</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px; background: #f8fafc;">
                <div class="mb-3">
                    <label class="fw-bold small text-muted">Filter by Fund Type:</label>
                    <select class="form-select" style="border-radius: 12px; border: 2px solid #e5e5c0;">
                        <option>All Funds</option>
                        <option>Share Capital</option>
                        <option>Emergency Fund</option>
                        <option>Development Fund</option>
                    </select>
                </div>
                <div style="position: relative; height: 350px;">
                    <canvas id="dashboardToolChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Engagement Tool Modal -->
<div class="modal fade" id="toolEngagementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;border:none;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: #F5F5DC; border-bottom: 1px solid rgba(229, 229, 192, 0.5); padding: 24px;">
                <h5 class="modal-title fw-bold text-dark mb-0"><i class="bi bi-bar-chart-line text-success me-2"></i> Interpret Engagement Graphs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px; background: #f8fafc;">
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">Date Range:</label>
                        <input type="date" class="form-control" style="border-radius: 12px; border: 2px solid #e5e5c0;">
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-muted">Member Type:</label>
                        <select class="form-select" style="border-radius: 12px; border: 2px solid #e5e5c0;">
                            <option>All Members</option>
                            <option>Active Contributors</option>
                            <option>Pending</option>
                        </select>
                    </div>
                </div>
                <div style="position: relative; height: 350px;">
                    <canvas id="engagementToolChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Members Tool Modal -->
<div class="modal fade" id="toolMembersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;border:none;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: #F5F5DC; border-bottom: 1px solid rgba(229, 229, 192, 0.5); padding: 24px;">
                <h5 class="modal-title fw-bold text-dark mb-0"><i class="bi bi-people-fill text-warning me-2"></i> Identify Active/At-Risk Members</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px; background: #f8fafc;">
                <div class="mb-3">
                    <label class="fw-bold small text-muted">Filter by Status:</label>
                    <select class="form-select" style="border-radius: 12px; border: 2px solid #e5e5c0;">
                        <option>All Members</option>
                        <option>Active</option>
                        <option>At-Risk</option>
                    </select>
                </div>
                <div style="position: relative; height: 350px; display: flex; justify-content: center;">
                    <canvas id="membersToolChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Heatmap Tool Modal -->
<div class="modal fade" id="toolHeatmapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;border:none;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header" style="background: #F5F5DC; border-bottom: 1px solid rgba(229, 229, 192, 0.5); padding: 24px;">
                <h5 class="modal-title fw-bold text-dark mb-0"><i class="bi bi-diagram-3 text-danger me-2"></i> View Sector Engagement Heatmap</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px; background: #f8fafc;">
                <div class="mb-3">
                    <label class="fw-bold small text-muted">Quarter:</label>
                    <select class="form-select" style="border-radius: 12px; border: 2px solid #e5e5c0;">
                        <option>Q1 2026</option>
                        <option>Q4 2025</option>
                        <option>Q3 2025</option>
                        <option>Year to Date</option>
                    </select>
                </div>
                <div style="position: relative; height: 350px;">
                    <canvas id="heatmapToolChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php include('../includes/footer.php'); ?>

<script>
    AOS.init({ once: true, duration: 800 });

    // Share Capital Growth Chart
    const capitalGrowthCtx = document.getElementById('capitalGrowthChart').getContext('2d');
    const capitalGrowthChart = new Chart(capitalGrowthCtx, {
        type: 'line',
        data: {
            labels: ['Sept', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb'],
            datasets: [{
                label: 'Share Capital (₱1000s)',
                data: [80, 95, 110, 120, 125, 135],
                borderColor: '#20a060',
                backgroundColor: 'rgba(32, 160, 96, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointBackgroundColor: '#20a060',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart',
                delay: (ctx) => ctx.dataIndex * 100
            },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false, labels: { font: { size: 12, weight: 600 }, padding: 15 } },
                tooltip: {
                    backgroundColor: 'rgba(26, 26, 26, 0.9)',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 12,
                    borderColor: '#20a060',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)', lineWidth: 1 },
                    ticks: { font: { size: 11, weight: 500 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, weight: 500 } }
                }
            }
        }
    });

    // Audit Completion Chart
    const auditCtx = document.getElementById('auditChart').getContext('2d');
    const auditChart = new Chart(auditCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Completion Rate (%)',
                data: [75, 82, 88, 85, 92, 95],
                backgroundColor: ['rgba(32, 160, 96, 0.8)', 'rgba(32, 160, 96, 0.85)', 'rgba(32, 160, 96, 0.9)', 'rgba(32, 160, 96, 0.87)', 'rgba(32, 160, 96, 0.95)', 'rgba(32, 160, 96, 0.98)'],
                borderColor: '#20a060',
                borderWidth: 2,
                borderRadius: 12,
                hoverBackgroundColor: '#20a060'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart',
                delay: (ctx) => ctx.dataIndex * 120
            },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(26, 26, 26, 0.9)',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 12,
                    borderColor: '#20a060',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)', lineWidth: 1 },
                    ticks: { font: { size: 11, weight: 500 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, weight: 500 } }
                }
            }
        }
    });

    // Fund Distribution Pie Chart
    const fundCtx = document.getElementById('fundChart').getContext('2d');
    const fundChart = new Chart(fundCtx, {
        type: 'doughnut',
        data: {
            labels: ['Operations', 'Development', 'Emergency Fund'],
            datasets: [{
                data: [45, 35, 20],
                backgroundColor: ['#20a060', '#0d6efd', '#ffc107'],
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart',
                animateScale: true
            },
            interaction: { mode: 'point', intersect: false },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { 
                        padding: 20, 
                        font: { size: 12, weight: 'bold' },
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(26, 26, 26, 0.9)',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 12,
                    borderColor: '#20a060',
                    borderWidth: 1
                }
            }
        }
    });

    // Transaction Timeline Chart
    const transactionCtx = document.getElementById('transactionChart').getContext('2d');
    const transactionChart = new Chart(transactionCtx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Transactions',
                data: [35, 48, 52, 42, 65, 38, 28],
                borderColor: '#20a060',
                backgroundColor: 'rgba(32, 160, 96, 0.15)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointBackgroundColor: '#20a060',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart',
                delay: (ctx) => ctx.dataIndex * 100
            },
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(26, 26, 26, 0.9)',
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 12,
                    borderColor: '#20a060',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)', lineWidth: 1 },
                    ticks: { font: { size: 11, weight: 500 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, weight: 500 } }
                }
            }
        }
    });

    // Tool Modal Functions
    function showToolModal(toolType) {
        let modalId = '#toolDashboardModal';
        
        if (toolType === 'dashboard') {
            modalId = '#toolDashboardModal';
        } else if (toolType === 'engagement') {
            modalId = '#toolEngagementModal';
        } else if (toolType === 'members') {
            modalId = '#toolMembersModal';
        } else if (toolType === 'heatmap') {
            modalId = '#toolHeatmapModal';
        }
        
        const modal = new bootstrap.Modal(document.querySelector(modalId));
        modal.show();
        
        // Initialize chart after modal is visible
        setTimeout(() => {
            initializeToolCharts(toolType);
        }, 300);
    }

    function initializeToolCharts(toolType) {
        if (toolType === 'dashboard') {
            const chartCanvas = document.getElementById('dashboardToolChart');
            if (chartCanvas && !chartCanvas.dataset.initialized) {
                const ctx = chartCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Fund A', 'Fund B', 'Fund C', 'Fund D'],
                        datasets: [{
                            label: 'Fund Performance',
                            data: [45, 38, 52, 41],
                            backgroundColor: ['rgba(32, 160, 96, 0.9)', 'rgba(32, 160, 96, 0.8)', 'rgba(32, 160, 96, 0.95)', 'rgba(32, 160, 96, 0.85)'],
                            borderColor: '#20a060',
                            borderWidth: 2,
                            borderRadius: 12,
                            hoverBackgroundColor: '#20a060'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart',
                            delay: (ctx) => ctx.dataIndex * 100
                        },
                        interaction: { mode: 'index', intersect: false },
                        plugins: { 
                            legend: { 
                                display: true,
                                labels: { font: { size: 11, weight: 600 }, padding: 15 }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(26, 26, 26, 0.9)',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                padding: 10,
                                cornerRadius: 10,
                                borderColor: '#20a060',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)' }, ticks: { font: { size: 10, weight: 500 } } },
                            x: { grid: { display: false }, ticks: { font: { size: 10, weight: 500 } } }
                        }
                    }
                });
                chartCanvas.dataset.initialized = 'true';
            }
        }
        else if (toolType === 'engagement') {
            const chartCanvas = document.getElementById('engagementToolChart');
            if (chartCanvas && !chartCanvas.dataset.initialized) {
                const ctx = chartCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'],
                        datasets: [{
                            label: 'Engagement Score',
                            data: [65, 72, 68, 80, 85],
                            borderColor: '#20a060',
                            backgroundColor: 'rgba(32, 160, 96, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 6,
                            pointBackgroundColor: '#20a060',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart',
                            delay: (ctx) => ctx.dataIndex * 100
                        },
                        interaction: { mode: 'index', intersect: false },
                        plugins: { 
                            legend: { 
                                display: true,
                                labels: { font: { size: 11, weight: 600 }, padding: 15 }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(26, 26, 26, 0.9)',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                padding: 10,
                                cornerRadius: 10,
                                borderColor: '#20a060',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            y: { beginAtZero: true, grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)' }, ticks: { font: { size: 10, weight: 500 } } },
                            x: { grid: { display: false }, ticks: { font: { size: 10, weight: 500 } } }
                        }
                    }
                });
                chartCanvas.dataset.initialized = 'true';
            }
        }
        else if (toolType === 'members') {
            const chartCanvas = document.getElementById('membersToolChart');
            if (chartCanvas && !chartCanvas.dataset.initialized) {
                const ctx = chartCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Active', 'At-Risk', 'Inactive'],
                        datasets: [{
                            data: [65, 25, 10],
                            backgroundColor: ['#20a060', '#ffc107', '#dc3545'],
                            borderColor: '#fff',
                            borderWidth: 3,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart',
                            animateScale: true
                        },
                        interaction: { mode: 'point', intersect: false },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { 
                                    padding: 20, 
                                    font: { size: 11, weight: 'bold' },
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(26, 26, 26, 0.9)',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                padding: 10,
                                cornerRadius: 10,
                                borderColor: '#20a060',
                                borderWidth: 1
                            }
                        }
                    }
                });
                chartCanvas.dataset.initialized = 'true';
            }
        }
        else if (toolType === 'heatmap') {
            const chartCanvas = document.getElementById('heatmapToolChart');
            if (chartCanvas && !chartCanvas.dataset.initialized) {
                const ctx = chartCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Rice', 'Corn', 'Fishery', 'Livestock', 'High Value Crops'],
                        datasets: [{
                            label: 'Activity Intensity',
                            data: [78, 65, 82, 72, 85],
                            backgroundColor: ['rgba(32, 160, 96, 0.9)', 'rgba(32, 160, 96, 0.7)', 'rgba(32, 160, 96, 0.95)', 'rgba(32, 160, 96, 0.75)', 'rgba(32, 160, 96, 0.85)'],
                            borderColor: '#20a060',
                            borderWidth: 2,
                            borderRadius: 12,
                            hoverBackgroundColor: '#20a060'
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart',
                            delay: (ctx) => ctx.dataIndex * 100
                        },
                        interaction: { mode: 'index', intersect: false },
                        plugins: { 
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(26, 26, 26, 0.9)',
                                titleFont: { size: 12, weight: 'bold' },
                                bodyFont: { size: 11 },
                                padding: 10,
                                cornerRadius: 10,
                                borderColor: '#20a060',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            x: { beginAtZero: true, grid: { drawBorder: false, color: 'rgba(0, 0, 0, 0.05)' }, ticks: { font: { size: 10, weight: 500 } } },
                            y: { grid: { display: false }, ticks: { font: { size: 10, weight: 500 } } }
                        }
                    }
                });
                chartCanvas.dataset.initialized = 'true';
            }
        }
    }

    // Add hover effects to quick tool cards
    document.querySelectorAll('.quick-tool-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
            this.style.boxShadow = '0 12px 24px rgba(32, 160, 96, 0.15)';
            this.style.borderColor = 'rgb(32, 160, 96)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'none';
            this.style.boxShadow = 'none';
            this.style.borderColor = 'rgba(32, 160, 96, 0.1)';
        });
    });
</script>
</body>
</html>