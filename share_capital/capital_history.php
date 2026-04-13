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

// ── Handle filter by member ────────────────────────────────────────────────────
$filter_member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;
$filter_type      = isset($_GET['tx_type']) ? trim($_GET['tx_type']) : '';
$search_name      = isset($_GET['search']) ? trim($_GET['search']) : '';

// ── Handle Add Capital (inline) ───────────────────────────────────────────────
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_capital'])) {
    $mem_id  = intval($_POST['member_id']);
    $amount  = floatval($_POST['amount']);
    $tx_type = trim($_POST['transaction_type']);
    $ref_no  = trim($_POST['reference_no']);
    $notes   = trim($_POST['notes']);
    $rec_by  = $user_id;

    if ($mem_id > 0 && $amount > 0) {
        $ins = $conn->prepare("INSERT INTO share_capital (user_id, amount, transaction_type, reference_no, notes, recorded_by) VALUES (?,?,?,?,?,?)");
        $ins->bind_param("idsssi", $mem_id, $amount, $tx_type, $ref_no, $notes, $rec_by);
        $msg = $ins->execute() ? "success" : "error";
    } else {
        $msg = "invalid";
    }
    header("Location: capital_history.php?member_id=" . $filter_member_id . "&msg=" . $msg);
    exit();
}

if (isset($_GET['msg'])) $msg = $_GET['msg'];

// ── Selected member info ──────────────────────────────────────────────────────
$selected_member = null;
if ($filter_member_id > 0) {
    $sm = $conn->prepare("SELECT id, first_name, last_name, sector, status FROM users WHERE id = ?");
    $sm->bind_param("i", $filter_member_id);
    $sm->execute();
    $selected_member = $sm->get_result()->fetch_assoc();
}

