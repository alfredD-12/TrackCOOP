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

// Static sectors data
$all_sectors = [
    1 => ['id' => 1, 'name' => 'Rice', 'description' => 'Rice farming and production', 'chairperson' => 'Juan Farmer', 'created_at' => '2024-01-15'],
    2 => ['id' => 2, 'name' => 'Corn', 'description' => 'Corn cultivation and trading', 'chairperson' => 'Maria Merchant', 'created_at' => '2024-02-10'],
    3 => ['id' => 3, 'name' => 'Fishery', 'description' => 'Fishing and aquaculture operations', 'chairperson' => 'Pedro Fish', 'created_at' => '2024-02-15'],
    4 => ['id' => 4, 'name' => 'Livestock', 'description' => 'Livestock raising and management', 'chairperson' => 'Rosa Rancher', 'created_at' => '2024-03-01'],
    5 => ['id' => 5, 'name' => 'High Value Crops', 'description' => 'High value crops production', 'chairperson' => 'Miguel Farmer', 'created_at' => '2024-03-05'],
];

// ── Get Sector ID from URL
$sector_id = intval($_GET['id'] ?? 0);
if ($sector_id <= 0 || !isset($all_sectors[$sector_id])) {
    header("Location: sectors.php");
    exit();
}

// Get the sector from static data
$sector = $all_sectors[$sector_id];

