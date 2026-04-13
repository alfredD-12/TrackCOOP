<?php
session_start();

// Fetch session info
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Admin';
$membership_type = $user_role;
$full_name = isset($_SESSION['fname']) ? $_SESSION['fname'] : "Administrator";
$user_initial = strtoupper(substr($full_name, 0, 1));

// STATIC GALLERY DATA (Updated with unified categories)
$static_gallery = [
    ['id' => 1, 'title' => 'Main Cooperative Office', 'description' => 'Our central hub for member services and admin.', 'category' => 'Sector News', 'date' => '2024-03-15', 'image' => 'Home.jpeg'],
    ['id' => 2, 'title' => 'General Assembly Meeting', 'description' => 'Members discussing the 2024 strategic roadmap.', 'category' => 'Meetings', 'date' => '2024-03-10', 'image' => 'meeting.jpg'],
    ['id' => 3, 'title' => 'Sector Management Workshop', 'description' => 'Training session for regional sector leaders.', 'category' => 'Meetings', 'date' => '2024-03-05', 'image' => 'sector.jpg'],
    ['id' => 4, 'title' => 'Sustainable Agriculture Expo', 'description' => 'Showcasing new farming techniques and tools.', 'category' => 'Agriculture', 'date' => '2024-02-28', 'image' => 'agriculture.webp'],
    ['id' => 5, 'title' => 'Community Fellowship Event', 'description' => 'Celebrating milestones with our cooperative family.', 'category' => 'Events', 'date' => '2024-02-20', 'image' => 'event.png'],
    ['id' => 6, 'title' => 'Rice Production Seminar', 'description' => 'Enhancing yield via organic methods.', 'category' => 'Meetings', 'date' => '2024-02-15', 'image' => 'agriculture.webp'],
    ['id' => 7, 'title' => 'Quarterly Sector Review', 'description' => 'Evaluating productivity across all sectors.', 'category' => 'Meetings', 'date' => '2024-02-10', 'image' => 'sector.jpg'],
    ['id' => 8, 'title' => 'Annual Budget Planning', 'description' => 'Coordinating funds for member welfare.', 'category' => 'Meetings', 'date' => '2024-02-01', 'image' => 'meeting.jpg'],
    ['id' => 9, 'title' => 'Harvest Season Kickoff', 'description' => 'Preparing for the 2024 main harvest.', 'category' => 'Agriculture', 'date' => '2024-01-25', 'image' => 'agriculture.webp'],
    ['id' => 10, 'title' => 'Coop Anniversary Celebration', 'description' => 'Marking 10 years of community service.', 'category' => 'Events', 'date' => '2024-01-15', 'image' => 'event.png'],
    ['id' => 11, 'title' => 'Member Benefits Outreach', 'description' => 'Explaining new health and loan packages.', 'category' => 'Sector News', 'date' => '2024-01-10', 'image' => 'meeting.jpg'],
    ['id' => 12, 'title' => 'Soil Nutrition Training', 'description' => 'Hands-on training for better crop health.', 'category' => 'Agriculture', 'date' => '2024-01-05', 'image' => 'sector.jpg'],
    ['id' => 13, 'title' => 'Local Market Partnership', 'description' => 'Expanding farm-to-market logistics.', 'category' => 'Sector News', 'date' => '2023-12-20', 'image' => 'agriculture.webp'],
    ['id' => 14, 'title' => 'Youth Leadership Program', 'description' => 'Training the next generation of leaders.', 'category' => 'Meetings', 'date' => '2023-12-15', 'image' => 'event.png'],
    ['id' => 15, 'title' => 'Year-End Review Meeting', 'description' => 'Reflecting on 2023 achievements.', 'category' => 'Meetings', 'date' => '2023-12-05', 'image' => 'meeting.jpg'],
    ['id' => 16, 'title' => 'Organic Pesticide Workshop', 'description' => 'DIY solutions for crop protection.', 'category' => 'Agriculture', 'date' => '2023-11-28', 'image' => 'agriculture.webp'],
    ['id' => 17, 'title' => 'Regional Planning Summit', 'description' => 'Aligning sector goals for 2025.', 'category' => 'Meetings', 'date' => '2023-11-15', 'image' => 'sector.jpg'],
    ['id' => 18, 'title' => 'Member Thanksgiving Lunch', 'description' => 'A small gathering to say thank you.', 'category' => 'Events', 'date' => '2023-11-10', 'image' => 'event.png'],
    ['id' => 19, 'title' => 'New Equipment Demo', 'description' => 'Showcasing subsidized tractor units.', 'category' => 'Agriculture', 'date' => '2023-11-05', 'image' => 'agriculture.webp'],
    ['id' => 20, 'title' => 'TrackCOOP Digital Launch', 'description' => 'Digitizing our portal operations.', 'category' => 'Sector News', 'date' => '2023-11-01', 'image' => 'Home.jpeg'],
];

