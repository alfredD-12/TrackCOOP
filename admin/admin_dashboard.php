<?php
session_start();
include('../auth/db_connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

// --- ADDITIONAL CODE FOR FULL NAME ---
$user_id = $_SESSION['user_id']; // Ensure this is set in your login script
$full_name = "Admin Jakob"; // Fallback text

$query = "SELECT first_name, last_name FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    // Concatenate First Name and Last Name
    $full_name = $user['first_name'] . " " . $user['last_name'];
}

// Fetch media activities for management modal
$media_activities_query = mysqli_query($conn, "SELECT id, title, description, category, activity_date, file_path FROM media_activities ORDER BY activity_date DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | TrackCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --track-green: #20a060; 
            --track-green-light: #e9f5ee;
            --track-dark: #1a1a1a; 
            --track-bg: #f8fafc;
            --track-beige: #F5F5DC; 
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --text-main: #212529; 
            --text-muted: #555555; 
        }

        @keyframes fadeInUpCustom {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        body { 
            background-color: var(--track-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
        }

        .logout-btn {
            border: 2px solid #dc2626;
            color: #dc2626;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: var(--transition-smooth);
            text-decoration: none;
            animation: fadeInUpCustom 0.8s ease-out 0.3s both;
        }

        .logout-btn:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220, 38, 38, 0.4);
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
        }
        .navbar-brand span { color: var(--track-green); }

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

        /* --- Dashboard Specific Styles --- */
        .admin-header {
            background: linear-gradient(135deg, var(--track-bg) 0%, var(--track-beige) 100%);
            padding: 60px 0 40px;
            border-bottom: 1px solid rgba(229, 229, 192, 0.4);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .status-badge {
            display: inline-flex; align-items: center; background: white; color: var(--track-green);
            font-weight: 700; padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; 
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.1); border: 1px solid rgba(32, 160, 96, 0.2);
        }

        /* Premium Modal Styles */
        .modal-glass {
            backdrop-filter: blur(15px);
            background: rgba(255, 255, 255, 0.8) !important;
        }
        .modal-header-gradient {
            background: linear-gradient(90deg, #f5f5dc 0%, #ffffff 100%);
            border-bottom: 1px solid rgba(32, 160, 96, 0.1);
        }
        
        @keyframes staggeredPop {
            0% { opacity: 0; transform: translateY(10px) scale(0.95); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        .stagger-1 { animation: staggeredPop 0.4s ease forwards 0.1s; opacity: 0; }
        .stagger-2 { animation: staggeredPop 0.4s ease forwards 0.2s; opacity: 0; }
        .stagger-3 { animation: staggeredPop 0.4s ease forwards 0.3s; opacity: 0; }

        .admin-header h1 { color: var(--track-dark); letter-spacing: -1.5px; }

        .admin-header::after {
            content: ''; position: absolute; top: -20%; right: -5%; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(32,160,96,0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%; z-index: 0; pointer-events: none;
        }

        .badge-platform {
            background: white; color: var(--track-green); font-weight: 700; padding: 6px 14px;
            border-radius: 50px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
            display: inline-flex; align-items: center; margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.1); border: 1px solid rgba(32, 160, 96, 0.2);
        }

        .stat-card {
            border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 20px; background: white;
            padding: 24px; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03); position: relative; overflow: hidden;
            z-index: 1;
        }

        .stat-card:hover {
            transform: translateY(-8px); box-shadow: 0 20px 40px rgba(32, 160, 96, 0.08); border-color: rgba(32, 160, 96, 0.3); z-index: 2;
        }

        .icon-box {
            width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;
            border-radius: 14px; margin-bottom: 16px; transition: 0.3s;
        }
        
        .stat-card:hover .icon-box { transform: scale(1.1) rotate(5deg); }

        .quick-tool-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-radius: 18px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03);
        }
        .quick-tool-card:hover {
            transform: translateY(-8px); border-color: rgba(32, 160, 96, 0.3) !important;
            box-shadow: 0 20px 40px rgba(32, 160, 96, 0.08); z-index: 10;
        }

        .btn-portal {
            background: var(--track-green); color: white; border-radius: 12px; padding: 14px 28px;
            font-weight: 700; border: none; box-shadow: 0 8px 20px rgba(32, 160, 96, 0.2);
            transition: var(--transition-smooth);
        }
        .btn-portal:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(32, 160, 96, 0.3); color: white; }

        .activity-item { padding: 12px; margin-bottom: 10px; border-radius: 14px; transition: var(--transition-smooth); border: 1px solid transparent; background: #fff; cursor: pointer; }
        .activity-item:hover { background: var(--track-bg); border-color: #e2e8f0; transform: translateX(5px); box-shadow: 4px 0 0 var(--track-green); }

        /* Report Modal Items Animation */
        .report-item { transition: all 0.3s ease; border: 1.5px solid #f1f5f9 !important; }
        .report-item:hover { transform: translateY(-4px); border-color: var(--track-green) !important; box-shadow: 0 10px 20px rgba(32, 160, 96, 0.08) !important; background-color: #fff !important; }

        /* Staggered Animations */
        .fade-in-up { opacity: 0; animation: fadeInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        .footer-track { background-color: var(--track-beige); border-top: 1px solid #e5e5c0; padding: 40px 0; margin-top: 60px; }

        .social-btn {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: white;
            color: var(--track-green);
            border: 1px solid #e5e5c0;
            transition: var(--transition-smooth);
        }
        /* Modal Tabs Styling Override */
        .nav-pills .nav-link.active {
            background-color: var(--track-green) !important;
            color: white !important;
        }
        .nav-pills .nav-link {
            transition: all 0.3s ease;
            color: var(--text-muted) !important;
        }
        .nav-pills .nav-link:hover {
            color: var(--track-green) !important;
            background-color: rgba(32, 160, 96, 0.05);
        }
        
        /* Modal Form Fields Override */
        .modal-body .form-control, .modal-body .form-select {
            border: 2px solid #e5e5c0 !important; /* Beige border */
            transition: var(--transition-smooth);
        }
        .modal-body .form-control:focus, .modal-body .form-select:focus {
            border-color: var(--track-green) !important;
            box-shadow: 0 0 0 3px rgba(32, 160, 96, 0.2) !important;
        }

        /* Manage Photos Advanced Animation & Design */
        @keyframes popInSelect {
            0% { opacity: 0; transform: translateY(15px) scale(0.98); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        .manage-row {
            opacity: 0;
            animation: popInSelect 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: all 0.3s ease;
            cursor: default;
        }
        .manage-row:nth-child(1) { animation-delay: 0.1s; }
        .manage-row:nth-child(2) { animation-delay: 0.15s; }
        .manage-row:nth-child(3) { animation-delay: 0.2s; }
        .manage-row:nth-child(4) { animation-delay: 0.25s; }
        .manage-row:nth-child(5) { animation-delay: 0.3s; }
        .manage-row:nth-child(n+6) { animation-delay: 0.35s; }
        
        .manage-row:hover {
            background-color: #f8fafc !important;
            transform: translateX(4px);
            box-shadow: inset 4px 0 0 var(--track-green);
        }
        
        .manage-photo-wrapper {
            overflow: hidden; border-radius: 12px; display: inline-block;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .manage-photo-img {
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .manage-row:hover .manage-photo-img {
            transform: scale(1.15);
        }

        .action-btns {
            opacity: 0.3; transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .manage-row:hover .action-btns {
            opacity: 1; transform: scale(1.05);
        }

        /* Predictive Insights Modal Animations */
        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .fade-in-card {
            opacity: 0;
        }

        .intervention-row {
            opacity: 0;
            transition: all 0.3s ease;
        }

        .intervention-row:hover {
            background-color: #f8fafc;
            transform: translateX(4px);
        }

        .prediction-item {
            opacity: 0;
            animation: slideInDown 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .prediction-item:nth-child(1) { animation-delay: 0.1s; }
        .prediction-item:nth-child(2) { animation-delay: 0.15s; }
        .prediction-item:nth-child(3) { animation-delay: 0.2s; }

        .risk-badge {
            transition: all 0.3s ease;
        }

        .risk-badge:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .status-preview {
            cursor: default;
        }

        @media (max-width: 768px) {
            #dataCenterModal .modal-lg {
                width: 95vw;
            }
        }
    </style>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../includes/footer.css">
</head>
<body>

<?php 
    $user_role = 'Admin';
    $active_page = 'dashboard';
    $membership_type = 'Administrator';
    include('../includes/dashboard_navbar.php'); 
?>

<div class="admin-header">
    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-center mb-0">
            <div class="col-lg-7 fade-in-up">
                <div class="status-badge">
                    <span class="spinner-grow spinner-grow-sm me-2 text-success" role="status" style="width: 10px; height: 10px;"></span>
                    System Live
                </div>
                <h1 class="fw-800 display-3 mb-2">Coop Insights.</h1>
                <p class="fs-5 text-muted mb-0" style="max-width: 600px;">Overview of membership growth and system activity records.</p>
            </div>
            <div class="col-lg-5 text-lg-end mt-4 mt-lg-0 fade-in-up d-flex flex-wrap justify-content-lg-end gap-2 text-center">
                <button class="btn-portal d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#dataCenterModal">
                    Predictive Insights
                </button>
                <button type="button" data-bs-toggle="modal" data-bs-target="#uploadMediaModal" class="btn-portal d-inline-flex align-items-center gap-2">
                    <i class="bi bi-images"></i> Upload Photo
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5" data-aos="fade-up" data-aos-duration="1000">
    <div class="row g-4 mb-5">
        <div class="col-md-3 fade-in-up delay-1">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-people-fill fs-4"></i></div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-bold">+12%</span>
                </div>
                <h6 class="text-uppercase fw-bold small mb-2 text-muted" style="letter-spacing: 0.5px;">Total Members</h6>
                <h2 class="fw-800 mb-0 text-dark">1,240</h2>
            </div>
        </div>
        <div class="col-md-3 fade-in-up delay-2">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-history fs-4"></i></div>
                    <i class="bi bi-exclamation-circle text-warning"></i>
                </div>
                <h6 class="text-uppercase fw-bold small mb-2 text-muted" style="letter-spacing: 0.5px;">Pending Apps</h6>
                <h2 class="fw-800 mb-0 text-dark">18</h2>
            </div>
        </div>
        <div class="col-md-3 fade-in-up delay-3">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-diagram-3-fill fs-4"></i></div>
                </div>
                <h6 class="text-uppercase fw-bold small mb-2 text-muted" style="letter-spacing: 0.5px;">Agri Sectors</h6>
                <h2 class="fw-800 mb-0 text-dark">5 Active</h2>
            </div>
        </div>
        <div class="col-md-3 fade-in-up delay-4">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-info bg-opacity-10 text-info"><i class="bi bi-cloud-check-fill fs-4"></i></div>
                </div>
                <h6 class="text-uppercase fw-bold small mb-2 text-muted" style="letter-spacing: 0.5px;">Stored Docs</h6>
                <h2 class="fw-800 mb-0 text-dark">145</h2>
            </div>
        </div>
    </div>


    <div class="row g-4">
        <div class="col-lg-8 fade-in-up delay-5">
            <div class="stat-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart-fill text-success me-2"></i> Sector Distribution</h5>
                    <a href="#" class="text-success text-decoration-none fw-bold small">Details <i class="bi bi-chevron-right"></i></a>
                </div>
                <div class="progress sector-progress mb-5 overflow-visible" style="height: 30px; border-radius: 50px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                    <div class="progress-bar bg-success" style="width: 40%; border-radius: 50px 0 0 50px;">Rice 40%</div>
                    <div class="progress-bar" style="width: 25%; background-color: #f1c40f;">Corn 25%</div>
                    <div class="progress-bar bg-primary" style="width: 20%">Fishery 20%</div>
                    <div class="progress-bar bg-info" style="width: 15%; border-radius: 0 50px 50px 0;">Others 15%</div>
                </div>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-white border rounded-4 text-center" style="transition: 0.3s;" onmouseover="this.style.transform='translateY(-5px)'; this.style.borderColor='var(--track-green)';" onmouseout="this.style.transform='none'; this.style.borderColor='#dee2e6';">
                            <small class="text-muted d-block fw-bold mb-1" style="font-size: 0.7rem;">RICE</small>
                            <span class="h4 fw-800 mb-0 text-success">40%</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-white border rounded-4 text-center" style="transition: 0.3s;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='none';">
                            <small class="text-muted d-block fw-bold mb-1" style="font-size: 0.7rem;">CORN</small>
                            <span class="h4 fw-800 mb-0 text-warning">25%</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-white border rounded-4 text-center" style="transition: 0.3s;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='none';">
                            <small class="text-muted d-block fw-bold mb-1" style="font-size: 0.7rem;">FISHERY</small>
                            <span class="h4 fw-800 mb-0 text-primary">20%</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-white border rounded-4 text-center" style="transition: 0.3s;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='none';">
                            <small class="text-muted d-block fw-bold mb-1" style="font-size: 0.7rem;">OTHERS</small>
                            <span class="h4 fw-800 mb-0 text-info">15%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 fade-in-up delay-5">
            <div class="stat-card h-100">
                <h5 class="fw-bold mb-4 pb-3 border-bottom text-dark"><i class="bi bi-activity text-success me-2"></i> Recent Activity</h5>
                <div class="activity-item d-flex align-items-center">
                    <div class="icon-box bg-success bg-opacity-10 text-success mb-0 me-3 rounded-circle" style="width: 45px; height: 45px;">
                        <i class="bi bi-file-earmark-check border-0"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold" style="font-size: 0.95rem; color: var(--track-dark);">COC 2026 Uploaded</p>
                        <small class="text-muted fw-semibold">2 hours ago</small>
                    </div>
                </div>
                <div class="activity-item d-flex align-items-center">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning mb-0 me-3 rounded-circle" style="width: 45px; height: 45px;">
                        <i class="bi bi-person-check border-0"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold" style="font-size: 0.95rem; color: var(--track-dark);">New Member Verified</p>
                        <small class="text-muted fw-semibold">5 hours ago</small>
                    </div>
                </div>
                <div class="activity-item d-flex align-items-center opacity-75">
                    <div class="icon-box bg-secondary bg-opacity-10 text-secondary mb-0 me-3 rounded-circle" style="width: 45px; height: 45px;">
                        <i class="bi bi-cloud-arrow-up border-0"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold" style="font-size: 0.95rem; color: var(--track-dark);">Cloud Backup Sync</p>
                        <small class="text-muted fw-semibold">Yesterday</small>
                    </div>
                </div>
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
            <h6 class="fw-bold text-dark mb-4"><i class="bi bi-graph-up text-success me-2"></i> Member Growth Trend</h6>
            <div style="position: relative; height: 300px;">
                <canvas id="memberGrowthChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6 fade-in-up delay-2">
        <div class="stat-card h-100">
            <h6 class="fw-bold text-dark mb-4"><i class="bi bi-check-circle text-primary me-2"></i> Monthly Contributions</h6>
            <div style="position: relative; height: 300px;">
                <canvas id="contributionsChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-lg-6 fade-in-up delay-3">
        <div class="stat-card h-100">
            <h6 class="fw-bold text-dark mb-4"><i class="bi bi-exclamation-triangle text-warning me-2"></i> Risk Distribution</h6>
            <div style="position: relative; height: 300px;">
                <canvas id="riskChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6 fade-in-up delay-4">
        <div class="stat-card h-100">
            <h6 class="fw-bold text-dark mb-4"><i class="bi bi-activity text-info me-2"></i> Activity Timeline</h6>
            <div style="position: relative; height: 300px;">
                <canvas id="activityChart"></canvas>
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
                    <label class="fw-bold small text-muted">Filter by Member Type:</label>
                    <select class="form-select" style="border-radius: 12px; border: 2px solid #e5e5c0;">
                        <option>All Members</option>
                        <option>Active</option>
                        <option>Pending</option>
                        <option>Inactive</option>
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
                        <label class="fw-bold small text-muted">Sector:</label>
                        <select class="form-select" style="border-radius: 12px; border: 2px solid #e5e5c0;">
                            <option>All Sectors</option>
                            <option>Rice</option>
                            <option>Corn</option>
                            <option>Fishery</option>
                            <option>Livestock</option>
                            <option>High Value Crops</option>
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




<!-- ===== GALLERY MANAGEMENT MODAL ===== -->
<div class="modal fade" id="uploadMediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;border:none;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header d-flex align-items-center" style="background-color: #f5f5dc; border-bottom: 1px solid rgba(229, 229, 192, 0.5); padding: 20px 24px;">
                <h5 class="modal-title fw-bold text-dark mb-0"><i class="bi bi-images text-success me-2"></i> Manage Gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body" style="padding: 24px; background: white;">
                <ul class="nav nav-pills mb-4" id="galleryTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active px-4 fw-bold rounded-pill" id="upload-tab" data-bs-toggle="pill" data-bs-target="#upload" type="button" role="tab"><i class="bi bi-cloud-upload-fill me-2"></i> Upload New</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link px-4 fw-bold rounded-pill" id="manage-tab" data-bs-toggle="pill" data-bs-target="#manage" type="button" role="tab"><i class="bi bi-list-check me-2"></i> Manage Photos</button>
                    </li>
                </ul>

                <div class="tab-content" id="galleryTabsContent">
                    <!-- UPLOAD TAB -->
                    <div class="tab-pane fade show active" id="upload" role="tabpanel">
                        <form action="../media/media_actions.php" method="POST" enctype="multipart/form-data" id="uploadForm" onsubmit="return TrackUI.confirmForm(event, 'Publish this activity photo to the public gallery?', 'Upload Media', 'primary', 'Publish Now', 'Review')">
                            <input type="hidden" name="upload_media" value="1">
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Activity Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Rice Farming Workshop" required style="border-radius:12px;">
                            </div>

                            <div class="row mb-3 g-2">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Category</label>
                                    <select name="category" class="form-select" required style="border-radius:12px;">
                                        <option value="Training">Training</option>
                                        <option value="Harvesting">Harvesting</option>
                                        <option value="Meeting">Meeting</option>
                                        <option value="Livelihood">Livelihood</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Activity Date</label>
                                    <input type="date" name="activity_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required style="border-radius:12px;">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-muted">Short Description</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Briefly describe what happened..." required style="border-radius:12px;"></textarea>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold small text-muted">Select Photo</label>
                                <input type="file" name="media_file" id="media_file" class="form-control mb-2" accept="image/*" required onchange="previewDashboardImage(event)" style="border-radius:12px;">
                                <div class="image-preview-box" id="previewAreaDashboard" style="width:100%;height:160px;background:#f8fafc;border:2px dashed #cbd5e1;border-radius:16px;display:flex;align-items:center;justify-content:center;flex-direction:column;color:#94a3b8;overflow:hidden;">
                                    <i class="bi bi-image fs-2 opacity-25"></i>
                                    <span class="small mt-1">No Image Selected</span>
                                </div>
                            </div>
                            
                            <div class="modal-footer mt-4" style="background-color: #f5f5dc; border-top: 1px solid rgba(229, 229, 192, 0.5); padding: 16px 24px; margin: 0 -24px -24px -24px; border-radius: 0 0 24px 24px;">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn px-4 fw-bold" style="background:var(--track-green);color:white;border-radius:50px;">
                                    <i class="bi bi-cloud-upload-fill me-2"></i> Upload Photo
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- MANAGE TAB -->
                    <div class="tab-pane fade" id="manage" role="tabpanel">
                        <div class="table-responsive pe-2" style="max-height: 420px; overflow-y: auto;">
                            <table class="table align-middle table-borderless" style="border-collapse: separate; border-spacing: 0 8px;">
                                <thead style="position: sticky; top: -8px; background: white; z-index: 2; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
                                    <tr>
                                        <th class="text-uppercase small text-muted fw-bold ps-3 py-3" style="letter-spacing: 1px;">Photo</th>
                                        <th class="text-uppercase small text-muted fw-bold py-3" style="letter-spacing: 1px;">Details</th>
                                        <th class="text-uppercase small text-muted fw-bold py-3" style="letter-spacing: 1px;">Date</th>
                                        <th class="text-uppercase small text-muted fw-bold text-end pe-3 py-3" style="letter-spacing: 1px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($media_activities_query) > 0): ?>
                                        <?php while($m = mysqli_fetch_assoc($media_activities_query)): ?>
                                        <tr class="manage-row bg-white" style="border: 1px solid #f1f5f9; border-radius: 16px;">
                                            <td class="ps-3 py-2" style="border-top-left-radius: 16px; border-bottom-left-radius: 16px;">
                                                <div class="manage-photo-wrapper">
                                                    <img src="../<?php echo htmlspecialchars($m['file_path']); ?>" class="manage-photo-img" style="width:70px;height:55px;object-fit:cover;">
                                                </div>
                                            </td>
                                            <td class="py-2">
                                                <div class="fw-bold text-dark mb-1" style="font-size:0.95rem;"><?php echo htmlspecialchars($m['title']); ?></div>
                                                <span class="badge" style="background: rgba(32,160,96,0.1); color: var(--track-green); border: 1px solid rgba(32,160,96,0.2); border-radius: 50px; padding: 4px 10px;">
                                                    <i class="bi bi-tag-fill me-1 opacity-75"></i> <?php echo htmlspecialchars($m['category']); ?>
                                                </span>
                                            </td>
                                            <td class="text-muted small fw-bold py-2">
                                                <i class="bi bi-calendar3 me-2 opacity-50"></i><?php echo date('M d, Y', strtotime($m['activity_date'])); ?>
                                            </td>
                                            <td class="text-end pe-3 py-2" style="border-top-right-radius: 16px; border-bottom-right-radius: 16px;">
                                                <div class="d-flex justify-content-end gap-2 action-btns">
                                                    <button type="button" class="btn btn-sm btn-light text-primary rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;" title="Edit"
                                                        onclick="openGalleryEditModal(
                                                            <?php echo $m['id']; ?>,
                                                            '<?php echo addslashes(htmlspecialchars($m['title'])); ?>',
                                                            '<?php echo addslashes(htmlspecialchars($m['description'])); ?>',
                                                            '<?php echo $m['category']; ?>',
                                                            '<?php echo $m['activity_date']; ?>'
                                                        )">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </button>
                                                    <a href="../media/media_actions.php?delete_id=<?php echo $m['id']; ?>" class="btn btn-sm btn-light text-danger rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;" title="Delete"
                                                       onclick="return TrackUI.confirmLink(event, 'Permanently delete this activity photo?', 'Delete Media', 'danger', 'Delete Now', 'Keep It')">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <i class="bi bi-images fs-1 opacity-25 d-block mb-3"></i>
                                                <div class="fw-bold">No Photos Found</div>
                                                <small>Your gallery is empty. Upload one from the Upload Tab.</small>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer mt-4" style="background-color: #f5f5dc; border-top: 1px solid rgba(229, 229, 192, 0.5); padding: 16px 24px; margin: 0 -24px -24px -24px; border-radius: 0 0 24px 24px;">
                            <button type="button" class="btn rounded-pill px-4 fw-bold" style="background:var(--track-green);color:white;box-shadow: 0 4px 12px rgba(32, 160, 96, 0.2);" data-bs-dismiss="modal">Close Gallery</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== PREDICTIVE INSIGHTS MODAL ===== -->
<div class="modal fade" id="dataCenterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;border:none;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header d-flex align-items-center" style="background: linear-gradient(135deg, #f5f5dc 0%, #ffffff 100%); border-bottom: 1px solid rgba(229, 229, 192, 0.5); padding: 24px;">
                <h5 class="modal-title fw-bold text-dark mb-0">Predictive Insights</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body" style="padding: 24px; background: #f8fafc;">
                <!-- View Predicted Member Status -->
                <div class="insight-section mb-4 fade-in-card" style="animation: slideInDown 0.6s ease-out;">
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="icon-box bg-success bg-opacity-10 text-success rounded-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0 ms-3">View Predicted Member Status</h6>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="status-preview p-3 rounded-3" style="background: white; border: 2px solid rgba(32, 160, 96, 0.1); transition: all 0.3s ease;" onmouseover="this.style.borderColor='rgb(32, 160, 96)'; this.style.boxShadow='0 8px 16px rgba(32, 160, 96, 0.1)';" onmouseout="this.style.borderColor='rgba(32, 160, 96, 0.1)'; this.style.boxShadow='none';">
                                <div class="fw-bold text-muted small mb-2">Active Members</div>
                                <h3 class="fw-800 text-success mb-0">1,087</h3>
                                <small class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Stable participation</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="status-preview p-3 rounded-3" style="background: white; border: 2px solid rgba(32, 160, 96, 0.1); transition: all 0.3s ease;" onmouseover="this.style.borderColor='rgb(32, 160, 96)'; this.style.boxShadow='0 8px 16px rgba(32, 160, 96, 0.1)';" onmouseout="this.style.borderColor='rgba(32, 160, 96, 0.1)'; this.style.boxShadow='none';">
                                <div class="fw-bold text-muted small mb-2">At-Risk Members</div>
                                <h3 class="fw-800 text-warning mb-0">98</h3>
                                <small class="text-warning"><i class="bi bi-exclamation-circle-fill me-1"></i> Needs attention</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Understand Risk Classification -->
                <div class="insight-section mb-4 fade-in-card" style="animation: slideInDown 0.6s ease-out 0.1s both;">
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="icon-box bg-info bg-opacity-10 text-info rounded-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0 ms-3">Understand Risk Classification</h6>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="risk-badge p-2 rounded-3 text-center" style="background: rgba(32, 160, 96, 0.05); border: 1px solid rgb(32, 160, 96);">
                                <div class="fw-bold text-success mb-1" style="font-size: 0.85rem;">LOW RISK</div>
                                <div class="text-muted small"><i class="bi bi-check-lg me-1"></i>Active (0-45 days)</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="risk-badge p-2 rounded-3 text-center" style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgb(255, 193, 7);">
                                <div class="fw-bold text-warning mb-1" style="font-size: 0.85rem;">MEDIUM RISK</div>
                                <div class="text-muted small"><i class="bi bi-exclamation-lg me-1"></i>Delaying (45-90 days)</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="risk-badge p-2 rounded-3 text-center" style="background: rgba(220, 53, 69, 0.1); border: 1px solid rgb(220, 53, 69);">
                                <div class="fw-bold text-danger mb-1" style="font-size: 0.85rem;">HIGH RISK</div>
                                <div class="text-muted small"><i class="bi bi-x-lg me-1"></i>Dormant (90+ days)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interpret Prediction Results -->
                <div class="insight-section mb-4 fade-in-card" style="animation: slideInDown 0.6s ease-out 0.2s both;">
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0 ms-3">Interpret Prediction Results</h6>
                    </div>
                    <div class="prediction-cards">
                        <div class="prediction-item p-3 rounded-3 mb-2" style="background: white; border-left: 4px solid rgb(32, 160, 96); transition: all 0.3s ease;" onmouseover="this.style.transform='translateX(8px)'; this.style.boxShadow='0 4px 12px rgba(32, 160, 96, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold text-dark mb-1">Stable Participation Trend</div>
                                    <small class="text-muted">Low-risk members showing consistent engagement and contribution patterns.</small>
                                </div>
                                <badge class="badge bg-success" style="border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-check"></i></badge>
                            </div>
                        </div>
                        <div class="prediction-item p-3 rounded-3 mb-2" style="background: white; border-left: 4px solid rgb(255, 193, 7); transition: all 0.3s ease;" onmouseover="this.style.transform='translateX(8px)'; this.style.boxShadow='0 4px 12px rgba(255, 193, 7, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold text-dark mb-1">Retention Risk Alert</div>
                                    <small class="text-muted">Members with activity gaps showing early signs of disengagement requiring outreach.</small>
                                </div>
                                <badge class="badge bg-warning" style="border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; color: #fff;"><i class="bi bi-exclamation"></i></badge>
                            </div>
                        </div>
                        <div class="prediction-item p-3 rounded-3" style="background: white; border-left: 4px solid rgb(220, 53, 69); transition: all 0.3s ease;" onmouseover="this.style.transform='translateX(8px)'; this.style.boxShadow='0 4px 12px rgba(220, 53, 69, 0.15)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold text-dark mb-1">Dormancy Forecast</div>
                                    <small class="text-muted">Critical engagement gaps indicating imminent or ongoing membership dormancy status.</small>
                                </div>
                                <badge class="badge bg-danger" style="border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-x"></i></badge>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Identify Members Requiring Intervention -->
                <div class="insight-section fade-in-card" style="animation: slideInDown 0.6s ease-out 0.3s both;">
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0 ms-3">Identify Members Requiring Intervention</h6>
                    </div>
                    <div class="intervention-table table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-sm align-middle mb-0">
                            <thead style="position: sticky; top: 0; background: white; z-index: 2;">
                                <tr style="border-bottom: 2px solid #f1f5f9;">
                                    <th class="fw-bold text-muted small" style="letter-spacing: 0.5px;">Member</th>
                                    <th class="fw-bold text-muted small text-center" style="letter-spacing: 0.5px;">Risk</th>
                                    <th class="fw-bold text-muted small text-center" style="letter-spacing: 0.5px;">Last Activity</th>
                                    <th class="fw-bold text-muted small text-end" style="letter-spacing: 0.5px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="intervention-row" style="animation: fadeIn 0.4s ease-out 0.2s both;">
                                    <td><strong class="text-dark">Maria Santos</strong><br><small class="text-muted">Rice Farming</small></td>
                                    <td class="text-center"><span class="badge bg-danger rounded-pill">HIGH</span></td>
                                    <td class="text-center"><small class="text-muted">137 days ago</small></td>
                                    <td class="text-end"><button class="btn btn-sm btn-light text-primary rounded-2" style="font-size: 0.75rem;">Contact</button></td>
                                </tr>
                                <tr class="intervention-row" style="animation: fadeIn 0.4s ease-out 0.3s both;">
                                    <td><strong class="text-dark">Juan dela Cruz</strong><br><small class="text-muted">Corn Production</small></td>
                                    <td class="text-center"><span class="badge bg-warning rounded-pill">MEDIUM</span></td>
                                    <td class="text-center"><small class="text-muted">68 days ago</small></td>
                                    <td class="text-end"><button class="btn btn-sm btn-light text-warning rounded-2" style="font-size: 0.75rem;">Monitor</button></td>
                                </tr>
                                <tr class="intervention-row" style="animation: fadeIn 0.4s ease-out 0.4s both;">
                                    <td><strong class="text-dark">Rosa Gonzales</strong><br><small class="text-muted">Fishery</small></td>
                                    <td class="text-center"><span class="badge bg-success rounded-pill">LOW</span></td>
                                    <td class="text-center"><small class="text-muted">12 days ago</small></td>
                                    <td class="text-end"><button class="btn btn-sm btn-light text-success rounded-2" style="font-size: 0.75rem;">Active</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="background-color: #f5f5dc; border-top: 1px solid rgba(229, 229, 192, 0.5); padding: 16px 24px;">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn px-4 fw-bold rounded-pill" style="background:var(--track-green);color:white;box-shadow: 0 4px 12px rgba(32, 160, 96, 0.2);">
                    <i class="bi bi-download me-2"></i> Generate Report
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===== EDIT ACTIVITY MODAL ===== -->
<div class="modal fade" id="editGalleryMediaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:24px;border:none;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.15);">
            <div class="modal-header d-flex align-items-center" style="background-color: #f5f5dc; border-bottom: 1px solid rgba(229, 229, 192, 0.5); padding: 20px 24px;">
                <h5 class="modal-title fw-bold text-dark mb-0"><i class="bi bi-pencil-square text-primary me-2"></i> Edit Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="../media/media_actions.php" method="POST">
                <input type="hidden" name="edit_media" id="edit_media_id" value="">
                
                <div class="modal-body" style="padding: 24px; background: white;">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Activity Title</label>
                        <input type="text" name="title" id="edit_gallery_title" class="form-control" required style="border-radius:12px;">
                    </div>

                    <div class="row mb-3 g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Category</label>
                            <select name="category" id="edit_gallery_category" class="form-select" required style="border-radius:12px;">
                                <option value="Training">Training</option>
                                <option value="Harvesting">Harvesting</option>
                                <option value="Meeting">Meeting</option>
                                <option value="Livelihood">Livelihood</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Activity Date</label>
                            <input type="date" name="activity_date" id="edit_gallery_activity_date" class="form-control" required style="border-radius:12px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Short Description</label>
                        <textarea name="description" id="edit_gallery_description" class="form-control" rows="3" required style="border-radius:12px;"></textarea>
                    </div>
                </div>

                <div class="modal-footer" style="background-color: #f5f5dc; border-top: 1px solid rgba(229, 229, 192, 0.5); padding: 16px 24px;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill">
                        <i class="bi bi-check-circle-fill me-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php include('../includes/footer.php'); ?>
<script>
AOS.init({ once: true, duration: 800 });

// Member Growth Chart
const memberGrowthCtx = document.getElementById('memberGrowthChart').getContext('2d');
const memberGrowthChart = new Chart(memberGrowthCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Total Members',
            data: [1050, 1100, 1150, 1180, 1220, 1240],
            borderColor: '#20a060',
            backgroundColor: 'rgba(32, 160, 96, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 6,
            pointBackgroundColor: '#20a060',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverRadius: 8,
            shadowOffsetX: 0,
            shadowOffsetY: 2,
            shadowBlur: 4,
            shadowColor: 'rgba(32, 160, 96, 0.2)'
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
                display: false,
                labels: { font: { size: 12, weight: 600 }, padding: 15 }
            },
            tooltip: {
                backgroundColor: 'rgba(26, 26, 26, 0.9)',
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                padding: 12,
                cornerRadius: 12,
                displayColors: true,
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

// Contributions Chart
const contributionsCtx = document.getElementById('contributionsChart').getContext('2d');
const contributionsChart = new Chart(contributionsCtx, {
    type: 'bar',
    data: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [{
            label: 'Contributions (₱)',
            data: [12000, 15000, 14500, 18000],
            backgroundColor: ['rgba(32, 160, 96, 0.8)', 'rgba(32, 160, 96, 0.9)', 'rgba(32, 160, 96, 0.85)', 'rgba(32, 160, 96, 0.95)'],
            borderColor: '#20a060',
            borderWidth: 2,
            borderRadius: 12,
            hoverBackgroundColor: '#20a060',
            shadowOffsetX: 0,
            shadowOffsetY: 4,
            shadowBlur: 8,
            shadowColor: 'rgba(32, 160, 96, 0.2)'
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
            legend: { display: false }
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

// Risk Distribution Chart
const riskCtx = document.getElementById('riskChart').getContext('2d');
const riskChart = new Chart(riskCtx, {
    type: 'doughnut',
    data: {
        labels: ['Low Risk', 'Medium Risk', 'High Risk'],
        datasets: [{
            data: [72, 18, 10],
            backgroundColor: ['#20a060', '#ffc107', '#dc3545'],
            borderColor: '#fff',
            borderWidth: 3,
            shadowOffsetX: 0,
            shadowOffsetY: 2,
            shadowBlur: 8,
            shadowColor: 'rgba(0, 0, 0, 0.1)'
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
                displayColors: true,
                borderColor: '#20a060',
                borderWidth: 1
            }
        }
    }
});

// Activity Timeline Chart
const activityCtx = document.getElementById('activityChart').getContext('2d');
const activityChart = new Chart(activityCtx, {
    type: 'line',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Activities',
            data: [45, 52, 48, 65, 70, 38, 42],
            borderColor: '#20a060',
            backgroundColor: 'rgba(32, 160, 96, 0.15)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointRadius: 6,
            pointBackgroundColor: '#20a060',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointHoverRadius: 8,
            shadowOffsetX: 0,
            shadowOffsetY: 2,
            shadowBlur: 4,
            shadowColor: 'rgba(32, 160, 96, 0.2)'
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
                displayColors: true,
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
                    labels: ['Rice', 'Corn', 'Fishery', 'Livestock', 'High Value Crops'],
                    datasets: [{
                        label: 'Members per Sector',
                        data: [495, 305, 245, 175, 220],
                        backgroundColor: ['rgba(32, 160, 96, 0.9)', 'rgba(32, 160, 96, 0.8)', 'rgba(32, 160, 96, 0.85)', 'rgba(32, 160, 96, 0.75)', 'rgba(32, 160, 96, 0.82)'],
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
                        data: [72, 78, 75, 82, 88],
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
                        data: [75, 18, 7],
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
                        label: 'Engagement Intensity',
                        data: [92, 78, 85, 68, 80],
                        backgroundColor: ['rgba(32, 160, 96, 0.9)', 'rgba(32, 160, 96, 0.7)', 'rgba(32, 160, 96, 0.95)', 'rgba(32, 160, 96, 0.65)', 'rgba(32, 160, 96, 0.8)'],
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

function previewDashboardImage(event) {
    const previewArea = document.getElementById('previewAreaDashboard');
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewArea.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;" alt="Preview">';
        }
        reader.readAsDataURL(file);
    }
}

function openGalleryEditModal(id, title, description, category, activity_date) {
    document.getElementById('edit_media_id').value = id;
    document.getElementById('edit_gallery_title').value = title;
    document.getElementById('edit_gallery_description').value = description;
    document.getElementById('edit_gallery_activity_date').value = activity_date;
    
    // Select the right category
    const sel = document.getElementById('edit_gallery_category');
    for (let i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value === category) { sel.selectedIndex = i; break; }
    }
    
    // Hide the main upload modal temporarily to avoid backdrop z-index issues
    const mainModalEl = document.getElementById('uploadMediaModal');
    const mainModal = bootstrap.Modal.getInstance(mainModalEl);
    if(mainModal) mainModal.hide();

    // Show the edit modal
    const editModal = new bootstrap.Modal(document.getElementById('editGalleryMediaModal'));
    editModal.show();
}
</script>

<?php if(isset($_GET['upload']) || isset($_GET['updated']) || isset($_GET['delete'])): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        <?php if(isset($_GET['upload']) && $_GET['upload'] == 'success'): ?>
            TrackUI.toast('Activity photo uploaded successfully!', 'success');
        <?php elseif(isset($_GET['upload']) && $_GET['upload'] == 'large_file'): ?>
            TrackUI.toast('File is too large. Max 5MB.', 'danger');
        <?php elseif(isset($_GET['upload']) && $_GET['upload'] == 'invalid_format'): ?>
            TrackUI.toast('Invalid format. Only JPG, PNG, GIF allowed.', 'danger');
        <?php elseif(isset($_GET['upload'])): ?>
            TrackUI.toast('An error occurred during upload.', 'danger');
        <?php endif; ?>

        <?php if(isset($_GET['updated']) && $_GET['updated'] == 'success'): ?>
            TrackUI.toast('Activity details updated successfully!', 'success');
        <?php endif; ?>

        <?php if(isset($_GET['delete']) && $_GET['delete'] == 'success'): ?>
            TrackUI.toast('Activity photo deleted permanently!', 'danger');
        <?php endif; ?>
    });
</script>
<?php endif; ?>

</body>
</html>