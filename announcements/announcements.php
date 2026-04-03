<?php
session_start();
include('../auth/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
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

// Static announcements data for display
$static_announcements = [
    ['id' => 1, 'title' => 'Annual General Assembly 2024', 'content' => 'Join us for our annual general assembly on April 15, 2024', 'date_posted' => '2024-03-20', 'author' => 'Administrator'],
    ['id' => 2, 'title' => 'New Member Registration Opened', 'content' => 'Registration for new members is now open. Fill out the form to apply', 'date_posted' => '2024-03-15', 'author' => 'Administrator'],
    ['id' => 3, 'title' => 'System Maintenance Notice', 'content' => 'System will be under maintenance on April 1st from 2 AM to 4 AM', 'date_posted' => '2024-03-10', 'author' => 'Administrator'],
];

// Announcements are static for demo
$all_announcements = $static_announcements;

// ── Category Counts ─── (Static for demo)
$stats = ['General' => 3, 'Important' => 1];
$msg_status = "";

// ── Handle POST Actions (Add / Edit / Delete) ─── (Demo Mode)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($user_role === 'Admin' || $user_role === 'Bookkeeper')) {
    if (isset($_POST['title']) && isset($_POST['content'])) {
        $msg_status = "success";
    }
}

// Fetch announcements list for display
$announcements_list = $all_announcements;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | TrackCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    
    <style>
        /* Announcements Page Specific Styles */
        .page-header {
            background: linear-gradient(135deg, var(--track-bg) 0%, var(--track-beige) 100%);
            padding: 60px 0 40px; border-bottom: 1px solid rgba(229,229,192,0.4); margin-bottom: 40px;
            position: relative; overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16,1,0.3,1) both;
        }
        .badge-platform {
            background: white; color: var(--track-green); font-weight: 700; padding: 6px 14px;
            border-radius: 50px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;
            display: inline-flex; align-items: center; margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(32,160,96,0.1); border: 1px solid rgba(32,160,96,0.2);
        }

        /* ── Announcement Cards ── */
        .ann-card {
            background: white; border-radius: 20px; border: 1px solid rgba(226,232,240,0.8);
            padding: 24px; transition: var(--transition-smooth); margin-bottom: 24px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02); position: relative;
        }
        .ann-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(32,160,96,0.08); border-color: rgba(32,160,96,0.3); }
        .cat-badge {
            font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px;
            padding: 5px 12px; border-radius: 50px; margin-bottom: 12px; display: inline-block;
        }
        .cat-general  { background: #f1f5f9; color: #64748b; }
        .cat-event    { background: #eef2ff; color: #4f46e5; }
        .cat-meeting  { background: #fff7ed; color: #ea580c; }
        .cat-deadline { background: #fef2f2; color: #dc2626; }

        /* ── Action Buttons ── */
        .btn-track { background: var(--track-green); color: white; border-radius: 12px; padding: 12px 24px; font-weight: 700; border: none; transition: var(--transition-smooth); }
        .btn-track:hover { background: #20a060; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(32,160,96,0.3); color: white; }
        
        .action-icon {
            width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; color: var(--text-muted); background: #f8fafc; border: 1px solid #e2e8f0;
            transition: 0.3s; text-decoration: none;
        }
        .action-icon:hover { background: var(--track-green); color: white; border-color: var(--track-green); }
        .action-icon.edit:hover { background: #3b82f6; border-color: #3b82f6; }
        .action-icon.del:hover  { background: #ef4444; border-color: #ef4444; }

        /* ── Search Wrapper ── */
        .search-wrapper { position: relative; max-width: 350px; width: 100%; }
        .search-wrapper input { 
            padding-left: 44px; border-radius: 12px; border: 1.5px solid #e5e5c0; 
            background: #fdfdf8; transition: all 0.3s ease;
        }
        .search-wrapper input:focus {
            border-color: var(--track-green);
            box-shadow: 0 0 0 4px rgba(32,160,96,0.1);
            background: white;
            outline: none;
        }

        .filter-group { background: white; border-radius: 24px; padding: 24px; border: 1px solid rgba(226,232,240,0.8); box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .filter-item {
            padding: 12px 16px; border-radius: 14px; display: flex; justify-content: space-between;
            color: var(--text-muted); text-decoration: none; font-weight: 700; font-size: 0.9rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); margin-bottom: 6px; align-items: center;
        }
        .filter-item i { font-size: 1.1rem; transition: 0.3s; opacity: 0.6; }
        .filter-item:hover { background: var(--track-bg); color: var(--track-dark); transform: translateX(5px); }
        .filter-item:hover i { opacity: 1; transform: scale(1.1); }
        .filter-item.active { 
            background: white; 
            color: var(--track-green); 
            box-shadow: 0 8px 20px rgba(32,160,96,0.1);
            border: 1px solid rgba(32,160,96,0.2);
            transform: translateX(8px);
        }
        .filter-item.active i { color: var(--track-green); opacity: 1; }

        /* ── Modal Tabs Styling ── */
        .nav-tabs .nav-link {
            color: #64748b !important;
            font-weight: 700;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent !important;
        }
        .nav-tabs .nav-link:hover {
            background: #f8fafc;
            color: var(--track-green) !important;
        }
        .nav-tabs .nav-link.active {
            background: white !important;
            color: var(--track-green) !important;
            border-bottom-color: var(--track-green) !important;
        }
        .tab-content {
            padding: 0;
        }
    </style>
    <link rel="stylesheet" href="../includes/footer.css">
</head>
<body>

<!-- NAVBAR -->
<?php 
    $active_page = 'announcements';
    $membership_type = ($user_role === 'Admin') ? 'Admin' : (($user_role === 'Bookkeeper') ? 'Bookkeeper' : 'Member');
    include('../includes/dashboard_navbar.php'); 
?>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="badge-platform">
                    <span class="spinner-grow spinner-grow-sm me-2 text-success" role="status" style="width:10px;height:10px;"></span>
                    System Live
                </div>
                <h1 class="fw-800 display-5 mb-2" style="letter-spacing:-1.5px;"><i class="bi bi-bell-fill me-3" style="color:#20a060;"></i>Cooperative Announcements</h1>
                <p class="fs-6 mb-0 text-muted">Stay updated with the latest news, events, and deadlines.</p>
            </div>
            <?php if ($user_role === 'Admin' || $user_role === 'Bookkeeper'): ?>
            <div class="col-md-4 text-md-end mt-4 mt-md-0">
                <button class="btn-track" data-bs-toggle="modal" data-bs-target="#newsModal"><i class="bi bi-plus-circle-fill me-2"></i> Create News & Messages</button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Flash Messages (GET or local) -->
    <?php 
    $display_msg = $_GET['msg_status'] ?? $msg_status;
    if ($display_msg === 'created'): ?>
        <div class="alert alert-success fw-bold rounded-4 mb-4" data-aos="fade-down"><i class="bi bi-check-circle-fill me-2"></i> Announcement published!</div>
    <?php elseif ($display_msg === 'updated'): ?>
        <div class="alert alert-success fw-bold rounded-4 mb-4" data-aos="fade-down"><i class="bi bi-check-circle-fill me-2"></i> Announcement updated!</div>
    <?php elseif ($display_msg === 'deleted'): ?>
        <div class="alert alert-warning fw-bold rounded-4 mb-4" data-aos="fade-down"><i class="bi bi-trash-fill me-2"></i> Announcement removed.</div>
    <?php elseif ($display_msg === 'sector_sent'): ?>
        <div class="alert alert-info fw-bold rounded-4 mb-4" data-aos="fade-down"><i class="bi bi-send-fill me-2"></i> Sector-based message sent successfully!</div>
    <?php elseif ($display_msg === 'error'): ?>
        <div class="alert alert-danger fw-bold rounded-4 mb-4" data-aos="fade-down"><i class="bi bi-exclamation-octagon-fill me-2"></i> Something went wrong.</div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="filter-group mb-4" data-aos="fade-right">
                <h6 class="fw-800 mb-3" style="letter-spacing:0.5px; text-transform:uppercase; font-size:0.75rem; color:#aaa;">Browse Categories</h6>
                <a href="announcements.php" class="filter-item <?php echo $filter_cat === '' ? 'active' : ''; ?>">
                    <span><i class="bi bi-grid-fill me-2"></i> All</span>
                    <span class="badge bg-light text-dark rounded-pill"><?php echo array_sum($stats); ?></span>
                </a>
                <a href="announcements.php?category=General" class="filter-item <?php echo $filter_cat === 'General' ? 'active' : ''; ?>">
                    <span><i class="bi bi-megaphone me-2"></i> General</span>
                    <span class="badge bg-light text-dark rounded-pill"><?php echo $stats['General'] ?? 0; ?></span>
                </a>
                <a href="announcements.php?category=Event" class="filter-item <?php echo $filter_cat === 'Event' ? 'active' : ''; ?>">
                    <span><i class="bi bi-calendar-event me-2"></i> Events</span>
                    <span class="badge bg-light text-dark rounded-pill"><?php echo $stats['Event'] ?? 0; ?></span>
                </a>
                <a href="announcements.php?category=Meeting" class="filter-item <?php echo $filter_cat === 'Meeting' ? 'active' : ''; ?>">
                    <span><i class="bi bi-people-fill me-2"></i> Meetings</span>
                    <span class="badge bg-light text-dark rounded-pill"><?php echo $stats['Meeting'] ?? 0; ?></span>
                </a>
                <a href="announcements.php?category=Deadline" class="filter-item <?php echo $filter_cat === 'Deadline' ? 'active' : ''; ?>">
                    <span><i class="bi bi-clock-history me-2"></i> Deadlines</span>
                    <span class="badge bg-light text-dark rounded-pill"><?php echo $stats['Deadline'] ?? 0; ?></span>
                </a>

                <?php
                    $sector_count = 0;
                    foreach ($stats as $cat => $count) {
                        if (strpos($cat, 'Sector:') === 0) {
                            $sector_count += $count;
                        }
                    }
                ?>
                <a href="announcements.php?sector_filter=1" class="filter-item <?php echo (isset($_GET['sector_filter']) && $_GET['sector_filter'] == 1) ? 'active' : ''; ?>">
                    <span><i class="bi bi-tag-fill me-2" style="color:#20a060;"></i> Sector</span>
                    <span class="badge bg-light text-dark rounded-pill"><?php echo $sector_count; ?></span>
                </a>
            </div>
        </div>

        <!-- Announcement Feed -->
        <div class="col-lg-9">
            <!-- Search Bar -->
            <form method="GET" class="mb-4 d-flex justify-content-between align-items-center">
                <div class="search-wrapper">
                    <input type="text" name="search" class="form-control" placeholder="Search by title..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                </div>
            </form>

            <?php if (count($announcements_list) > 0): ?>
                <?php 
                $delay = 0;
                foreach ($announcements_list as $row): 
                    $cat_class = 'general';
                    $delay_ms = $delay * 100;
                ?>
                <div class="ann-card" data-aos="fade-up" data-aos-delay="<?php echo $delay_ms; ?>">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="cat-badge cat-<?php echo $cat_class; ?>">General</span>
                        <?php if ($user_role === 'Admin'): ?>
                        <div class="d-flex gap-2">
                            <button class="action-icon edit" title="Edit" 
                                onclick="openEdit('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['title'], ENT_QUOTES); ?>', 'General', '<?php echo htmlspecialchars(json_encode($row['content']), ENT_QUOTES); ?>')"
                                data-bs-toggle="modal" data-bs-target="#editAnnModal"><i class="bi bi-pencil"></i></button>
                            <button class="action-icon del" title="Delete" 
                                onclick="confirmDelete('<?php echo $row['id']; ?>', '<?php echo htmlspecialchars($row['title'], ENT_QUOTES); ?>')"
                                data-bs-toggle="modal" data-bs-target="#deleteAnnModal"><i class="bi bi-trash"></i></button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <h4 class="fw-800 mb-2" style="letter-spacing:-0.5px;"><?php echo htmlspecialchars($row['title']); ?></h4>
                    <p class="text-muted small mb-3">
                        <i class="bi bi-calendar3 me-1"></i> <?php echo date('M d, Y', strtotime($row['date_posted'] ?? date('Y-m-d'))); ?> 
                        <span class="mx-2">|</span>
                        <i class="bi bi-person me-1"></i> Posted by <?php echo htmlspecialchars($row['author'] ?? 'Administrator'); ?>
                    </p>
                    <div class="ann-content text-dark mb-0">
                        <?php echo nl2br(htmlspecialchars($row['content'])); ?>
                    </div>
                </div>
                <?php 
                    $delay++;
                endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-broadcast" style="font-size:4rem; opacity:0.1;"></i>
                    <h5 class="fw-700 mt-3 text-muted">No announcements found</h5>
                    <p class="text-muted">Stay tuned for updates from the cooperative management.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- COMBINED NEWS & MESSAGING MODAL (With Tabs) -->
<div class="modal fade" id="newsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header" style="background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22,74,54,0.3); padding: 24px; color: white;">
                <h5 class="modal-title fw-800 text-white"><i class="bi bi-megaphone-fill text-success me-2"></i>Create News & Messages</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            
            
            <!-- Single Unified Form -->
            <form method="POST" onsubmit="return handleNewsSubmit(event)">
                <input type="hidden" name="message_type" id="hidden_message_type" value="announcement">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- Combined Message Type & Sector Selection -->
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Message Type</label>
                            <select id="messageTypeSelect" class="form-select" style="border-radius: 12px; border: 1.5px solid #e5e5c0; padding: 12px 14px; font-size: 16px;" onchange="toggleSectorFields()">
                                <option value="announcement" selected>Announcement (All Members)</option>
                                <option value="sector:Rice">Sector: Rice</option>
                                <option value="sector:Corn">Sector: Corn</option>
                                <option value="sector:Fishery">Sector: Fishery</option>
                                <option value="sector:Livestock">Sector: Livestock</option>
                                <option value="sector:High Value Crops">Sector: High Value Crops</option>
                            </select>
                        </div>

                        <!-- Priority Field (Hidden by default) -->
                        <div class="col-md-6" id="priorityField" style="display: none;">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Priority</label>
                            <select name="priority" class="form-select" style="border-radius: 12px; border: 1.5px solid #e5e5c0; padding: 12px 14px; font-size: 16px;">
                                <option value="Normal">Normal</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>

                        <!-- Category Field (Shown for announcements) -->
                        <div class="col-md-6" id="categoryField">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Category</label>
                            <select name="category" class="form-select" style="border-radius: 12px; border: 1.5px solid #e5e5c0; padding: 12px 14px; font-size: 16px;">
                                <option value="General">General</option>
                                <option value="Event">Event</option>
                                <option value="Meeting">Meeting</option>
                                <option value="Deadline">Deadline</option>
                            </select>
                        </div>

                        <!-- Title/Subject -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1" id="titleLabel">Title</label>
                            <input type="text" name="title" class="form-control" style="border-radius: 12px; border: 1.5px solid #e5e5c0;" placeholder="What's this about?" required>
                        </div>

                        <!-- Content -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Content</label>
                            <textarea name="content" class="form-control" rows="6" style="border-radius: 12px; border: 1.5px solid #e5e5c0;" placeholder="Type your message here..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: rgba(22, 74, 54, 0.95); border-top: 1px solid rgba(22,74,54,0.3); padding: 20px; color: white;">
                    <button type="button" class="btn fw-bold px-4 py-2" style="background: #206970; color: white; border: none; border-radius: 10px; transition: all 0.3s ease;" onmouseover="this.style.background='#20a060'; this.style.boxShadow='0 8px 20px rgba(32, 160, 96, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#206970'; this.style.boxShadow='none'; this.style.transform='translateY(0)';" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn fw-bold px-4 py-2" style="background: #20a060; color: white; border: none; border-radius: 10px; transition: all 0.3s ease;" onmouseover="this.style.background='#1a8548'; this.style.boxShadow='0 8px 20px rgba(32, 160, 96, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#20a060'; this.style.boxShadow='none'; this.style.transform='translateY(0)';" id="submitBtn">Publish Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT ANNOUNCEMENT MODAL -->
<div class="modal fade" id="editAnnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header" style="background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22,74,54,0.3); padding: 24px; color: white;">
                <h5 class="modal-title fw-800 text-white"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <form method="POST" onsubmit="return TrackUI.confirmForm(event, 'Save changes to this announcement?', 'Update Announcement', 'primary')">
                <input type="hidden" name="edit_announcement" value="1">
                <input type="hidden" name="ann_id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-9">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Title</label>
                            <input type="text" name="title" id="edit_title" class="form-control" style="border-radius: 12px; border: 1.5px solid #e5e5c0;" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Category</label>
                            <select name="category" id="edit_category" class="form-select" style="border-radius: 12px; border: 1.5px solid #e5e5c0;">
                                <option value="General">General</option>
                                <option value="Event">Event</option>
                                <option value="Meeting">Meeting</option>
                                <option value="Deadline">Deadline</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-1">Content</label>
                            <textarea name="content" id="edit_content" class="form-control" rows="6" style="border-radius: 12px; border: 1.5px solid #e5e5c0;" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: rgba(22, 74, 54, 0.95); border-top: 1px solid rgba(22,74,54,0.3); padding: 20px; color: white;">
                    <button type="button" class="btn fw-bold px-4 py-2" style="background: #206970; color: white; border: none; border-radius: 10px; transition: all 0.3s ease;" onmouseover="this.style.background='#20a060'; this.style.boxShadow='0 8px 20px rgba(32, 160, 96, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#206970'; this.style.boxShadow='none'; this.style.transform='translateY(0)';" data-bs-dismiss="modal">Discard</button>
                    <button type="submit" name="edit_announcement" class="fw-bold px-4 py-2" style="border-radius: 10px; background: #206970; color: white; border: none; transition: all 0.3s ease;" onmouseover="this.style.background='#20a060'; this.style.boxShadow='0 8px 20px rgba(32, 160, 96, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#206970'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">Update Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteAnnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header" style="background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22,74,54,0.3); padding: 24px; color: white;">
                <h5 class="modal-title fw-800 text-danger"><i class="bi bi-trash3-fill me-2"></i>Delete Post?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="filter: invert(1);"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="delete_announcement" value="1">
                <input type="hidden" name="ann_id" id="del_id">
                <div class="modal-body p-4">
                    <p class="mb-0 text-muted">Are you sure you want to delete <strong id="del_title"></strong>? This action cannot be undone.</p>
                </div>
                <div class="modal-footer" style="background: rgba(22, 74, 54, 0.95); border-top: 1px solid rgba(22,74,54,0.3); padding: 20px; color: white;">
                    <button type="button" class="btn fw-bold px-4 py-2" style="background: #206970; color: white; border: none; border-radius: 10px; transition: all 0.3s ease;" onmouseover="this.style.background='#20a060'; this.style.boxShadow='0 8px 20px rgba(32, 160, 96, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#206970'; this.style.boxShadow='none'; this.style.transform='translateY(0)';" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="delete_announcement" class="btn fw-bold px-4 py-2" style="background: #dc2626; color: white; border: none; border-radius: 10px; transition: all 0.3s ease;" onmouseover="this.style.background='#991b1b'; this.style.boxShadow='0 8px 20px rgba(220, 38, 38, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#dc2626'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>



<?php include('../includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 700, once: true });

    function openEdit(id, title, cat, content) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_category').value = cat;
        // Content is JSON encoded to handle newlines
        document.getElementById('edit_content').value = JSON.parse(content);
    }

    function confirmDelete(id, title) {
        document.getElementById('del_id').value = id;
        document.getElementById('del_title').textContent = '"' + title + '"';
    }

    function toggleSectorFields() {
        const typeSelect = document.getElementById('messageTypeSelect').value;
        const priorityField = document.getElementById('priorityField');
        const categoryField = document.getElementById('categoryField');
        const titleLabel = document.getElementById('titleLabel');
        const submitBtn = document.getElementById('submitBtn');
        const hiddenMessageType = document.getElementById('hidden_message_type');

        if (typeSelect.startsWith('sector:')) {
            // Extract sector from value like "sector:Rice"
            const sector = typeSelect.substring(7); // Remove "sector:" prefix
            hiddenMessageType.value = 'sector:' + sector;
            priorityField.style.display = 'block';
            categoryField.style.display = 'none';
            titleLabel.textContent = 'Subject';
            submitBtn.textContent = 'Send Message';
            submitBtn.style.background = 'var(--track-green)';
        } else {
            hiddenMessageType.value = 'announcement';
            priorityField.style.display = 'none';
            categoryField.style.display = 'block';
            titleLabel.textContent = 'Title';
            submitBtn.textContent = 'Publish Now';
            submitBtn.style.background = '';
            submitBtn.classList.add('btn-track');
        }
    }

    function handleNewsSubmit(event) {
        const messageType = document.getElementById('messageTypeSelect').value;
        
        if (messageType.startsWith('sector:')) {
            return TrackUI.confirmForm(event, 'Send this message to the selected sector?', 'Send Message', 'primary');
        } else {
            return TrackUI.confirmForm(event, 'Do you want to publish this announcement?', 'Publish Announcement', 'primary');
        }
    }
</script>
</body>
</html>
