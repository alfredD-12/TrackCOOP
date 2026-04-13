<?php
session_start();
include('../auth/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id   = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$membership_type = $user_role;
$full_name = isset($_SESSION['fname']) ? $_SESSION['fname'] : "Administrator";

// Try to fetch from database, but use session data if unavailable
if ($conn) {
    @$q = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    if ($q) {
        @$q->bind_param("i", $user_id);
        @$q->execute();
        @$result = $q->get_result();
        if ($u = @$result->fetch_assoc()) {
            $full_name = $u['first_name'] . " " . $u['last_name'];
        }
    }
}

// Static announcements data for display - one per category
$static_announcements = [
    ['id' => 1, 'title' => 'Annual General Assembly 2024', 'content' => 'Join us for our annual general assembly on April 15, 2024. All members are required to attend.', 'date_posted' => '2024-03-20', 'author' => 'Administrator', 'category' => 'General'],
    ['id' => 2, 'title' => 'Cooperative Founding Anniversary Event', 'content' => 'Celebrate our founding anniversary with a grand event on May 1, 2024 at the main hall.', 'date_posted' => '2024-03-18', 'author' => 'Administrator', 'category' => 'Event'],
    ['id' => 3, 'title' => 'Board Meeting – Q2 2024', 'content' => 'A board meeting is scheduled on April 5, 2024 to discuss the Q2 operational plans.', 'date_posted' => '2024-03-15', 'author' => 'Administrator', 'category' => 'Meeting'],
    ['id' => 4, 'title' => 'Loan Application Deadline', 'content' => 'All loan applications must be submitted on or before March 31, 2024. No extensions will be granted.', 'date_posted' => '2024-03-10', 'author' => 'Administrator', 'category' => 'Deadline'],
    ['id' => 5, 'title' => 'Rice Sector: Irrigation Update', 'content' => 'The irrigation schedule for the Rice sector has been updated. Please coordinate with your sector head.', 'date_posted' => '2024-03-08', 'author' => 'Administrator', 'category' => 'Sector'],
    ['id' => 6, 'title' => 'New Health Insurance Partnership', 'content' => 'The cooperative has partnered with a new health insurance provider for all members.', 'date_posted' => '2024-03-05', 'author' => 'Administrator', 'category' => 'General'],
    ['id' => 7, 'title' => 'Fishery Sector: New Equipment Arrival', 'content' => 'New modernized fishing nets and storage units have arrived at the coastal warehouse.', 'date_posted' => '2024-03-02', 'author' => 'Administrator', 'category' => 'Sector'],
    ['id' => 8, 'title' => 'Community Outreach Program', 'content' => 'Volunteers needed for our upcoming community outreach program this Saturday.', 'date_posted' => '2024-02-28', 'author' => 'Administrator', 'category' => 'Event'],
    ['id' => 9, 'title' => 'Emergency Fund Application', 'content' => 'Members affected by the recent storms may now apply for the emergency relief fund.', 'date_posted' => '2024-02-25', 'author' => 'Administrator', 'category' => 'Deadline'],
    ['id' => 10, 'title' => 'Corn Sector: Fertilizer Subsidy', 'content' => 'Government subsidies for fertilizers are now available for collection for Corn sector members.', 'date_posted' => '2024-02-22', 'author' => 'Administrator', 'category' => 'Sector'],
    ['id' => 11, 'title' => 'Technical Training Workshop', 'content' => 'A workshop on sustainable farming practices will be held next Tuesday.', 'date_posted' => '2024-02-20', 'author' => 'Administrator', 'category' => 'Meeting'],
    ['id' => 12, 'title' => 'Livestock: Vaccination Drive', 'content' => 'Yearly livestock vaccination drive starts next month. Please register your animals.', 'date_posted' => '2024-02-15', 'author' => 'Administrator', 'category' => 'General'],
    ['id' => 13, 'title' => 'Credit Committee Meeting', 'content' => 'The credit committee will meet to review pending loan applications.', 'date_posted' => '2024-02-12', 'author' => 'Administrator', 'category' => 'Meeting'],
    ['id' => 14, 'title' => 'Office Renovation Notice', 'content' => 'The coop office will be closed for minor renovations from Feb 10-12.', 'date_posted' => '2024-02-08', 'author' => 'Administrator', 'category' => 'General'],
    ['id' => 15, 'title' => 'Scholarship Program 2024', 'content' => 'Applications for the scholarship program for dependents are now open.', 'date_posted' => '2024-02-05', 'author' => 'Administrator', 'category' => 'Deadline'],
];

// Announcements are static for demo
$all_announcements = $static_announcements;

// ── Category Counts ─── (Static for demo)
$stats = ['General' => 3, 'Important' => 1];
$msg_status = "";

// ── Handle POST Actions (Add / Edit / Delete) ─── (Demo Mode)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($user_role === 'Admin' || $user_role === 'Bookkeeper')) {
    if (isset($_POST['title']) && isset($_POST['content'])) {
        $msg_status = "success";
    }
}

