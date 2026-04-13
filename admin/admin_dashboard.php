<?php
session_start();
include('../auth/db_connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

// --- ADDITIONAL CODE FOR FULL NAME ---
$user_id = $_SESSION['user_id']; // Ensure this is set in your login script
$full_name = isset($_SESSION['fname']) ? $_SESSION['fname'] : "Administrator"; // Use session name or fallback

// Try to fetch from database, but use session data if unavailable
@$query = "SELECT first_name, last_name FROM users WHERE id = ?";
@$stmt = $conn->prepare($query);
if ($stmt) {
    @$stmt->bind_param("i", $user_id);
    @$stmt->execute();
    @$result = $stmt->get_result();
    if ($user = @$result->fetch_assoc()) {
        $full_name = $user['first_name'] . " " . $user['last_name'];
    }
}

// Fetch media activities for management modal (Static demo data)
$static_media_activities = [
    ['id' => 1, 'title' => 'Harvest Training Workshop', 'description' => 'Members attending the annual agricultural best practices session.', 'category' => 'Training', 'activity_date' => '2024-03-15', 'file_path' => 'agriculture.webp'],
    ['id' => 2, 'title' => 'General Assembly 2024', 'description' => 'Cooperative members meeting for the annual planning.', 'category' => 'Meetings', 'activity_date' => '2024-03-10', 'file_path' => 'meeting.jpg'],
    ['id' => 3, 'title' => 'Community Harvest Festival', 'description' => 'Celebrating the successful harvest season with active members.', 'category' => 'Events', 'activity_date' => '2024-03-05', 'file_path' => 'event.png'],
    ['id' => 4, 'title' => 'Sector Focus Group', 'description' => 'Specialized discussion for local agricultural sectors.', 'category' => 'Livelihood', 'activity_date' => '2024-03-01', 'file_path' => 'sector.jpg'],
];
$media_activities_query = $static_media_activities;

// --- TOTAL MEMBERS COUNT ---
$member_count = 0;
$count_query = "SELECT COUNT(*) as total FROM users WHERE role = 'Member'";
$count_result = $conn->query($count_query);
if ($count_result) {
    $row = $count_result->fetch_assoc();
    $member_count = $row['total'];
}

// --- TOTAL DOCUMENTS COUNT ---
$doc_count = 0;
$doc_count_query = "SELECT COUNT(*) as total FROM documents";
$doc_count_result = $conn->query($doc_count_query);
if ($doc_count_result) {
    $doc_row = $doc_count_result->fetch_assoc();
    $doc_count = $doc_row['total'];
}

// --- TOTAL ANNOUNCEMENTS COUNT ---
$ann_count = 0;
$ann_count_query = "SELECT COUNT(*) as total FROM announcements";
$ann_count_result = $conn->query($ann_count_query);
if ($ann_count_result) {
    $ann_row = $ann_count_result->fetch_assoc();
    $ann_count = $ann_row['total'];
}

// --- DATA FOR CHARTS ---

// 1. Member Growth (Last 6 Months) with Sample Data
$months = [];
$counts = [];
$sample_base = [12, 18, 25, 32, 45, 62]; // Realistic growth trend
for ($i = 5; $i >= 0; $i--) {
    $idx = 5 - $i;
    $month = date('M', strtotime("-$i month"));
    $full_month = date('Y-m', strtotime("-$i month"));
    $months[] = $month;
    $q = "SELECT COUNT(*) as count FROM users WHERE role = 'Member' AND created_at LIKE '$full_month%'";
    $r = $conn->query($q);
    $db_count = ($r && $row = $r->fetch_assoc()) ? $row['count'] : 0;
    $counts[] = $db_count + $sample_base[$idx]; // DB + Sample
}

// 2. Sector Distribution with Sample Data
$sector_labels = ['Rice', 'Corn', 'Fishery', 'Livestock', 'Crops'];
$sample_sectors = [15, 12, 8, 10, 14]; // Realistic distribution
$sector_counts = [];
foreach ($sector_labels as $index => $sec) {
    $q = "SELECT COUNT(*) as count FROM users WHERE role = 'Member' AND sector LIKE '%$sec%'";
    $r = $conn->query($q);
    $db_count = ($r && $row = $r->fetch_assoc()) ? $row['count'] : 0;
    $sector_counts[] = $db_count + $sample_sectors[$index];
}

?>


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | TRACKCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    
    <style>
        :root {
            --track-green: #206970; 
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
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            background: #f8fafc;
            overflow-x: hidden;
            line-height: 1.6;
            min-height: 100vh;
        }

        .logout-btn {
            border: 2px solid #dc2626;
            background: #dc2626;
            color: white;
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
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220, 38, 38, 0.6);
        }

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
            background: transparent !important; 
        }

        /* All Buttons in Gallery Modal - Elite Green Styling */
        #uploadMediaModal .btn,
        #uploadMediaModal .cancel-btn,
        #uploadMediaModal .upload-btn,
        #uploadMediaModal .close-btn {
            background-color: #20a060 !important;
            color: white !important;
            border: none !important;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.2);
        }
        #uploadMediaModal .btn:hover,
        #uploadMediaModal .cancel-btn:hover,
        #uploadMediaModal .upload-btn:hover,
        #uploadMediaModal .close-btn:hover {
            background-color: #1b8a53 !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(32, 160, 96, 0.4);
        }

        /* Elite Green Pills for Gallery Tabs */
        #uploadMediaModal .nav-pills .nav-link.active {
            background-color: #20a060 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.3);
        }
        #uploadMediaModal .nav-pills .nav-link {
            color: #64748b;
            transition: all 0.3s ease;
        }
        #uploadMediaModal .nav-pills .nav-link:hover:not(.active) {
            color: #20a060;
            background-color: rgba(32, 160, 96, 0.05);
        }

        /* Premium Gallery Action Buttons - Document Match Design */
        .edit-btn {
            background-color: rgba(245, 158, 11, 0.08) !important;
            color: #f59e0b !important;
            border: 1.5px solid rgba(245, 158, 11, 0.3) !important;
            border-radius: 12px !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .edit-btn:hover {
            background-color: #f59e0b !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(245, 158, 11, 0.2);
        }

        .delete-btn {
            background-color: rgba(239, 68, 68, 0.08) !important;
            color: #ef4444 !important;
            border: 1.5px solid rgba(239, 68, 68, 0.3) !important;
            border-radius: 12px !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .delete-btn:hover {
            background-color: #ef4444 !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(239, 68, 68, 0.2);
        }

        /* --- Dashboard Specific Styles --- */
        .admin-header {
            background: transparent;
            padding: 10px 0;
            border-bottom: none;
            margin-bottom: 0;
            position: relative;
            overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
            color: #ffffff !important;
        }

        .admin-header h1 { 
            color: #20a060 !important; 
        }

        .admin-header p, .admin-header .text-muted { 
            color: #ffffff !important; 
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .status-badge {
            display: inline-flex; align-items: center; background: white; color: var(--track-green);
            font-weight: 700; padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; 
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.1); border: 1px solid rgba(32, 160, 96, 0.2);
        }

        .btn-portal {
            background-color: #20a060 !important;
            color: white !important;
            border: none !important;
            border-radius: 50px !important;
            padding: 12px 28px !important;
            font-weight: 800 !important;
            font-size: 0.9rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 10px !important;
            text-decoration: none !important;
            box-shadow: 0 8px 18px rgba(32, 160, 96, 0.2) !important;
            cursor: pointer !important;
        }

        .btn-portal:hover {
            background-color: #1a8a53 !important;
            transform: translateY(-3px) scale(1.02) !important;
            box-shadow: 0 12px 25px rgba(32, 160, 96, 0.3) !important;
            color: white !important;
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


        .stat-card {
            border: 1.5px solid rgba(255, 255, 255, 0.4) !important; 
            border-radius: 42px; 
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 35px; 
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 
                0 15px 35px -10px rgba(0, 0, 0, 0.08),
                0 8px 15px -5px rgba(0, 0, 0, 0.03); 
            position: relative; 
            overflow: hidden;
            z-index: 1;
        }

        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 10% 10%, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 70%);
            z-index: -1; opacity: 0; transition: 0.5s;
        }

        .stat-card:hover {
            transform: translateY(-15px) scale(1.015); 
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 
                0 45px 80px -20px rgba(39, 174, 96, 0.18),
                0 15px 30px -10px rgba(0, 0, 0, 0.05); 
            border-color: rgba(39, 174, 96, 0.4) !important; 
            z-index: 10;
        }

        .stat-card:hover::before { opacity: 1; }

        /* Modern Shine Sweep */
        .stat-card::after {
            content: ""; 
            position: absolute; 
            top: -100%; 
            left: -100%; 
            width: 250%; 
            height: 250%;
            background: linear-gradient(
                60deg, 
                transparent 10%, 
                rgba(255, 255, 255, 0.15) 45%, 
                rgba(255, 255, 255, 0.4) 50%, 
                rgba(255, 255, 255, 0.15) 55%, 
                transparent 90%
            );
            transform: rotate(45deg); 
            transition: 0s; 
            pointer-events: none; 
            opacity: 0;
            z-index: 2;
        }
        .stat-card:hover::after { 
            animation: shineSweep 1.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 1; 
        }

        @keyframes shineSweep {
            0% { transform: rotate(45deg) translate(-50%, -50%); }
            100% { transform: rotate(45deg) translate(30%, 30%); }
        }

        @keyframes floatIcon {
            0%, 100% { transform: translateY(0) scale(1.05); }
            50% { transform: translateY(-7px) scale(1.15); }
        }

        .icon-box {
            width: 54px; height: 54px; display: flex; align-items: center; justify-content: center;
            border-radius: 16px; margin-bottom: 20px; transition: all 0.4s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0,0,0,0.03);
        }
        
        .stat-card:hover .icon-box { 
            animation: floatIcon 2.5s ease-in-out infinite;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }

        .stat-card h6 {
            color: #64748b !important;
            font-size: 0.75rem;
            letter-spacing: 1.2px;
            font-weight: 700;
        }

        .stat-card h2 {
            font-weight: 900 !important;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #1a1a1a 0%, #4a4a4a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }



        /* ── Global Modal Styles ── */
        .modal-content {
            border-radius: 30px !important;
            overflow: hidden !important;
        }
        .modal-header .btn-close {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            background: #ef4444 !important;
            background-image: none !important;
            border-radius: 50% !important;
            opacity: 1 !important;
            filter: none !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35) !important;
            transition: all 0.2s ease !important;
            padding: 0 !important;
            position: relative !important;
        }
        .modal-header .btn-close::before,
        .modal-header .btn-close::after {
            content: "" !important;
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            width: 14px !important;
            height: 2px !important;
            background-color: white !important;
            border-radius: 2px !important;
        }
        .modal-header .btn-close::before {
            transform: translate(-50%, -50%) rotate(45deg) !important;
        }
        .modal-header .btn-close::after {
            transform: translate(-50%, -50%) rotate(-45deg) !important;
        }
        .modal-header .btn-close:hover {
            background-color: #dc2626 !important;
            transform: scale(1.1) !important;
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.45) !important;
        }

        .glass-mini-card {
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            padding: 18px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }
        .glass-mini-card:hover {
            transform: translateY(-8px) scale(1.03);
            background: rgba(255, 255, 255, 0.85);
            border-color: rgba(39, 174, 96, 0.3);
            box-shadow: 0 15px 30px rgba(39, 174, 96, 0.12);
        }
        
        /* Modern Slim Progress Bars */
        .sector-progress {
            height: 8px !important;
            border-radius: 50px !important;
            background: rgba(0, 0, 0, 0.04) !important;
            box-shadow: none !important;
            margin-bottom: 2rem !important;
        }
        .sector-progress .progress-bar {
            border-radius: 50px;
            background: linear-gradient(90deg, #27ae60, #2ecc71) !important;
            box-shadow: 0 2px 10px rgba(39, 174, 96, 0.3);
        }

        /* Timeline Activity Styled Feed */
        .activity-timeline {
            position: relative;
            padding-left: 20px;
        }
        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10px;
            bottom: 10px;
            width: 2px;
            background: linear-gradient(180deg, rgba(39, 174, 96, 0.3) 0%, rgba(39, 174, 96, 0.05) 100%);
            border-radius: 2px;
        }
        .activity-item { 
            padding: 18px; 
            margin-bottom: 16px; 
            border-radius: 22px; 
            transition: var(--transition-smooth); 
            border: 1px solid rgba(255, 255, 255, 0.5); 
            background: rgba(255, 255, 255, 0.4); 
            backdrop-filter: blur(8px);
            cursor: pointer; 
            position: relative;
        }
        .activity-item::after {
            content: '';
            position: absolute;
            left: -24px;
            top: 34px;
            width: 10px;
            height: 10px;
            background: #27ae60;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 4px rgba(39, 174, 96, 0.1);
        }
        .activity-item:hover { 
            background: #fff; 
            border-color: rgba(39, 174, 96, 0.2); 
            transform: translateX(12px); 
            box-shadow: 0 12px 25px rgba(0,0,0,0.04);
        }

        /* Predictive Insight Tiles */
        .insight-tile {
            background: white;
            border: 2.5px solid rgba(0, 0, 0, 0.03);
            border-radius: 24px;
            padding: 24px;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .insight-tile:hover {
            transform: scale(1.02) translateY(-5px);
            border-color: var(--insight-color);
            box-shadow: 0 20px 40px var(--insight-glow);
        }

        /* Staggered Animations */
        .fade-in-up { opacity: 0; animation: fadeInUpCustom 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
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
            animation: slideInDown 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: all 0.3s ease;
        }

        .intervention-row:nth-child(1) { animation-delay: 0.6s; }
        .intervention-row:nth-child(2) { animation-delay: 0.7s; }
        .intervention-row:nth-child(3) { animation-delay: 0.8s; }

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
        
        /* Gallery Modal Button Styles */
        #uploadMediaModal .btn-sm {
            transition: all 0.3s ease !important;
        }
        
        #uploadMediaModal .btn-sm.edit-btn {
            background: #206970 !important;
            color: white !important;
            border: none !important;
        }
        
        #uploadMediaModal .btn-sm.edit-btn:hover {
            background: #20a060 !important;
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.3) !important;
            transform: translateY(-2px) !important;
        }
        
        #uploadMediaModal .btn-sm.delete-btn {
            background: #206970 !important;
            color: white !important;
            border: none !important;
        }
        
        #uploadMediaModal .btn-sm.delete-btn:hover {
            background: #dc2626 !important;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3) !important;
            transform: translateY(-2px) !important;
        }
        
        #uploadMediaModal .modal-footer .btn {
            transition: all 0.3s ease !important;
        }
        
        #uploadMediaModal .modal-footer .btn.cancel-btn {
            background: #206970 !important;
            color: white !important;
            border: none !important;
        }
        
        #uploadMediaModal .modal-footer .btn.cancel-btn:hover {
            background: #20a060 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.3) !important;
        }
        
        #uploadMediaModal .modal-footer .btn.upload-btn {
            background: #206970 !important;
            color: white !important;
            border: none !important;
        }
        
        #uploadMediaModal .modal-footer .btn.upload-btn:hover {
            background: #20a060 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.3) !important;
        }
        
        #uploadMediaModal .modal-footer .btn.close-btn {
            background: #206970 !important;
            color: white !important;
            border: none !important;
        }
        
        #uploadMediaModal .modal-footer .btn.close-btn:hover {
            background: #20a060 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.3) !important;
        }
    </style>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

