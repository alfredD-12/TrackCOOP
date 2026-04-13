<?php
session_start();
include('../auth/db_connect.php'); // Path kept for stability, but DB not used in demo

// Static Identity for Full-Static Demo
$user_id   = 1;
$user_role = 'Admin';
$full_name = "Administrator";

$error_msg = "";
$success_msg = "";

// ── Standardized 5-Sector Static Member Data ──
$static_members = [
    ['id' => 1, 'first_name' => 'Juan', 'middle_name' => 'A', 'last_name' => 'Dela Cruz', 'username' => 'juan123', 'sector' => 'Rice', 'role' => 'Member', 'status' => 'Approved'],
    ['id' => 2, 'first_name' => 'Maria', 'middle_name' => 'B', 'last_name' => 'Santos', 'username' => 'maria456', 'sector' => 'Corn', 'role' => 'Member', 'status' => 'Approved'],
    ['id' => 3, 'first_name' => 'Pedro', 'middle_name' => 'C', 'last_name' => 'Garcia', 'username' => 'pedro789', 'sector' => 'Fishery', 'role' => 'Member', 'status' => 'Pending'],
    ['id' => 4, 'first_name' => 'Rosa', 'middle_name' => 'D', 'last_name' => 'Lopez', 'username' => 'rosa101', 'sector' => 'Livestock', 'role' => 'Member', 'status' => 'Approved'],
    ['id' => 5, 'first_name' => 'Alex', 'middle_name' => 'E', 'last_name' => 'Reyes', 'username' => 'alexreyes', 'sector' => 'High Value Crops', 'role' => 'Member', 'status' => 'Approved'],
    ['id' => 6, 'first_name' => 'Lito', 'middle_name' => 'F', 'last_name' => 'Perez', 'username' => 'litoperez', 'sector' => 'Rice', 'role' => 'Member', 'status' => 'Approved'],
    ['id' => 7, 'first_name' => 'Elena', 'middle_name' => 'G', 'last_name' => 'Cruz', 'username' => 'elenacruz', 'sector' => 'High Value Crops', 'role' => 'Member', 'status' => 'Approved'],
];

// Handle POST Requests (Add / Edit) - Simulated
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action_type = isset($_POST['action_type']) ? $_POST['action_type'] : '';
    
    if ($action_type === 'add_member' && $user_role === 'Admin') {
        $success_msg = "New cooperative member added successfully. (Demo Mode)";
    } elseif ($action_type === 'edit_member' && $user_role === 'Admin') {
        $success_msg = "Member details updated successfully. (Demo Mode)";
    }
}

// Handle GET Actions (Approve / Deactivate / Delete) - Simulated
if (isset($_GET['action']) && isset($_GET['id']) && $user_role === 'Admin') {
    $action_get = $_GET['action'];
    if (in_array($action_get, ['approve', 'deactivate', 'delete'])) {
        if ($action_get === 'delete') {
            header("Location: members.php?msg=deleted");
            exit();
        } else {
            header("Location: members.php?msg=" . $action_get);
            exit();
        }
    }
}

