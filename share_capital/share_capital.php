<?php
session_start();
include('../auth/db_connect.php');

// Allow Admin and Bookkeeper only
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Bookkeeper'])) {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

$user_id   = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$full_name = "User";

$q = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$q->bind_param("i", $user_id);
$q->execute();
$r = $q->get_result();
if ($u = $r->fetch_assoc()) {
    $full_name = $u['first_name'] . " " . $u['last_name'];
}

// ── Handle Add Capital (Admin/Bookkeeper) ─────────────────────────────────────
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_capital'])) {
    $mem_id    = intval($_POST['member_id']);
    $amount    = floatval($_POST['amount']);
    $tx_type   = trim($_POST['transaction_type']);
    $ref_no    = trim($_POST['reference_no']);
    $notes     = trim($_POST['notes']);
    $rec_by    = $user_id;

    if ($mem_id > 0 && $amount > 0) {
        $ins = $conn->prepare("INSERT INTO share_capital (user_id, amount, transaction_type, reference_no, notes, recorded_by) VALUES (?,?,?,?,?,?)");
        $ins->bind_param("idsssi", $mem_id, $amount, $tx_type, $ref_no, $notes, $rec_by);
        if ($ins->execute()) {
            $msg = "success";
        } else {
            $msg = "error";
        }
    } else {
        $msg = "invalid";
    }
}

// ── Summary Stats ─────────────────────────────────────────────────────────────
$total_capital   = 0;
$member_count    = 0;
$avg_capital     = 0;
$monthly_capital = 0;

$res = $conn->query("SELECT COALESCE(SUM(amount),0) AS total FROM share_capital WHERE transaction_type='deposit'");
if ($res) { $row = $res->fetch_assoc(); $total_capital = $row['total']; }

$res2 = $conn->query("SELECT COUNT(DISTINCT user_id) AS cnt FROM share_capital");
if ($res2) { $row2 = $res2->fetch_assoc(); $member_count = $row2['cnt']; }

$avg_capital = ($member_count > 0) ? ($total_capital / $member_count) : 0;

$res3 = $conn->query("SELECT COALESCE(SUM(amount),0) AS monthly FROM share_capital WHERE transaction_type='deposit' AND MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())");
if ($res3) { $row3 = $res3->fetch_assoc(); $monthly_capital = $row3['monthly']; }