</head>
<div class="sidebar-layout">
    <?php 
        $user_role = 'Admin';
        $active_page = 'dashboard';
        $membership_type = 'Admin';
        $full_name = htmlspecialchars($full_name);
        include('../includes/dashboard_sidebar.php'); 
    ?>
    <div class="main-content-wrapper">
        <div class="admin-header" data-aos="fade-in">
            <div class="container py-4">
                <div class="row g-4 mb-4">
                    <!-- Total Members Card -->
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-card p-4 border-0" style="background: #164a36; border-radius: 32px; box-shadow: 0 15px 35px rgba(22, 74, 54, 0.25);">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box me-3 mb-0" style="background: rgba(255, 255, 255, 0.1); color: #ffffff; width: 48px; height: 48px; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.1);">
                                    <i class="bi bi-people-fill fs-4"></i>
                                </div>
                                <h6 class="text-white fw-800 text-uppercase mb-0" style="letter-spacing: 1px; font-size: 0.75rem; color: #ffffff !important;">Total Members</h6>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h1 class="fw-900 mb-0" style="font-size: 3.5rem; letter-spacing: -2px; color: #ffffff; text-shadow: 0 4px 12px rgba(0,0,0,0.1);"><?php echo number_format($member_count); ?></h1>
                                <span class="badge bg-white bg-opacity-10 text-white fw-bold p-2" style="font-size: 0.72rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                                    <i class="bi bi-check-circle-fill me-1" style="color: #4ade80;"></i>ACTIVE
                                </span>
                            </div>
                            <p class="text-white small mt-2 mb-0 fw-bold">Live database registry</p>
                        </div>
                    </div>

                    <!-- Active Sectors Card -->
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-card p-4 border-0" style="background: #164a36; border-radius: 32px; box-shadow: 0 15px 35px rgba(22, 74, 54, 0.25);">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box me-3 mb-0" style="background: rgba(255, 255, 255, 0.1); color: #ffffff; width: 48px; height: 48px; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.1);">
                                    <i class="bi bi-grid-fill fs-4"></i>
                                </div>
                                <h6 class="text-white fw-800 text-uppercase mb-0" style="letter-spacing: 1px; font-size: 0.75rem; color: #ffffff !important;">Active Sectors</h6>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h1 class="fw-900 mb-0" style="font-size: 3.5rem; letter-spacing: -2px; color: #ffffff; text-shadow: 0 4px 12px rgba(0,0,0,0.1);">5</h1>
                                <span class="badge bg-white bg-opacity-10 text-white fw-bold p-2" style="font-size: 0.72rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                                    <i class="bi bi-geo-alt-fill me-1" style="color: #4ade80;"></i>LIVE
                                </span>
                            </div>
                            <p class="text-white small mt-2 mb-0 fw-bold">Operational agricultural zones</p>
                        </div>
                    </div>

                    <!-- Total Documents Card -->
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-card p-4 border-0" style="background: #164a36; border-radius: 32px; box-shadow: 0 15px 35px rgba(22, 74, 54, 0.25);">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box me-3 mb-0" style="background: rgba(255, 255, 255, 0.1); color: #ffffff; width: 48px; height: 48px; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.1);">
                                    <i class="bi bi-file-earmark-text-fill fs-4"></i>
                                </div>
                                <h6 class="text-white fw-800 text-uppercase mb-0" style="letter-spacing: 1px; font-size: 0.75rem; color: #ffffff !important;">Total Documents</h6>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h1 class="fw-900 mb-0" style="font-size: 3.5rem; letter-spacing: -2px; color: #ffffff; text-shadow: 0 4px 12px rgba(0,0,0,0.1);"><?php echo number_format($doc_count); ?></h1>
                                <span class="badge bg-white bg-opacity-10 text-white fw-bold p-2" style="font-size: 0.72rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                                    <i class="bi bi-cloud-check-fill me-1" style="color: #4ade80;"></i>SECURE
                                </span>
                            </div>
                            <p class="text-white small mt-2 mb-0 fw-bold">Digital storage registry</p>
                        </div>
                    </div>

                    <!-- Total Announcements Card -->
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="stat-card p-4 border-0" style="background: #164a36; border-radius: 32px; box-shadow: 0 15px 35px rgba(22, 74, 54, 0.25);">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-box me-3 mb-0" style="background: rgba(255, 255, 255, 0.1); color: #ffffff; width: 48px; height: 48px; border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.1);">
                                    <i class="bi bi-megaphone-fill fs-4"></i>
                                </div>
                                <h6 class="text-white fw-800 text-uppercase mb-0" style="letter-spacing: 1px; font-size: 0.75rem; color: #ffffff !important;">Total Announcements</h6>
                            </div>
                            <div class="d-flex align-items-baseline gap-2">
                                <h1 class="fw-900 mb-0" style="font-size: 3.5rem; letter-spacing: -2px; color: #ffffff; text-shadow: 0 4px 12px rgba(0,0,0,0.1);"><?php echo number_format($ann_count); ?></h1>
                                <span class="badge bg-white bg-opacity-10 text-white fw-bold p-2" style="font-size: 0.72rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1);">
                                    <i class="bi bi-broadcast me-1" style="color: #4ade80;"></i>BROADCAST
                                </span>
                            </div>
                            <p class="text-white small mt-2 mb-0 fw-bold">Live community alerts</p>
                        </div>
                    </div>
                </div>

                <hr class="border-light opacity-50 mb-4">
            </div>
        </div>

        <div class="container py-2">
        <div class="row g-4 mb-5">
            <!-- Chart 1: Member Growth -->
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="elite-card p-4 h-100" style="background: #164a36; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-800 mb-0" style="color: #ffffff; letter-spacing: -0.5px;">Member Growth <span class="text-white opacity-50 fw-bold ms-2" style="font-size: 0.8rem;">(Last 6 Months)</span></h5>
                        <i class="bi bi-graph-up-arrow text-white"></i>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="memberGrowthChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart 2: Sector Distribution -->
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <div class="elite-card p-4 h-100" style="background: #164a36; border-radius: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-800 mb-0" style="color: #ffffff; letter-spacing: -0.5px;">Sector Distribution <span class="text-white opacity-50 fw-bold ms-2" style="font-size: 0.8rem;">(Active Members)</span></h5>
                        <i class="bi bi-pie-chart-fill text-white"></i>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="sectorDistributionChart"></canvas>
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

