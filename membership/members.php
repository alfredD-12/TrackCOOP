<?php
session_start();
include('../auth/db_connect.php');

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Bookkeeper')) {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$full_name = "User";

$query = "SELECT first_name, last_name FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($user = $result->fetch_assoc()) {
    $full_name = $user['first_name'] . " " . $user['last_name'];
}

$error_msg = "";
$success_msg = "";

// Handle POST Requests (Add / Edit)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action_type = isset($_POST['action_type']) ? $_POST['action_type'] : '';
    
    if ($action_type === 'add_member' && $user_role === 'Admin') {
        $fname  = trim($_POST['fname']);
        $mname  = trim($_POST['mname']);
        $lname  = trim($_POST['lname']);
        $user_name = trim($_POST['username']);
        $pass   = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sector = trim($_POST['sector']);
        $role_input = trim($_POST['role']);
        $status = trim($_POST['status']);

        $chk = "SELECT id FROM users WHERE username = ?";
        $chk_stmt = $conn->prepare($chk);
        $chk_stmt->bind_param("s", $user_name);
        $chk_stmt->execute();
        if ($chk_stmt->get_result()->num_rows > 0) {
            $error_msg = "Username already exists. Please choose a different one.";
        } else {
            $sql = "INSERT INTO users (first_name, middle_name, last_name, username, password, sector, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $ins_stmt = $conn->prepare($sql);
            $ins_stmt->bind_param("ssssssss", $fname, $mname, $lname, $user_name, $pass, $sector, $role_input, $status);
            if ($ins_stmt->execute()) {
                $success_msg = "New cooperative member added successfully.";
            } else {
                $error_msg = "Error adding member. Please try again.";
            }
        }
    } elseif ($action_type === 'edit_member' && $user_role === 'Admin') {
        $target_id = intval($_POST['target_id']);
        $fname  = trim($_POST['fname']);
        $mname  = trim($_POST['mname']);
        $lname  = trim($_POST['lname']);
        $sector = trim($_POST['sector']);
        $role_input = trim($_POST['role']);
        $status = trim($_POST['status']);
        
        $upd = "UPDATE users SET first_name=?, middle_name=?, last_name=?, sector=?, role=?, status=? WHERE id=?";
        $stmt_upd = $conn->prepare($upd);
        $stmt_upd->bind_param("ssssssi", $fname, $mname, $lname, $sector, $role_input, $status, $target_id);
        
        if ($stmt_upd->execute()) {
            $success_msg = "Member details updated successfully.";
        } else {
            $error_msg = "Error updating member details.";
        }
    }
}

// Handle GET Actions (Approve / Deactivate / Delete)
if (isset($_GET['action']) && isset($_GET['id']) && $user_role === 'Admin') {
    $action_get = $_GET['action'];
    $target_id = intval($_GET['id']);
    
    if (in_array($action_get, ['approve', 'deactivate', 'delete'])) {
        if ($action_get === 'approve') {
            $update_status = "Approved";
        } elseif ($action_get === 'deactivate') {
            $update_status = "Inactive";
        } elseif ($action_get === 'delete') {
            $del_query = "DELETE FROM users WHERE id = ?";
            $del_stmt = $conn->prepare($del_query);
            $del_stmt->bind_param("i", $target_id);
            if($del_stmt->execute()) {
                 header("Location: members.php?msg=deleted");
                 exit();
            }
        }
        
        if(isset($update_status)) {
            $upd_query = "UPDATE users SET status = ? WHERE id = ?";
            $upd_stmt = $conn->prepare($upd_query);
            $upd_stmt->bind_param("si", $update_status, $target_id);
            $upd_stmt->execute();
            header("Location: members.php?msg=" . $action_get);
            exit();
        }
    }
}

