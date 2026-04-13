<?php
session_start();
include('../auth/db_connect.php');

// Security check: Admin and Bookkeeper only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

// ── DATA FETCHING (Members Only) ──
$filter_sector = isset($_GET['sector']) ? $_GET['sector'] : '';

// ── FETCH MEMBERSHIP DATA ── (Static for demo)
$static_report_data = [
    ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'sector' => 'Rice', 'status' => 'Approved', 'created_at' => '2024-01-20', 'balance' => 5000.00],
    ['first_name' => 'Maria', 'last_name' => 'Santos', 'sector' => 'Corn', 'status' => 'Approved', 'created_at' => '2024-01-25', 'balance' => 7500.50],
    ['first_name' => 'Pedro', 'last_name' => 'Garcia', 'sector' => 'Fishery', 'status' => 'Pending', 'created_at' => '2024-02-15', 'balance' => 0.00],
    ['first_name' => 'Rosa', 'last_name' => 'Lopez', 'sector' => 'Livestock', 'status' => 'Approved', 'created_at' => '2024-02-20', 'balance' => 12000.00],
    ['first_name' => 'Antonio', 'last_name' => 'Luna', 'sector' => 'Rice', 'status' => 'Approved', 'created_at' => '2024-03-01', 'balance' => 3200.75],
    ['first_name' => 'Jose', 'last_name' => 'Rizal', 'sector' => 'High Value Crops', 'status' => 'Approved', 'created_at' => '2024-03-05', 'balance' => 15000.00],
    ['first_name' => 'Andres', 'last_name' => 'Bonifacio', 'sector' => 'Corn', 'status' => 'Pending', 'created_at' => '2024-03-10', 'balance' => 0.00],
    ['first_name' => 'Apolinario', 'last_name' => 'Mabini', 'sector' => 'Fishery', 'status' => 'Approved', 'created_at' => '2024-03-12', 'balance' => 8400.25],
    ['first_name' => 'Emilio', 'last_name' => 'Aguinaldo', 'sector' => 'Rice', 'status' => 'Approved', 'created_at' => '2024-03-15', 'balance' => 6700.00],
    ['first_name' => 'Gabriela', 'last_name' => 'Silang', 'sector' => 'Livestock', 'status' => 'Approved', 'created_at' => '2024-03-18', 'balance' => 9100.20],
    ['first_name' => 'Melchora', 'last_name' => 'Aquino', 'sector' => 'High Value Crops', 'status' => 'Pending', 'created_at' => '2024-03-20', 'balance' => 0.00],
    ['first_name' => 'Marcelo', 'last_name' => 'del Pilar', 'sector' => 'Rice', 'status' => 'Approved', 'created_at' => '2024-03-22', 'balance' => 4500.00],
    ['first_name' => 'Juan', 'last_name' => 'Luna', 'sector' => 'Corn', 'status' => 'Approved', 'created_at' => '2024-03-25', 'balance' => 7800.50],
    ['first_name' => 'Gregorio', 'last_name' => 'del Pilar', 'sector' => 'Fishery', 'status' => 'Approved', 'created_at' => '2024-03-28', 'balance' => 5600.75],
    ['first_name' => 'Teresa', 'last_name' => 'Magbanua', 'sector' => 'Livestock', 'status' => 'Approved', 'created_at' => '2024-03-30', 'balance' => 11200.00],
    ['first_name' => 'Epifanio', 'last_name' => 'delos Santos', 'sector' => 'Rice', 'status' => 'Pending', 'created_at' => '2024-04-01', 'balance' => 0.00],
    ['first_name' => 'Diego', 'last_name' => 'Silang', 'sector' => 'Corn', 'status' => 'Approved', 'created_at' => '2024-04-03', 'balance' => 6300.20],
    ['first_name' => 'Felipe', 'last_name' => 'Agoncillo', 'sector' => 'Fishery', 'status' => 'Approved', 'created_at' => '2024-04-05', 'balance' => 4900.00],
    ['first_name' => 'Lope', 'last_name' => 'K. Santos', 'sector' => 'Livestock', 'status' => 'Approved', 'created_at' => '2024-04-08', 'balance' => 8700.50],
    ['first_name' => 'Claro', 'last_name' => 'M. Recto', 'sector' => 'High Value Crops', 'status' => 'Approved', 'created_at' => '2024-04-10', 'balance' => 10500.25],
];

if ($filter_sector && $filter_sector !== 'All') {
    $report_data = array_filter($static_report_data, function($r) use ($filter_sector) {
        return $r['sector'] === $filter_sector;
    });
} else {
    $report_data = $static_report_data;
}

// ── CALCULATE GRAND TOTAL ──
$total_combined_balance = 0;
foreach ($report_data as $row) {
    $total_combined_balance += $row['balance'];
}

// Unified Nav Vars
$full_name = isset($_SESSION['fname']) ? $_SESSION['fname'] : "Administrator";
$active_page = 'reports';
$user_role = $_SESSION['role'];
$membership_type = $user_role;

