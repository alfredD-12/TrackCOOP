<?php
session_start();
include('../auth/db_connect.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

$user_id   = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
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

// Static sectors data for display
$static_sectors = [
    ['id' => 1, 'name' => 'Rice', 'description' => 'Rice farming and production', 'chairperson' => 'Juan Farmer', 'created_at' => '2024-01-15', 'member_count' => 15],
    ['id' => 2, 'name' => 'Corn', 'description' => 'Corn cultivation and trading', 'chairperson' => 'Maria Merchant', 'created_at' => '2024-02-10', 'member_count' => 12],
    ['id' => 3, 'name' => 'Fishery', 'description' => 'Fishing and aquaculture operations', 'chairperson' => 'Pedro Fish', 'created_at' => '2024-02-15', 'member_count' => 18],
    ['id' => 4, 'name' => 'Livestock', 'description' => 'Livestock raising and management', 'chairperson' => 'Rosa Rancher', 'created_at' => '2024-03-01', 'member_count' => 10],
    ['id' => 5, 'name' => 'High Value Crops', 'description' => 'High value crops farming and production', 'chairperson' => 'Miguel Farmer', 'created_at' => '2024-03-05', 'member_count' => 9],
];

// ── Handle Add / Edit / Delete Sector ───── (Demo Mode)
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_sector'])) {
        $msg = "added";
    } elseif (isset($_POST['edit_sector'])) {
        $msg = "edited";
    } elseif (isset($_POST['delete_sector'])) {
        $msg = "deleted";
    }
}

// ── Fetch Sectors with member count ─── (Using static data for demo)
$sectors_array = $static_sectors;