$all_media = $static_gallery;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Management | TRACKCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--track-bg); color: var(--text-main); }
        
        .upload-form-card {
            display: none;
            animation: slideInDown 0.5s ease-out;
            margin-bottom: 30px;
        }

        .thumb-preview {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            object-fit: cover;
            border: 2.5px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        @keyframes slideInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Premium Modal Styles */
        .modal-content { border-radius: 30px !important; border: none; box-shadow: 0 25px 60px rgba(0,0,0,0.15); overflow: hidden !important; }
        .modal-header { background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22, 74, 54, 0.3); padding: 24px 28px; color: white; }
        .modal-title { font-weight: 800; font-size: 1.3rem; letter-spacing: -1px; color: #27ae60 !important; display: flex; align-items: center; gap: 12px; }
        .modal-body { padding: 30px; }
        .modal-footer { background: rgba(22, 74, 54, 0.95); border-top: 1px solid rgba(22, 74, 54, 0.3); padding: 20px 28px; color: white; }
        
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

        /* Filter Pills matching Announcement design */
        .filter-pill {
            padding: 7px 18px;
            border-radius: 12px;
            font-size: 0.78rem;
            font-weight: 700;
            border: 1.5px solid #e2e8f0;
            background: white;
            color: #64748b;
            cursor: pointer;
            transition: all 0.25s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            min-width: 110px;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            box-shadow: 0 4px 12px rgba(39,174,96,0.35);
        }
        /* Dropzone matching user image reference */
        .dropzone-design {
            border: 2px dashed #cbd5e1;
            border-radius: 20px;
            background: #ffffff;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        .dropzone-design:hover {
            border-color: #27ae60;
            background: #f8fafc;
        }
        .dropzone-icon-box {
            width: 56px;
            height: 56px;
            background: #e9f5ee;
            color: #27ae60;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
        }
        .dropzone-text-primary {
            font-weight: 700;
            color: #27ae60;
            margin-right: 5px;
        }
        .dropzone-text-secondary {
            color: #64748b;
            font-weight: 500;
        }

        .user-table-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-table-avatar-box {
            width: 38px;
            height: 38px;
            background: #27ae60;
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(39, 174, 96, 0.2);
        }

        .user-table-info {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .user-table-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.95rem;
        }

        .user-table-role {
            font-size: 0.8rem;
            color: #94a3b8;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="sidebar-layout">
    <?php 
    $active_page = 'gallery';
    $user_role = 'Admin';
    $membership_type = 'Admin';
    $full_name = htmlspecialchars($full_name);
    include('../includes/dashboard_sidebar.php'); 
    ?>

    <div class="main-content-wrapper">
        <div class="container-fluid px-4 py-4">
            <!-- TOOLBAR -->
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2">
                <div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="search-wrapper position-relative mb-0" style="width: 350px;">
                        <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 18px; z-index: 5;"></i>
                        <input type="text" id="gallerySearch" class="form-control border-0 shadow-sm" placeholder="Search activities..." style="background: #ffffff; border-radius: 12px; padding-left: 45px !important; height: 50px;">
                    </div>
                    <button class="btn btn-upload-gold shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#uploadMediaModal" style="height: 50px; padding: 0 25px !important; border-radius: 12px !important; font-weight: 700;">
                        <i class="bi bi-plus-lg"></i> New Upload
                    </button>
                        <!-- Notification bell removed by user request -->
                </div>
            </div>

            <!-- STANDALONE DRAG & DROP BAR - Matches user's design reference -->
            <div class="dropzone-design mb-4 animate-fade-in" data-bs-toggle="modal" data-bs-target="#uploadMediaModal" style="padding: 25px !important; display: flex; align-items: center; justify-content: center; gap: 15px;">
                <div class="dropzone-icon-box mb-0" style="width: 44px; height: 44px; margin: 0;">
                    <i class="bi bi-cloud-arrow-up"></i>
                </div>
                <div class="dropzone-text mb-0">
                    <span class="dropzone-text-primary">Drag & Drop</span>
                    <span class="dropzone-text-secondary">your activity photo here (PNG, JPG)</span>
                </div>
            </div>

            <!-- UPLOAD MEDIA MODAL -->
            <div class="modal fade" id="uploadMediaModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-cloud-upload text-success me-2"></i> Post Media Activity</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        
                        <form onsubmit="return handleFormSubmit(event)">
                            <div class="modal-body">
                                <div class="row g-4">
                                    <div class="col-lg-4">
                                        <div class="dropzone-design h-100 d-flex flex-column align-items-center justify-content-center" id="photoDropzone" onclick="document.getElementById('fileInput').click()">
                                            <div id="previewContainer" class="text-center w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                                <div class="dropzone-icon-box">
                                                    <i class="bi bi-cloud-upload"></i>
                                                </div>
                                                <div class="dropzone-text">
                                                    <span class="dropzone-text-primary">Drag & Drop</span>
                                                    <span class="dropzone-text-secondary">photo here</span>
                                                    <small class="d-block text-muted mt-2">Max 5MB</small>
                                                </div>
                                            </div>
                                            <input type="file" id="fileInput" class="d-none" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="row g-3">
                                            <div class="col-md-7">
                                                <label class="form-label">Activity Title</label>
                                                <input type="text" class="form-control" placeholder="e.g. 2024 Rice Harvest">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Category</label>
                                                <select class="form-select">
                                                    <option value="Events">Events</option>
                                                    <option value="Agriculture" selected>Agriculture</option>
                                                    <option value="Meetings">Meetings</option>
                                                    <option value="Sector News">Sector News</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label">Date Held</label>
                                                <input type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            <div class="col-md-7">
                                                <label class="form-label">Description</label>
                                                <input type="text" class="form-control" placeholder="Brief activity details...">
                                            </div>
                                            <div class="col-12 mt-4 text-end pe-2">
                                                <button type="button" class="btn fw-bold me-2" style="height: 50px; padding: 0 30px !important; border-radius: 50px !important; background: #ef4444; color: white; border: none; font-weight: 700;" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn-upload-gold" style="height: 50px; padding: 0 35px !important; border-radius: 50px !important; font-weight: 700; border: none;">
                                                    <i class="bi bi-cloud-arrow-up-fill me-2"></i> Upload Activity
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- GALLERY TABLE -->
            <div class="table-card fade-in-up shadow-sm border-0" style="border-radius: 20px;">
                <!-- FILTER PILLS Integrated inside table card matching Announcement design -->
                <div class="d-flex align-items-center flex-wrap gap-2 mb-4 pb-3" style="border-bottom: 1px solid #f1f5f9;">
                    <button class="filter-pill active" data-filter="all"><i class="bi bi-grid-fill"></i> ALL</button>
                    <button class="filter-pill" data-filter="Events"><i class="bi bi-calendar-event-fill"></i> EVENTS</button>
                    <button class="filter-pill" data-filter="Agriculture"><i class="bi bi-tree-fill"></i> AGRICULTURE</button>
                    <button class="filter-pill" data-filter="Meetings"><i class="bi bi-people-fill"></i> MEETINGS</button>
                    <button class="filter-pill" data-filter="Sector News"><i class="bi bi-tag-fill"></i> SECTOR NEWS</button>
                </div>

                <table class="table table-elite align-middle">
                        <thead>
                            <tr>
                                <th style="min-width: 250px;">PHOTO & ACTIVITY</th>
                                <th style="min-width: 140px;">CATEGORY</th>
                                <th style="min-width: 180px;">AUTHOR</th>
                                <th style="min-width: 160px;">DATE CREATED</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                    <tbody id="galleryTableBody">
                        <?php foreach($all_media as $m): 
                            $badge_class = ($m['category'] == 'Agriculture' || $m['category'] == 'Meetings') ? 'badge-signed' : 'badge-pending';
                        ?>
                        <tr class="manage-row" data-category="<?php echo $m['category']; ?>" data-search-matched="true">
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #e9f5ee; border-radius: 12px; overflow: hidden; flex-shrink: 0;">
                                        <img src="../<?php echo $m['image']; ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="Activity">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;"><?php echo $m['title']; ?></div>
                                        <div class="text-muted small"><?php echo htmlspecialchars(substr($m['description'], 0, 45)) . (strlen($m['description']) > 45 ? '...' : ''); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge-status <?php echo $badge_class; ?>">
                                    <i class="bi bi-tag-fill small opacity-75"></i> <?php echo strtoupper($m['category']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="user-table-item">
                                    <?php 
                                        $role_name = ($m['id'] % 2 !== 0) ? 'Administrator' : 'Bookkeeper';
                                        $initial = ($role_name === 'Administrator') ? 'A' : 'B';
                                    ?>
                                    <div class="user-table-avatar-box"><?php echo $initial; ?></div>
                                    <div class="user-table-info">
                                        <span class="user-table-name"><?php echo $role_name; ?></span>
                                        <span class="user-table-role"><?php echo $role_name; ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-muted small fw-600">
                                    <i class="bi bi-calendar3 me-1"></i> <?php echo date('M d, Y', strtotime($m['date'])); ?>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end align-items-center">
                                    <button class="btn-doc-action btn-action-view" title="View"><i class="bi bi-eye"></i></button>
                                    <button class="btn-doc-action btn-action-edit" title="Edit"><i class="bi bi-pencil-square"></i></button>
                                    <button class="btn-doc-action btn-action-delete" title="Delete"><i class="bi bi-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="pagination-elite mt-4">
                    <span class="pagination-elite-label">ROWS PER PAGE</span>
                    <select id="rowsPerPage" class="pagination-elite-select">
                        <option value="5" selected>5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                    </select>
                    <span id="paginationInfo" class="pagination-elite-info">1–5 of 20</span>
                    <div class="pagination-elite-buttons">
                        <button id="prevPage" class="pagination-elite-btn" title="Previous Page"><i class="bi bi-chevron-left"></i></button>
                        <button id="nextPage" class="pagination-elite-btn" title="Next Page"><i class="bi bi-chevron-right"></i></button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // --- UI INTERACTIONS ---
    function toggleUploadForm() {
        const section = document.getElementById('uploadSection');
        const btn = document.getElementById('toggleUploadBtn');
        if(section.style.display === 'block') {
            section.style.display = 'none';
            btn.innerHTML = '<i class="bi bi-plus-lg me-2"></i> New Upload';
            btn.classList.add('btn-upload-gold');
        } else {
            section.style.display = 'block';
            btn.innerHTML = '<i class="bi bi-dash-lg me-2"></i> Hide Form';
            btn.classList.remove('btn-upload-gold');
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    document.getElementById('toggleUploadBtn').addEventListener('click', toggleUploadForm);

    function handleFormSubmit(e) {
        e.preventDefault();
        alert('Action simulation: Activity saved successfully!');
        toggleUploadForm();
        return false;
    }

    // --- PHOTO PREVIEW ---
    document.getElementById('fileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('previewContainer').innerHTML = `<img src="${evt.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:18px;">`;
            }
            reader.readAsDataURL(file);
        }
    });

    // --- PAGINATION, SEARCH & FILTER LOGIC ---
    let currentPage = 1;
    let rowsPerPage = parseInt(document.getElementById('rowsPerPage').value);
    let activeCategoryFilter = 'all';

    function updateTable() {
        const searchVal = document.getElementById('gallerySearch').value.toLowerCase().trim();
        const allRows = Array.from(document.querySelectorAll('#galleryTableBody tr.manage-row'));
        
        let visibleCount = 0;
        
        allRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const category = row.dataset.category;
            
            const textMatch = text.includes(searchVal);
            const categoryMatch = activeCategoryFilter === 'all' || category === activeCategoryFilter;
            
            if (textMatch && categoryMatch) {
                row.setAttribute('data-search-matched', 'true');
                visibleCount++;
            } else {
                row.setAttribute('data-search-matched', 'false');
                row.style.display = 'none';
            }
        });

        // Pagination over matched rows
        const matchedRows = allRows.filter(r => r.getAttribute('data-search-matched') === 'true');
        const totalPages = Math.ceil(matchedRows.length / rowsPerPage);
        
        if (currentPage > totalPages) currentPage = totalPages || 1;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        matchedRows.forEach((row, index) => {
            if (index >= start && index < end) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Update Info
        const infoStart = matchedRows.length === 0 ? 0 : start + 1;
        const infoEnd = Math.min(end, matchedRows.length);
        document.getElementById('paginationInfo').textContent = `${infoStart}–${infoEnd} of ${matchedRows.length}`;
        
        // Buttons
        document.getElementById('prevPage').disabled = (currentPage === 1);
        document.getElementById('nextPage').disabled = (currentPage === totalPages || totalPages === 0);
    }

    // Category Filter Click Handling
    document.querySelectorAll('.filter-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            activeCategoryFilter = this.dataset.filter;
            currentPage = 1;
            updateTable();
        });
    });

    document.getElementById('gallerySearch').addEventListener('input', () => { currentPage = 1; updateTable(); });
    document.getElementById('rowsPerPage').addEventListener('change', function() { 
        rowsPerPage = parseInt(this.value); 
        currentPage = 1; 
        updateTable(); 
    });
    
    document.getElementById('prevPage').addEventListener('click', () => { 
        if(currentPage > 1) { 
            currentPage--; 
            updateTable(); 
        } 
    });
    
    document.getElementById('nextPage').addEventListener('click', () => { 
        const matchedCount = document.querySelectorAll('#galleryTableBody tr.manage-row[data-search-matched="true"]').length;
        if (currentPage < Math.ceil(matchedCount / rowsPerPage)) {
            currentPage++; 
            updateTable(); 
        }
    });

    updateTable();
</script>
        </div> <!-- .container-fluid -->
    </div> <!-- .main-content-wrapper -->
</div> <!-- .sidebar-layout -->

</body>
</html>