// Fetch announcements list for display
$announcements_list = $all_announcements;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | TRACKCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    
    <style>
        /* Announcements Page Specific Styles */
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)), url('../Home.jpeg') top center / 100% 100% no-repeat fixed;
            overflow-x: hidden;
            line-height: 1.6;
            min-height: 100vh;
        }

        .page-header {
            background: transparent;
            padding: 10px 0 5px;
            border-bottom: none;
            margin-bottom: 10px;
            position: relative;
            overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
            color: #ffffff !important;
        }
        .page-header h1 { 
            color: #27ae60 !important; 
            letter-spacing: -1.5px;
            font-weight: 800 !important;
        }


        /* ── Announcement Cards ── */
        .ann-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 24px; transition: var(--transition-smooth); margin-bottom: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.05); position: relative;
        }
        .ann-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(32,160,96,0.15); border-color: rgba(32,160,96,0.5); }
        .cat-badge {
            font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px;
            padding: 5px 12px; border-radius: 50px; margin-bottom: 12px; display: inline-block;
        }
        .cat-general  { background: #f1f5f9; color: #64748b; }
        .cat-event    { background: #eef2ff; color: #4f46e5; }
        .cat-meeting  { background: #fff7ed; color: #ea580c; }
        .cat-deadline { background: #fef2f2; color: #dc2626; }

        /* ── Action Buttons ── */
        .btn-track { background: #27ae60; color: white; border-radius: 12px; padding: 12px 24px; font-weight: 700; border: none; transition: var(--transition-smooth); box-shadow: 0 4px 14px rgba(39, 174, 96, 0.3); }
        .btn-track:hover { background: #1a8548; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(39, 174, 96, 0.4); color: white; }
        
        .action-btn {
            display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center;
            border-radius: 10px; color: #64748b; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: #f8fafc; border: none;
            text-decoration: none; padding: 0; outline: none; font-size: 0.9rem; cursor: pointer;
        }
        
        .action-btn.edit { color: #f59e0b; background: rgba(245, 158, 11, 0.05); }
        .action-btn.edit:hover { opacity: 0.8; transform: translateY(-2px); }

        .action-btn.del { color: #ef4444; background: rgba(239, 68, 68, 0.05); }
        .action-btn.del:hover { opacity: 0.8; transform: translateY(-2px); }


        .ann-avatar {
            width: 44px; height: 44px; border-radius: 12px;
            background: var(--track-green-light, #e9f5ee);
            display: flex; align-items: center; justify-content: center;
            color: #27ae60; font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(39, 174, 96, 0.1);
            flex-shrink: 0;
        }

        /* Premium Modal Styles */
        .modal-content { border-radius: 30px !important; border: none; box-shadow: 0 25px 60px rgba(0,0,0,0.15); overflow: hidden !important; }
        .modal-header { background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22, 74, 54, 0.3); padding: 24px 28px; color: white; }
        .modal-title { font-weight: 800; font-size: 1.3rem; letter-spacing: -1px; color: #27ae60 !important; display: flex; align-items: center; gap: 12px; }
        .modal-body { padding: 30px; }
        
        /* Red Circle Close Button */
        .modal-header .btn-close {
            width: 36px !important; height: 36px !important; min-width: 36px !important;
            background: #ef4444 !important; background-image: none !important;
            border-radius: 50% !important; opacity: 1 !important; filter: none !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4) !important;
            transition: all 0.2s ease !important; padding: 0 !important; position: relative !important;
        }
        .modal-header .btn-close::before,
        .modal-header .btn-close::after {
            content: "" !important; position: absolute !important; top: 50% !important; left: 50% !important;
            width: 14px !important; height: 2px !important; background-color: white !important; border-radius: 2px !important;
        }
        .modal-header .btn-close::before { transform: translate(-50%, -50%) rotate(45deg) !important; }
        .modal-header .btn-close::after { transform: translate(-50%, -50%) rotate(-45deg) !important; }
        .modal-header .btn-close:hover {
            background-color: #dc2626 !important; transform: scale(1.1) !important;
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.5) !important;
        }

        /* ── Author Avatar Stack Styles ── */
        .user-table-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-table-avatar-box {
            width: 38px;
            height: 38px;
            background: #27ae60;
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(39, 174, 96, 0.2);
        }
        .user-table-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }
        .user-table-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.95rem;
        }
        .user-table-role {
            font-size: 0.8rem;
            color: #94a3b8;
            font-weight: 600;
        }

        /* ── Global Modal Styles ── */
        .modal-content {
            border-radius: 30px !important; border: none; box-shadow: 0 25px 60px rgba(0,0,0,0.15); overflow: hidden !important;
        }
        .modal-header {
            background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22, 74, 54, 0.3); padding: 24px 28px; color: white;
        }
        .modal-body { padding: 28px; }
        .modal-footer { background: rgba(22, 74, 54, 0.95); border-top: 1px solid rgba(22, 74, 54, 0.3); padding: 20px 28px; color: white; }

        /* ── Red Circle Close Button ── */
        .modal-header .btn-close {
            width: 36px !important; height: 36px !important; min-width: 36px !important;
            background: #ef4444 !important; background-image: none !important;
            border-radius: 50% !important; opacity: 1 !important; filter: none !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4) !important;
            transition: all 0.2s ease !important; padding: 0 !important; position: relative !important;
        }
        .modal-header .btn-close::before,
        .modal-header .btn-close::after {
            content: "" !important; position: absolute !important; top: 50% !important; left: 50% !important;
            width: 14px !important; height: 2px !important; background-color: white !important; border-radius: 2px !important;
        }
        .modal-header .btn-close::before { transform: translate(-50%, -50%) rotate(45deg) !important; }
        .modal-header .btn-close::after { transform: translate(-50%, -50%) rotate(-45deg) !important; }
        .modal-header .btn-close:hover {
            background-color: #dc2626 !important; transform: scale(1.1) !important;
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.5) !important;
        }


        .ann-toolbar {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }


        .ann-toolbar-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        /* Filter Pills */
        .filter-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-pill {
            padding: 7px 18px;
            border-radius: 12px;
            font-size: 0.78rem;
            font-weight: 700;
            border: 1.5px solid #e2e8f0;
            background: white;
            color: #64748b;
            cursor: pointer;
            transition: all 0.25s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            min-width: 110px;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .filter-pill:hover {
            border-color: #27ae60;
            color: #27ae60;
            background: #e9f5ee;
        }
        .filter-pill.active {
            background: #27ae60;
            color: white;
            border-color: #27ae60;
            box-shadow: 0 4px 12px rgba(39,174,96,0.25);
        }

        /* Filter Card */
        .filter-card {
            border: 2px solid #20a060 !important;
            border-radius: 20px;
            background: #ffffff !important;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            animation: fadeInUpCustom 0.8s ease-out 0.2s both;
            opacity: 1 !important;
        }
        .filter-card-label {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 12px;
        }
        /* ── Fixed Modal Size ── */
        .modal-fixed-size {
            height: 500px !important;
            max-height: 500px !important;
            min-height: 500px !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
            border-radius: 30px !important;
        }
        .modal-fixed-size .modal-header { flex-shrink: 0 !important; }
        .modal-fixed-size .modal-footer { flex-shrink: 0 !important; }
        .modal-fixed-size .modal-body {
            flex: 1 1 auto !important;
            overflow-y: auto !important;
            min-height: 0 !important;
        }

        .nav-tabs .nav-link {
            color: #64748b !important;
            font-weight: 700;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent !important;
        }
        .nav-tabs .nav-link:hover {
            background: #f8fafc;
            color: var(--track-green) !important;
        }
        .nav-tabs .nav-link.active {
            background: white !important;
            color: var(--track-green) !important;
            border-bottom-color: var(--track-green) !important;
        }
        .tab-content {
            padding: 0;
        }

        /* Hide Horizontal Scrollbar for Elite Table Containers */
        .table-responsive::-webkit-scrollbar {
            display: none !important;
        }
        .table-responsive {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }
    </style>
</head>
<div class="sidebar-layout">
    <?php 
        $active_page = 'announcements';
        $user_role = $_SESSION['role'];
        $membership_type = ($user_role === 'Admin') ? 'Admin' : (($user_role === 'Bookkeeper') ? 'Bookkeeper' : 'Member');
        $full_name = htmlspecialchars($full_name);
        include('../includes/dashboard_sidebar.php'); 
    ?>

    <div class="main-content-wrapper">

<!-- HEADER & TOP BAR -->
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <div class="search-wrapper position-relative mb-0" style="width: 300px;">
                <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 18px; z-index: 5;"></i>
                <input type="text" id="annSearch" class="form-control border-0 shadow-sm" placeholder="Search" style="background: #f1f5f9; border-radius: 10px; padding-left: 45px !important;">
            </div>
            
            <?php if ($user_role === 'Admin' || $user_role === 'Bookkeeper'): ?>
            <button class="btn btn-upload-gold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#newsModal" style="height: 50px; padding: 0 25px !important; border-radius: 12px !important; font-weight: 700;">
                <i class="bi bi-plus-circle"></i> Create Announcement
            </button>
            <?php endif; ?>
            
            <!-- Notification bell removed by user request -->
        </div>
    </div>
</div>

<div class="container-fluid px-4 pb-5">
    <!-- Flash Messages -->
    <?php 
    $display_msg = $_GET['msg_status'] ?? $msg_status;
    if ($display_msg === 'created'): ?>
        <div class="alert alert-success fw-bold rounded-4 mb-4"><i class="bi bi-check-circle-fill me-2"></i> Announcement published!</div>
    <?php elseif ($display_msg === 'updated'): ?>
        <div class="alert alert-success fw-bold rounded-4 mb-4"><i class="bi bi-check-circle-fill me-2"></i> Announcement updated!</div>
    <?php elseif ($display_msg === 'deleted'): ?>
        <div class="alert alert-warning fw-bold rounded-4 mb-4"><i class="bi bi-trash-fill me-2"></i> Announcement removed.</div>
    <?php elseif ($display_msg === 'sector_sent'): ?>
        <div class="alert alert-info fw-bold rounded-4 mb-4"><i class="bi bi-send-fill me-2"></i> Sector-based message sent successfully!</div>
    <?php elseif ($display_msg === 'error'): ?>
        <div class="alert alert-danger fw-bold rounded-4 mb-4"><i class="bi bi-exclamation-octagon-fill me-2"></i> Something went wrong.</div>
    <?php endif; ?>


    <!-- ANNOUNCEMENTS TABLE + FILTER -->
    <div class="table-card fade-in-up">
        <!-- FILTER PILLS inside table card -->
        <div class="d-flex align-items-center flex-wrap gap-2 mb-4 pb-3" style="border-bottom: 1px solid #f1f5f9;">
            <span class="filter-pill active" data-filter="all"><i class="bi bi-grid-fill me-1"></i> All</span>
            <span class="filter-pill" data-filter="general"><i class="bi bi-megaphone me-1"></i> General</span>
            <span class="filter-pill" data-filter="event"><i class="bi bi-calendar-event me-1"></i> Events</span>
            <span class="filter-pill" data-filter="meeting"><i class="bi bi-people-fill me-1"></i> Meetings</span>
            <span class="filter-pill" data-filter="deadline"><i class="bi bi-clock-history me-1"></i> Deadlines</span>
            <span class="filter-pill" data-filter="sector"><i class="bi bi-tag-fill me-1"></i> Sector</span>
        </div>
        <div class="table-responsive" style="min-height: 420px;">
        <?php if (count($announcements_list) > 0): ?>
        <table class="table table-elite align-middle">
            <thead>
                <tr>
                    <th style="min-width: 280px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            ANNOUNCEMENT
                        </div>
                    </th>
                    <th style="min-width: 150px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            CATEGORY
                        </div>
                    </th>
                    <th style="min-width: 180px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            AUTHOR
                        </div>
                    </th>
                    <th style="min-width: 160px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            DATE POSTED
                        </div>
                    </th>
                    <th class="text-end"></th>
                </tr>
            </thead>
            <tbody style="font-size: 0.95rem;" id="annTableBody">
                <?php foreach ($announcements_list as $row):
                    $cat = $row['category'] ?? 'General';
                    $bc = [['General'=>['bg'=>'#f1f5f9','color'=>'#64748b'],'Event'=>['bg'=>'#eef2ff','color'=>'#4f46e5'],'Meeting'=>['bg'=>'#fff7ed','color'=>'#ea580c'],'Deadline'=>['bg'=>'#fef2f2','color'=>'#dc2626'],'Sector'=>['bg'=>'#e9f5ee','color'=>'#27ae60']][$cat] ?? ['bg'=>'#f1f5f9','color'=>'#64748b']];
                    $catLower = strtolower($cat);
                    $iconsMap = ['general'=>'bi-megaphone-fill', 'event'=>'bi-calendar-event-fill', 'meeting'=>'bi-people-fill', 'deadline'=>'bi-clock-fill', 'sector'=>'bi-tag-fill'];
                    $icon = $iconsMap[$catLower] ?? 'bi-megaphone-fill';
                    $bc = [['General'=>['bg'=>'#f1f5f9','color'=>'#64748b'],'Event'=>['bg'=>'#eef2ff','color'=>'#4f46e5'],'Meeting'=>['bg'=>'#fff7ed','color'=>'#ea580c'],'Deadline'=>['bg'=>'#fef2f2','color'=>'#dc2626'],'Sector'=>['bg'=>'#e9f5ee','color'=>'#27ae60']][$cat] ?? ['bg'=>'#f1f5f9','color'=>'#64748b']];
                    $bgColor = $bc[0]['bg']; $txtColor = $bc[0]['color'];
                ?>
                <tr class="ann-row" 
                    data-category="<?php echo $catLower; ?>"
                    data-title="<?php echo htmlspecialchars($row['title'], ENT_QUOTES); ?>"
                    data-content="<?php echo htmlspecialchars($row['content'], ENT_QUOTES); ?>"
                    data-author="<?php echo htmlspecialchars($row['author'] ?? 'Administrator', ENT_QUOTES); ?>"
                    data-date="<?php echo date('M d, Y', strtotime($row['date_posted'] ?? date('Y-m-d'))); ?>"
                    data-cat-label="<?php echo htmlspecialchars($cat, ENT_QUOTES); ?>"
                    data-cat-bg="<?php echo $bgColor; ?>" data-cat-color="<?php echo $txtColor; ?>"
                    data-icon="<?php echo $icon; ?>">
                    <td class="py-3">
                        <div class="d-flex align-items-center">
                            <div class="ann-avatar me-3"><i class="bi <?php echo $icon; ?>"></i></div>
                            <div>
                                <div class="fw-bold ann-title" style="color:#1e293b;"><?php echo htmlspecialchars($row['title']); ?></div>
                                <small class="text-muted" style="font-size:0.8rem;"><?php echo htmlspecialchars(substr($row['content'], 0, 60)) . (strlen($row['content']) > 60 ? '...' : ''); ?></small>
                            </div>
                        </div>
                    </td>
                    <td class="py-3">
                        <span class="ann-category badge" style="background:<?php echo $bgColor; ?>; color:<?php echo $txtColor; ?>; font-weight:700; padding:5px 12px; border-radius:20px; font-size:0.7rem;"><?php echo htmlspecialchars($cat); ?></span>
                    </td>
                    <td class="py-3">
                        <div class="user-table-item">
                            <?php 
                                $ann_author = ($row['id'] % 2 !== 0) ? 'Administrator' : 'Bookkeeper';
                                $ann_initial = ($ann_author === 'Administrator') ? 'A' : 'B';
                            ?>
                            <div class="user-table-avatar-box"><?php echo $ann_initial; ?></div>
                            <div class="user-table-info">
                                <span class="user-table-name"><?php echo $ann_author; ?></span>
                                <span class="user-table-role"><?php echo $ann_author; ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 text-muted ann-date" style="font-size:0.85rem;"><?php echo date('M d, Y', strtotime($row['date_posted'] ?? date('Y-m-d'))); ?></td>
                    <?php if ($user_role === 'Admin' || $user_role === 'Bookkeeper'): ?>
                    <td class="py-3 text-end" onclick="event.stopPropagation();">
                        <div class="d-flex gap-1 justify-content-end align-items-center">
                            <button type="button" class="action-btn view btn-action-view" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="action-btn edit btn-action-edit" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button type="button" class="action-btn del btn-action-delete" title="Delete">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div id="annEmptyState" style="display:none;" class="text-center py-5">
            <i class="bi bi-broadcast" style="font-size:4rem; opacity:0.1;"></i>
            <h5 class="fw-700 mt-3 text-muted">No announcements found</h5>
            <p class="text-muted">Stay tuned for updates from the cooperative management.</p>
        </div>

        <!-- FUNCTIONAL PAGINATION -->
        <div class="pagination-elite">
            <span class="pagination-elite-label">Rows per page</span>
            <select id="rowsPerPage" class="pagination-elite-select">
                <option value="5" selected>5</option>
                <option value="10">10</option>
                <option value="20">20</option>
            </select>
            <span id="paginationInfo" class="pagination-elite-info">1–5 of 14</span>
            <div class="pagination-elite-buttons">
                <button id="prevPage" class="pagination-elite-btn" title="Previous Page">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button id="nextPage" class="pagination-elite-btn" title="Next Page">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-broadcast" style="font-size:4rem; opacity:0.1;"></i>
            <h5 class="fw-700 mt-3 text-muted">No announcements yet</h5>
            <p class="text-muted">Stay tuned for updates from the cooperative management.</p>
        </div>
        <?php endif; ?>
        </div><!-- end table-responsive -->
    </div><!-- end table-card -->
</div><!-- end container -->

<!-- SINGLE FIXED VIEW ANNOUNCEMENT MODAL -->
<div class="modal fade" id="viewAnnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modal-fixed-size" style="border-radius: 20px; border: none;">
            <div class="modal-header" style="background: rgba(22, 74, 54, 0.95); padding: 24px; color: white;">
                <h5 class="modal-title fw-800" id="viewAnnTitle" style="color: #27ae60 !important;">Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3" style="border-bottom: 1px solid #f1f5f9;">
                    <div class="ann-avatar" id="viewAnnIcon" style="width:52px;height:52px;font-size:1.4rem;flex-shrink:0;"><i class="bi bi-megaphone-fill"></i></div>
                    <div>
                        <span id="viewAnnBadge" class="badge mb-1" style="font-size:0.7rem; font-weight:700; padding:5px 14px; border-radius:20px;">General</span>
                        <div class="text-muted" style="font-size:0.85rem;">
                            <i class="bi bi-person me-1"></i><span id="viewAnnAuthor">Administrator</span>
                            <span class="mx-2">·</span>
                            <i class="bi bi-calendar3 me-1"></i><span id="viewAnnDate">—</span>
                        </div>
                    </div>
                </div>
                <p id="viewAnnContent" class="text-dark mb-4" style="font-size: 0.97rem; line-height: 1.8; white-space: pre-wrap;"></p>
                <div class="text-end">
                    <button type="button" class="btn fw-bold px-4" style="height: 50px; padding: 0 30px !important; border-radius: 50px !important; background: #ef4444; color: white; border: none; font-weight: 700;" data-bs-dismiss="modal">Close Announcement</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- COMBINED NEWS & MESSAGING MODAL (With Tabs) -->
<div class="modal fade" id="newsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header" style="background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22,74,54,0.3); padding: 24px; color: white;">
                <h5 class="modal-title fw-800" style="color: #20a060 !important;">Create News &amp; Messages</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            
            
            <!-- Single Unified Form -->
            <form method="POST" onsubmit="return handleNewsSubmit(event)">
                <input type="hidden" name="message_type" id="hidden_message_type" value="announcement">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Message Type (Fixed to Announcement) -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Message Type</label>
                            <input type="text" class="form-control" value="Announcement (All Members)" readonly style="border-radius: 12px; border: 1.5px solid #e5e5c0; background: #f8fafc;">
                            <input type="hidden" id="messageTypeSelect" value="announcement">
                        </div>

                        <!-- Category Field (Always shown for static announcements) -->
                        <div class="col-md-6" id="categoryField">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Category</label>
                            <select name="category" class="form-select" style="border-radius: 12px; border: 1.5px solid #e5e5c0; padding: 12px 14px; font-size: 16px;">
                                <option value="General">General</option>
                                <option value="Event">Event</option>
                                <option value="Meeting">Meeting</option>
                                <option value="Deadline">Deadline</option>
                            </select>
                        </div>

                        <!-- Title/Subject -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1" id="titleLabel">Title</label>
                            <input type="text" name="title" class="form-control" style="border-radius: 12px; border: 1.5px solid #e5e5c0;" placeholder="What's this about?" required>
                        </div>

                        <!-- Content -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Content</label>
                            <textarea name="content" class="form-control" rows="6" style="border-radius: 12px; border: 1.5px solid #e5e5c0;" placeholder="Type your message here..." required></textarea>
                        </div>
                        <div class="col-12 mt-4 text-end pe-3 pb-2">
                             <button type="button" class="btn fw-bold me-2" style="height: 50px; padding: 0 30px !important; border-radius: 50px !important; background: #ef4444; color: white; border: none; font-weight: 700;" data-bs-dismiss="modal">Cancel</button>
                             <button type="submit" class="btn fw-bold px-4 py-2" style="height: 50px; padding: 0 35px !important; border-radius: 50px !important; background: #27ae60; color: white; border: none; font-weight: 700; transition: all 0.3s ease;" onmouseover="this.style.background='#1a8548'; this.style.boxShadow='0 8px 20px rgba(39, 174, 96, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#27ae60'; this.style.boxShadow='none'; this.style.transform='translateY(0)';" id="submitBtn">Publish Now</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT ANNOUNCEMENT MODAL -->
<div class="modal fade" id="editAnnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header" style="background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22,74,54,0.3); padding: 24px; color: white;">
                <h5 class="modal-title fw-800 text-white"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <form method="POST" onsubmit="return TrackUI.confirmForm(event, 'Save changes to this announcement?', 'Update Announcement', 'primary')">
                <input type="hidden" name="edit_announcement" value="1">
                <input type="hidden" name="ann_id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-9">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Title</label>
                            <input type="text" name="title" id="edit_title" class="form-control" style="border-radius: 12px; border: 1.5px solid #e5e5c0;" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Category</label>
                            <select name="category" id="edit_category" class="form-select" style="border-radius: 12px; border: 1.5px solid #e5e5c0;">
                                <option value="General">General</option>
                                <option value="Event">Event</option>
                                <option value="Meeting">Meeting</option>
                                <option value="Deadline">Deadline</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Content</label>
                            <textarea name="content" id="edit_content" class="form-control" rows="6" style="border-radius: 12px; border: 1.5px solid #e5e5c0;" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: rgba(22, 74, 54, 0.95); border-top: 1px solid rgba(22,74,54,0.3); padding: 20px; color: white;">
                    <button type="button" class="btn fw-bold px-4 py-2" style="background: #ef4444; color: white; border: none; border-radius: 50px; transition: all 0.3s ease;" onmouseover="this.style.background='#dc2626'; this.style.boxShadow='0 8px 20px rgba(239,68,68,0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#ef4444'; this.style.boxShadow='none'; this.style.transform='translateY(0)';" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" name="edit_announcement" class="btn fw-bold px-4 py-2" style="border-radius: 50px; background: #20a060; color: white; border: none; transition: all 0.3s ease;" onmouseover="this.style.background='#1a8548'; this.style.boxShadow='0 8px 20px rgba(32, 160, 96, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#20a060'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteAnnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header" style="background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22,74,54,0.3); padding: 24px; color: white;">
                <h5 class="modal-title fw-800 text-danger"><i class="bi bi-trash3-fill me-2"></i>Delete Post?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="delete_announcement" value="1">
                <input type="hidden" name="ann_id" id="del_id">
                <div class="modal-body p-4">
                    <p class="mb-0 text-muted">Are you sure you want to delete <strong id="del_title"></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer" style="background: rgba(22, 74, 54, 0.95); border-top: 1px solid rgba(22,74,54,0.3); padding: 20px; color: white;">
                    <button type="button" class="btn fw-bold px-4 py-2" style="background: #ef4444; color: white; border: none; border-radius: 50px; transition: all 0.3s ease;" onmouseover="this.style.background='#dc2626'; this.style.boxShadow='0 8px 20px rgba(239,68,68,0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#ef4444'; this.style.boxShadow='none'; this.style.transform='translateY(0)';" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_announcement" class="btn fw-bold px-4 py-2" style="background: #dc2626; color: white; border: none; border-radius: 50px; transition: all 0.3s ease;" onmouseover="this.style.background='#991b1b'; this.style.boxShadow='0 8px 20px rgba(220, 38, 38, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#dc2626'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>




    </div> <!-- .main-content-wrapper -->
</div> <!-- .sidebar-layout -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 700, once: true });

    // Open Fixed View Modal — populate from row data attributes
    function openViewModal(row) {
        document.getElementById('viewAnnTitle').textContent   = row.dataset.title;
        document.getElementById('viewAnnContent').textContent = row.dataset.content;
        document.getElementById('viewAnnAuthor').textContent  = row.dataset.author;
        document.getElementById('viewAnnDate').textContent    = row.dataset.date;

        const badge = document.getElementById('viewAnnBadge');
        badge.textContent         = row.dataset.catLabel;
        badge.style.background    = row.dataset.catBg;
        badge.style.color         = row.dataset.catColor;

        const icon = document.getElementById('viewAnnIcon');
        icon.innerHTML            = '<i class="bi ' + row.dataset.icon + '"></i>';
        icon.style.background     = row.dataset.catBg;
        icon.style.color          = row.dataset.catColor;
    }

    // Active filter tracker
    let activeFilter = 'all';

    function applyFilters() {
        const term = document.getElementById('annSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('#annTableBody .ann-row');
        let found = 0;

        rows.forEach(row => {
            const title = row.querySelector('.ann-title')?.textContent.toLowerCase() || '';
            const author = row.querySelector('.ann-author')?.textContent.toLowerCase() || '';
            const rowCategory = (row.dataset.category || '').toLowerCase();

            const matchesSearch = term === '' || title.includes(term) || author.includes(term);
            const matchesFilter = activeFilter === 'all' || rowCategory === activeFilter;

            if (matchesSearch && matchesFilter) {
                row.style.display = '';
                found++;
            } else {
                row.style.display = 'none';
            }
        });

        const emptyState = document.getElementById('annEmptyState');
        if (emptyState) emptyState.style.display = found === 0 ? 'block' : 'none';
    }

    // Real-time Search
    document.getElementById('annSearch').addEventListener('input', applyFilters);

    // Filter Pills
    document.querySelectorAll('.filter-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            activeFilter = this.dataset.filter;
            applyFilters();
        });
    });

    function openEdit(id, title, cat, content) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_category').value = cat;
        document.getElementById('edit_content').value = JSON.parse(content);
    }

    function confirmDelete(id, title) {
        document.getElementById('del_id').value = id;
        document.getElementById('del_title').textContent = '"' + title + '"';
    }

    // --- FUNCTIONAL PAGINATION ---
    let currentPage = 1;
    let rowsPerPage = parseInt(document.getElementById('rowsPerPage').value);
    
    function updatePagination() {
        const rows = Array.from(document.querySelectorAll('.ann-row:not(.search-hidden)'));
        const totalRows = rows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);
        
        if (currentPage > totalPages) currentPage = totalPages || 1;
        if (currentPage < 1) currentPage = 1;

        const startIdx = (currentPage - 1) * rowsPerPage;
        const endIdx = startIdx + rowsPerPage;

        rows.forEach((row, index) => {
            if (index >= startIdx && index < endIdx) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Update Info Text
        const startDisplay = totalRows === 0 ? 0 : startIdx + 1;
        const endDisplay = Math.min(endIdx, totalRows);
        const infoEl = document.getElementById('paginationInfo');
        if (infoEl) infoEl.textContent = `${startDisplay}-${endDisplay} of ${totalRows}`;

        // Disable/Enable buttons
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        
        if (prevBtn) {
            prevBtn.disabled = (currentPage === 1);
            prevBtn.style.opacity = (currentPage === 1) ? '0.5' : '1';
        }
        if (nextBtn) {
            nextBtn.disabled = (currentPage === totalPages || totalPages === 0);
            nextBtn.style.opacity = (currentPage === totalPages || totalPages === 0) ? '0.5' : '1';
        }
    }

    document.getElementById('rowsPerPage').addEventListener('change', function() {
        rowsPerPage = parseInt(this.value);
        currentPage = 1;
        updatePagination();
    });

    document.getElementById('prevPage').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            updatePagination();
        }
    });

    document.getElementById('nextPage').addEventListener('click', function() {
        const rows = document.querySelectorAll('.ann-row:not(.search-hidden)');
        if (currentPage < Math.ceil(rows.length / rowsPerPage)) {
            currentPage++;
            updatePagination();
        }
    });

    // Integrated Search
    function performAnnSearch() {
        const term = document.getElementById('annSearch').value.toLowerCase().trim();
        const activeFilter = document.querySelector('.filter-pill.active').getAttribute('data-filter');
        const allRows = document.querySelectorAll('.ann-row');
        let found = 0;

        allRows.forEach(row => {
            const title = row.getAttribute('data-title').toLowerCase();
            const content = row.getAttribute('data-content').toLowerCase();
            const author = row.getAttribute('data-author').toLowerCase();
            const category = row.getAttribute('data-category');

            const matchesSearch = title.includes(term) || content.includes(term) || author.includes(term);
            const matchesFilter = activeFilter === 'all' || category === activeFilter;

            if (matchesSearch && matchesFilter) {
                row.classList.remove('search-hidden');
                found++;
            } else {
                row.classList.add('search-hidden');
            }
        });

        const emptyState = document.getElementById('annEmptyState');
        if (found === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }

        currentPage = 1; 
        updatePagination();
    }

    document.getElementById('annSearch').addEventListener('input', performAnnSearch);

    // Initial load
    updatePagination();

    // Re-link existing filter logic to search
    document.querySelectorAll('.filter-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            performAnnSearch();
        });
    });

    function toggleSectorFields() {
        // Static announcements only
        const hiddenMessageType = document.getElementById('hidden_message_type');
        hiddenMessageType.value = 'announcement';
        
        const titleLabel = document.getElementById('titleLabel');
        const submitBtn = document.getElementById('submitBtn');
        
        titleLabel.textContent = 'Title';
        submitBtn.textContent = 'Publish Now';
    }

    function handleNewsSubmit(event) {
        return TrackUI.confirmForm(event, 'Do you want to publish this announcement?', 'Publish Announcement', 'primary');
    }
</script>
</body>
</html>