// ── Summary Stats ─── (Static for demo)
$total_sectors  = count($static_sectors);
$total_members  = 16; // Demo: 5+8+3+other
$active_sectors = count($static_sectors);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sector Management | TRACKCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)), url('../Home.jpeg') top center / 100% 100% no-repeat fixed;
            overflow-x: hidden;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .navbar {
            background-color: rgba(22, 74, 54, 0.95) !important;
            backdrop-filter: blur(10px);
            padding: 15px 0;
            border-bottom: 1px solid rgba(22, 74, 54, 0.3);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            animation: fadeInUpCustom 0.8s ease-out;
        }
        .navbar-brand { font-weight: 800; font-size: 1.5rem; letter-spacing: -0.8px; color: #ffffff !important; }
        .navbar-brand span { color: #20a060; }
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8) !important; font-weight: 600; font-size: 0.95rem;
            margin: 0 10px; padding: 8px 0 !important; position: relative;
            transition: var(--transition-smooth); display: flex; align-items: center; gap: 6px;
        }
        .navbar-nav .nav-link::after {
            content: ''; position: absolute; bottom: 0; left: 0;
            width: 0; height: 2px; background-color: var(--track-green); transition: width 0.3s ease;
        }
        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after { width: 100%; }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active { color: #20a060 !important; }
        .logout-btn {
            border: 2px solid #dc2626; background: #dc2626; color: white;
            width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 12px; transition: var(--transition-smooth); text-decoration: none;
        }
        .logout-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(220, 38, 38, 0.6); }

        /* ── Page Header ── */
        .page-header {
            background: transparent;
            padding: 60px 0 40px;
            border-bottom: none;
            margin-bottom: 40px; position: relative; overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16,1,0.3,1) both;
            color: #ffffff !important;
        }
        .page-header h1 { 
            color: #20a060 !important; 
            letter-spacing: -1.5px;
            font-weight: 800 !important;
        }
        .page-header p, .page-header .text-muted { 
            color: #ffffff !important; 
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }


        /* ── Stat Cards ── */
        .stat-card {
            border: 1px solid rgba(226,232,240,0.8); border-radius: 20px; background: #ffffff !important;
            padding: 24px; transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); position: relative; overflow: hidden; z-index: 1;
            opacity: 1 !important;
        }
        .stat-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(32,160,96,0.08); border-color: rgba(32,160,96,0.3); z-index: 2; }
        .icon-box {
            width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;
            border-radius: 14px; margin-bottom: 16px; transition: 0.3s;
        }
        .stat-card:hover .icon-box { transform: scale(1.1) rotate(5deg); }


        .sector-avatar {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, var(--track-green), #1a8548);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 1.1rem;
            box-shadow: 0 4px 10px rgba(32, 160, 96, 0.2);
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


        /* ── Action Buttons (Standardized) ── */
        .action-btn {
            display: inline-flex; width: 38px; height: 38px; align-items: center; justify-content: center;
            border-radius: 10px; color: #64748b; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); background: #f8fafc; border: 1px solid #e2e8f0;
            text-decoration: none; padding: 0; outline: none; font-size: 1rem; cursor: pointer;
        }
        
        .action-btn.view { color: #3b82f6; border-color: rgba(59, 130, 246, 0.3); background: rgba(59, 130, 246, 0.05); }
        .action-btn.view:hover { background: #3b82f6; color: white; border-color: #3b82f6; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }

        .action-btn.edit { color: #f59e0b; border-color: rgba(245, 158, 11, 0.3); background: rgba(245, 158, 11, 0.05); }
        .action-btn.edit:hover { background: #f59e0b; color: white; border-color: #f59e0b; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }

        .action-btn.delete { color: #ef4444; border-color: rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.05); }
        .action-btn.delete:hover { background: #ef4444; color: white; border-color: #ef4444; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }

        /* ── Modal ── */
        .modal-content { border-radius: 30px !important; border: none; box-shadow: 0 25px 60px rgba(0,0,0,0.15); overflow: hidden !important; }
        .modal-header { background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22, 74, 54, 0.3); padding: 24px 28px; color: white; }
        .modal-title { font-weight: 800; font-size: 1.3rem; letter-spacing: -0.5px; color: white; display: flex; align-items: center; gap: 10px; }
        .modal-title i { color: var(--track-green); }
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
        .form-label { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .form-control, .form-select {
            border-radius: 12px; padding: 12px 16px; border: 1.5px solid #e5e5c0;
            background-color: #fdfdf8; transition: 0.3s; font-size: 0.95rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--track-green); box-shadow: 0 0 0 4px rgba(32,160,96,0.12); background: #fff;
        }
        .btn-track { background: #20a060; color: white; border: none; border-radius: 50px; padding: 12px 28px; font-weight: 700; transition: var(--transition-smooth); box-shadow: 0 4px 14px rgba(32, 160, 96, 0.3); }
        .btn-track:hover { background: #1a8548; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(32, 160, 96, 0.4); color: white; }
        .btn-cancel { background: #ef4444; color: white; border: none; border-radius: 50px; padding: 12px 24px; font-weight: 600; transition: 0.3s; }
        .btn-cancel:hover { background: #dc2626; color: white; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3); }
        .btn-danger-soft { background: #fee2e2; color: #ef4444; border: none; border-radius: 12px; padding: 12px 24px; font-weight: 700; transition: 0.3s; }
        .btn-danger-soft:hover { background: #ef4444; color: white; box-shadow: 0 4px 12px rgba(239,68,68,0.3); }

        /* ── Sector name badge in table ── */
        .sector-badge { background: var(--track-beige); color: var(--track-dark); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; border: 1px solid rgba(229,229,192,0.8); }

        /* ── Stagger ── */
        .fade-in-up { opacity: 0; animation: fadeInUpCustom 0.8s cubic-bezier(0.16,1,0.3,1) forwards; }
        .delay-1 { animation-delay: 0.1s; } .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; } .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        /* ── Portal Button ── */
        .btn-portal {
            background: #20a060; color: white; border-radius: 50px; padding: 12px 24px;
            font-weight: 700; border: none; box-shadow: 0 8px 20px rgba(32, 160, 96, 0.2);
            transition: var(--transition-smooth); display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-portal:hover { transform: translateY(-3px); background: #1a8548; box-shadow: 0 12px 25px rgba(32, 160, 96, 0.3); color: white; }

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



        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 4rem; opacity: 0.1; display: block; margin-bottom: 16px; color: var(--track-green); }
    </style>
</head>
<div class="sidebar-layout">
    <?php 
        $active_page = 'sectors';
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
                <input type="text" id="sectorSearch" class="form-control border-0 shadow-sm" placeholder="Search" style="background: #f1f5f9; border-radius: 10px; padding-left: 45px !important;">
            </div>
            
            <?php if ($user_role === 'Admin'): ?>
            <button class="btn btn-upload-gold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addSectorModal" style="height: 50px; padding: 0 25px !important; border-radius: 12px !important; font-weight: 700;">
                <i class="bi bi-plus-circle"></i> Add Sector
            </button>
            <?php endif; ?>
            
            <!-- Notification bell removed by user request -->
        </div>
    </div>
</div>

<div class="container-fluid px-4 pb-5">

    <!-- Flash Messages -->
    <?php if ($msg === 'added'): ?>
        <div class="alert alert-success fw-bold rounded-4 mb-4"><i class="bi bi-check-circle-fill me-2"></i> Sector added successfully.</div>
    <?php elseif ($msg === 'edited'): ?>
        <div class="alert alert-success fw-bold rounded-4 mb-4"><i class="bi bi-check-circle-fill me-2"></i> Sector updated successfully.</div>
    <?php elseif ($msg === 'deleted'): ?>
        <div class="alert alert-warning fw-bold rounded-4 mb-4"><i class="bi bi-trash-fill me-2"></i> Sector deleted.</div>
    <?php elseif ($msg === 'error'): ?>
        <div class="alert alert-danger fw-bold rounded-4 mb-4"><i class="bi bi-exclamation-octagon-fill me-2"></i> An error occurred. Please try again.</div>
    <?php elseif ($msg === 'invalid'): ?>
        <div class="alert alert-warning fw-bold rounded-4 mb-4"><i class="bi bi-exclamation-triangle-fill me-2"></i> Sector name is required.</div>
    <?php endif; ?>



    <div class="table-card fade-in-up table-responsive" style="overflow-x: auto; padding: 2px;">
        <?php if (count($sectors_array) > 0): ?>
        <table class="table table-elite align-middle">
            <thead>
                <tr>
                    <th style="min-width: 200px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            SECTOR NAME
                        </div>
                    </th>
                    <th style="min-width: 180px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            CHAIRPERSON
                        </div>
                    </th>
                    <th style="min-width: 150px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            ESTABLISHED
                        </div>
                    </th>
                    <th style="min-width: 120px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            MEMBERS
                        </div>
                    </th>
                    <th class="text-end"></th>
                </tr>
            </thead>
            <tbody style="font-size: 0.95rem;" id="sectorTable">
                <?php foreach ($sectors_array as $row): ?>
                <tr style="cursor: default;">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="user-table-avatar-initials me-3" style="background: var(--track-green); color: white;">
                                <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                            </div>
                            <div class="user-table-info">
                                <span class="user-table-name sector-title"><?php echo htmlspecialchars($row['name']); ?></span>
                                <span class="user-table-email sector-desc"><?php echo htmlspecialchars($row['description']); ?></span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-600 text-dark sector-chair">
                            <?php echo $row['chairperson'] ? htmlspecialchars($row['chairperson']) : '<em class="text-muted">No Chairperson</em>'; ?>
                        </div>
                    </td>
                    <td class="text-muted small fw-600">
                        <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                    </td>
                    <td>
                        <span class="badge-status badge-signed">
                            <i class="bi bi-people-fill me-1"></i><?php echo $row['member_count']; ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end align-items-center">
                            <button class="btn-doc-action btn-action-view" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                            <?php if ($user_role === 'Admin'): ?>
                                <button class="btn-doc-action btn-action-edit" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn-doc-action btn-action-delete" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
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
        <div class="empty-state">
            <i class="bi bi-diagram-3"></i>
            <h5 class="fw-700 mb-2">No Sectors Yet</h5>
            <p class="text-muted mb-0">Add your first cooperative sector to get started.</p>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- ADD SECTOR MODAL -->
<div class="modal fade" id="addSectorModal" tabindex="-1" aria-labelledby="addSectorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSectorLabel">
                    <i class="bi bi-plus-circle"></i> ADD NEW SECTOR
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" onsubmit="return TrackUI.confirmForm(event, 'Register this new sector to the cooperative organization?', 'New Registry', 'primary', 'Record Now', 'Back')">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Sector Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Rice Sector" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Chairperson</label>
                            <input type="text" name="chairperson" class="form-control" placeholder="e.g. Juan Dela Cruz">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Brief description of this sector..."></textarea>
                        </div>
                    </div>
                    <div class="mt-4 text-end pe-3 pb-2">
                        <button type="button" class="btn fw-bold me-2" style="height: 50px; padding: 0 30px !important; border-radius: 50px !important; background: #ef4444; color: white; border: none; font-weight: 700;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_sector" class="btn-track" style="height: 50px; padding: 0 35px !important; border-radius: 50px !important; font-weight: 700; border: none;">Save Sector Registry</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT SECTOR MODAL -->
<div class="modal fade" id="editSectorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square"></i> EDIT SECTOR
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" onsubmit="return TrackUI.confirmForm(event, 'Save changes to this sector record?', 'Update Sector', 'primary', 'Publish Now', 'Review')">
                <input type="hidden" name="sector_id" id="editSectorId">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Sector Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Chairperson</label>
                            <input type="text" name="chairperson" id="editChairperson" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="text-end mt-4 pe-2">
                        <button type="button" class="btn fw-bold me-2" style="height: 50px; padding: 0 30px !important; border-radius: 50px !important; background: #ef4444; color: white; border: none; font-weight: 700;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="edit_sector" class="btn-track" style="height: 50px; padding: 0 35px !important; border-radius: 50px !important; font-weight: 700; border: none;"><i class="bi bi-check-circle me-2"></i>Update Sector</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DELETE CONFIRM MODAL -->
<div class="modal fade" id="deleteSectorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill" style="color: #ef4444;"></i> DELETE SECTOR
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="sector_id" id="deleteSectorId">
                <div class="modal-body">
                    <p class="mb-0 text-center py-3">Are you sure you want to delete the sector <strong id="deleteSectorName"></strong>? This will <strong>not</strong> remove members — only the sector record.</p>
                    <div class="text-end mt-4">
                        <button type="button" class="btn fw-bold me-2" style="height: 50px; padding: 0 30px !important; border-radius: 50px !important; background: #ef4444; color: white; border: none; font-weight: 700;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="delete_sector" class="btn fw-bold" style="height: 50px; padding: 0 35px !important; border-radius: 50px !important; background: #ef4444; color: white; border: none; font-weight: 700;"><i class="bi bi-trash me-2"></i>Delete Sector</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic' });

    function openEdit(id, name, description, chairperson) {
        document.getElementById('editSectorId').value    = id;
        document.getElementById('editName').value        = name;
        document.getElementById('editDescription').value = description;
        document.getElementById('editChairperson').value = chairperson;
    }

    function confirmDelete(id, name) {
        document.getElementById('deleteSectorId').value = id;
        document.getElementById('deleteSectorName').textContent = '"' + name + '"';
    }

    // --- FUNCTIONAL PAGINATION & SEARCH LOGIC ---
    let currentPage = 1;
    let rowsPerPage = 5;

    function updateTablePagination() {
        const term = document.getElementById('sectorSearch').value.toLowerCase().trim();
        const allRows = Array.from(document.querySelectorAll('#sectorTable tr:not(#noSectorResults)'));
        
        // Filter rows based on search
        const filteredRows = allRows.filter(row => {
            const titleEl = row.querySelector('.sector-title');
            const descEl = row.querySelector('.sector-desc');
            const chairEl = row.querySelector('.sector-chair');
            
            const title = titleEl ? titleEl.textContent.toLowerCase() : '';
            const desc = descEl ? descEl.textContent.toLowerCase() : '';
            const chair = chairEl ? chairEl.textContent.toLowerCase() : '';
            
            return title.includes(term) || desc.includes(term) || chair.includes(term);
        });

        const totalItems = filteredRows.length;
        const totalPages = Math.ceil(totalItems / rowsPerPage);
        
        // Fix currentPage if it's out of bounds
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * rowsPerPage;
        const end = Math.min(start + rowsPerPage, totalItems);

        // Hide all rows first
        allRows.forEach(row => row.style.display = 'none');

        // Show rows for current page
        filteredRows.forEach((row, index) => {
            if (index >= start && index < end) {
                row.style.display = '';
                row.style.animation = 'scaleIn 0.3s ease forwards';
            }
        });

        // Update Pagination Info
        const infoEl = document.getElementById('paginationInfo');
        if (totalItems === 0) {
            infoEl.textContent = '0-0 of 0';
            
            // Show custom empty result if searching
            const tbody = document.getElementById('sectorTable');
            let noResults = document.getElementById('noSectorResults');
            if (term !== "" && !noResults) {
                noResults = document.createElement('tr');
                noResults.id = 'noSectorResults';
                noResults.innerHTML = `
                    <td colspan="5" class="py-5 text-center">
                        <div class="opacity-25 mb-3"><i class="bi bi-diagram-3" style="font-size: 3rem;"></i></div>
                        <h5 class="fw-bold text-muted">No sectors found</h5>
                        <p class="text-muted small mb-0">We couldn't find any sector matching matching your search.</p>
                    </td>
                `;
                tbody.appendChild(noResults);
            }
        } else {
            infoEl.textContent = `${start + 1}-${end} of ${totalItems}`;
            const noResults = document.getElementById('noSectorResults');
            if (noResults) noResults.remove();
        }

        // Enable/Disable Buttons
        document.getElementById('prevPage').disabled = (currentPage === 1);
        document.getElementById('nextPage').disabled = (currentPage === totalPages || totalPages === 0);
        document.getElementById('prevPage').style.opacity = (currentPage === 1) ? '0.5' : '1';
        document.getElementById('nextPage').style.opacity = (currentPage === totalPages || totalPages === 0) ? '0.5' : '1';
    }

    // Attach Listeners
    document.getElementById('rowsPerPage').addEventListener('change', function() {
        rowsPerPage = parseInt(this.value);
        currentPage = 1;
        updateTablePagination();
    });

    document.getElementById('prevPage').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            updateTablePagination();
        }
    });

    document.getElementById('nextPage').addEventListener('click', () => {
        const term = document.getElementById('sectorSearch').value.toLowerCase().trim();
        const allRows = document.querySelectorAll('#sectorTable tr:not(#noSectorResults)');
        const filteredCount = Array.from(allRows).filter(row => {
            const title = row.querySelector('.sector-title')?.textContent.toLowerCase() || '';
            const desc = row.querySelector('.sector-desc')?.textContent.toLowerCase() || '';
            const chair = row.querySelector('.sector-chair')?.textContent.toLowerCase() || '';
            return title.includes(term) || desc.includes(term) || chair.includes(term);
        }).length;

        if (currentPage < Math.ceil(filteredCount / rowsPerPage)) {
            currentPage++;
            updateTablePagination();
        }
    });

    document.getElementById('sectorSearch').addEventListener('input', () => {
        currentPage = 1;
        updateTablePagination();
    });

    // Initial load
    updateTablePagination();
</script>
    </div> <!-- .main-content-wrapper -->
</div> <!-- .sidebar-layout -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