// Fetch all members - using static data for demo
$all_members = $static_members;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Management | TRACKCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)), url('../Home.jpeg') top center / 100% 100% no-repeat fixed;
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
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220, 38, 38, 0.6);
        }

        .navbar {
            background-color: rgba(22, 74, 54, 0.95) !important;
            backdrop-filter: blur(10px);
            padding: 15px 0;
            border-bottom: 1px solid rgba(22, 74, 54, 0.3);
            animation: fadeInUpCustom 0.8s ease-out;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.8px;
            color: #ffffff !important;
        }
        .navbar-brand span { color: #27ae60; }

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
        .navbar-nav .nav-link.active::after { width: 100%; }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active { color: #27ae60 !important; background: transparent !important; }

        .admin-header {
            background: transparent;
            padding: 10px 0 5px;
            border-bottom: none;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
            color: #ffffff !important;
        }

        .admin-header h1 { 
            color: #27ae60 !important; 
            letter-spacing: -1.5px;
            font-weight: 800 !important;
        }

        .admin-header p, .admin-header .text-muted { 
            color: #ffffff !important; 
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        .admin-header::after {
            content: ''; position: absolute; top: -20%; right: -5%; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(39,174,96,0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%; z-index: 0; pointer-events: none;
        }
        .badge-platform {
            background: white; color: var(--track-green); font-weight: 700; padding: 6px 14px;
            border-radius: 50px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
            display: inline-flex; align-items: center; margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.1); border: 1px solid rgba(32, 160, 96, 0.2);
        }

        
        
        .table-responsive {
            overflow-x: auto !important; /* Only show scrollbar if content is wider than screen */
            overflow-y: visible !important;
            -webkit-overflow-scrolling: touch;
        }
        
        .badge-status { 
            padding: 8px 16px; 
            border-radius: 12px; 
            font-size: 0.75rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .badge-approved { background: #eefdf5; color: #27ae60; }
        .badge-pending { background: #fff9e6; color: #d97706; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }

        .action-btn {
            display: inline-flex; width: 38px; height: 38px; align-items: center; justify-content: center;
            border-radius: 10px; color: #64748b; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: #f8fafc; border: 1px solid #e2e8f0;
            text-decoration: none; padding: 0; outline: none; font-size: 1rem; cursor: pointer;
        }
        
        /* Unified Action Styles based on Documents */
        .action-btn.view { color: #3b82f6; border-color: rgba(59, 130, 246, 0.3); background: rgba(59, 130, 246, 0.05); }
        .action-btn.view:hover { background: #3b82f6; color: white; border-color: #3b82f6; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }

        .action-btn.edit { color: #f59e0b; border-color: rgba(245, 158, 11, 0.3); background: rgba(245, 158, 11, 0.05); }
        .action-btn.edit:hover { background: #f59e0b; color: white; border-color: #f59e0b; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }

        .action-btn.approve { color: #27ae60; border-color: rgba(32, 160, 96, 0.3); background: rgba(32, 160, 96, 0.05); }
        .action-btn.approve:hover { background: #27ae60; color: white; border-color: #27ae60; box-shadow: 0 4px 12px rgba(32, 160, 96, 0.3); }

        .action-btn.delete { color: #ef4444; border-color: rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.05); }
        .action-btn.delete:hover { background: #ef4444; color: white; border-color: #ef4444; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }
        
        .btn-success { transition: all 0.3s ease; background: #27ae60 !important; border: none !important; }
        .btn-success:hover { background: #1a8548 !important; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(32, 160, 96, 0.3) !important; }
        
        .form-label { font-size: 0.95rem; font-weight: 600; color: var(--track-dark); margin-bottom: 8px; }
        .form-control, .form-select { border-radius: 8px; padding: 10px 14px; border: 2px solid #EAE0C8; background-color: #fff; transition: 0.3s; }
        .form-control:focus, .form-select:focus { border-color: var(--track-green); box-shadow: 0 0 0 3px rgba(32, 160, 96, 0.1); }
        .modal-header-beige { background-color: rgba(22, 74, 54, 0.95); border-bottom: none; padding: 20px 30px; border-radius: 20px 20px 0 0; color: white; }
        .modal-footer-beige { background-color: rgba(22, 74, 54, 0.95); border-top: none; padding: 20px 30px; border-radius: 0 0 20px 20px; color: white; }

        /* Modal Customizations */
        .modal-content { border-radius: 30px !important; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden !important; }
        .modal-header { border-bottom: 1px solid #f1f5f9; padding: 20px 30px; }
        .modal-footer { border-top: 1px solid #f1f5f9; padding: 20px 30px; }
        .modal-body { padding: 30px; }

        /* ── Red Circle Close Button ── */
        .modal-content .btn-close {
            width: 36px !important; height: 36px !important; min-width: 36px !important;
            background: #ef4444 !important; background-image: none !important;
            border-radius: 50% !important; opacity: 1 !important; filter: none !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4) !important;
            transition: all 0.2s ease !important; padding: 0 !important; position: relative !important;
            margin: 0 !important; /* Force override to prevent alignment issues */
        }
        .modal-content .btn-close::before,
        .modal-content .btn-close::after {
            content: "" !important; position: absolute !important; top: 50% !important; left: 50% !important;
            width: 14px !important; height: 2px !important; background-color: white !important; border-radius: 2px !important;
        }
        .modal-content .btn-close::before { transform: translate(-50%, -50%) rotate(45deg) !important; }
        .modal-content .btn-close::after { transform: translate(-50%, -50%) rotate(-45deg) !important; }
        .modal-content .btn-close:hover {
            background-color: #dc2626 !important; transform: scale(1.1) !important;
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.5) !important;
        }

        .profile-header-modal { background: linear-gradient(135deg, var(--track-green) 0%, #167e4a 100%); height: 120px; position: relative; border-radius: 20px 20px 0 0; margin-top: -30px; margin-left: -30px; margin-right: -30px; margin-bottom: 50px; }
        .profile-avatar-modal { 
            width: 100px; height: 100px; border-radius: 50%; background: white; border: 4px solid white;
            position: absolute; bottom: -50px; left: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; color: var(--track-green);
        }
        .info-label { font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; font-weight: 700; letter-spacing: 1px; margin-bottom: 4px; display: block; }
        .info-value { font-size: 1.05rem; font-weight: 600; color: var(--track-dark); margin-bottom: 24px; }

        /* --- SEARCH STYLING --- */
        .search-wrapper {
            position: relative;
            display: block;
            margin-bottom: 30px;
            max-width: 600px;
            animation: fadeInUpCustom 0.8s ease-out 0.2s both;
        }

        .search-input-group {
            position: relative;
            flex: 1;
        }

        .search-input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--track-green);
            font-size: 1.1rem;
            z-index: 5;
        }

        .search-input {
            width: 100%;
            height: 52px;
            padding: 0 20px 0 50px;
            border-radius: 14px;
            border: 1px solid #e2e8f0 !important;
            background: white;
            transition: var(--transition-smooth);
            font-weight: 600;
            color: var(--track-dark);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--track-green);
            box-shadow: 0 6px 15px rgba(32, 160, 96, 0.1);
        }

        .btn-search-trigger {
            height: 52px;
            background: var(--track-green);
            color: white;
            border: none;
            border-radius: 14px;
            padding: 0 30px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition-smooth);
            box-shadow: 0 6px 15px rgba(32, 160, 96, 0.2);
            white-space: nowrap;
        }

        .btn-search-trigger:hover {
            background: #1a8548;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.3);
            color: white;
        }
    </style>
    <link rel="stylesheet" href="../includes/footer.css">
</head>
<body>
<div class="sidebar-layout">
    <?php 
        $active_page = 'members';
        $user_role = $_SESSION['role'];
        $membership_type = $user_role;
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
                <input type="text" id="memberSearch" class="form-control border-0 shadow-sm" placeholder="Search" style="background: #f1f5f9; border-radius: 10px; padding-left: 45px !important;">
            </div>
            
            <?php if ($user_role === 'Admin'): ?>
            <!-- Add Member trigger removed by user request -->
            <?php endif; ?>
            
            <!-- Notification bell removed by user request -->
        </div>
    </div>
</div>

<div class="container-fluid px-4 pb-5">
    <?php if($success_msg): ?>
        <div class="alert alert-success fw-bold"><i class="bi bi-check-circle-fill me-2"></i> <?php echo $success_msg; ?></div>
    <?php endif; ?>
    <?php if($error_msg): ?>
        <div class="alert alert-danger fw-bold"><i class="bi bi-exclamation-octagon-fill me-2"></i> <?php echo $error_msg; ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['msg']) && $_GET['msg'] === 'approve'): ?>
        <div class="alert alert-success fw-bold"><i class="bi bi-check-circle-fill me-2"></i> Account has been approved.</div>
    <?php elseif(isset($_GET['msg']) && $_GET['msg'] === 'deactivate'): ?>
        <div class="alert alert-success fw-bold"><i class="bi bi-check-circle-fill me-2"></i> Account has been deactivated.</div>
    <?php elseif(isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-danger fw-bold"><i class="bi bi-trash-fill me-2"></i> Account deleted successfully.</div>
    <?php endif; ?>


    <div class="table-card fade-in-up table-responsive">
        <table class="table table-elite align-middle">
            <thead>
                <tr>
                    <th style="min-width: 200px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            NAME
                        </div>
                    </th>
                    <th style="min-width: 150px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            SECTOR
                        </div>
                    </th>
                    <th style="min-width: 120px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            ROLE
                        </div>
                    </th>
                    <th style="min-width: 130px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            STATUS
                        </div>
                    </th>
                    <th class="text-end"></th>
                </tr>
            </thead>
            <tbody style="font-size: 0.95rem;">
                <?php $modalsHtml = ''; ?>
                <?php foreach($all_members as $row): ?>
                <tr style="cursor: default;">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="user-table-avatar-initials me-3" style="background: var(--track-green); color: white;">
                                <?php echo strtoupper(substr($row['first_name'],0,1) . substr($row['last_name'],0,1)); ?>
                            </div>
                            <div class="user-table-info">
                                <span class="user-table-name member-name"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></span>
                                <span class="user-table-email member-user">@<?php echo htmlspecialchars($row['username']); ?></span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-600 text-dark">
                            <?php echo htmlspecialchars($row['sector']); ?>
                        </div>
                    </td>
                    <td class="text-muted small fw-600">
                        <?php echo htmlspecialchars($row['role']); ?>
                    </td>
                    <td>
                        <?php 
                            if($row['status'] === 'Approved') echo '<span class="badge-status badge-approved"><i class="bi bi-check-circle-fill"></i> APPROVED</span>';
                            elseif($row['status'] === 'Pending') echo '<span class="badge-status badge-pending"><i class="bi bi-exclamation-triangle-fill"></i> PENDING</span>';
                            else echo '<span class="badge-status badge-inactive"><i class="bi bi-x-circle-fill"></i> INACTIVE</span>';
                        ?>
                    </td>
                    <td class="text-end" onclick="event.stopPropagation();">
                        <div class="d-flex gap-1 justify-content-end align-items-center">
                            <button type="button" class="btn-doc-action btn-action-view" title="View Profile">
                                <i class="bi bi-eye"></i>
                            </button>
                            
                            <?php if($user_role === 'Admin'): ?>
                                <button type="button" class="btn-doc-action btn-action-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <?php if($row['status'] === 'Pending'): ?>
                                    <button type="button" class="btn-doc-action btn-action-approve" title="Approve">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                <?php endif; ?>
                                <button type="button" class="btn-doc-action btn-action-delete" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>

                <?php ob_start(); ?>
                <!-- View Member Modal -->
                <div class="modal fade" id="viewMemberModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                                <div class="modal-header-beige d-flex justify-content-between align-items-center">
                                    <h5 class="modal-title fw-bold m-0" style="color: var(--track-dark);"><i class="bi bi-person-vcard text-success me-2"></i> View Profile</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <div class="d-flex align-items-center mb-4 pb-4 border-bottom">
                                        <div class="bg-success text-white fw-bold me-3 d-flex align-items-center justify-content-center" style="width:60px;height:60px;border-radius:12px;font-size:1.5rem;">
                                            <?php echo strtoupper(substr($row['first_name'],0,1) . substr($row['last_name'],0,1)); ?>
                                        </div>
                                        <div>
                                            <h4 class="fw-bold mb-1" style="color: var(--track-dark);"><?php echo htmlspecialchars($row['first_name'] . " " . $row['middle_name'] . " " . $row['last_name']); ?></h4>
                                            <div class="text-muted fw-semibold" style="font-size: 0.9rem;">@<?php echo htmlspecialchars($row['username']); ?></div>
                                        </div>
                                        <div class="ms-auto">
                                            <?php 
                                                if($row['status'] === 'Approved') echo '<span class="badge-status badge-approved">Approved</span>';
                                                elseif($row['status'] === 'Pending') echo '<span class="badge-status badge-pending">Pending</span>';
                                                else echo '<span class="badge-status badge-inactive">Inactive</span>';
                                            ?>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-3 pb-2 text-muted" style="border-bottom: 1px solid #e2e8f0;">Cooperative Details</h6>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted mb-1" style="font-size: 0.8rem; text-transform: uppercase;">Cooperative Sector</label>
                                            <div class="fw-bold d-flex align-items-center" style="color: var(--track-dark); font-size: 1.05rem;">
                                                <i class="bi bi-flower1 text-success me-2"></i><?php echo htmlspecialchars($row['sector']); ?> Sector
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted mb-1" style="font-size: 0.8rem; text-transform: uppercase;">System Role</label>
                                            <div class="fw-bold d-flex align-items-center" style="color: var(--track-dark); font-size: 1.05rem;">
                                                <i class="bi bi-shield-lock-fill text-muted me-2"></i><?php echo htmlspecialchars($row['role']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h6 class="fw-bold mb-3 pb-2 text-muted" style="border-bottom: 1px solid #e2e8f0;">Account Information</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label text-muted mb-1" style="font-size: 0.8rem; text-transform: uppercase;">Account ID</label>
                                            <div class="fw-bold" style="color: var(--track-dark); font-size: 1.05rem;">#<?php echo str_pad($row['id'], 6, '0', STR_PAD_LEFT); ?></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label text-muted mb-1" style="font-size: 0.8rem; text-transform: uppercase;">Registration Date</label>
                                            <div class="fw-bold" style="color: var(--track-dark); font-size: 1.05rem;"><?php echo isset($row['created_at']) ? date('M j, Y', strtotime($row['created_at'])) : 'Unknown'; ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer-beige d-flex justify-content-end gap-2">
                                    <button type="button" class="btn px-4 fw-bold" style="height: 50px; padding: 0 30px !important; border-radius: 50px !important; background: #ef4444; color: white; border: none; font-weight: 700; transition: all 0.3s ease;" onmouseover="this.style.background='#dc2626'; this.style.boxShadow='0 8px 20px rgba(239, 68, 68, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#ef4444'; this.style.boxShadow='none'; this.style.transform='translateY(0)';" data-bs-dismiss="modal">Close</button>
                                    <?php if($user_role === 'Admin'): ?>
                                    <button type="button" class="btn px-4 fw-bold text-white shadow-sm" style="height: 50px; padding: 0 35px !important; border-radius: 50px !important; background: var(--track-green); font-weight: 700;" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editMemberModal<?php echo $row['id']; ?>">Edit Details</button>
                                    <?php endif; ?>
                                </div>
                        </div>
                    </div>
                </div>

                <?php if($user_role === 'Admin'): ?>
                <!-- Edit Member Modal -->
                <div class="modal fade" id="editMemberModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <form method="POST" action="members.php" onsubmit="return TrackUI.confirmForm(event, 'Apply updates to this member profile?', 'Save Changes', 'primary', 'Update Now', 'Review')">
                                <input type="hidden" name="action_type" value="edit_member">
                                <input type="hidden" name="target_id" value="<?php echo $row['id']; ?>">
                                
                                <div class="modal-header-beige d-flex justify-content-between align-items-center">
                                    <h5 class="modal-title fw-bold m-0" style="color: var(--track-dark);"><i class="bi bi-pencil-square text-success me-2"></i> Edit Member</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-muted">Personal Information</h6>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4">
                                            <label class="form-label">First Name</label>
                                            <input type="text" name="fname" class="form-control" value="<?php echo htmlspecialchars($row['first_name']); ?>" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Middle Name</label>
                                            <input type="text" name="mname" class="form-control" value="<?php echo htmlspecialchars($row['middle_name']); ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" name="lname" class="form-control" value="<?php echo htmlspecialchars($row['last_name']); ?>" required>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-muted">Account Details</h6>
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-12">
                                            <label class="form-label">Username (Read Only)</label>
                                            <input type="text" class="form-control bg-light text-muted fw-bold" value="<?php echo htmlspecialchars($row['username']); ?>" readonly>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-muted">Cooperative Setup</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Agri Sector</label>
                                            <select name="sector" class="form-select" required>
                                                <?php
                                                $sectors = ['Rice', 'Corn', 'Fishery', 'Livestock', 'High Value Crops'];
                                                foreach($sectors as $sec) {
                                                    $sel = ($row['sector'] === $sec) ? 'selected' : '';
                                                    echo "<option value=\"$sec\" $sel>$sec</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">System Role</label>
                                            <select name="role" class="form-select" required>
                                                <?php
                                                $roles = ['Member', 'Bookkeeper', 'Admin'];
                                                foreach($roles as $rl) {
                                                    $sel = ($row['role'] === $rl) ? 'selected' : '';
                                                    echo "<option value=\"$rl\" $sel>$rl</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Account Status</label>
                                            <select name="status" class="form-select" required>
                                                <?php
                                                $statuses = ['Approved', 'Pending', 'Inactive'];
                                                foreach($statuses as $st) {
                                                    $sel = ($row['status'] === $st) ? 'selected' : '';
                                                    echo "<option value=\"$st\" $sel>" . ($st == 'Approved' ? 'Approved / Active' : $st) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer-beige d-flex justify-content-end gap-2">
                                    <button type="button" class="btn px-4 fw-bold text-white" style="height: 50px; padding: 0 30px !important; background: #ef4444; border-radius: 50px !important; border: none; font-weight: 700; transition: all 0.3s ease; box-shadow: 0 8px 15px rgba(239, 68, 68, 0.2);" onmouseover="this.style.background='#dc2626'; this.style.boxShadow='0 12px 25px rgba(239, 68, 68, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#ef4444'; this.style.boxShadow='0 8px 15px rgba(239, 68, 68, 0.2)'; this.style.transform='translateY(0)';" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn px-4 fw-bold text-white" style="height: 50px; padding: 0 35px !important; background: var(--track-green); border-radius: 50px !important; border: none; font-weight: 700; transition: all 0.3s ease; box-shadow: 0 8px 15px rgba(39, 174, 96, 0.2);" onmouseover="this.style.background='#1a8548'; this.style.boxShadow='0 12px 25px rgba(39, 174, 96, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#27ae60'; this.style.boxShadow='0 8px 15px rgba(39, 174, 96, 0.2)'; this.style.transform='translateY(0)';" >Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php $modalsHtml .= ob_get_clean(); ?>

                <?php endforeach; ?>

                <?php if(empty($all_members)): ?>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No members found in the cooperative database.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <!-- FUNCTIONAL PAGINATION -->
        <div class="pagination-elite">
            <span class="pagination-elite-label">Rows per page</span>
            <select id="rowsPerPage" class="pagination-elite-select">
                <option value="5" selected>5</option>
                <option value="10">10</option>
                <option value="20">20</option>
            </select>
            <span id="paginationInfo" class="pagination-elite-info">1–5 of <?php echo count($all_members); ?></span>
            <div class="pagination-elite-buttons">
                <button id="prevPage" class="pagination-elite-btn" title="Previous Page">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button id="nextPage" class="pagination-elite-btn" title="Next Page">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<?php if($user_role === 'Admin'): ?>
<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" action="members.php" onsubmit="return TrackUI.confirmForm(event, 'Register this member to the TrackCOOP system?', 'New Registry', 'primary', 'Register Now', 'Review')">
                <input type="hidden" name="action_type" value="add_member">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold m-0"><i class="bi bi-person-plus-fill me-2"></i> Add New Member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-muted">Personal Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <input type="text" name="fname" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" name="mname" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="lname" class="form-control" required>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-muted">Account Setup</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-muted">Cooperative Setup</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Agri Sector</label>
                            <select name="sector" class="form-select" required>
                                <option value="Rice">Rice</option>
                                <option value="Corn">Corn</option>
                                <option value="Fishery">Fishery</option>
                                <option value="Livestock">Livestock</option>
                                <option value="High Value Crops">High Value Crops</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">System Role</label>
                            <select name="role" class="form-select" required>
                                <option value="Member">Member</option>
                                <option value="Bookkeeper">Bookkeeper</option>
                                <option value="Admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Account Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Approved">Approved / Active</option>
                                <option value="Pending">Pending Approval</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 text-end pe-3 pb-2">
                        <button type="submit" class="btn px-4 py-2 fw-bold text-white" style="background: var(--track-green); border-radius: 50px; border: none; transition: all 0.3s ease; box-shadow: 0 8px 15px rgba(39, 174, 96, 0.2);" onmouseover="this.style.background='#1a8548'; this.style.boxShadow='0 12px 25px rgba(39, 174, 96, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#27ae60'; this.style.boxShadow='0 8px 15px rgba(39, 174, 96, 0.2)'; this.style.transform='translateY(0)';" >Save Registry Entry</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php echo isset($modalsHtml) ? $modalsHtml : ''; ?>

<script>
    // --- FUNCTIONAL PAGINATION ---
    let currentPage = 1;
    let rowsPerPage = parseInt(document.getElementById('rowsPerPage').value);
    
    function updatePagination() {
        const rows = Array.from(document.querySelectorAll('tbody tr:not(.no-results-row)'));
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
        const rows = document.querySelectorAll('tbody tr:not(.no-results-row)');
        if (currentPage < Math.ceil(rows.length / rowsPerPage)) {
            currentPage++;
            updatePagination();
        }
    });

    // Integrated Search + Pagination
    function performMemberSearch() {
        const term = document.getElementById('memberSearch').value.toLowerCase().trim();
        const allRows = document.querySelectorAll('tbody tr:not(.no-results-row)');
        let found = 0;

        allRows.forEach(row => {
            const name = row.querySelector('.member-name').textContent.toLowerCase();
            const username = row.querySelector('.member-user').textContent.toLowerCase();
            const sectorElement = row.querySelector('.fw-600.text-dark');
            const sector = sectorElement ? sectorElement.textContent.toLowerCase() : '';
            
            if (name.includes(term) || username.includes(term) || sector.includes(term)) {
                row.classList.remove('search-hidden');
                found++;
            } else {
                row.classList.add('search-hidden');
            }
        });

        const tbody = document.querySelector('tbody');
        let emptyRow = document.getElementById('noResultsRow');
        
        if (found === 0) {
            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.id = 'noResultsRow';
                emptyRow.className = 'no-results-row';
                emptyRow.innerHTML = `
                    <td colspan="5" class="text-center py-5">
                        <div class="opacity-25 mb-3"><i class="bi bi-person-search" style="font-size: 3rem;"></i></div>
                        <h5 class="fw-bold text-muted">No members found</h5>
                        <p class="text-muted small mb-0">We couldn't find any registry entry matching your search.</p>
                    </td>
                `;
                tbody.appendChild(emptyRow);
            }
        } else if (emptyRow) {
            emptyRow.remove();
        }

        currentPage = 1; 
        updatePaginationAfterSearch();
    }

    function updatePaginationAfterSearch() {
        const activeRows = Array.from(document.querySelectorAll('tbody tr:not(.no-results-row):not(.search-hidden)'));
        const hiddenBySearch = document.querySelectorAll('tbody tr.search-hidden');
        
        hiddenBySearch.forEach(r => r.style.display = 'none');

        const totalRows = activeRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage);
        
        const startIdx = (currentPage - 1) * rowsPerPage;
        const endIdx = startIdx + rowsPerPage;

        activeRows.forEach((row, index) => {
            if (index >= startIdx && index < endIdx) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        const startDisplay = totalRows === 0 ? 0 : startIdx + 1;
        const endDisplay = Math.min(endIdx, totalRows);
        const infoEl = document.getElementById('paginationInfo');
        if (infoEl) infoEl.textContent = `${startDisplay}-${endDisplay} of ${totalRows}`;

        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        if (prevBtn) prevBtn.disabled = (currentPage === 1);
        if (nextBtn) nextBtn.disabled = (currentPage === totalPages || totalPages === 0);
    }

    document.getElementById('memberSearch').addEventListener('input', performMemberSearch);
    
    // Initialize
    updatePagination();
</script>

    </div> <!-- .main-content-wrapper -->
</div> <!-- .sidebar-layout -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