// ── CSV EXPORT LOGIC ──
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="membership_report_'.date('Y-m-d').'.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['First Name', 'Last Name', 'Sector', 'Status', 'Balance', 'Date Joined']);
    foreach($report_data as $row) fputcsv($output, [
        $row['first_name'],
        $row['last_name'],
        $row['sector'],
        $row['status'],
        number_format($row['balance'], 2),
        $row['created_at']
    ]);
    fclose($output);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Insights | TRACKCOOP</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    
    <style>
        :root {
            --track-green: #27ae60;
            --track-bg: #f8fafc;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body {
            background-color: var(--track-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
        }

        /* Premium Header Section */
        .report-page-header {
            padding: 40px 0 20px;
        }

        .report-title-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #e9f5ee;
            color: #27ae60;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        /* Filter Pills matching Gallery design */
        .filter-container {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .filter-pill {
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 700;
            border: 1.5px solid #e2e8f0;
            background: white;
            color: #64748b;
            cursor: pointer;
            transition: all 0.25s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
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
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
        }

        /* Action Buttons */
        .btn-report-action {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
        }

        .btn-csv {
            background: #1e293b;
            color: white;
            box-shadow: 0 4px 12px rgba(30, 41, 59, 0.2);
        }

        .btn-csv:hover {
            background: #334155;
            transform: translateY(-2px);
            color: white;
        }

        .btn-print {
            background: #27ae60;
            color: white;
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.25);
        }

        .btn-print:hover {
            background: #219150;
            transform: translateY(-2px);
            color: white;
        }

        /* Report Table Overrides */
        .table-elite thead th {
            color: #64748b;
            font-size: 0.7rem;
            letter-spacing: 1.2px;
        }

        .sector-badge {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 0.7rem;
            padding: 6px 12px;
            border-radius: 8px;
            text-transform: uppercase;
        }

        .status-pill {
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        .status-approved { background: #e9f5ee; color: #27ae60; }
        .status-pending { background: #fffbeb; color: #d97706; }

        @media print {
            .no-print { display: none !important; }
            .main-content-wrapper { margin: 0 !important; padding: 0 !important; }
            .table-card { border: none !important; box-shadow: none !important; padding: 0 !important; }
            body { background: white !important; }
            .filter-container { display: none !important; }
            .report-seal-container { margin-bottom: 1.5rem; }
        }
        .main-content-wrapper { overflow-x: hidden !important; }
    </style>
</head>
<body>

<div class="sidebar-layout">
    <?php include('../includes/dashboard_sidebar.php'); ?>
    
    <div class="main-content-wrapper">
        <div class="container-fluid px-4">
            

            <div class="table-card mt-4 scroll-reveal">
                
                <div class="mb-5 mt-4">
                    <div class="text-center mb-4">
                        <div class="h6 text-success fw-bold text-uppercase mb-2" style="letter-spacing: 2px;">Nasugbu Farmers & Fisherfolks Agriculture Cooperative</div>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <!-- Left Spacer for centering -->
                        <div class="d-none d-lg-block" style="flex: 1;"></div>
                        
                        <!-- Centered Content -->
                        <div class="text-center" style="flex: 2;">
                            <h2 class="fw-800 mb-0" style="font-size: 2.5rem; letter-spacing: -1.5px; color: #1e293b;">Membership Masterlist</h2>
                            <div class="text-muted fw-600 mt-2 d-flex align-items-center justify-content-center gap-2">
                                <span class="badge bg-light text-dark rounded-pill border px-3 py-2">
                                    <i class="bi bi-clock-history text-success me-1"></i> Generated on <?php echo date('F d, Y \a\t g:i A'); ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Right-aligned Actions -->
                        <div class="no-print text-end" style="flex: 1;">
                            <button onclick="window.print()" class="btn-report-action btn-print shadow-sm">
                                <i class="bi bi-printer"></i> Print Report
                            </button>
                        </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-elite align-middle">
                        <thead>
                            <tr>
                                <th style="width: 30%;">MEMBER NAME</th>
                                <th style="width: 20%;">AGRICULTURE SECTOR</th>
                                <th style="width: 20%;">MEMBERSHIP STATUS</th>
                                <th style="width: 15%;">TOTAL BALANCE</th>
                                <th style="width: 15%;">REGISTRATION DATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($report_data): ?>
                                <?php foreach($report_data as $row): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="fw-bold" style="color: #1e293b; font-size: 1.05rem;">
                                                    <?php echo htmlspecialchars($row['first_name'].' '.$row['last_name']); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="sector-badge">
                                                <?php echo htmlspecialchars($row['sector'] ?: 'General'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-pill <?php echo ($row['status']=='Approved') ? 'status-approved' : 'status-pending'; ?>">
                                                <i class="bi <?php echo ($row['status']=='Approved') ? 'bi-check-circle-fill' : 'bi-hourglass-split'; ?> me-1"></i>
                                                <?php echo $row['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold" style="color: #27ae60; font-size: 0.95rem;">
                                                ₱<?php echo number_format($row['balance'], 2); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted fw-600" style="font-size: 0.85rem;">
                                                <i class="bi bi-calendar3 me-1"></i> <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <!-- GRAND TOTAL ROW -->
                                <tr class="bg-light" style="border-top: 2px solid #e2e8f0;">
                                    <td colspan="3" class="text-end fw-800 py-3" style="color: #64748b; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px;">Grand Capital Total</td>
                                    <td class="fw-900 py-3" style="color: #1e293b; font-size: 1rem; border-left: 2px solid #eee;">
                                        ₱<?php echo number_format($total_combined_balance, 2); ?>
                                    </td>
                                    <td></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="bi bi-inbox text-muted opacity-25" style="font-size: 4rem;"></i>
                                        <h5 class="fw-700 text-muted mt-3">No matching records found</h5>
                                        <p class="text-muted small">Try selecting a different sector or clearing the filters.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 pt-4 border-top text-center text-muted small fw-600" style="letter-spacing: 1px;">
                    --- END OF OFFICIAL MEMBERSHIP REPORT ---
                </div>
            </div>
            
            <p class="text-center text-muted mt-4 small fw-500 no-print">
                &copy; <?php echo date('Y'); ?> Nasugbu Farmers & Fisherfolks Agriculture Cooperative. All Rights Reserved.
            </p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