// Fetch all members
$members_query = "SELECT * FROM users ORDER BY id DESC";
$members_result = $conn->query($members_query);
$all_members = [];
if ($members_result) {
    while($row = $members_result->fetch_assoc()) {
        $all_members[] = $row;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Management | TrackCOOP</title>
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
        }

        .logout-btn:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220, 38, 38, 0.4);
        }

        .navbar {
            background-color: rgba(245, 245, 220, 0.9) !important;
            backdrop-filter: blur(10px);
            padding: 15px 0;
            border-bottom: 1px solid rgba(229, 229, 192, 0.5);
            animation: fadeInUpCustom 0.8s ease-out;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.8px;
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
        .navbar-nav .nav-link.active::after { width: 100%; }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active { color: var(--track-dark) !important; background: transparent !important; }

        .admin-header {
            background: linear-gradient(135deg, var(--track-bg) 0%, var(--track-beige) 100%);
            padding: 60px 0 40px;
            border-bottom: 1px solid rgba(229, 229, 192, 0.4);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
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

        .table-card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 20px;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            animation: fadeInUpCustom 0.8s ease-out 0.4s both;
            padding: 24px; 
        }
        
        .badge-status { padding: 4px 12px; border-radius: 50px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-approved { background: #dcfce7; color: #166534; }
        .badge-pending { background: #fef9c3; color: #854d0e; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }

        .action-btn-outline {
            display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center;
            border-radius: 6px; color: #64748b; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); background: transparent; border: 1px solid #e2e8f0;
            text-decoration: none; padding: 0; outline: none;
        }
        .action-btn-outline:hover { transform: translateY(-3px); color: var(--track-green); border-color: var(--track-green); box-shadow: 0 4px 12px rgba(32, 160, 96, 0.15); }
        .action-btn-outline.delete:hover { color: #ef4444; border-color: #ef4444; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15); }
        .action-btn-outline.approve:hover { color: #27ae60; border-color: #27ae60; box-shadow: 0 4px 12px rgba(39, 174, 96, 0.15); }
        
        .table-hover tbody tr { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .table-hover tbody tr:hover { transform: translateY(-2px) scale(1.002); box-shadow: 0 8px 15px rgba(0,0,0,0.04); z-index: 10; position: relative; }
        .table-hover tbody tr:hover td { background-color: #fff !important; }
        
        .form-label { font-size: 0.95rem; font-weight: 600; color: var(--track-dark); margin-bottom: 8px; }
        .form-control, .form-select { border-radius: 8px; padding: 10px 14px; border: 2px solid #EAE0C8; background-color: #fff; transition: 0.3s; }
        .form-control:focus, .form-select:focus { border-color: var(--track-green); box-shadow: 0 0 0 3px rgba(32, 160, 96, 0.1); }
        .modal-header-beige { background-color: var(--track-beige); border-bottom: none; padding: 20px 30px; border-radius: 20px 20px 0 0; }
        .modal-footer-beige { background-color: var(--track-beige); border-top: none; padding: 20px 30px; border-radius: 0 0 20px 20px; }

        /* Modal Customizations */
        .modal-content { border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .modal-header { border-bottom: 1px solid #f1f5f9; padding: 20px 30px; border-radius: 20px 20px 0 0; }
        .modal-footer { border-top: 1px solid #f1f5f9; padding: 20px 30px; border-radius: 0 0 20px 20px; }
        .modal-body { padding: 30px; }

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
            border: 2px solid var(--track-beige);
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

<?php 
    $active_page = 'members';
    $membership_type = $user_role;
    include('../includes/dashboard_navbar.php'); 
?>

<div class="admin-header">
    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="badge-platform">
                    <span class="spinner-grow spinner-grow-sm me-2 text-success" role="status" style="width: 10px; height: 10px;"></span>
                    System Live
                </div>
                <h1 class="fw-800 display-4 mb-3">Cooperative Members</h1>
                <p class="fs-5 mb-0 text-muted">View and manage all registered NFFAC members across sectors.</p>
            </div>
            <?php if($user_role === 'Admin'): ?>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <button type="button" class="btn btn-success px-4 py-3 fw-bold shadow-sm" style="border-radius: 12px; border: none; background: var(--track-green);" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="bi bi-person-plus-fill me-2"></i> Add New Member
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container pb-5">
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

    <!-- SEARCH TOOLBAR -->
    <div class="search-wrapper">
        <div class="search-input-group">
            <i class="bi bi-search"></i>
            <input type="text" id="memberSearch" class="search-input" placeholder="Search members by name or username...">
        </div>
    </div>

    <div class="table-card table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="text-uppercase" style="font-size: 0.7rem; font-weight: 800; color: #475569; letter-spacing: 1px;">
                <tr>
                    <th class="border-bottom pb-3">NAME</th>
                    <th class="border-bottom pb-3">SECTOR</th>
                    <th class="border-bottom pb-3">ROLE</th>
                    <th class="border-bottom pb-3">STATUS</th>
                    <th class="border-bottom pb-3 text-end">ACTIONS</th>
                </tr>
            </thead>
            <tbody style="font-size: 0.95rem;">
                <?php $modalsHtml = ''; ?>
                <?php foreach($all_members as $row): ?>
                <tr style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#viewMemberModal<?php echo $row['id']; ?>">
                    <td class="py-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white fw-bold me-3 d-flex align-items-center justify-content-center" style="width:44px;height:44px;border-radius:8px;font-size:1.1rem;">
                                <?php echo strtoupper(substr($row['first_name'],0,1) . substr($row['last_name'],0,1)); ?>
                            </div>
                            <div>
                                <div class="fw-semibold member-name" style="color:#1e293b; font-size:0.95rem; margin-bottom: 2px;">
                                    <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                                </div>
                                <div class="text-muted member-user" style="color:#64748b; font-size:0.8rem;">@<?php echo htmlspecialchars($row['username']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 align-middle"><span class="badge border" style="background:#fff; color:#475569; font-weight:600; padding:4px 8px;"><?php echo htmlspecialchars($row['sector']); ?> Sector</span></td>
                    <td class="py-3 align-middle" style="color:#475569;"><?php echo htmlspecialchars($row['role']); ?></td>
                    <td class="py-3 align-middle">
                        <?php 
                            if($row['status'] === 'Approved') echo '<span class="badge-status badge-approved">Approved</span>';
                            elseif($row['status'] === 'Pending') echo '<span class="badge-status badge-pending">Pending</span>';
                            else echo '<span class="badge-status badge-inactive">Inactive</span>';
                        ?>
                    </td>
                    <td class="py-3 text-end align-middle" onclick="event.stopPropagation();">
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="action-btn-outline" title="View Profile" data-bs-toggle="modal" data-bs-target="#viewMemberModal<?php echo $row['id']; ?>"><i class="bi bi-eye"></i></button>
                            
                            <?php if($user_role === 'Admin'): ?>
                                <button type="button" class="action-btn-outline" title="Edit" data-bs-toggle="modal" data-bs-target="#editMemberModal<?php echo $row['id']; ?>"><i class="bi bi-pencil"></i></button>
                                <?php if($row['status'] === 'Pending'): ?>
                                    <a href="members.php?action=approve&id=<?php echo $row['id']; ?>" class="action-btn-outline approve" title="Approve" onclick="return TrackUI.confirmLink(event, 'Approve this member for full cooperative access?', 'Membership Approval', 'primary', 'Approve Now', 'Review Later')"><i class="bi bi-check-lg"></i></a>
                                <?php endif; ?>
                                <a href="members.php?action=delete&id=<?php echo $row['id']; ?>" class="action-btn-outline delete" title="Delete" onclick="return TrackUI.confirmLink(event, 'Remove this member from the registry? This action is permanent.', 'Permanent Delete', 'danger', 'Yes, Delete', 'Keep Member')"><i class="bi bi-trash"></i></a>
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
                                    <button type="button" class="btn bg-white px-4 fw-bold" style="border: 1px solid #e2e8f0; color: var(--track-dark); border-radius: 8px;" data-bs-dismiss="modal">Close</button>
                                    <?php if($user_role === 'Admin'): ?>
                                    <button type="button" class="btn px-4 fw-bold text-white shadow-sm" style="background: var(--track-green); border-radius: 8px;" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editMemberModal<?php echo $row['id']; ?>">Edit Details</button>
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
                                    <button type="button" class="btn bg-white px-4 fw-bold" style="border: 1px solid #e2e8f0; color: var(--track-dark); border-radius: 8px;" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn px-4 fw-bold text-white shadow-sm" style="background: var(--track-green); border-radius: 8px;">Save Changes</button>
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
    </div>
</div>

<?php if($user_role === 'Admin'): ?>
<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" action="members.php" onsubmit="return TrackUI.confirmForm(event, 'Register this member to the TrackCOOP system?', 'New Registry', 'primary', 'Register Now', 'Review')">
                <input type="hidden" name="action_type" value="add_member">
                <div class="modal-header-beige d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold m-0" style="color: var(--track-dark);"><i class="bi bi-pencil-square text-success me-2"></i> Add New Member</h5>
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
                </div>
                <div class="modal-footer-beige d-flex justify-content-end gap-2">
                    <button type="button" class="btn bg-white px-4 fw-bold" style="border: 1px solid #e2e8f0; color: var(--track-dark); border-radius: 8px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn px-4 fw-bold text-white shadow-sm" style="background: var(--track-green); border-radius: 8px;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php echo isset($modalsHtml) ? $modalsHtml : ''; ?>

<script src="../includes/footer-logic.js"></script>
<script>
    // --- REAL-TIME MEMBER SEARCH ---
    function performMemberSearch() {
        const term = document.getElementById('memberSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('tbody tr:not(.no-results-row)');
        let found = 0;

        rows.forEach(row => {
            const name = row.querySelector('.member-name').textContent.toLowerCase();
            const username = row.querySelector('.member-user').textContent.toLowerCase();
            const sector = row.querySelector('.badge').textContent.toLowerCase();
            
            if (name.includes(term) || username.includes(term) || sector.includes(term)) {
                row.style.display = '';
                row.style.animation = 'fadeInUpCustom 0.4s ease forwards';
                found++;
            } else {
                row.style.display = 'none';
            }
        });

        // Handle Empty State
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
        } else {
            if (emptyRow) emptyRow.remove();
        }
    }

    // Trigger on input (real-time)
    document.getElementById('memberSearch').addEventListener('input', performMemberSearch);
    
    // Trigger on Enter key
    document.getElementById('memberSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') performMemberSearch();
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include('../includes/footer.php'); ?>
<script>
    AOS.init({ once: true, duration: 800 });
</script>
</body>
</html>
