<?php
session_start();
include('../auth/db_connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Only Admin can access documents
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
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

// Static documents data for display
$static_documents = [
    ['id' => 1, 'name' => 'Bylaws and Constitution', 'type' => 'PDF', 'date_uploaded' => '2024-01-10', 'file_size' => '245 KB'],
    ['id' => 2, 'name' => 'Member Handbook 2024', 'type' => 'PDF', 'date_uploaded' => '2024-02-01', 'file_size' => '512 KB'],
    ['id' => 3, 'name' => 'Financial Report 2023', 'type' => 'PDF', 'date_uploaded' => '2024-03-15', 'file_size' => '870 KB'],
    ['id' => 4, 'name' => 'Meeting Minutes Q1', 'type' => 'DOCX', 'date_uploaded' => '2024-04-02', 'file_size' => '156 KB'],
    ['id' => 5, 'name' => 'Annual General Assembly Notes', 'type' => 'PDF', 'date_uploaded' => '2024-05-20', 'file_size' => '1.2 MB'],
    ['id' => 6, 'name' => 'New Member Application Form', 'type' => 'DOCX', 'date_uploaded' => '2024-06-12', 'file_size' => '88 KB'],
    ['id' => 7, 'name' => 'Audit Report Q2 2024', 'type' => 'PDF', 'date_uploaded' => '2024-07-05', 'file_size' => '950 KB'],
    ['id' => 8, 'name' => 'Official Seal Certificate', 'type' => 'PDF', 'date_uploaded' => '2024-07-28', 'file_size' => '2.1 MB'],
    ['id' => 9, 'name' => 'Sector Management Guidelines', 'type' => 'PDF', 'date_uploaded' => '2024-08-14', 'file_size' => '340 KB'],
    ['id' => 10, 'name' => 'Loan Application Template v2', 'type' => 'DOCX', 'date_uploaded' => '2024-09-01', 'file_size' => '120 KB'],
    ['id' => 11, 'name' => 'Board Resolution 2024-08', 'type' => 'PDF', 'date_uploaded' => '2024-09-18', 'file_size' => '195 KB'],
    ['id' => 12, 'name' => 'Insurance Policy Documents', 'type' => 'PDF', 'date_uploaded' => '2024-10-02', 'file_size' => '3.5 MB'],
    ['id' => 13, 'name' => 'Registration Masterlist 2024', 'type' => 'XLSX', 'date_uploaded' => '2024-10-15', 'file_size' => '420 KB'],
    ['id' => 14, 'name' => 'Cooperative Training Manual', 'type' => 'PDF', 'date_uploaded' => '2024-11-01', 'file_size' => '5.8 MB'],
    ['id' => 15, 'name' => 'Emergency Contact Directory', 'type' => 'PDF', 'date_uploaded' => '2024-11-05', 'file_size' => '120 KB'],
    ['id' => 16, 'name' => 'Equipment Inventory 2024', 'type' => 'XLSX', 'date_uploaded' => '2024-11-10', 'file_size' => '85 KB'],
    ['id' => 17, 'name' => 'Irrigation Project Proposal', 'type' => 'DOCX', 'date_uploaded' => '2024-11-15', 'file_size' => '210 KB'],
    ['id' => 18, 'name' => 'Monthly Sales Report - Oct', 'type' => 'PDF', 'date_uploaded' => '2024-11-18', 'file_size' => '650 KB'],
    ['id' => 19, 'name' => 'Member Benefits Portfolio', 'type' => 'PDF', 'date_uploaded' => '2024-11-20', 'file_size' => '2.4 MB'],
    ['id' => 20, 'name' => 'Health and Safety Guidelines', 'type' => 'PDF', 'date_uploaded' => '2024-11-22', 'file_size' => '1.1 MB'],
    ['id' => 21, 'name' => 'Governance Reform Document', 'type' => 'DOCX', 'date_uploaded' => '2024-11-25', 'file_size' => '95 KB'],
    ['id' => 22, 'name' => 'Strategic Plan 2025-2030', 'type' => 'PDF', 'date_uploaded' => '2024-11-28', 'file_size' => '4.2 MB'],
    ['id' => 23, 'name' => 'Coop Museum History Photo', 'type' => 'JPG', 'date_uploaded' => '2024-12-01', 'file_size' => '8.5 MB'],
    ['id' => 24, 'name' => 'Audit Response Letter', 'type' => 'PDF', 'date_uploaded' => '2024-12-05', 'file_size' => '130 KB'],
];

// Documents are static for demo
$all_documents = $static_documents;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents | TRACKCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            background-color: var(--track-bg);
            overflow-x: hidden;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* --- NAVBAR --- */
        .navbar {
            background-color: rgba(22, 74, 54, 0.95) !important;
            backdrop-filter: blur(10px);
            padding: 15px 0;
            border-bottom: 1px solid rgba(22, 74, 54, 0.3);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            animation: fadeInUpCustom 0.8s ease-out;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -0.8px;
            color: #ffffff !important;
        }

        .navbar-brand span {
            color: #20a060;
        }

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
        .navbar-nav .nav-link.active::after {
            width: 100%;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #20a060 !important;
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
            animation: fadeInUpCustom 0.8s ease-out 0.3s both;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(220, 38, 38, 0.6);
        }

        /* --- HEADER --- */
        .admin-header {
            display: none; /* Obsolete now */
        }

        /* --- TOOLBAR --- */
        .doc-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }


        .doc-avatar {
            width: 44px; height: 44px; border-radius: 12px;
            background: var(--track-green-light);
            display: flex; align-items: center; justify-content: center;
            color: var(--track-green); font-size: 1.2rem;
            box-shadow: 0 4px 10px rgba(32, 160, 96, 0.1);
        }


        /* --- DOC CARDS --- */


        /* --- ACTION BUTTONS --- */
        .doc-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        .action-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition-smooth);
            text-decoration: none;
            font-size: 16px;
        }

        .action-btn.view { color: #3b82f6; border-color: rgba(59, 130, 246, 0.3); background: rgba(59, 130, 246, 0.05); }
        .action-btn.view:hover { background: #3b82f6; color: white; border-color: #3b82f6; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }

        .action-btn.edit { color: #f59e0b; border-color: rgba(245, 158, 11, 0.3); background: rgba(245, 158, 11, 0.05); }
        .action-btn.edit:hover { background: #f59e0b; color: white; border-color: #f59e0b; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }

        .action-btn.delete { color: #ef4444; border-color: rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.05); }
        .action-btn.delete:hover { background: #ef4444; color: white; border-color: #ef4444; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }

        /* --- UPLOAD BUTTON --- */
        .btn-upload {
            background: #20a060;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0 30px;
            height: 52px;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 4px 14px rgba(32, 160, 96, 0.3);
            transition: var(--transition-smooth);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .btn-upload:hover {
            background: #1a8548;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.4);
            color: white;
        }

        /* --- EMPTY STATE --- */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state-icon {
            font-size: 80px;
            color: var(--track-green);
            opacity: 0.1;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: var(--track-dark);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .footer-track {
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .doc-card {
                flex-direction: column;
            }

            .doc-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-upload {
                width: 100%;
                justify-content: center;
            }
        }

        /* --- MODAL STYLES --- */
        .modal-content {
            border: none;
            border-radius: 30px !important;
            overflow: hidden !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            background: #ffffff;
        }

        .modal-header {
            border-bottom: 2px solid rgba(22,74,54,0.3);
            padding: 24px 28px;
            background: rgba(22, 74, 54, 0.95);
            color: white;
        }

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
            background-color: #dc2626 !important; transform: scale(1.1) !important; filter: none !important;
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.5) !important;
        }

        .modal-title {
            font-weight: 700;
            color: white;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .modal-title i {
            color: var(--track-green);
            font-size: 1.8rem;
        }

        .modal-body {
            padding: 28px;
        }

        .modal-footer {
            border-top: 2px solid rgba(22,74,54,0.3);
            padding: 20px 28px;
            background: rgba(22, 74, 54, 0.95);
            color: white;
        }

        /* Form Styles */
        .modal .form-label {
            font-weight: 600;
            color: var(--track-dark);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .modal .form-control,
        .modal .form-select {
            border: 2px solid var(--track-beige);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: var(--transition-smooth);
            background: #ffffff;
        }

        .modal .form-control:focus,
        .modal .form-select:focus {
            border-color: var(--track-green);
            box-shadow: 0 0 0 4px rgba(32, 160, 96, 0.15);
            background: #ffffff;
        }

        /* Modal Buttons */
        .btn-modal-submit {
            background: #20a060;
            color: white;
            border: none;
            border-radius: 50px !important;
            padding: 0 35px !important;
            height: 50px !important;
            font-weight: 700 !important;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-modal-submit:hover {
            background: #1a8548;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.3);
            color: white;
        }

        .btn-modal-cancel {
            background: #ef4444 !important;
            color: white !important;
            border: none !important;
            border-radius: 50px !important;
            padding: 0 30px !important;
            height: 50px !important;
            font-weight: 700 !important;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-modal-cancel:hover {
            background: #dc2626 !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.3);
        }

        /* File Upload Area */
        .upload-area {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition-smooth);
            background: linear-gradient(135deg, rgba(245, 245, 220, 0.3) 0%, rgba(255, 255, 255, 0.5) 100%);
        }

        .upload-area:hover {
            background: linear-gradient(135deg, var(--track-beige) 0%, rgba(255, 255, 255, 0.7) 100%);
            border-color: var(--track-green);
        }

        .upload-area i {
            font-size: 3rem;
            color: var(--track-green);
            margin-bottom: 12px;
        }

        .upload-area-text {
            color: var(--track-dark);
            font-size: 0.95rem;
        }

        .upload-area-text small {
            color: var(--text-muted);
        }

        .upload-area-highlight {
            color: var(--track-green);
            font-weight: 700;
        }
    </style>
    <link rel="stylesheet" href="../includes/footer.css">
</head>
<div class="sidebar-layout">
    <?php 
        $active_page = 'documents';
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
                <input type="text" id="docSearch" class="form-control border-0 shadow-sm" placeholder="Search" style="background: #f1f5f9; border-radius: 10px; padding-left: 45px !important;">
            </div>
            
            <button class="btn btn-upload-gold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal" style="height: 50px; padding: 0 25px !important; border-radius: 12px !important; font-weight: 700;">
                <i class="bi bi-upload"></i> Upload
            </button>
            
            <!-- Notification bell removed by user request -->
        </div>
    </div>

    <!-- DRAG AND DROP ZONE -->
    <div class="dropzone-elite animate-fade-in mb-4" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal" style="cursor: pointer;">
        <div class="dropzone-icon-wrapper">
            <i class="bi bi-cloud-arrow-up"></i>
        </div>
        <div class="dropzone-text">
            <span>Drag & Drop</span> your folders and documents here (PNG, JPG, PDF)
        </div>
    </div>
</div>

    <!-- DOCUMENTS TABLE -->
    <div class="table-card fade-in-up table-responsive" style="overflow-x: auto; padding: 2px;">
        <?php if (count($all_documents) > 0): ?>
        <table class="table table-elite align-middle">
            <thead>
                <tr>
                    <th style="min-width: 250px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            NAME
                        </div>
                    </th>
                    <th style="min-width: 160px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            SIGNING STATUS
                        </div>
                    </th>
                    <th style="min-width: 160px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            DATE CREATED
                        </div>
                    </th>
                    <th style="min-width: 220px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            CREATED BY
                        </div>
                    </th>
                    <th style="min-width: 100px; white-space: nowrap;">
                        <div class="d-flex align-items-center">
                            SIZE
                        </div>
                    </th>
                    <th class="text-end"></th>
                </tr>
            </thead>
            <tbody id="docTableBody">
                <?php foreach ($all_documents as $row): 
                    $is_signed = ($row['id'] % 2 !== 0);
                    $icon_class = ($row['type'] === 'PDF') ? 'text-danger' : 'text-primary';
                    $icon = ($row['type'] === 'PDF') ? 'bi-file-pdf-fill' : 'bi-file-word-fill';
                ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi <?php echo $icon; ?> <?php echo $icon_class; ?> fs-4"></i>
                            <span class="fw-700 text-dark"><?php echo htmlspecialchars($row['name']); ?>.<?php echo strtolower($row['type']); ?></span>
                        </div>
                    </td>
                    <td>
                        <?php if ($is_signed): ?>
                            <span class="badge-status badge-signed"><i class="bi bi-check-circle-fill"></i> SIGNED</span>
                        <?php else: ?>
                            <span class="badge-status badge-pending"><i class="bi bi-exclamation-triangle-fill"></i> PENDING</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small fw-600"><?php echo date('M d, Y', strtotime($row['date_uploaded'])); ?></td>
                    <td>
                        <?php 
                            $is_admin = ($row['id'] % 2 !== 0);
                            $role_name = $is_admin ? 'Administrator' : 'Bookkeeper';
                            $role_email = $is_admin ? 'admin@trackcoop.com' : 'bookkeeper@trackcoop.net';
                            $role_initial = $is_admin ? 'A' : 'B';
                            $role_bg = '#20a060'; // Uniform Brand Green for both roles
                        ?>
                        <div class="user-table-item">
                            <div class="user-table-avatar-initials" style="background: <?php echo $role_bg; ?>; color: #ffffff;"><?php echo $role_initial; ?></div>
                            <div class="user-table-info">
                                <span class="user-table-name"><?php echo $role_name; ?></span>
                                <span class="user-table-email"><?php echo $role_email; ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted small fw-600"><?php echo $row['file_size']; ?></td>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end align-items-center">
                            <button class="btn-doc-action btn-action-view" title="View Document">
                                <i class="bi bi-eye"></i>
                            </button>
                            <?php if (!$is_signed): ?>
                                <button class="btn-doc-action btn-action-approve" title="Approve">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                            <?php endif; ?>
                            <button class="btn-doc-action btn-action-view" title="Download"><i class="bi bi-cloud-download"></i></button>
                            <button class="btn-doc-action btn-action-delete" title="Delete"><i class="bi bi-trash"></i></button>
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
            <i class="bi bi-file-earmark-text empty-state-icon"></i>
            <h3>No Documents Yet</h3>
            <p>Upload your first cooperative document to get started.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- VIEW DOCUMENT MODAL -->
<div class="modal fade" id="viewDocumentModal" tabindex="-1" aria-labelledby="viewDocumentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="viewDocumentLabel">
                    <i class="bi bi-file-earmark"></i>
                    <span id="viewDocName">Document</span>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">File Name</label>
                        <div class="form-control" id="viewFileName" style="background: #f8fafc; cursor: default;"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">File Size</label>
                        <div class="form-control" style="background: #f8fafc; cursor: default;">2.4 MB</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category</label>
                        <div class="form-control" style="background: #f8fafc; cursor: default;">Records</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Upload Date</label>
                        <div class="form-control" style="background: #f8fafc; cursor: default;">Oct 24, 2024</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <div class="form-control" style="background: #f8fafc; cursor: default;">
                        <span class="badge" style="background: var(--track-green);">✓ Verified</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" rows="4" readonly style="background: #f8fafc;">Member registry document for 2024 - Contains all registered member information and details.</textarea>
                </div>
            </div>
                <div class="text-end mt-4">
                    <button type="button" class="btn-modal-cancel me-2" data-bs-dismiss="modal">Close</button>
                    <a href="#" class="btn-modal-submit" style="text-decoration: none;">
                        <i class="bi bi-download me-2"></i>Download
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- EDIT DOCUMENT MODAL -->
<div class="modal fade" id="editDocumentModal" tabindex="-1" aria-labelledby="editDocumentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="editDocumentLabel">
                    <i class="bi bi-pencil-square"></i>
                    <span id="editDocName">Edit Document</span>
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editDocumentName" class="form-label">Document Name</label>
                        <input type="text" class="form-control" id="editDocumentName" placeholder="Enter document name">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="editCategory" class="form-label">Category</label>
                            <select class="form-select" id="editCategory">
                                <option selected>Records</option>
                                <option>Finance</option>
                                <option>Compliance</option>
                                <option>Reports</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus">
                                <option>✓ Verified</option>
                                <option selected>⏳ Pending Review</option>
                                <option>⚠ Review Required</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" rows="4" placeholder="Enter document description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Document Tags</label>
                        <input type="text" class="form-control" placeholder="Add tags separated by commas">
                    </div>
                </div>
                    <div class="text-end mt-4">
                        <button type="button" class="btn-modal-cancel me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-modal-submit">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- UPLOAD DOCUMENT MODAL -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="uploadDocumentLabel" style="color: #20a060 !important;">Upload New Document</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label mb-3">Select File</label>
                        <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                            <i class="bi bi-cloud-arrow-up"></i>
                            <div class="upload-area-text">
                                <div class="upload-area-highlight">Click to upload</div>
                                <div>or drag and drop</div>
                                <small style="color: var(--text-muted);">PDF, DOC, XLSX, JPG (Max: 10MB)</small>
                            </div>
                            <input type="file" id="fileInput" style="display: none;">
                        </div>
                        <div id="fileName" class="mt-2" style="color: var(--track-green); font-weight: 600;"></div>
                    </div>
                    <div class="mb-3">
                        <label for="uploadDocName" class="form-label">Document Name</label>
                        <input type="text" class="form-control" id="uploadDocName" placeholder="Enter document name">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="uploadCategory" class="form-label">Category</label>
                            <select class="form-select" id="uploadCategory">
                                <option selected>Records</option>
                                <option>Finance</option>
                                <option>Compliance</option>
                                <option>Reports</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="uploadStatus" class="form-label">Status</label>
                            <select class="form-select" id="uploadStatus">
                                <option selected>⏳ Pending Review</option>
                                <option>✓ Verified</option>
                                <option>⚠ Review Required</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="uploadDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="uploadDescription" rows="3" placeholder="Enter document description"></textarea>
                    </div>
                    <div class="mt-4 text-end pe-3 pb-2">
                        <button type="button" class="btn-modal-cancel me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-modal-submit">Upload Document</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Handle View Document Modal
    const viewDocumentModal = document.getElementById('viewDocumentModal');
    viewDocumentModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const docName = button.getAttribute('data-doc-name');
        document.getElementById('viewDocName').textContent = docName;
        document.getElementById('viewFileName').textContent = docName;
    });

    // Handle Edit Document Modal
    const editDocumentModal = document.getElementById('editDocumentModal');
    editDocumentModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const docName = button.getAttribute('data-doc-name');
        document.getElementById('editDocName').textContent = 'Edit - ' + docName;
        document.getElementById('editDocumentName').value = docName;
    });

    // --- FUNCTIONAL PAGINATION LOGIC ---
    let currentPage = 1;
    let rowsPerPage = 5;

    function updateTablePagination() {
        const term = document.getElementById('docSearch').value.toLowerCase().trim();
        const allRows = Array.from(document.querySelectorAll('#docTableBody tr:not(.empty-state)'));
        
        // Filter rows based on search
        const filteredRows = allRows.filter(row => {
            const nameEl = row.querySelector('.fw-700.text-dark');
            const statusEl = row.querySelector('.badge-status');
            const name = nameEl ? nameEl.textContent.toLowerCase() : '';
            const status = statusEl ? statusEl.textContent.toLowerCase() : '';
            return name.includes(term) || status.includes(term);
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
        } else {
            infoEl.textContent = `${start + 1}-${end} of ${totalItems}`;
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
        const term = document.getElementById('docSearch').value.toLowerCase().trim();
        const allRows = document.querySelectorAll('#docTableBody tr:not(.empty-state)');
        const filteredCount = Array.from(allRows).filter(row => {
            const name = row.querySelector('.fw-700.text-dark')?.textContent.toLowerCase() || '';
            const status = row.querySelector('.badge-status')?.textContent.toLowerCase() || '';
            return name.includes(term) || status.includes(term);
        }).length;

        if (currentPage < Math.ceil(filteredCount / rowsPerPage)) {
            currentPage++;
            updateTablePagination();
        }
    });

    // Integrated Search + Pagination
    function performSearch() {
        currentPage = 1; // Reset to page 1 on search
        updateTablePagination();
    }
    document.getElementById('docSearch').addEventListener('input', performSearch);

    // Initial load - ensures 5 per page on refresh
    updateTablePagination();

    // Handle File Upload Select
    document.getElementById('fileInput').addEventListener('change', function(event) {
        const fileName = event.target.files[0]?.name || '';
        if (fileName) {
            document.getElementById('fileName').textContent = '✓ Selected: ' + fileName;
            document.getElementById('uploadDocName').value = fileName.split('.')[0];
        }
    });

    // Handle Dropzone Logic
    const dropzone = document.querySelector('.dropzone-elite');
    ['dragenter', 'dragover'].forEach(name => {
        dropzone.addEventListener(name, (e) => { e.preventDefault(); dropzone.style.background = '#f1f5f9'; dropzone.style.borderColor = '#20a060'; });
    });
    ['dragleave', 'drop'].forEach(name => {
        dropzone.addEventListener(name, (e) => { e.preventDefault(); dropzone.style.background = '#f8fafc'; dropzone.style.borderColor = '#cbd5e1'; });
    });
    dropzone.addEventListener('drop', (e) => {
        const files = e.dataTransfer.files;
        document.getElementById('fileInput').files = files;
        document.getElementById('fileInput').dispatchEvent(new Event('change'));
    });

    // Handle Form Submissions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const modalEl = this.closest('.modal');
            const formType = modalEl.id;
            let actionText = (formType === 'editDocumentModal') ? 'updated' : 'uploaded';
            
            const alertPlaceholder = document.createElement('div');
            alertPlaceholder.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 animate-scale-in" role="alert" style="border-radius: 12px; background: #eefdf5; color: #27ae60; font-weight: 600;">
                    <i class="bi bi-check-circle-fill me-2"></i> Document ${actionText} successfully!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.querySelector('.container-fluid').prepend(alertPlaceholder);
            bootstrap.Modal.getInstance(modalEl).hide();
            document.getElementById('docSearch').value = '';
            performSearch();
        });
    });

    // Handle Download Mockup
    document.querySelectorAll('.bi-cloud-download').forEach(icon => {
        icon.parentElement.addEventListener('click', function(e) {
            e.stopPropagation();
            const name = this.closest('tr').querySelector('.fw-700.text-dark').textContent;
            const toast = document.createElement('div');
            toast.style = "position: fixed; bottom: 300px; right: 30px; z-index: 2000; background: #1a1f26; color: white; padding: 16px 24px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 12px; animation: fadeInUpCustom 0.5s ease;";
            toast.innerHTML = `<i class="bi bi-cloud-download text-success fs-4"></i> <div><div class="fw-bold">Downloading...</div><div class="small opacity-75">${name}</div></div>`;
            document.body.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(20px)'; toast.style.transition = 'all 0.5s ease'; setTimeout(() => toast.remove(), 500); }, 3000);
        });
    });
</script>

    </div> <!-- .main-content-wrapper -->
</div> <!-- .sidebar-layout -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>