// ── Members in this Sector ──── (Static demo data)
$static_members = [
    ['id' => 1, 'first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'email' => 'juan@example.com', 'contact_number' => '09123456789', 'status' => 'Approved', 'created_at' => '2024-01-20', 'capital_balance' => 5450.00],
    ['id' => 2, 'first_name' => 'Jose', 'last_name' => 'Garcia', 'email' => 'jose@example.com', 'contact_number' => '09234567890', 'status' => 'Approved', 'created_at' => '2024-02-15', 'capital_balance' => 3200.00],
    ['id' => 3, 'first_name' => 'Miguel', 'last_name' => 'Santos', 'email' => 'miguel@example.com', 'contact_number' => '09345678901', 'status' => 'Pending', 'created_at' => '2024-03-10', 'capital_balance' => 0.00],
];

$members_rows = $static_members;
$member_count = count($members_rows);
$total_capital = array_sum(array_column($members_rows, 'capital_balance'));
$approved_count = count(array_filter($members_rows, fn($m) => $m['status'] === 'Approved'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($sector['name']); ?> | TrackCOOP</title>
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
        * { box-sizing: border-box; }

        @keyframes fadeInUpCustom {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

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
            background-color: rgba(245,245,220,0.95) !important;
            backdrop-filter: blur(10px); padding: 15px 0;
            border-bottom: 1px solid rgba(229,229,192,0.5);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            animation: fadeInUpCustom 0.8s ease-out;
        }
        .navbar-brand { font-weight: 800; font-size: 1.5rem; letter-spacing: -0.8px; color: var(--track-dark) !important; }
        .navbar-brand span { color: var(--track-green); }
        .navbar-nav .nav-link {
            color: var(--text-muted) !important; font-weight: 600; font-size: 0.95rem;
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
            border: 2px solid #dc2626; color: #dc2626;
            width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 12px; transition: var(--transition-smooth); text-decoration: none;
        }
        .logout-btn:hover { background: #dc2626; color: white; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(220, 38, 38, 0.4); }

        /* ── Page Header ── */
        .page-header {
            background: transparent;
            padding: 50px 0 36px;
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
        .badge-platform {
            background: white; color: var(--track-green); font-weight: 700; padding: 6px 14px;
            border-radius: 50px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
            display: inline-flex; align-items: center; margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(32,160,96,0.1); border: 1px solid rgba(32,160,96,0.2);
        }
        .btn-back {
            display: inline-flex; align-items: center; justify-content: center;
            width: 42px; height: 42px; border-radius: 12px; border: 1.5px solid #e5e5c0;
            background: white; color: var(--text-muted); text-decoration: none;
            transition: var(--transition-smooth); margin-right: 16px;
        }
        .btn-back:hover { border-color: var(--track-green); color: var(--track-green); background: var(--track-green-light); }

        /* ── Stat Cards ── */
        .stat-card {
            border: 1px solid rgba(226,232,240,0.8); border-radius: 20px; background: white;
            padding: 24px; transition: all 0.4s cubic-bezier(0.16,1,0.3,1);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03); position: relative; overflow: hidden; z-index:1;
        }
        .stat-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(32,160,96,0.08); border-color: rgba(32,160,96,0.3); }
        .icon-box {
            width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;
            border-radius: 14px; margin-bottom: 16px; transition: 0.3s;
        }
        .stat-card:hover .icon-box { transform: scale(1.1) rotate(5deg); }

        /* ── Sector Info Card ── */
        .sector-info-card {
            background: white; border-radius: 20px; border: 1px solid rgba(226,232,240,0.8);
            padding: 28px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);
            border-top: 4px solid var(--track-green);
        }

        /* ── Table Card ── */
        .table-card {
            border: 1px solid rgba(226,232,240,0.8); border-radius: 20px; background: white;
            padding: 28px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);
        }
        .table thead th {
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            color: var(--text-muted); border-bottom: 2px solid #f1f5f9; padding-bottom: 14px; border-top: none;
        }
        .table tbody tr { transition: background-color 0.25s ease, box-shadow 0.25s ease; }
        .table tbody tr:hover { background-color: #edf7f2; box-shadow: inset 4px 0 0 var(--track-green); }
        .table tbody td { padding: 14px 8px; vertical-align: middle; border-color: #f8fafc; font-size: 0.95rem; }
        .table > :not(caption) > * > * { border-bottom-color: #f1f5f9; }

        /* ── Avatar ── */
        .member-avatar {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg, var(--track-green), #1a8548);
            color: white; font-weight: 800; font-size: 0.85rem;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        /* ── Badges ── */
        .badge-status { padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; }
        .badge-approved { background: #eefdf5; color: #27ae60; }
        .badge-pending  { background: #fff9e6; color: #d97706; }
        .badge-inactive { background: #fee2e2; color: #ef4444; }

        /* ── Action icons ── */
        .action-icon {
            display: inline-flex; width: 34px; height: 34px; align-items: center; justify-content: center;
            border-radius: 8px; color: #3b82f6; transition: all 0.3s ease;
            background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.3); text-decoration: none;
            font-size: 1rem;
        }
        .action-icon:hover { background: #3b82f6; color: white; border-color: #3b82f6; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }

        /* ── Stagger ── */
        .fade-in-up { opacity: 0; animation: fadeInUpCustom 0.8s cubic-bezier(0.16,1,0.3,1) forwards; }
        .delay-1 { animation-delay: 0.1s; } .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; } .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }
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
                <li class="nav-item"><a class="nav-link" href="../share_capital/share_capital.php"><i class="bi bi-wallet2"></i> Share Capital</a></li>
                <li class="nav-item"><a class="nav-link active" href="sectors.php"><i class="bi bi-diagram-3"></i> Sectors</a></li>
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
        <div class="d-flex align-items-center mb-3">
            <a href="sectors.php" class="btn-back" title="Back to Sectors"><i class="bi bi-arrow-left fs-5"></i></a>
        </div>
        <h1 class="fw-800 display-5 mb-1" style="letter-spacing:-1.5px;"><?php echo htmlspecialchars($sector['name']); ?></h1>
        <p class="fs-6 mb-0 text-muted"><?php echo $sector['description'] ? htmlspecialchars($sector['description']) : 'Sector details and member list.'; ?></p>
    </div>
</div>

<div class="container pb-5">

    <!-- MEMBERS TABLE -->
    <div class="table-card" data-aos="fade-up" data-aos-delay="100">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h5 class="fw-800 mb-1" style="letter-spacing:-0.5px;"><i class="bi bi-people text-success me-2"></i>Members in <?php echo htmlspecialchars($sector['name']); ?></h5>
                <small class="text-muted"><?php echo $member_count; ?> member<?php echo $member_count != 1 ? 's' : ''; ?> belong to this sector</small>
            </div>
            <!-- Live search -->
            <div class="position-relative" style="max-width:260px; width:100%;">
                <i class="bi bi-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#aaa;font-size:0.85rem;"></i>
                <input type="text" id="memberSearch" class="form-control" placeholder="Search member..."
                    style="padding-left:38px;border-radius:12px;border:1.5px solid #e5e5c0;background:#fdfdf8;font-size:0.9rem;"
                    oninput="filterMembers()">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="border-0">Member</th>
                        <th class="border-0">Contact</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Capital Balance</th>
                        <th class="border-0">Joined</th>
                        <th class="border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody id="memberTableBody">
                    <?php if (empty($members_rows)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people" style="font-size:3rem;opacity:0.1;display:block;margin-bottom:10px;"></i>
                            No members assigned to this sector yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($members_rows as $m):
                        $initials = strtoupper(substr($m['first_name'],0,1) . substr($m['last_name'],0,1));
                    ?>
                    <tr class="member-row" data-name="<?php echo strtolower($m['first_name'] . ' ' . $m['last_name']); ?>">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="member-avatar"><?php echo $initials; ?></div>
                                <div>
                                    <div class="fw-700" style="color:var(--track-dark);"><?php echo htmlspecialchars($m['first_name'] . ' ' . $m['last_name']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($m['email'] ?? '—'); ?></small>
                                </div>
                            </div>
                        </td>
                        <td><small class="text-muted"><?php echo htmlspecialchars($m['contact_number'] ?? '—'); ?></small></td>
                        <td>
                            <?php
                            if ($m['status'] === 'Approved')  echo '<span class="badge-status badge-approved">Approved</span>';
                            elseif ($m['status'] === 'Pending') echo '<span class="badge-status badge-pending">Pending</span>';
                            else echo '<span class="badge-status badge-inactive">Inactive</span>';
                            ?>
                        </td>
                        <td>
                            <span class="fw-700" style="color:<?php echo $m['capital_balance'] > 0 ? '#27ae60' : 'var(--text-muted)'; ?>;">
                                ₱<?php echo number_format($m['capital_balance'], 2); ?>
                            </span>
                        </td>
                        <td><small class="text-muted"><?php echo date('M d, Y', strtotime($m['created_at'])); ?></small></td>
                        <td class="text-end">
                            <a href="../share_capital/capital_history.php?member_id=<?php echo $m['id']; ?>" class="action-icon" title="View Capital History">
                                <i class="bi bi-wallet2"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic' });

    function filterMembers() {
        const q = document.getElementById('memberSearch').value.toLowerCase();
        document.querySelectorAll('.member-row').forEach(row => {
            row.style.display = row.dataset.name.includes(q) ? '' : 'none';
        });
    }
</script>
</body>
</html>