// ── Per Member Capital Balance ─────────────────────────────────────────────────
$members_cap = $conn->query("
    SELECT u.id, u.first_name, u.last_name, u.sector, u.status,
           COALESCE(SUM(CASE WHEN sc.transaction_type='deposit'    THEN sc.amount ELSE 0 END),0)
         - COALESCE(SUM(CASE WHEN sc.transaction_type='withdrawal' THEN sc.amount ELSE 0 END),0) AS balance,
           COUNT(sc.id) AS tx_count
    FROM users u
    LEFT JOIN share_capital sc ON u.id = sc.user_id
    WHERE u.role = 'Member'
    GROUP BY u.id
    ORDER BY balance DESC
");

// ── Member list for modal dropdown ────────────────────────────────────────────
$member_list = $conn->query("SELECT id, first_name, last_name FROM users WHERE role='Member' AND status='Approved' ORDER BY first_name");

// ── All Transactions for Full History Modal ────────────────────────────────────
$all_tx = $conn->query("
    SELECT sc.id, sc.amount, sc.transaction_type, sc.reference_no, sc.notes, sc.created_at,
           u.id AS uid, u.first_name, u.last_name, u.sector,
           r.first_name AS rec_fname, r.last_name AS rec_lname
    FROM share_capital sc
    JOIN users u ON sc.user_id = u.id
    LEFT JOIN users r ON sc.recorded_by = r.id
    ORDER BY sc.created_at DESC
");
$all_tx_rows = [];
if ($all_tx) { while ($row = $all_tx->fetch_assoc()) $all_tx_rows[] = $row; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Capital Overview | TrackCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
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
            display: flex;
            flex-direction: column;
        }

        /* ── Navbar ── */
        .navbar {
            background-color: rgba(245,245,220,0.95) !important;
            backdrop-filter: blur(10px);
            padding: 15px 0;
            border-bottom: 1px solid rgba(229,229,192,0.5);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            animation: fadeInUpCustom 0.8s ease-out;
        }
        .navbar-brand { font-weight: 800; font-size: 1.5rem; letter-spacing: -0.8px; color: var(--track-dark) !important; }
        .navbar-brand span { color: var(--track-green); }
        .navbar-nav .nav-link {
            color: var(--text-muted) !important;
            font-weight: 600; font-size: 0.95rem; margin: 0 10px;
            padding: 8px 0 !important; position: relative;
            transition: var(--transition-smooth); display: flex; align-items: center; gap: 6px;
        }
        .navbar-nav .nav-link::after {
            content: ''; position: absolute; bottom: 0; left: 0;
            width: 0; height: 2px; background-color: var(--track-green); transition: width 0.3s ease;
        }
        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after { width: 100%; }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active { color: var(--track-dark) !important; background: transparent !important; }
        .logout-btn {
            border: 2px solid #dc2626; color: #dc2626;
            width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 12px; transition: var(--transition-smooth); text-decoration: none;
        }
        .logout-btn:hover { background: #dc2626; color: white; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(220, 38, 38, 0.4); }

        /* ── Page Header ── */
        .page-header {
            background: linear-gradient(135deg, var(--track-bg) 0%, var(--track-beige) 100%);
            padding: 60px 0 40px;
            border-bottom: 1px solid rgba(229,229,192,0.4);
            margin-bottom: 40px;
            position: relative; overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16,1,0.3,1) both;
        }
        .page-header::after {
            content: ''; position: absolute; top: -20%; right: -5%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(32,160,96,0.08) 0%, rgba(255,255,255,0) 70%);
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
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); position: relative; overflow: hidden;
            z-index: 1;
        }
        .stat-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(32,160,96,0.08); border-color: rgba(32,160,96,0.3); z-index: 2; }
        .icon-box {
            width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;
            border-radius: 14px; margin-bottom: 16px; transition: 0.3s;
        }
        .stat-card:hover .icon-box { transform: scale(1.1) rotate(5deg); }

        /* ── Table Card ── */
        .table-card {
            border: 1px solid rgba(226,232,240,0.8); border-radius: 20px; background: white;
            padding: 28px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);
        }
        .table thead th {
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-muted); border-bottom: 2px solid #f1f5f9; padding-bottom: 14px; border-top: none;
        }
        .table tbody tr { transition: background-color 0.25s ease, box-shadow 0.25s ease; cursor: pointer; }
        .table tbody tr:hover { background-color: #edf7f2; box-shadow: inset 4px 0 0 var(--track-green); }
        .table tbody td { padding: 14px 8px; vertical-align: middle; border-color: #f8fafc; font-size: 0.95rem; }
        .table > :not(caption) > * > * { border-bottom-color: #f1f5f9; }

        /* ── Avatar ── */
        .member-avatar {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg, var(--track-green), #1a8548);
            color: white; font-weight: 800; font-size: 0.85rem;
            display: flex; align-items: center; justify-content: center;
        }

        /* ── Badges ── */
        .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; }
        .badge-approved { background: #eefdf5; color: #27ae60; }
        .badge-pending  { background: #fff9e6; color: #d97706; }
        .badge-inactive { background: #fee2e2; color: #ef4444; }

        /* ── Action ── */
        .action-icon {
            display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center;
            border-radius: 8px; color: var(--text-muted); transition: 0.3s;
            background: #f8fafc; border: 1px solid #e2e8f0; text-decoration: none;
        }
        .action-icon:hover { background: var(--track-green); color: white; border-color: var(--track-green); }

        /* ── Modal ── */
        .modal-content { border: none; border-radius: 20px; box-shadow: 0 25px 60px rgba(0,0,0,0.15); }
        .modal-header { background: var(--track-beige); border-bottom: 2px solid rgba(229,229,192,0.6); border-radius: 20px 20px 0 0; padding: 24px 28px; }
        .modal-title { font-weight: 800; font-size: 1.3rem; letter-spacing: -0.5px; color: var(--track-dark); gap: 10px; display: flex; align-items: center; }
        .modal-title i { color: var(--track-green); }
        .modal-body { padding: 28px; }
        .modal-footer { background: var(--track-beige); border-top: 1px solid rgba(229,229,192,0.6); border-radius: 0 0 20px 20px; padding: 20px 28px; }
        .form-label { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .form-control, .form-select {
            border-radius: 12px; padding: 12px 16px; border: 1.5px solid #e5e5c0;
            background-color: #fdfdf8; transition: 0.3s; font-size: 0.95rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--track-green); box-shadow: 0 0 0 4px rgba(32,160,96,0.12);
            background-color: #fff;
        }
        .btn-track { background: var(--track-green); color: white; border: none; border-radius: 12px; padding: 12px 28px; font-weight: 700; transition: var(--transition-smooth); }
        .btn-track:hover { background: #1a8548; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(32,160,96,0.3); color: white; }
        .btn-cancel { background: white; color: var(--text-muted); border: 1.5px solid #e5e5c0; border-radius: 12px; padding: 12px 24px; font-weight: 600; transition: 0.3s; }
        .btn-cancel:hover { background: #fdfdf8; border-color: var(--track-green); color: var(--track-dark); }

        /* ── Select2 Custom Theme (Beige TrackCOOP) ── */
        .select2-container--default .select2-selection--single {
            border: 1.5px solid #e5e5c0 !important;
            border-radius: 12px !important;
            height: 48px !important;
            background-color: #fdfdf8 !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 48px !important;
            padding-left: 16px !important;
            color: var(--text-main);
            font-size: 0.95rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 10px !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--track-green) !important;
            box-shadow: 0 0 0 4px rgba(32,160,96,0.12) !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--track-green) !important;
            box-shadow: 0 0 0 4px rgba(32,160,96,0.12) !important;
            outline: none !important;
        }
        .select2-dropdown {
            border: 1.5px solid #e5e5c0 !important;
            border-radius: 14px !important;
            box-shadow: 0 12px 40px rgba(0,0,0,0.1) !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1.5px solid #e5e5c0;
            border-radius: 8px;
            padding: 8px 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.9rem;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--track-green);
            outline: none;
            box-shadow: 0 0 0 3px rgba(32,160,96,0.1);
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--track-green) !important;
            color: white !important;
        }
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: var(--track-green-light) !important;
            color: #1a5c38 !important;
            font-weight: 700;
        }
        .select2-results__option {
            font-size: 0.92rem;
            padding: 10px 14px;
        }
        .select2-search--dropdown { padding: 10px 12px 6px; }

        /* ── Capital bar ── */
        .capital-bar { height: 6px; border-radius: 99px; background: #e2e8f0; overflow: hidden; }
        .capital-bar-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--track-green), #1a8548); transition: width 1.2s cubic-bezier(0.16,1,0.3,1); }

        /* ── Sector badge ── */
        .sector-badge { background: var(--track-beige); color: var(--track-dark); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; border: 1px solid rgba(229,229,192,0.8); }

        /* ── Stagger Animations (mirrors admin_dashboard) ── */
        .fade-in-up { opacity: 0; animation: fadeInUpCustom 0.8s cubic-bezier(0.16,1,0.3,1) forwards; }
        .delay-1 { animation-delay: 0.1s; } .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; } .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        /* ── Button portal style (matches admin) ── */
        .btn-portal {
            background: var(--track-green); color: white; border-radius: 12px; padding: 12px 24px;
            font-weight: 700; border: none; box-shadow: 0 8px 20px rgba(32,160,96,0.2);
            transition: var(--transition-smooth); display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-portal:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(32,160,96,0.3); color: white; }

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
    </style>
    <link rel="stylesheet" href="../includes/footer.css">
</head>
<body>

<?php 
    $active_page = 'share_capital';
    $membership_type = $user_role;
    include('../includes/dashboard_navbar.php'); 
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container position-relative" style="z-index:1;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="badge-platform">
                    <span class="spinner-grow spinner-grow-sm me-2 text-success" role="status" style="width: 10px; height: 10px;"></span>
                    System Live
                </div>
                <h1 class="fw-800 display-5 mb-2" style="letter-spacing:-1.5px;">Capital Overview</h1>
                <p class="fs-6 mb-0 text-muted">Monitor and manage share capital contributions from all cooperative members.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button class="btn-portal" data-bs-toggle="modal" data-bs-target="#addCapitalModal">
                    <i class="bi bi-plus-circle-fill"></i> Record Capital
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">

    <!-- Flash Message -->
    <?php if ($msg === 'success'): ?>
        <div class="alert alert-success fw-bold rounded-4 mb-4" role="alert"><i class="bi bi-check-circle-fill me-2"></i> Capital contribution recorded successfully.</div>
    <?php elseif ($msg === 'error'): ?>
        <div class="alert alert-danger fw-bold rounded-4 mb-4" role="alert"><i class="bi bi-exclamation-octagon-fill me-2"></i> Error saving record. Please try again.</div>
    <?php elseif ($msg === 'invalid'): ?>
        <div class="alert alert-warning fw-bold rounded-4 mb-4" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i> Invalid data submitted. Please check the form.</div>
    <?php endif; ?>

    <!-- STAT CARDS -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 fade-in-up delay-1">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-cash-coin fs-4"></i></div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-bold" style="font-size:0.7rem;">Total</span>
                </div>
                <h6 class="text-uppercase fw-bold small mb-1 text-muted" style="letter-spacing:0.5px;">Total Capital</h6>
                <h2 class="fw-800 mb-0 text-dark">₱<?php echo number_format($total_capital, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-3 fade-in-up delay-2">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-people-fill fs-4"></i></div>
                </div>
                <h6 class="text-uppercase fw-bold small mb-1 text-muted" style="letter-spacing:0.5px;">Contributing Members</h6>
                <h2 class="fw-800 mb-0 text-dark"><?php echo number_format($member_count); ?></h2>
            </div>
        </div>
        <div class="col-md-3 fade-in-up delay-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="bi bi-bar-chart-line-fill fs-4"></i></div>
                </div>
                <h6 class="text-uppercase fw-bold small mb-1 text-muted" style="letter-spacing:0.5px;">Avg. per Member</h6>
                <h2 class="fw-800 mb-0 text-dark">₱<?php echo number_format($avg_capital, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-3 fade-in-up delay-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-box bg-info bg-opacity-10 text-info"><i class="bi bi-calendar-check-fill fs-4"></i></div>
                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill fw-bold" style="font-size:0.7rem;">This Month</span>
                </div>
                <h6 class="text-uppercase fw-bold small mb-1 text-muted" style="letter-spacing:0.5px;">Monthly Collection</h6>
                <h2 class="fw-800 mb-0 text-dark">₱<?php echo number_format($monthly_capital, 2); ?></h2>
            </div>
        </div>
    </div>

    <!-- SEARCH TOOLBAR -->
    <div class="search-wrapper">
        <div class="search-input-group">
            <i class="bi bi-search"></i>
            <input type="text" id="memberCapitalSearch" class="search-input" placeholder="Search members by name or sector...">
        </div>
    </div>

    <!-- MEMBER CAPITAL TABLE -->
    <div class="table-card" data-aos="fade-up" data-aos-delay="100">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h5 class="fw-800 mb-1" style="letter-spacing:-0.5px;"><i class="bi bi-wallet2 text-success me-2"></i>Member Capital Balances</h5>
                <small class="text-muted">Click a member row to view their transaction history</small>
            </div>
            <button class="btn btn-light border fw-bold px-4" style="border-radius:12px;" data-bs-toggle="modal" data-bs-target="#fullHistoryModal" onclick="filterHistory('all')">
                <i class="bi bi-clock-history me-2"></i> Full History
            </button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="border-0">Member</th>
                        <th class="border-0">Sector</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Capital Balance</th>
                        <th class="border-0">Transactions</th>
                        <th class="border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($members_cap && $members_cap->num_rows > 0):
                        $max_balance = 1;
                        $rows_data = [];
                        while ($rc = $members_cap->fetch_assoc()) {
                            $rows_data[] = $rc;
                            if ($rc['balance'] > $max_balance) $max_balance = $rc['balance'];
                        }
                        foreach ($rows_data as $rc):
                            $initials = strtoupper(substr($rc['first_name'],0,1) . substr($rc['last_name'],0,1));
                            $bar_pct  = ($max_balance > 0) ? round(($rc['balance'] / $max_balance) * 100) : 0;
                    ?>
                    <tr onclick="showMemberHistory(<?php echo $rc['id']; ?>, '<?php echo htmlspecialchars($rc['first_name'] . ' ' . $rc['last_name'], ENT_QUOTES); ?>')" style="cursor:pointer;">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="member-avatar"><?php echo $initials; ?></div>
                                <div>
                                    <div class="fw-700 member-name" style="color:var(--track-dark);"><?php echo htmlspecialchars($rc['first_name'] . ' ' . $rc['last_name']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="sector-badge member-sector"><?php echo htmlspecialchars($rc['sector'] ?: '—'); ?></span></td>
                        <td>
                            <?php
                                if ($rc['status'] === 'Approved')  echo '<span class="badge-status badge-approved">Approved</span>';
                                elseif ($rc['status'] === 'Pending') echo '<span class="badge-status badge-pending">Pending</span>';
                                else echo '<span class="badge-status badge-inactive">Inactive</span>';
                            ?>
                        </td>
                        <td style="min-width:180px;">
                            <div class="fw-700 mb-1" style="color:var(--track-dark);">₱<?php echo number_format($rc['balance'], 2); ?></div>
                            <div class="capital-bar"><div class="capital-bar-fill" style="width:<?php echo $bar_pct; ?>%"></div></div>
                        </td>
                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold"><?php echo $rc['tx_count']; ?> txn<?php echo $rc['tx_count'] != 1 ? 's' : ''; ?></span></td>
                        <td class="text-end" onclick="event.stopPropagation();">
                            <button class="action-icon me-1" title="View History"
                                onclick="event.stopPropagation(); showMemberHistory(<?php echo $rc['id']; ?>, '<?php echo htmlspecialchars($rc['first_name'] . ' ' . $rc['last_name'], ENT_QUOTES); ?>')"
                                data-bs-toggle="modal" data-bs-target="#fullHistoryModal">
                                <i class="bi bi-clock-history"></i>
                            </button>
                            <button class="action-icon ms-1" title="Quick Add Capital"
                                onclick="quickAdd(<?php echo $rc['id']; ?>, '<?php echo htmlspecialchars($rc['first_name'] . ' ' . $rc['last_name']); ?>')"
                                data-bs-toggle="modal" data-bs-target="#addCapitalModal">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-wallet2" style="font-size:3rem;opacity:0.1;display:block;margin-bottom:10px;"></i>
                            No share capital records found. Start recording contributions above.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- FULL HISTORY MODAL -->
<div class="modal fade" id="fullHistoryModal" tabindex="-1" aria-labelledby="fullHistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fullHistoryLabel">
                    <i class="bi bi-clock-history"></i>
                    <span id="historyModalTitle">Full Transaction History</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 0;">
                <!-- Filter bar inside modal -->
                <div style="padding: 20px 28px 0; border-bottom: 1px solid #f1f5f9; background: #f8fafc;">
                    <div class="d-flex gap-3 align-items-center flex-wrap pb-3">
                        <div class="position-relative flex-grow-1" style="max-width:260px;">
                            <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#aaa;"></i>
                            <input type="text" id="historySearch" class="form-control form-control-sm" placeholder="Search member..." style="padding-left:36px;border-radius:10px;" oninput="applyHistoryFilter()">
                        </div>
                        <select id="historyTypeFilter" class="form-select form-select-sm" style="max-width:180px;border-radius:10px;" onchange="applyHistoryFilter()">
                            <option value="all">All Types</option>
                            <option value="deposit">Deposits Only</option>
                            <option value="withdrawal">Withdrawals Only</option>
                        </select>
                        <small class="text-muted ms-auto" id="historyCount"></small>
                    </div>
                </div>

                <div class="table-responsive" style="max-height:480px;">
                    <table class="table align-middle mb-0" style="font-size:0.9rem;">
                        <thead style="position:sticky;top:0;background:#fff;z-index:10;">
                            <tr>
                                <th class="border-0 ps-4" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;padding-bottom:12px;padding-top:14px;">Member</th>
                                <th class="border-0" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;">Type</th>
                                <th class="border-0" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;">Amount</th>
                                <th class="border-0" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;">Reference</th>
                                <th class="border-0" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;">Notes</th>
                                <th class="border-0" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;">Date</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <?php if (empty($all_tx_rows)): ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">No transactions recorded yet.</td></tr>
                            <?php else: ?>
                            <?php foreach ($all_tx_rows as $tx):
                                $ini = strtoupper(substr($tx['first_name'],0,1) . substr($tx['last_name'],0,1));
                            ?>
                            <tr class="history-row"
                                data-uid="<?php echo $tx['uid']; ?>"
                                data-name="<?php echo strtolower($tx['first_name'] . ' ' . $tx['last_name']); ?>"
                                data-type="<?php echo $tx['transaction_type']; ?>">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#20a060,#1a8548);color:white;font-weight:800;font-size:0.7rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <?php echo $ini; ?>
                                        </div>
                                        <span class="fw-600" style="color:#1a1a1a;"><?php echo htmlspecialchars($tx['first_name'] . ' ' . $tx['last_name']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($tx['transaction_type'] === 'deposit'): ?>
                                        <span style="background:#eefdf5;color:#27ae60;padding:4px 12px;border-radius:20px;font-size:0.72rem;font-weight:700;"><i class="bi bi-arrow-down-circle me-1"></i>Deposit</span>
                                    <?php else: ?>
                                        <span style="background:#fee2e2;color:#ef4444;padding:4px 12px;border-radius:20px;font-size:0.72rem;font-weight:700;"><i class="bi bi-arrow-up-circle me-1"></i>Withdrawal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-800" style="color:<?php echo $tx['transaction_type'] === 'deposit' ? '#27ae60' : '#ef4444'; ?>;">
                                        <?php echo $tx['transaction_type'] === 'withdrawal' ? '−' : '+'; ?>₱<?php echo number_format($tx['amount'], 2); ?>
                                    </span>
                                </td>
                                <td><small class="badge bg-light text-dark border"><?php echo $tx['reference_no'] ? htmlspecialchars($tx['reference_no']) : '—'; ?></small></td>
                                <td><small class="text-muted"><?php echo $tx['notes'] ? htmlspecialchars($tx['notes']) : '—'; ?></small></td>
                                <td>
                                    <small class="fw-600" style="color:#374151;"><?php echo date('M d, Y', strtotime($tx['created_at'])); ?></small><br>
                                    <small class="text-muted" style="font-size:0.7rem;"><?php echo date('h:i A', strtotime($tx['created_at'])); ?></small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <small class="text-muted">Showing all capital transactions across all members.</small>
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ADD CAPITAL MODAL -->
<div class="modal fade" id="addCapitalModal" tabindex="-1" aria-labelledby="addCapitalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCapitalLabel"><i class="bi bi-plus-circle-fill"></i> Record Capital Contribution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" onsubmit="return TrackUI.confirmForm(event, 'Register this capital contribution/transaction to the member ledger?', 'Finance Entry', 'primary', 'Record Now', 'Back')">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Member</label>
                            <select name="member_id" id="memberSelect" class="form-select" required>
                                <option value="">-- Select Member --</option>
                                <?php if ($member_list): while ($ml = $member_list->fetch_assoc()): ?>
                                <option value="<?php echo $ml['id']; ?>"><?php echo htmlspecialchars($ml['first_name'] . ' ' . $ml['last_name']); ?></option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Transaction Type</label>
                            <select name="transaction_type" class="form-select" required>
                                <option value="deposit">Deposit (Add Capital)</option>
                                <option value="withdrawal">Withdrawal (Deduct Capital)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Amount (₱)</label>
                            <input type="number" name="amount" class="form-control" min="1" step="0.01" placeholder="e.g. 500.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference No.</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="e.g. OR-2024-001">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_capital" class="btn-track"><i class="bi bi-check-circle me-2"></i> Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic' });

    // ── Select2: Searchable member dropdown ────────────────────────────────────
    $(document).ready(function() {
        $('#memberSelect').select2({
            dropdownParent: $('#addCapitalModal'),
            placeholder: '-- Select Member --',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 0
        });

        // Sync quickAdd with Select2
        window.quickAdd = function(memberId) {
            $('#memberSelect').val(memberId).trigger('change');
        };
    });

    // ── Show Full History (all members) ───────────────────────────────────────
    function filterHistory(mode) {
        document.getElementById('historyModalTitle').textContent = 'Full Transaction History';
        document.getElementById('historySearch').value = '';
        document.getElementById('historyTypeFilter').value = 'all';
        // show all rows
        document.querySelectorAll('.history-row').forEach(r => r.style.display = '');
        updateCount();
    }

    // ── Show History filtered to one member ───────────────────────────────────
    function showMemberHistory(uid, name) {
        document.getElementById('historyModalTitle').textContent = name + ' — Capital History';
        document.getElementById('historySearch').value = '';
        document.getElementById('historyTypeFilter').value = 'all';
        // filter by uid
        document.querySelectorAll('.history-row').forEach(r => {
            r.style.display = (r.dataset.uid == uid) ? '' : 'none';
        });
        updateCount();
    }

    // ── Live search + type filter inside modal ────────────────────────────────
    function applyHistoryFilter() {
        const search = document.getElementById('historySearch').value.toLowerCase().trim();
        const type   = document.getElementById('historyTypeFilter').value;

        document.querySelectorAll('.history-row').forEach(r => {
            const nameMatch = !search || r.dataset.name.includes(search);
            const typeMatch = type === 'all' || r.dataset.type === type;
            r.style.display = (nameMatch && typeMatch) ? '' : 'none';
        });

        // reset title to generic if searching
        if (search) {
            document.getElementById('historyModalTitle').textContent = 'Transaction History — Search Results';
        }
        updateCount();
    }

    function updateCount() {
        const visible = document.querySelectorAll('.history-row:not([style*="display: none"])').length;
        const total   = document.querySelectorAll('.history-row').length;
        document.getElementById('historyCount').textContent = visible + ' of ' + total + ' records';
    }

    // Init count on modal open
    document.getElementById('fullHistoryModal').addEventListener('shown.bs.modal', updateCount);

    // ── Real-Time Capital Search ─────────────────────────────────────────────
    function performCapitalSearch() {
        const term = document.getElementById('memberCapitalSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('tbody:not(#historyTableBody) tr:not(.no-results-row)');
        let found = 0;

        rows.forEach(row => {
            const name = row.querySelector('.member-name').textContent.toLowerCase();
            const sector = row.querySelector('.member-sector').textContent.toLowerCase();
            
            if (name.includes(term) || sector.includes(term)) {
                row.style.display = '';
                row.style.animation = 'fadeInUpCustom 0.4s ease forwards';
                found++;
            } else {
                row.style.display = 'none';
            }
        });

        // Handle Empty State
        const tbody = document.querySelector('tbody:not(#historyTableBody)');
        let noResults = document.getElementById('noCapitalResultsRow');

        if (found === 0) {
            if (!noResults) {
                noResults = document.createElement('tr');
                noResults.id = 'noCapitalResultsRow';
                noResults.className = 'no-results-row';
                noResults.innerHTML = `
                    <td colspan="6" class="text-center py-5">
                        <div class="opacity-25 mb-3"><i class="bi bi-wallet2" style="font-size: 3rem;"></i></div>
                        <h5 class="fw-bold text-muted">No balances found</h5>
                        <p class="text-muted small mb-0">We couldn't find any member matching your search criteria.</p>
                    </td>
                `;
                tbody.appendChild(noResults);
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
