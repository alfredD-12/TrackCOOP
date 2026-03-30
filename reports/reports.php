<?php
session_start();
include('../auth/db_connect.php');

// Security check: Admin and Bookkeeper only
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Bookkeeper'])) {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

// ── DATA FETCHING (Members Only) ──
$filter_sector = isset($_GET['sector']) ? $_GET['sector'] : '';

// ── GET CURRENT URL FOR MODAL TRACKING ──
$current_view = isset($_GET['view']) ? $_GET['view'] : '';

// ── FETCH MEMBERSHIP DATA ──
$sql = "SELECT first_name, last_name, sector, status, created_at FROM users WHERE role='Member'";
if ($filter_sector) $sql .= " AND sector = '" . mysqli_real_escape_string($conn, $filter_sector) . "'";
$sql .= " ORDER BY created_at DESC";
$report_data = $conn->query($sql);
$report_title = "Membership Masterlist";

// Unified Nav Vars
$full_name = "User";
$q_u = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$q_u->bind_param("i", $_SESSION['user_id']);
$q_u->execute();
if ($u_inf = $q_u->get_result()->fetch_assoc()) $full_name = $u_inf['first_name'].' '.$u_inf['last_name'];
$active_page = 'reports';
$user_role = $_SESSION['role'];
$membership_type = $user_role;

// ── CSV EXPORT LOGIC (Members Only) ──
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="membership_report_'.date('Y-m-d').'.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['First Name', 'Last Name', 'Sector', 'Status', 'Date Joined']);
    while($row = $report_data->fetch_assoc()) fputcsv($output, $row);
    fclose($output);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | TrackCOOP</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <?php if (isset($_GET['view']) && $_GET['view'] === 'modal'): ?>
    <style>
        body { background: white !important; padding: 10px !important; overflow-x: hidden; }
        .report-header { padding: 10px 0 !important; margin-bottom: 10px !important; border-bottom: none !important; }
        .report-header h4 { font-size: 1.4rem !important; }
        .container { max-width: 100% !important; padding: 0 10px !important; }
        .navbar, footer { display: none !important; }
        .report-card { border: none !important; box-shadow: none !important; padding: 20px !important; }
    </style>
    <?php endif; ?>
    
    <style>
        :root {
            --track-green: #20a060;
            --track-bg: #f8fafc;
            --track-beige: #F5F5DC;
            --text-main: #1a202c;
            --text-muted: #718096;
        }

        body {
            background-color: var(--track-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
        }

        .report-header {
            background: white;
            padding: 30px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }

        .report-card {
            background: white; border-radius: 20px; padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .report-table { width: 100%; border-collapse: separate; border-spacing: 0 12px; }
        .report-table th { background: transparent; padding: 12px 20px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; border: none; }
        .report-table td { background: #fdfdfd; padding: 16px 20px; vertical-align: middle; border-top: 1px solid #f0f0f0; border-bottom: 1px solid #f0f0f0; transition: 0.3s; }
        .report-table tr td:first-child { border-left: 1px solid #f0f0f0; border-radius: 12px 0 0 12px; }
        .report-table tr td:last-child { border-right: 1px solid #f0f0f0; border-radius: 0 12px 12px 0; }
        .report-table tr:hover td { background: #fff; border-color: var(--track-green); }

        .btn-action { border-radius: 12px; padding: 10px 20px; font-weight: 600; font-size: 0.875rem; transition: 0.3s; }
        .btn-csv { border: 1.5px solid #edf2f7; background: white; color: var(--text-main); }
        .btn-csv:hover { background: #f8fafc; border-color: #cbd5e0; }
        .btn-print { background: var(--track-green); border: none; font-weight: 700; color: white; box-shadow: 0 4px 12px rgba(32, 160, 96, 0.2); }
        .btn-print:hover { background: #1b8a52; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(32, 160, 96, 0.3); }

        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .report-card { border: none; box-shadow: none; padding: 0; }
            .report-header { border-bottom: 2px solid #000; padding: 20px 0; }
            .report-table th { background: #eee !important; color: black; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<?php 
if (!(isset($_GET['view']) && $_GET['view'] === 'modal')) {
    include('../includes/dashboard_navbar.php'); 
}
?>

<!-- REPORT ACTION BAR -->
<div class="report-header no-print" style="margin-bottom: 20px; padding: 20px 0;">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <h4 class="fw-800 m-0"><i class="bi bi-file-earmark-bar-graph text-success me-2"></i> Report Center</h4>
        </div>
        <div class="d-flex gap-2">
            <a href="?export=csv" class="btn btn-action btn-csv"><i class="bi bi-file-earmark-spreadsheet me-1"></i> CSV</a>
            <button onclick="window.print()" class="btn btn-action btn-print"><i class="bi bi-printer me-1"></i> Print</button>
        </div>
    </div>
</div>

<div class="container pb-5">

    <div class="report-card">
        <div class="text-center mb-5">
            <div class="h6 text-success fw-bold text-uppercase mb-2">Nasugbu Farmers & Fisherfolks Agriculture Cooperative</div>
            <h2 class="fw-800">Membership Masterlist</h2>
            <div class="text-muted small">Generated on <?php echo date('F d, Y \a\t g:i A'); ?></div>
        </div>

        <div class="table-responsive">
            <!-- FIXED MEMBERSHIP TABLE -->
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Member Name</th>
                        <th>Sector</th>
                        <th>Status</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($report_data && $report_data->num_rows > 0): ?>
                        <?php while($row = $report_data->fetch_assoc()): ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?></td>
                                <td><span class="badge border text-dark bg-light"><?php echo htmlspecialchars($row['sector'] ?: 'General'); ?></span></td>
                                <td><span class="badge <?php echo ($row['status']=='Approved') ? 'bg-success' : 'bg-warning'; ?>"><?php echo $row['status']; ?></span></td>
                                <td class="text-muted small"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center py-5 text-muted">No records found for this report.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-5 pt-4 border-top text-center text-muted small">
            --- End of Official Report ---
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include('../includes/footer.php'); ?>
</body>
</html>
