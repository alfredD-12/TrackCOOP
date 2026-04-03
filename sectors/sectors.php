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
@$q = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
if ($q) {
    @$q->bind_param("i", $user_id);
    @$q->execute();
    @$result = $q->get_result();
    if ($u = @$result->fetch_assoc()) {
        $full_name = $u['first_name'] . " " . $u['last_name'];
    }
}

// Static sectors data for display
$static_sectors = [
    ['id' => 1, 'name' => 'Rice', 'description' => 'Rice farming and production', 'chairperson' => 'Juan Farmer', 'created_at' => '2024-01-15', 'member_count' => 5],
    ['id' => 2, 'name' => 'Corn', 'description' => 'Corn cultivation and trading', 'chairperson' => 'Maria Merchant', 'created_at' => '2024-02-10', 'member_count' => 4],
    ['id' => 3, 'name' => 'Fishery', 'description' => 'Fishing and aquaculture operations', 'chairperson' => 'Pedro Fish', 'created_at' => '2024-02-15', 'member_count' => 3],
    ['id' => 4, 'name' => 'Livestock', 'description' => 'Livestock raising and management', 'chairperson' => 'Rosa Rancher', 'created_at' => '2024-03-01', 'member_count' => 2],
    ['id' => 5, 'name' => 'High Value Crops', 'description' => 'High value crops production', 'chairperson' => 'Miguel Farmer', 'created_at' => '2024-03-05', 'member_count' => 2],
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
    <title>Sector Management | TrackCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
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
        * { box-sizing: border-box; }

        @keyframes fadeInUpCustom {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        body {
            background-color: var(--track-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            display: flex; flex-direction: column;
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
            background: linear-gradient(135deg, var(--track-bg) 0%, var(--track-beige) 100%);
            padding: 60px 0 40px;
            border-bottom: 1px solid rgba(229,229,192,0.4);
            margin-bottom: 40px; position: relative; overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16,1,0.3,1) both;
        }
        .page-header::after {
            content: ''; position: absolute; top: -20%; right: -5%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(32,160,96,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .badge-platform {
            background: white; color: var(--track-green); font-weight: 700; padding: 6px 14px;
            border-radius: 50px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
            display: inline-flex; align-items: center; margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(32,160,96,0.1); border: 1px solid rgba(32,160,96,0.2);
        }

        /* ── Stat Cards ── */
        .stat-card {
            border: 1px solid rgba(226,232,240,0.8); border-radius: 20px; background: white;
            padding: 24px; transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); position: relative; overflow: hidden; z-index: 1;
        }
        .stat-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(32,160,96,0.08); border-color: rgba(32,160,96,0.3); z-index: 2; }
        .icon-box {
            width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;
            border-radius: 14px; margin-bottom: 16px; transition: 0.3s;
        }
        .stat-card:hover .icon-box { transform: scale(1.1) rotate(5deg); }

        /* ── Sector Cards Grid ── */
        .sector-card {
            border: 1px solid rgba(226,232,240,0.8); border-radius: 20px; background: white;
            padding: 24px; transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); position: relative; overflow: hidden;
            cursor: pointer; text-decoration: none; color: inherit; display: block;
        }
        .sector-card:hover {
            transform: translateY(-6px); box-shadow: 0 20px 40px rgba(32,160,96,0.1);
            border-color: rgba(32,160,96,0.4); color: inherit;
        }
        .sector-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, var(--track-green), #1a8548);
            border-radius: 20px 20px 0 0;
        }
        .sector-icon {
            width: 56px; height: 56px; border-radius: 16px;
            background: linear-gradient(135deg, var(--track-green), #1a8548);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1.4rem; margin-bottom: 16px;
            transition: 0.3s;
        }
        .sector-card:hover .sector-icon { transform: scale(1.1) rotate(5deg); }
        .member-count-badge {
            background: var(--track-green-light); color: var(--track-green);
            padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;
        }

        /* ── Table Card ── */
        .table-card {
            border: 1px solid rgba(226,232,240,0.8); border-radius: 20px; background: white;
            padding: 28px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);
        }

        /* ── Action Buttons ── */
        .action-icon {
            display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center;
            border-radius: 8px; color: var(--text-muted); transition: 0.3s;
            background: #f8fafc; border: 1px solid #e2e8f0; text-decoration: none; cursor: pointer;
        }
        .action-icon:hover.edit  { background: #3b82f6; color: white; border-color: #3b82f6; }
        .action-icon:hover.del   { background: #ef4444; color: white; border-color: #ef4444; }
        .action-icon:hover.view  { background: var(--track-green); color: white; border-color: var(--track-green); }

        /* ── Modal ── */
        .modal-content { border: none; border-radius: 20px; box-shadow: 0 25px 60px rgba(0,0,0,0.15); }
        .modal-header { background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22, 74, 54, 0.3); border-radius: 20px 20px 0 0; padding: 24px 28px; color: white; }
        .modal-title { font-weight: 800; font-size: 1.3rem; letter-spacing: -0.5px; color: white; display: flex; align-items: center; gap: 10px; }
        .modal-title i { color: var(--track-green); }
        .modal-body { padding: 28px; }
        .modal-footer { background: rgba(22, 74, 54, 0.95); border-top: 1px solid rgba(22, 74, 54, 0.3); border-radius: 0 0 20px 20px; padding: 20px 28px; color: white; }
        .form-label { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .form-control, .form-select {
            border-radius: 12px; padding: 12px 16px; border: 1.5px solid #e5e5c0;
            background-color: #fdfdf8; transition: 0.3s; font-size: 0.95rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--track-green); box-shadow: 0 0 0 4px rgba(32,160,96,0.12); background: #fff;
        }
        .btn-track { background: var(--track-green); color: white; border: none; border-radius: 12px; padding: 12px 28px; font-weight: 700; transition: var(--transition-smooth); }
        .btn-track:hover { background: #20a060; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(32,160,96,0.3); color: white; }
        .btn-cancel { background: #206970; color: white; border: none; border-radius: 12px; padding: 12px 24px; font-weight: 600; transition: 0.3s; }
        .btn-cancel:hover { background: #20a060; color: white; transform: translateY(-2px); }
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
            background: var(--track-green); color: white; border-radius: 12px; padding: 12px 24px;
            font-weight: 700; border: none; box-shadow: 0 8px 20px rgba(32,160,96,0.2);
            transition: var(--transition-smooth); display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-portal:hover { transform: translateY(-3px); background: #20a060; box-shadow: 0 12px 25px rgba(32,160,96,0.3); color: white; }

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


        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 60px 20px; }
        .empty-state i { font-size: 4rem; opacity: 0.1; display: block; margin-bottom: 16px; }
    </style>
    <link rel="stylesheet" href="../includes/footer.css">
</head>
<body>

<?php 
    $active_page = 'sectors';
    $membership_type = $user_role;
    include('../includes/dashboard_navbar.php'); 
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container position-relative" style="z-index:1;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="badge-platform">
                    <span class="spinner-grow spinner-grow-sm me-2 text-success" role="status" style="width:10px;height:10px;"></span>
                    System Live
                </div>
                <h1 class="fw-800 display-5 mb-2" style="letter-spacing:-1.5px;">Sector Management</h1>
                <p class="fs-6 mb-0 text-muted">Manage cooperative sectors and view their member distribution.</p>
            </div>
            <?php if ($user_role === 'Admin'): ?>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button class="btn-portal" data-bs-toggle="modal" data-bs-target="#addSectorModal">
                    <i class="bi bi-plus-circle-fill"></i> Add Sector
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container pb-5">

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

    <!-- STAT CARDS -->
    <div class="row g-4 mb-5">
        <div class="col-md-4 fade-in-up delay-1">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-diagram-3-fill fs-4"></i></div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-bold" style="font-size:0.7rem;">Total</span>
                </div>
                <h6 class="text-uppercase fw-bold small mb-1 text-muted" style="letter-spacing:0.5px;">Total Sectors</h6>
                <h2 class="fw-800 mb-0 text-dark"><?php echo number_format($total_sectors); ?></h2>
            </div>
        </div>
        <div class="col-md-4 fade-in-up delay-2">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-people-fill fs-4"></i></div>
                </div>
                <h6 class="text-uppercase fw-bold small mb-1 text-muted" style="letter-spacing:0.5px;">Total Members</h6>
                <h2 class="fw-800 mb-0 text-dark"><?php echo number_format($total_members); ?></h2>
            </div>
        </div>
        <div class="col-md-4 fade-in-up delay-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="bi bi-activity fs-4"></i></div>
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill fw-bold" style="font-size:0.7rem;">Active</span>
                </div>
                <h6 class="text-uppercase fw-bold small mb-1 text-muted" style="letter-spacing:0.5px;">Active Sectors</h6>
                <h2 class="fw-800 mb-0 text-dark"><?php echo number_format($active_sectors); ?></h2>
            </div>
        </div>
    </div>

    <!-- SEARCH TOOLBAR -->
    <div class="search-wrapper">
        <div class="search-input-group">
            <i class="bi bi-search"></i>
            <input type="text" id="sectorSearch" class="search-input" placeholder="Search sectors by name, description, or chair...">
        </div>
    </div>

    <!-- SECTOR CARDS GRID -->
    <?php if (count($sectors_array) > 0):
        $sector_rows = $sectors_array;
    ?>
    <div class="row g-4 mb-5" id="sectorGrid">
        <?php foreach ($sector_rows as $i => $s):
            $delay = ($i % 4) + 1;
            $icons = ['bi-tree-fill','bi-droplet-fill','bi-egg-fill','bi-flower1','bi-cloud-sun-fill','bi-grid-3x3-gap-fill'];
            $icon  = $icons[$i % count($icons)];
        ?>
        <div class="col-md-6 col-lg-4 fade-in-up sector-item delay-<?php echo $delay; ?>">
            <a href="sector_details.php?id=<?php echo $s['id']; ?>" class="sector-card h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="sector-icon"><i class="bi <?php echo $icon; ?>"></i></div>
                    <?php if ($user_role === 'Admin'): ?>
                    <div onclick="event.stopPropagation();" class="d-flex gap-2">
                        <button class="action-icon edit" title="Edit"
                            onclick="openEdit(<?php echo $s['id']; ?>, '<?php echo htmlspecialchars($s['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($s['description'] ?? '', ENT_QUOTES); ?>', '<?php echo htmlspecialchars($s['chairperson'] ?? '', ENT_QUOTES); ?>')"
                            data-bs-toggle="modal" data-bs-target="#editSectorModal">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="action-icon del" title="Delete"
                            onclick="confirmDelete(<?php echo $s['id']; ?>, '<?php echo htmlspecialchars($s['name'], ENT_QUOTES); ?>')"
                            data-bs-toggle="modal" data-bs-target="#deleteSectorModal">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                <h5 class="fw-800 mb-1 sector-title" style="letter-spacing:-0.4px;"><?php echo htmlspecialchars($s['name']); ?></h5>
                <p class="text-muted small mb-3 sector-desc" style="min-height:20px;"><?php echo $s['description'] ? htmlspecialchars($s['description']) : '<em class="text-muted">No description</em>'; ?></p>
                <div class="d-flex align-items-center justify-content-between mt-auto">
                    <span class="member-count-badge"><i class="bi bi-people me-1"></i><?php echo $s['member_count']; ?> member<?php echo $s['member_count'] != 1 ? 's' : ''; ?></span>
                    <?php if ($s['chairperson']): ?>
                    <small class="text-muted"><i class="bi bi-person-badge me-1"></i><span class="sector-chair"><?php echo htmlspecialchars($s['chairperson']); ?></span></small>
                    <?php endif; ?>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="table-card fade-in-up delay-2">
        <div class="empty-state">
            <i class="bi bi-diagram-3"></i>
            <h5 class="fw-700 mb-2">No Sectors Yet</h5>
            <p class="text-muted mb-0">Add your first cooperative sector to get started.</p>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ADD SECTOR MODAL -->
<div class="modal fade" id="addSectorModal" tabindex="-1" aria-labelledby="addSectorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSectorLabel"><i class="bi bi-plus-circle-fill"></i> Add New Sector</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_sector" class="btn-track"><i class="bi bi-check-circle me-2"></i>Save Sector</button>
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
                <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Edit Sector</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_sector" class="btn-track"><i class="bi bi-check-circle me-2"></i>Update Sector</button>
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
                <h5 class="modal-title" style="color:#ef4444;"><i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;"></i> Delete Sector</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="sector_id" id="deleteSectorId">
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to delete the sector <strong id="deleteSectorName"></strong>? This will <strong>not</strong> remove members — only the sector record.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_sector" class="btn-danger-soft"><i class="bi bi-trash me-2"></i>Delete</button>
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

    // --- REAL-TIME SECTOR SEARCH ---
    function performSectorSearch() {
        const term = document.getElementById('sectorSearch').value.toLowerCase().trim();
        const items = document.querySelectorAll('.sector-item');
        let found = 0;

        items.forEach(item => {
            const title = item.querySelector('.sector-title').textContent.toLowerCase();
            const desc = item.querySelector('.sector-desc').textContent.toLowerCase();
            const chair = item.querySelector('.sector-chair') ? item.querySelector('.sector-chair').textContent.toLowerCase() : '';
            
            if (title.includes(term) || desc.includes(term) || chair.includes(term)) {
                item.style.display = 'block';
                item.style.animation = 'fadeInUpCustom 0.4s ease forwards';
                found++;
            } else {
                item.style.display = 'none';
            }
        });

        // Handle Empty State
        const grid = document.getElementById('sectorGrid');
        let noResults = document.getElementById('noSectorResults');

        if (found === 0) {
            if (!noResults) {
                noResults = document.createElement('div');
                noResults.id = 'noSectorResults';
                noResults.className = 'col-12 py-5 text-center';
                noResults.innerHTML = `
                    <div class="opacity-25 mb-3"><i class="bi bi-diagram-3" style="font-size: 4rem;"></i></div>
                    <h5 class="fw-bold text-muted">No sectors found</h5>
                    <p class="text-muted small mb-0">We couldn't find any sector matching "${document.getElementById('sectorSearch').value}"</p>
                `;
                grid.appendChild(noResults);
            }
        } else {
            if (noResults) noResults.remove();
        }
    }

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include('../includes/footer.php'); ?>
</body>
</html>
