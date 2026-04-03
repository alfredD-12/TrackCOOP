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
@$query = "SELECT first_name, last_name FROM users WHERE id = ?";
@$stmt = $conn->prepare($query);
if ($stmt) {
    @$stmt->bind_param("i", $user_id);
    @$stmt->execute();
    @$result = $stmt->get_result();
    if ($user = @$result->fetch_assoc()) {
        $full_name = $user['first_name'] . " " . $user['last_name'];
    }
}

// Static documents data for display
$static_documents = [
    ['id' => 1, 'name' => 'Bylaws and Constitution', 'type' => 'PDF', 'date_uploaded' => '2024-01-10', 'file_size' => '245 KB'],
    ['id' => 2, 'name' => 'Member Handbook 2024', 'type' => 'PDF', 'date_uploaded' => '2024-02-01', 'file_size' => '512 KB'],
    ['id' => 3, 'name' => 'Financial Report 2023', 'type' => 'PDF', 'date_uploaded' => '2024-03-15', 'file_size' => '870 KB'],
    ['id' => 4, 'name' => 'Meeting Minutes Q1', 'type' => 'DOCX', 'date_uploaded' => '2024-04-02', 'file_size' => '156 KB'],
];

// Documents are static for demo
$all_documents = $static_documents;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents | TrackCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

        @keyframes fadeInUpCustom {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        body {
            background-color: var(--track-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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

        /* --- TOOLBAR --- */
        .doc-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .search-wrapper {
            position: relative;
            flex: 1;
            max-width: 500px;
        }

        .search-input-group {
            position: relative;
            flex: 1;
        }

        .search-input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--track-green);
            font-size: 1.1rem;
            z-index: 5;
        }

        .search-input {
            width: 100%;
            padding: 0 20px 0 45px;
            height: 52px;
            border-radius: 14px;
            border: 2px solid var(--track-beige);
            background: white;
            transition: var(--transition-smooth);
            font-weight: 500;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--track-green);
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.1);
        }

        .btn-search-trigger {
            background: var(--track-green);
            color: white;
            border: none;
            border-radius: 14px;
            padding: 0 30px;
            height: 52px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition-smooth);
            box-shadow: 0 4px 14px rgba(32, 160, 96, 0.2);
            white-space: nowrap;
        }

        .btn-search-trigger:hover {
            background: #1a8548;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.3);
            color: white;
        }

        /* --- DOC CARDS --- */
        .doc-card {
            border: 2px solid var(--track-beige);
            border-radius: 20px;
            background: #ffffff;
            padding: 24px;
            transition: var(--transition-smooth);
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.08);
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .doc-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08);
            border-color: var(--track-green);
        }

        .doc-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            background: var(--track-green-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--track-green);
            font-size: 28px;
            flex-shrink: 0;
        }

        .doc-info {
            flex: 1;
        }

        .doc-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--track-dark);
            margin-bottom: 8px;
        }

        .doc-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .doc-meta-item {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .doc-meta-item strong {
            color: var(--track-dark);
        }

        .doc-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .badge-custom {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-records {
            background: #e9f5ee;
            color: var(--track-green);
        }

        .badge-finance {
            background: var(--track-beige);
            color: var(--track-dark);
        }

        .badge-verified {
            background: #e9f5ee;
            color: var(--track-green);
        }

        .badge-pending {
            background: #fff9e6;
            color: #d97706;
        }

        /* --- ACTION BUTTONS --- */
        .doc-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
        }

        .doc-btn {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 2px solid var(--track-beige);
            background: #ffffff;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition-smooth);
            text-decoration: none;
            font-size: 16px;
        }

        .doc-btn:hover {
            background: var(--track-green);
            color: white;
            border-color: var(--track-green);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.3);
        }

        /* --- UPLOAD BUTTON --- */
        .btn-upload {
            background: var(--track-green);
            color: white;
            border: none;
            border-radius: 14px;
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

        /* --- FOOTER --- */
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
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            background: #ffffff;
        }

        .modal-header {
            border-bottom: 2px solid rgba(22,74,54,0.3);
            padding: 24px 28px;
            background: rgba(22, 74, 54, 0.95);
            color: white;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        .modal-header .btn-close:hover {
            filter: invert(0.8);
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
            background: var(--track-green);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 28px;
            font-weight: 700;
            transition: var(--transition-smooth);
        }

        .btn-modal-submit:hover {
            background: #1a8548;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.3);
            color: white;
        }

        .btn-modal-cancel {
            background: #206970;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 28px;
            font-weight: 600;
            transition: var(--transition-smooth);
        }

        .btn-modal-cancel:hover {
            background: #20a060;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.3);
        }

        /* File Upload Area */
        .upload-area {
            border: 3px dashed var(--track-green);
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
<body>

<?php 
    $active_page = 'documents';
    $user_role = $_SESSION['role'];
    $membership_type = $user_role;
    include('../includes/dashboard_navbar.php'); 
?>

<!-- HEADER -->
<div class="admin-header">
    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="badge-platform">
                    <span class="spinner-grow spinner-grow-sm me-2 text-success" role="status" style="width: 10px; height: 10px;"></span>
                    System Live
                </div>
                <h1 class="fw-800 display-4 mb-3">Document Management</h1>
                <p class="fs-5 mb-0 text-muted">Manage, upload, and organize official documents for NFFAC members and records.</p>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="container pb-5">
    <!-- TOOLBAR -->
    <div class="doc-toolbar">
        <div>
            <h5 class="fw-bold mb-0" style="color: var(--track-dark);">Recent Documents</h5>
            <small class="text-muted">Manage all your cooperative documents</small>
        </div>
        
        <!-- SEARCH TOOLBAR -->
        <div class="search-wrapper">
            <div class="search-input-group">
                <i class="bi bi-search"></i>
                <input type="text" id="docSearch" class="search-input" placeholder="Search files, categories, or keywords...">
            </div>
        </div>

        <button class="btn-upload" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal" style="border: none; cursor: pointer;">
            <i class="bi bi-cloud-arrow-up"></i> Upload Document
        </button>
    </div>

    <!-- DOCUMENTS LIST -->
    <div class="d-flex flex-column gap-3 mb-5">
        <!-- Document 1 -->
        <div class="doc-card">
            <div class="doc-icon">
                <i class="bi bi-file-pdf"></i>
            </div>
            <div class="doc-info">
                <div class="doc-title">Member_Registry_2024.pdf</div>
                <div class="doc-meta">
                    <span class="doc-meta-item"><strong>Category:</strong> Records</span>
                    <span class="doc-meta-item"><strong>Size:</strong> 2.4 MB</span>
                    <span class="doc-meta-item"><strong>Uploaded:</strong> Oct 24, 2024</span>
                </div>
                <div class="doc-badges">
                    <span class="badge-custom badge-records">Records</span>
                    <span class="badge-custom badge-verified">✓ Verified</span>
                </div>
            </div>
            <div class="doc-actions">
                <button class="doc-btn" title="View" data-bs-toggle="modal" data-bs-target="#viewDocumentModal" data-doc-name="Member_Registry_2024.pdf">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="doc-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editDocumentModal" data-doc-name="Member_Registry_2024.pdf">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="doc-btn" title="Delete" onclick="TrackUI.show('Are you sure you want to delete this document? This cannot be undone.', 'Permanent Delete', 'danger', 'Yes, Delete', 'Keep it').then(res => { if(res) console.log('Deleted'); })">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>

        <!-- Document 2 -->
        <div class="doc-card">
            <div class="doc-icon" style="background: #fff3e0; color: #f57c00;">
                <i class="bi bi-file-earmark-spreadsheet"></i>
            </div>
            <div class="doc-info">
                <div class="doc-title">Financial_Report_Q3.xlsx</div>
                <div class="doc-meta">
                    <span class="doc-meta-item"><strong>Category:</strong> Finance</span>
                    <span class="doc-meta-item"><strong>Size:</strong> 1.8 MB</span>
                    <span class="doc-meta-item"><strong>Uploaded:</strong> Nov 02, 2024</span>
                </div>
                <div class="doc-badges">
                    <span class="badge-custom badge-finance">Finance</span>
                    <span class="badge-custom badge-pending">⏳ Pending Review</span>
                </div>
            </div>
            <div class="doc-actions">
                <button class="doc-btn" title="View" data-bs-toggle="modal" data-bs-target="#viewDocumentModal" data-doc-name="Financial_Report_Q3.xlsx">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="doc-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editDocumentModal" data-doc-name="Financial_Report_Q3.xlsx">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="doc-btn" title="Delete" onclick="TrackUI.show('Are you sure you want to delete this document? This cannot be undone.', 'Permanent Delete', 'danger', 'Yes, Delete', 'Keep it').then(res => { if(res) console.log('Deleted'); })">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>

        <!-- Document 3 -->
        <div class="doc-card">
            <div class="doc-icon" style="background: #e3f2fd; color: #1976d2;">
                <i class="bi bi-file-word"></i>
            </div>
            <div class="doc-info">
                <div class="doc-title">Certificate_of_Compliance_2024.docx</div>
                <div class="doc-meta">
                    <span class="doc-meta-item"><strong>Category:</strong> Compliance</span>
                    <span class="doc-meta-item"><strong>Size:</strong> 950 KB</span>
                    <span class="doc-meta-item"><strong>Uploaded:</strong> Nov 15, 2024</span>
                </div>
                <div class="doc-badges">
                    <span class="badge-custom badge-records">Compliance</span>
                    <span class="badge-custom badge-verified">✓ Verified</span>
                </div>
            </div>
            <div class="doc-actions">
                <button class="doc-btn" title="View" data-bs-toggle="modal" data-bs-target="#viewDocumentModal" data-doc-name="Certificate_of_Compliance_2024.docx">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="doc-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editDocumentModal" data-doc-name="Certificate_of_Compliance_2024.docx">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="doc-btn" title="Delete" onclick="TrackUI.show('Are you sure you want to delete this document? This cannot be undone.', 'Permanent Delete', 'danger', 'Yes, Delete', 'Keep it').then(res => { if(res) console.log('Deleted'); })">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
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
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn-modal-submit" style="text-decoration: none;">
                    <i class="bi bi-download me-2"></i>Download
                </a>
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
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-submit">Save Changes</button>
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
                <h1 class="modal-title" id="uploadDocumentLabel">
                    <i class="bi bi-cloud-arrow-up"></i>
                    Upload New Document
                </h1>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-modal-submit">Upload Document</button>
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

    // --- CENTRALIZED DOCUMENT SEARCH ---
    function performSearch() {
        const term = document.getElementById('docSearch').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.doc-card');
        let found = 0;

        cards.forEach(card => {
            const title = card.querySelector('.doc-title').textContent.toLowerCase();
            const meta = card.querySelector('.doc-meta').textContent.toLowerCase();
            
            if (title.includes(term) || meta.includes(term)) {
                card.style.display = 'flex';
                card.style.animation = 'fadeInUpCustom 0.4s ease forwards';
                found++;
            } else {
                card.style.display = 'none';
            }
        });

        // Handle empty state
        const listContainer = document.querySelector('.d-flex.flex-column.gap-3.mb-5');
        let noResults = document.getElementById('noResultsMsg');

        if (found === 0) {
            if (!noResults) {
                noResults = document.createElement('div');
                noResults.id = 'noResultsMsg';
                noResults.className = 'empty-state py-5';
                noResults.innerHTML = `
                    <div class="empty-state-icon"><i class="bi bi-search"></i></div>
                    <h3>No matching documents</h3>
                    <p>We couldn't find any files matching your criteria.</p>
                `;
                listContainer.appendChild(noResults);
            }
        } else {
            if (noResults) noResults.remove();
        }
    }

    // Trigger on Input (for real-time)
    document.getElementById('docSearch').addEventListener('input', performSearch);
    
    // Trigger on Enter Key or Button Click
    document.getElementById('docSearch').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') performSearch();
    });
    document.getElementById('btnSearch').addEventListener('click', performSearch);

    // --- MODAL DATA POPULATE ---
    // Handle File Upload
    document.getElementById('fileInput').addEventListener('change', function(event) {
        const fileName = event.target.files[0]?.name || '';
        if (fileName) {
            document.getElementById('fileName').textContent = '✓ File selected: ' + fileName;
            document.getElementById('uploadDocName').value = fileName.split('.')[0];
        }
    });

    // Handle Upload Area Drag & Drop
    const uploadArea = document.querySelector('.upload-area');
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.style.background = 'rgba(32, 160, 96, 0.1)';
            uploadArea.style.borderColor = '#1a8548';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, () => {
            uploadArea.style.background = 'rgba(32, 160, 96, 0.02)';
            uploadArea.style.borderColor = '#20a060';
        }, false);
    });

    uploadArea.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        document.getElementById('fileInput').files = files;
        const event = new Event('change', { bubbles: true });
        document.getElementById('fileInput').dispatchEvent(event);
    }, false);

    // Handle Form Submissions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formType = this.closest('.modal-dialog').parentElement.id;
            
            let actionName = (formType === 'editDocumentModal') ? 'save changes to this document' : 'upload this document';
            let title = (formType === 'editDocumentModal') ? 'Update Record' : 'Upload File';
            let btnLabel = (formType === 'editDocumentModal') ? 'Save Changes' : 'Upload Now';

            if (await TrackUI.show(`Are you sure you want to ${actionName}?`, title, 'primary', btnLabel, 'Back')) {
                if (formType === 'editDocumentModal') {
                    alert('Document updated successfully!');
                } else if (formType === 'uploadDocumentModal') {
                    alert('Document uploaded successfully!');
                }
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(this.closest('.modal'));
                if (modal) modal.hide();
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include('../includes/footer.php'); ?>
</body>
</html>