// ── Member capital summary ─────────────────────────────────────────────────────
$member_balance  = 0;
$member_deposits = 0;
$member_withdraw = 0;
if ($filter_member_id > 0) {
    $bs = $conn->prepare("
        SELECT 
            COALESCE(SUM(CASE WHEN transaction_type='deposit'    THEN amount ELSE 0 END),0) AS dep,
            COALESCE(SUM(CASE WHEN transaction_type='withdrawal' THEN amount ELSE 0 END),0) AS wit
        FROM share_capital WHERE user_id = ?
    ");
    $bs->bind_param("i", $filter_member_id);
    $bs->execute();
    $brow = $bs->get_result()->fetch_assoc();
    $member_deposits = $brow['dep'];
    $member_withdraw = $brow['wit'];
    $member_balance  = $member_deposits - $member_withdraw;
}

// ── Transactions list ─────────────────────────────────────────────────────────
$where_clauses = ["1=1"];
$params = [];
$types  = "";

if ($filter_member_id > 0) {
    $where_clauses[] = "sc.user_id = ?";
    $params[] = &$filter_member_id;
    $types .= "i";
}
if ($filter_type !== '' && in_array($filter_type, ['deposit','withdrawal'])) {
    $where_clauses[] = "sc.transaction_type = ?";
    $params[] = &$filter_type;
    $types .= "s";
}
if ($search_name !== '') {
    $like = "%" . $search_name . "%";
    $where_clauses[] = "(CONCAT(u.first_name,' ',u.last_name) LIKE ?)";
    $params[] = &$like;
    $types .= "s";
}

$where_sql = implode(" AND ", $where_clauses);

$tx_query = "
    SELECT sc.id, sc.amount, sc.transaction_type, sc.reference_no, sc.notes, sc.created_at,
           u.first_name, u.last_name, u.sector,
           r.first_name AS rec_fname, r.last_name AS rec_lname
    FROM share_capital sc
    JOIN users u ON sc.user_id = u.id
    LEFT JOIN users r ON sc.recorded_by = r.id
    WHERE $where_sql
    ORDER BY sc.created_at DESC
";

$tx_stmt = $conn->prepare($tx_query);
if (!empty($types)) {
    $bind_args = array_merge([$types], $params);
    call_user_func_array([$tx_stmt, 'bind_param'], $bind_args);
}
$tx_stmt->execute();
$tx_result = $tx_stmt->get_result();

// ── Member list for dropdown ───────────────────────────────────────────────────
$member_list = $conn->query("SELECT id, first_name, last_name FROM users WHERE role='Member' AND status='Approved' ORDER BY first_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capital History | TrackCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
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
            to   { opacity: 1; transform: translateY(0); }
        }

        body { 
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            color: var(--text-main);
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)), url('../Home.jpeg') top center / 100% 100% no-repeat fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ── Navbar ── */
        .navbar {
            background-color: #164a36 !important;
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
        .navbar-nav .nav-link.active { color: #20a060 !important; background: transparent !important; }
        .logout-btn {
            border: 2px solid #dc2626; color: #dc2626;
            width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 12px; transition: var(--transition-smooth); text-decoration: none;
        }
        .logout-btn:hover { background: #dc2626; color: white; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(220, 38, 38, 0.4); }

        /* ── Page Header ── */
        .page-header {
            background: transparent;
            padding: 60px 0 40px; border-bottom: none; margin-bottom: 40px;
            position: relative; overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16,1,0.3,1) both;
            color: #ffffff !important;
        }
        .page-header h1 { color: #20a060 !important; font-weight: 900 !important; }
        .page-header p { color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
        .page-header::after {
            content: ''; position: absolute; top: -20%; right: -5%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(32,160,96,0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
        }

        /* ── Member Profile Banner ── */
        .member-banner {
            border-radius: 20px; padding: 28px;
            background: linear-gradient(135deg, #fff 0%, var(--track-green-light) 100%);
            border: 1px solid rgba(32,160,96,0.15);
            box-shadow: 0 8px 24px rgba(32,160,96,0.08);
            margin-bottom: 32px;
        }
        .member-avatar-lg {
            width: 72px; height: 72px; border-radius: 20px;
            background: linear-gradient(135deg, var(--track-green), #1a8548);
            color: white; font-weight: 800; font-size: 1.5rem;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 20px rgba(32,160,96,0.3);
        }

        /* ── Summary Mini Cards ── */
        .mini-card {
            border-radius: 16px; padding: 20px; border: 1px solid rgba(226,232,240,0.8);
            background: white; transition: var(--transition-smooth);
            box-shadow: 0 4px 6px rgba(0,0,0,0.03);
        }
        .mini-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.08); }

        /* ── Filter Bar ── */
        .filter-bar {
            background: white; border-radius: 16px; padding: 20px 24px;
            border: 1px solid rgba(226,232,240,0.8); margin-bottom: 24px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        /* ── Table Card ── */
        .table-card {
            border: 1px solid rgba(226,232,240,0.8); border-radius: 20px; background: white;
            padding: 28px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);
        }
        .table thead th {
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-muted); border-bottom: 2px solid #f1f5f9; padding-bottom: 14px;
        }
        .table tbody tr { transition: var(--transition-smooth); }
        .table tbody tr:hover { background-color: var(--track-green-light); }
        .table tbody td { padding: 14px 8px; vertical-align: middle; border-color: #f8fafc; }

        /* ── Transaction Type Badges ── */
        .tx-deposit    { background: #eefdf5; color: #27ae60; padding: 5px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .tx-withdrawal { background: #fee2e2; color: #ef4444; padding: 5px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }

        /* ── Back button ── */
        .btn-back {
            display: inline-flex; align-items: center; justify-content: center;
            width: 44px; height: 44px; border-radius: 50%;
            background: white; border: 1.5px solid #e2e8f0;
            color: var(--text-muted); transition: var(--transition-smooth);
            text-decoration: none;
        }
        .btn-back:hover { background: var(--track-green); color: white; border-color: var(--track-green); transform: translateX(-3px); }

        /* ── Modal ── */
        .modal-content { border: none; border-radius: 20px; box-shadow: 0 25px 60px rgba(0,0,0,0.15); }
        .modal-header { background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22, 74, 54, 0.3); border-radius: 20px 20px 0 0; padding: 24px 28px; color: white; }
        .modal-title { font-weight: 800; font-size: 1.3rem; letter-spacing: -0.5px; color: white; gap: 10px; display: flex; align-items: center; }
        .modal-title i { color: var(--track-green); }
        .modal-body { padding: 28px; }
        .modal-footer { background: rgba(22, 74, 54, 0.95); color: white; border-top: 1px solid rgba(22, 74, 54, 0.3); border-radius: 0 0 20px 20px; padding: 20px 28px; }
        .form-label { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .form-control, .form-select {
            border-radius: 12px; padding: 12px 16px; border: 1.5px solid #e2e8f0;
            background-color: #f8fafc; transition: 0.3s; font-size: 0.95rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--track-green); box-shadow: 0 0 0 4px rgba(32,160,96,0.12); background-color: #fff;
        }
        .btn-track { background: #20a060; color: white; border: none; border-radius: 12px; padding: 12px 28px; font-weight: 700; transition: var(--transition-smooth); box-shadow: 0 4px 14px rgba(32, 160, 96, 0.3); }
        .btn-track:hover { background: #1a8548; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(32, 160, 96, 0.4); color: white; }
        .btn-cancel { background: #206970; color: white; border: none; border-radius: 12px; padding: 12px 24px; font-weight: 600; transition: 0.3s; }
        .btn-cancel:hover { background: #20a060; color: white; transform: translateY(-2px); }

        /* ── Stagger ── */
        .fade-in-up { opacity: 0; animation: fadeInUpCustom 0.8s cubic-bezier(0.16,1,0.3,1) forwards; }
        .delay-1 { animation-delay: 0.1s; } .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; } .delay-4 { animation-delay: 0.4s; }
    </style>
    <link rel="stylesheet" href="../includes/footer.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $user_role === 'Admin' ? '../admin/admin_dashboard.php' : '../bookkeeper/bookkeeper_dashboard.php'; ?>">
            Track<span>COOP</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $user_role === 'Admin' ? '../admin/admin_dashboard.php' : '../bookkeeper/bookkeeper_dashboard.php'; ?>">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link" href="../membership/members.php"><i class="bi bi-people"></i> Members</a></li>
                <li class="nav-item"><a class="nav-link active" href="share_capital.php"><i class="bi bi-wallet2"></i> Share Capital</a></li>
                <li class="nav-item"><a class="nav-link" href="../sectors/sectors.php"><i class="bi bi-diagram-3"></i> Sectors</a></li>
                <li class="nav-item"><a class="nav-link" href="../documents/documents.php"><i class="bi bi-folder-check"></i> Documents</a></li>
                <li class="nav-item"><a class="nav-link" href="../announcements/announcements.php"><i class="bi bi-broadcast"></i> Announcements</a></li>
            </ul>
            <div class="d-flex align-items-center">
                <div class="text-end me-3 d-none d-lg-block">
                    <div class="small fw-bold lh-1" style="color:var(--track-dark);"><?php echo htmlspecialchars($full_name); ?></div>
                    <small class="text-muted" style="font-size:0.75rem;"><?php echo htmlspecialchars($user_role); ?></small>
                </div>
                <a href="../auth/logout.php" class="logout-btn" title="Logout" 
                   onclick="return TrackUI.confirmLink(event, 'Log out from the system?', 'Confirm Logout', 'logout')">
                   <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container position-relative" style="z-index:1;">
        <div class="d-flex align-items-start gap-4">
            <a href="share_capital.php" class="btn-back mt-1" title="Back to Overview"><i class="bi bi-arrow-left fs-5"></i></a>
            <div class="flex-grow-1">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <small class="text-muted fw-bold d-block mb-2 text-uppercase" style="letter-spacing:1px; font-size:0.75rem;">
                            <i class="bi bi-wallet2 me-1"></i> Share Capital
                        </small>
                        <h1 class="fw-800 display-5 mb-1" style="letter-spacing:-1.5px;">Transaction History</h1>
                        <p class="fs-6 mb-0 text-muted">
                            <?php if ($selected_member): ?>
                                Viewing all capital records for <strong><?php echo htmlspecialchars($selected_member['first_name'] . ' ' . $selected_member['last_name']); ?></strong>
                            <?php else: ?>
                                Complete capital transaction log for all cooperative members.
                            <?php endif; ?>
                        </p>
                    </div>
                    <button class="btn btn-track px-4 py-2" data-bs-toggle="modal" data-bs-target="#addCapitalModal">
                        <i class="bi bi-plus-circle-fill me-2"></i> Record Capital
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">

    <!-- Flash Message -->
    <?php if ($msg === 'success'): ?>
        <div class="alert alert-success fw-bold rounded-4 mb-4"><i class="bi bi-check-circle-fill me-2"></i> Capital contribution recorded successfully.</div>
    <?php elseif ($msg === 'error'): ?>
        <div class="alert alert-danger fw-bold rounded-4 mb-4"><i class="bi bi-exclamation-octagon-fill me-2"></i> Error saving record. Please try again.</div>
    <?php endif; ?>

    <?php if ($selected_member): ?>
    <!-- MEMBER PROFILE BANNER -->
    <div class="member-banner fade-in-up delay-1">
        <div class="d-flex flex-column flex-md-row align-items-md-center gap-4">
            <div class="member-avatar-lg">
                <?php echo strtoupper(substr($selected_member['first_name'],0,1) . substr($selected_member['last_name'],0,1)); ?>
            </div>
            <div class="flex-grow-1">
                <h4 class="fw-800 mb-1" style="letter-spacing:-0.5px;"><?php echo htmlspecialchars($selected_member['first_name'] . ' ' . $selected_member['last_name']); ?></h4>
                <p class="text-muted mb-0">
                    <span class="badge bg-light text-dark border me-2"><?php echo htmlspecialchars($selected_member['sector']); ?></span>
                    <?php if ($selected_member['status'] === 'Approved') echo '<span class="badge" style="background:#eefdf5;color:#27ae60;">✓ Approved</span>'; ?>
                </p>
            </div>
            <div class="row g-3 text-center" style="min-width:360px;">
                <div class="col-4">
                    <div class="mini-card">
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:0.7rem;letter-spacing:0.5px;">Total Deposits</small>
                        <div class="fw-800 mt-1" style="color:#27ae60;font-size:1.1rem;">₱<?php echo number_format($member_deposits,2); ?></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="mini-card">
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:0.7rem;letter-spacing:0.5px;">Withdrawals</small>
                        <div class="fw-800 mt-1" style="color:#ef4444;font-size:1.1rem;">₱<?php echo number_format($member_withdraw,2); ?></div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="mini-card">
                        <small class="text-muted fw-bold text-uppercase d-block" style="font-size:0.7rem;letter-spacing:0.5px;">Net Balance</small>
                        <div class="fw-800 mt-1" style="color:var(--track-dark);font-size:1.1rem;">₱<?php echo number_format($member_balance,2); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- FILTER BAR -->
    <form method="GET" class="filter-bar fade-in-up delay-2">
        <?php if ($filter_member_id > 0): ?>
            <input type="hidden" name="member_id" value="<?php echo $filter_member_id; ?>">
        <?php endif; ?>
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Filter by Member</label>
                <select name="member_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Members</option>
                    <?php
                    $ml2 = $conn->query("SELECT id, first_name, last_name FROM users WHERE role='Member' ORDER BY first_name");
                    while ($ml = $ml2->fetch_assoc()):
                        $sel = ($filter_member_id == $ml['id']) ? 'selected' : '';
                    ?>
                    <option value="<?php echo $ml['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($ml['first_name'] . ' ' . $ml['last_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Transaction Type</label>
                <select name="tx_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="deposit"    <?php echo $filter_type === 'deposit'    ? 'selected' : ''; ?>>Deposits Only</option>
                    <option value="withdrawal" <?php echo $filter_type === 'withdrawal' ? 'selected' : ''; ?>>Withdrawals Only</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Search Name</label>
                <input type="text" name="search" class="form-control" placeholder="Member name..." value="<?php echo htmlspecialchars($search_name); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-track w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
            </div>
        </div>
    </form>

    <!-- TRANSACTION TABLE -->
    <div class="table-card" data-aos="fade-up">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-800 mb-1" style="letter-spacing:-0.5px;"><i class="bi bi-clock-history text-success me-2"></i>Capital Transactions</h5>
                <small class="text-muted"><?php echo $tx_result->num_rows; ?> record<?php echo $tx_result->num_rows != 1 ? 's' : ''; ?> found</small>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="border-0">#</th>
                        <th class="border-0">Member</th>
                        <th class="border-0">Type</th>
                        <th class="border-0">Amount</th>
                        <th class="border-0">Reference No.</th>
                        <th class="border-0">Notes</th>
                        <th class="border-0">Recorded By</th>
                        <th class="border-0">Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($tx_result->num_rows > 0):
                        $counter = 1;
                        while ($tx = $tx_result->fetch_assoc()):
                            $initials = strtoupper(substr($tx['first_name'],0,1) . substr($tx['last_name'],0,1));
                    ?>
                    <tr>
                        <td><small class="text-muted fw-bold"><?php echo $counter++; ?></small></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,var(--track-green),#1a8548);color:white;font-weight:800;font-size:0.75rem;display:flex;align-items:center;justify-content:center;">
                                    <?php echo $initials; ?>
                                </div>
                                <div class="fw-600" style="color:var(--track-dark);"><?php echo htmlspecialchars($tx['first_name'] . ' ' . $tx['last_name']); ?></div>
                            </div>
                        </td>
                        <td>
                            <?php if ($tx['transaction_type'] === 'deposit'): ?>
                                <span class="tx-deposit"><i class="bi bi-arrow-down-circle me-1"></i>Deposit</span>
                            <?php else: ?>
                                <span class="tx-withdrawal"><i class="bi bi-arrow-up-circle me-1"></i>Withdrawal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="fw-800" style="color:<?php echo $tx['transaction_type'] === 'deposit' ? '#27ae60' : '#ef4444'; ?>;">
                                <?php echo $tx['transaction_type'] === 'withdrawal' ? '−' : '+'; ?>₱<?php echo number_format($tx['amount'],2); ?>
                            </span>
                        </td>
                        <td><span class="badge bg-light text-dark border"><?php echo $tx['reference_no'] ? htmlspecialchars($tx['reference_no']) : '—'; ?></span></td>
                        <td><small class="text-muted"><?php echo $tx['notes'] ? htmlspecialchars($tx['notes']) : '—'; ?></small></td>
                        <td>
                            <small class="text-muted">
                                <?php echo $tx['rec_fname'] ? htmlspecialchars($tx['rec_fname'] . ' ' . $tx['rec_lname']) : 'System'; ?>
                            </small>
                        </td>
                        <td>
                            <small class="text-muted fw-600"><?php echo date('M d, Y', strtotime($tx['created_at'])); ?></small><br>
                            <small class="text-muted" style="font-size:0.7rem;"><?php echo date('h:i A', strtotime($tx['created_at'])); ?></small>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-clock-history" style="font-size:3rem;opacity:0.1;display:block;margin-bottom:10px;"></i>
                            No transactions found matching your filter.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
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
            <form method="POST" onsubmit="return TrackUI.confirmForm(event, 'Register this capital transaction to the member ledger?', 'Finance Entry', 'primary', 'Record Now', 'Back')">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Member</label>
                            <select name="member_id" class="form-select" required>
                                <option value="">-- Select Member --</option>
                                <?php if ($member_list): while ($ml = $member_list->fetch_assoc()): ?>
                                <option value="<?php echo $ml['id']; ?>" <?php echo ($filter_member_id == $ml['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($ml['first_name'] . ' ' . $ml['last_name']); ?>
                                </option>
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
<script>
    AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic' });
</script>
</body>
</html>