// Chart.js Default Styling
Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
Chart.defaults.color = "#ffffff"; 
Chart.defaults.scale.ticks.color = "#ffffff";
Chart.defaults.scale.grid.color = "rgba(255, 255, 255, 0.1)";

// Data from PHP
const chartMonths = <?php echo json_encode($months); ?>;
const memberCounts = <?php echo json_encode($counts); ?>;
const sectorLabels = <?php echo json_encode($sector_labels); ?>;
const sectorData = <?php echo json_encode($sector_counts); ?>;

// 1. Member Growth Chart
new Chart(document.getElementById('memberGrowthChart'), {
    type: 'line',
    data: {
        labels: chartMonths,
        datasets: [{
            label: 'New Members',
            data: memberCounts,
            borderColor: '#4ade80', 
            backgroundColor: 'rgba(74, 222, 128, 0.15)',
            fill: true,
            tension: 0.4,
            borderWidth: 4,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#4ade80',
            pointBorderWidth: 2,
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: { 
                backgroundColor: 'rgba(22, 74, 54, 0.9)',
                titleColor: '#ffffff',
                bodyColor: '#ffffff',
                borderColor: 'rgba(255,255,255,0.1)',
                borderWidth: 1
            }
        },
        scales: {
            y: { 
                beginAtZero: true, 
                ticks: { color: '#ffffff' },
                grid: { color: 'rgba(255, 255, 255, 0.1)' } 
            },
            x: { 
                ticks: { color: '#ffffff' },
                grid: { display: false } 
            }
        }
    }
});

// 2. Sector Distribution Chart
new Chart(document.getElementById('sectorDistributionChart'), {
    type: 'doughnut',
    data: {
        labels: sectorLabels,
        datasets: [{
            data: sectorData,
            backgroundColor: ['#4ade80', '#2dd4bf', '#34d399', '#ffffff', '#10b981'],
            borderWidth: 2,
            borderColor: '#164a36',
            hoverOffset: 15
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: { 
                position: 'bottom', 
                labels: { color: '#ffffff', usePointStyle: true, padding: 20 } 
            }
        }
    }
});
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
</body>
</html>