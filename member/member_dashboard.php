<?php
// ── FULLY STATIC MEMBER DASHBOARD (Demo Mode) ──

// Static Identity
$user_id   = 1;
$first_name = "Member";
$last_name  = "";
$username   = "member";
$full_name  = "Member";
$user_role  = 'Member';
$user_sector = "Rice";
$static_membership_type = "Member";
$static_share_capital   = "5,450.00";

// 1. Announcements Snippet
$dashboard_announcements = [
    ['title' => 'Annual General Assembly 2024', 'cat' => 'General', 'date' => 'Mar 20, 2024', 'author' => 'Admin User', 'avatar' => 'AU', 'desc' => 'The annual general assembly will be held this March. All members are encouraged to attend and participate in key cooperative decisions.'],
    ['title' => 'Rice Sector: Irrigation Update', 'cat' => 'Sector News', 'date' => 'Mar 08, 2024', 'author' => 'Bookkeeper', 'avatar' => 'B', 'desc' => 'New irrigation schedules have been issued for rice sector members. Please coordinate with your sector head for the updated timetable.'],
    ['title' => 'Loan Application Deadline', 'cat' => 'Deadlines', 'date' => 'Mar 10, 2024', 'author' => 'Admin User', 'avatar' => 'AU', 'desc' => 'The deadline for loan applications for this quarter is fast approaching. Submit all required documents to the cooperative office before the cutoff.'],
];

// 2. Capital Activity Snippet
$capital_activity = [
    ['type' => 'deposit', 'amount' => '1,200.00', 'date' => 'Apr 02, 2024', 'ref' => 'REF782'],
    ['type' => 'deposit', 'amount' => '2,250.00', 'date' => 'Mar 15, 2024', 'ref' => 'REF551'],
    ['type' => 'deposit', 'amount' => '2,000.00', 'date' => 'Feb 20, 2024', 'ref' => 'REF239'],
];

// 3. Document Vault Snippet
$vault_docs = [
    ['name' => 'Bylaws and Constitution', 'type' => 'PDF', 'size' => '245 KB'],
    ['name' => 'Member Handbook 2024',    'type' => 'PDF', 'size' => '512 KB'],
    ['name' => 'Loan Application Form',   'type' => 'DOCX', 'size' => '120 KB'],
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Portal | TrackCOOP Elite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            background: #f8fafc;
            min-height: 100vh;
        }

        .unified-container { padding-top: 30px; padding-bottom: 60px; }

        /* ── Glassmorphic Sections ── */
        .glass-panel {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 28px;
            padding: 30px;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1);
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }
        .glass-panel:hover { border-color: rgba(32, 160, 96, 0.3); }

        .panel-title {
            font-weight: 800; font-size: 1.1rem; color: #1e293b;
            letter-spacing: -0.5px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
        }
        .panel-title i { color: #20a060; }

        /* ── Announcement Feed ── */
        .feed-item {
            display: flex; gap: 16px; padding: 16px; border-radius: 20px;
            background: #ffffff; border: 1.5px solid #f1f5f9;
            transition: all 0.2s ease; cursor: pointer; margin-bottom: 12px;
        }
        .feed-item:hover { transform: translateX(5px); border-color: #20a060; background: #f0fdf4; }
        
        .feed-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }

        /* ── Capital Activity ── */
        .activity-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 12px 0; border-bottom: 1px solid #f1f5f9;
        }
        .activity-row:last-child { border-bottom: none; }

        .amount-green { color: #15803d; font-weight: 800; }
        .amount-red { color: #b91c1c; font-weight: 800; }

        /* ── Document Vault ── */
        .doc-vault-item {
            padding: 15px; border-radius: 16px; background: #ffffff;
            border: 1.5px solid #f1f5f9; transition: 0.3s;
            display: flex; align-items: center; gap: 12px;
        }
        .doc-vault-item:hover { border-color: #206970; background: #f8fafc; }

        /* ── Elite Cards ── */
        .card-elite {
            background: #ffffff; border-radius: 24px; padding: 24px;
            border: 1.5px solid #f1f5f9; transition: 0.4s; height: 100%;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .card-elite:hover { transform: translateY(-8px); border-color: #20a060; box-shadow: 0 20px 40px -10px rgba(32,160,96,0.15); }

        .stat-icon {
            width: 48px; height: 48px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; margin-bottom: 20px;
        }

        .btn-portal-sm {
            padding: 8px 20px; border-radius: 50px; font-weight: 700;
            font-size: 0.85rem; transition: 0.3s; border: none;
            background: var(--track-green); color: white;
        }
        .btn-portal-sm:hover { transform: translateY(-2px); opacity: 0.9; }

        .btn-glass {
            background: rgba(32, 160, 96, 0.1); color: #1a5c38;
            border: none; border-radius: 12px; padding: 10px 15px;
            font-weight: 700; font-size: 0.9rem; transition: 0.3s;
        }
        .btn-glass:hover { background: #20a060; color: white; }

        /* ── NEW ELITE 3.0 PREMIUM DESIGN ── */
        .elite-card {
            background: #ffffff;
            border-radius: 48px;
            padding: 35px;
            box-shadow: 0 15px 45px -10px rgba(32,160,96,0.12);
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .elite-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 55px -12px rgba(32,160,96,0.18);
        }

        .elite-icon-sq {
            width: 72px;
            height: 72px;
            background: #20a060;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            box-shadow: 0 10px 25px -5px rgba(32,160,96,0.3);
            transition: 0.3s;
        }
        .elite-icon-sq i {
            color: white;
            font-size: 1.8rem;
        }

        .elite-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 900;
            color: #20a060;
            font-size: 1.35rem;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
        }

        .elite-desc {
            font-family: 'Inter', sans-serif;
            color: #64748b;
            font-size: 0.98rem;
            line-height: 1.6;
            margin-bottom: 0;
            font-weight: 500;
        }

        .elite-stat {
            font-size: 2.2rem;
            font-weight: 900;
            color: #1e293b;
            letter-spacing: -1.5px;
            margin-top: 5px;
        }

        .dashboard-field {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px 16px;
            font-weight: 900;
            color: #1e293b;
            font-size: 0.9rem;
            width: 100%;
            outline: none;
            transition: all 0.2s ease;
        }
        .dashboard-field:focus {
            border-color: #20a060;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(32,160,96,0.1);
        }

        /* ── COMPACT ELITE CARDS ── */
        .elite-card-compact {
            background: #ffffff;
            border-radius: 30px;
            padding: 16px 20px;
            box-shadow: 0 8px 30px -8px rgba(32,160,96,0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
            height: 100%;
        }
        .elite-card-compact:hover {
            transform: scale(1.02);
            box-shadow: 0 12px 40px -10px rgba(32,160,96,0.15);
        }

        .compact-icon-sq {
            width: 44px;
            height: 44px;
            background: #20a060;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 6px 12px rgba(32,160,96,0.2);
        }
        .compact-icon-sq i {
            color: white;
            font-size: 1.1rem;
        }

        .compact-title {
            font-size: 0.82rem;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .compact-value {
            font-size: 1.1rem;
            font-weight: 900;
            color: #1e293b;
            line-height: 1.2;
        }
    </style>
</head>
<body>

<?php 
    $active_page = 'dashboard'; 
    $user_role = 'Member';
    $membership_type = $static_membership_type; 
    include('../includes/dashboard_navbar.php'); 
?>

<div class="unified-container container">



    <!-- TOP SUMMARY BAR: 4-COLUMN LAYOUT -->
    <div class="row g-3 mb-4 fade-in-up">
        <!-- Card 1: Member -->
        <div class="col-lg-3">
            <div class="elite-card-compact h-100">
                <div class="compact-icon-sq" style="background:#20a060;">
                    <span style="font-size: 1.1rem; font-weight: 900; color: white;">
                        <?php echo strtoupper(substr($first_name,0,1)) . strtoupper(substr($last_name,0,1)); ?>
                    </span>
                </div>
                <div>
                    <div class="compact-title">Member</div>
                    <div class="compact-value" style="font-size:1.05rem;"><?php echo htmlspecialchars($full_name); ?></div>
                </div>
            </div>
        </div>

        <!-- Card 2: Share Capital -->
        <div class="col-lg-3">
            <div class="elite-card-compact h-100">
                <div class="compact-icon-sq" style="background: #20a060;">
                    <i class="bi bi-wallet2 text-white"></i>
                </div>
                <div>
                    <div class="compact-title">Share Capital</div>
                    <div class="compact-value">₱ <?php echo $static_share_capital; ?></div>
                </div>
            </div>
        </div>

        <!-- Card 3: Document -->
        <div class="col-lg-3">
            <div class="elite-card-compact h-100">
                <div class="compact-icon-sq">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                </div>
                <div>
                    <div class="compact-title">Document</div>
                    <div class="compact-value">Analytics</div>
                </div>
            </div>
        </div>

        <!-- Card 4: Sector Info -->
        <div class="col-lg-3">
            <div class="elite-card-compact h-100">
                <div class="compact-icon-sq" style="background: #20a060;">
                    <i class="bi bi-geo-alt text-white"></i>
                </div>
                <div>
                    <div class="compact-title"><?php echo $user_sector; ?> Sector</div>
                    <div class="compact-value">Intelligence</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Two Column: Personal + Family -->
    <div class="row g-3 mb-4">

        <!-- Personal Information -->
        <div class="col-lg-6">
            <div class="elite-card">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <span class="fw-900" style="color:#20a060;font-size:0.82rem;letter-spacing:0.5px;">PERSONAL INFORMATION</span>
                    <button class="btn btn-sm d-flex align-items-center gap-1 border-0" style="background: rgba(245,158,11,0.1); color: #f59e0b; border-radius: 10px; font-size: 0.72rem; font-weight: 800; padding: 4px 12px;">
                        <i class="bi bi-pencil-square"></i> EDIT
                    </button>
                </div>
                <div class="row g-4">
                    <div class="col-6">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">First Name</div>
                        <input type="text" class="dashboard-field" value="<?php echo htmlspecialchars($first_name); ?>">
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Middle Name</div>
                        <input type="text" class="dashboard-field" value="">
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Last Name</div>
                        <input type="text" class="dashboard-field" value="<?php echo htmlspecialchars($last_name); ?>">
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Email</div>
                        <input type="email" class="dashboard-field" value="">
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Phone</div>
                        <input type="tel" class="dashboard-field" value="">
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Status</div>
                        <input type="text" class="dashboard-field" value="">
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Place of Birth</div>
                        <input type="text" class="dashboard-field" value="">
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Birthday</div>
                        <input type="text" class="dashboard-field" value="">
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Current Home</div>
                        <input type="text" class="dashboard-field" value="">
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Work / Occupation</div>
                        <input type="text" class="dashboard-field" value="">
                    </div>
                </div>
            </div>
        </div>

        <!-- Family Information -->
        <div class="col-lg-6">
            <div class="elite-card">
                <div class="mb-3 d-flex align-items-center justify-content-between">
                    <span class="fw-900" style="color:#20a060;font-size:0.82rem;letter-spacing:0.5px;">FAMILY INFORMATION</span>
                    <button class="btn btn-sm d-flex align-items-center gap-1 border-0" style="background: rgba(245,158,11,0.1); color: #f59e0b; border-radius: 10px; font-size: 0.72rem; font-weight: 800; padding: 4px 12px;">
                        <i class="bi bi-pencil-square"></i> EDIT
                    </button>
                </div>
                <div class="row g-4 mb-3">
                    <div class="col-4">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Father's Name</div>
                        <input type="text" class="dashboard-field" value="">
                    </div>
                    <div class="col-4">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Mother's Name</div>
                        <input type="text" class="dashboard-field" value="">
                    </div>
                    <div class="col-4">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Wife's Name</div>
                        <input type="text" class="dashboard-field" value="">
                    </div>
                </div>

                <hr style="border-color:#f1f5f9;margin:20px 0;">

                <div class="mb-3">
                    <span class="fw-900" style="color:#20a060;font-size:0.82rem;letter-spacing:0.5px;">CHILDREN INFORMATION</span>
                </div>
                <div class="row g-4">
                    <div class="col-4">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Child's Name</div>
                        <input type="text" class="dashboard-field" value="">
                    </div>
                    <div class="col-4">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Age</div>
                        <input type="number" class="dashboard-field" value="">
                    </div>
                    <div class="col-4">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Benefit</div>
                        <input type="text" class="dashboard-field" value="None">
                    </div>
                </div>

                <hr style="border-color:#f1f5f9;margin:20px 0;">

                <div class="mb-3">
                    <span class="fw-900" style="color:#20a060;font-size:0.82rem;letter-spacing:0.5px;">ACCOUNT SECURITY</span>
                </div>
                <div class="row g-4">
                    <div class="col-6">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Username</div>
                        <input type="text" class="dashboard-field" value="<?php echo htmlspecialchars($username); ?>">
                    </div>
                    <div class="col-6">
                        <div class="text-muted small mb-2 fw-700" style="letter-spacing: 0.3px;">Password</div>
                        <input type="password" class="dashboard-field" value="password" style="letter-spacing: 3px;">
                    </div>
                </div>
            </div>
        </div>



    </div>

</div>













<!-- MODALS -->

<!-- PROFILE INFO MODAL -->
<div class="modal fade" id="profileInfoModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius:24px;overflow:hidden;">
            <div class="modal-header border-0 px-4 py-3 d-flex align-items-center justify-content-center position-relative" style="background:#164a36;">
                <div class="d-flex align-items-center gap-2">
                    <img src="../TrackCOOP Logo.svg" alt="Logo" style="height:30px;">
                    <span style="color:white;font-weight:900;font-size:1.2rem;letter-spacing:-1px;">Track<span style="color:#20a060;">COOP</span></span>
                </div>
                <button type="button" class="btn border-0 position-absolute end-0 me-3" data-bs-dismiss="modal" style="color:white;background:transparent;font-size:1.2rem;"><i class="bi bi-x-lg"></i></button>
            </div>
            <div class="modal-body px-5 py-4">
                <!-- PAGE 1 -->
                <div id="profilePage1">
                    <p class="text-center fw-900 mb-3" style="color:#20a060;font-size:0.85rem;">Page 1 of 2</p>
                    <h6 class="fw-900 mb-4" style="color:#20a060;">PERSONAL DETAILS</h6>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="profile-label">FIRST NAME</label><input type="text" class="profile-input" value="<?php echo htmlspecialchars($first_name); ?>"></div>
                        <div class="col-md-6"><label class="profile-label">MIDDLE NAME</label><input type="text" class="profile-input" placeholder="Middle name"></div>
                        <div class="col-md-6"><label class="profile-label">LAST NAME</label><input type="text" class="profile-input" value="<?php echo htmlspecialchars($last_name); ?>" placeholder="Last name"></div>
                        <div class="col-md-6"><label class="profile-label">EMAIL</label><input type="email" class="profile-input" placeholder="Email address"></div>
                        <div class="col-md-6">
                            <label class="profile-label">PHONE</label>
                            <div class="d-flex gap-2">
                                <div class="profile-input text-center fw-bold" style="width:60px;flex-shrink:0;color:#555;">+63</div>
                                <input type="tel" class="profile-input flex-grow-1" placeholder="9123456789">
                            </div>
                        </div>
                        <div class="col-md-6"><label class="profile-label">STATUS</label><select class="profile-input"><option value="">-- Select Status --</option><option>Single</option><option>Married</option><option>Widowed</option></select></div>
                        <div class="col-md-6"><label class="profile-label">PLACE OF BIRTH</label><input type="text" class="profile-input" placeholder="Place of birth"></div>
                        <div class="col-md-6"><label class="profile-label">BIRTHDAY</label><input type="date" class="profile-input"></div>
                        <div class="col-md-6"><label class="profile-label">CURRENT HOME</label><select class="profile-input"><option value="">-- Select Barangay --</option><option>Barangay Biliran</option><option>Barangay Bucana</option><option>Barangay Nasugbu</option></select></div>
                        <div class="col-md-6"><label class="profile-label">WORK/OCCUPATION</label><input type="text" class="profile-input" placeholder="Occupation"></div>
                        <div class="col-md-6"><label class="profile-label">SECTOR</label><input type="text" class="profile-input" value="<?php echo htmlspecialchars($user_sector); ?>" readonly style="background:#f0f0f0;color:#888;cursor:not-allowed;"></div>
                        <div class="col-md-6"><label class="profile-label">ROLE</label><input type="text" class="profile-input" value="<?php echo htmlspecialchars($user_role); ?>" readonly style="background:#f0f0f0;color:#888;cursor:not-allowed;"></div>
                    </div>
                    <div class="d-flex gap-3 mt-4">
                        <button type="button" class="btn fw-800 py-3 w-50 rounded-pill" data-bs-dismiss="modal" style="background:#ef4444;color:white;">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                        <button type="button" class="btn fw-800 py-3 w-50 rounded-pill" onclick="document.getElementById('profilePage1').style.display='none';document.getElementById('profilePage2').style.display='block';" style="background:#20a060;color:white;">
                            Next <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                    </div>
                </div>
                <!-- PAGE 2 -->
                <div id="profilePage2" style="display:none;">
                    <p class="text-center fw-900 mb-3" style="color:#20a060;font-size:0.85rem;">Page 2 of 2</p>
                    <h6 class="fw-900 mb-3" style="color:#20a060;">FAMILY INFORMATION</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4"><label class="profile-label">FATHER'S NAME</label><input type="text" class="profile-input" placeholder="Father's name"></div>
                        <div class="col-md-4"><label class="profile-label">MOTHER'S NAME</label><input type="text" class="profile-input" placeholder="Mother's name"></div>
                        <div class="col-md-4"><label class="profile-label">WIFE'S NAME</label><input type="text" class="profile-input" placeholder="Wife's name"></div>
                    </div>
                    <h6 class="fw-900 mb-3" style="color:#20a060;">CHILDREN INFORMATION</h6>
                    <div class="row g-3 mb-4 align-items-end" id="childrenRows">
                        <div class="col-md-5"><label class="profile-label">CHILD'S NAME</label><input type="text" class="profile-input" placeholder="Child's name"></div>
                        <div class="col-md-3"><label class="profile-label">AGE</label><input type="number" class="profile-input" placeholder="Age"></div>
                        <div class="col-md-3"><label class="profile-label">BENEFIT</label><select class="profile-input"><option>None</option><option>Education</option><option>Health</option></select></div>
                        <div class="col-md-1 pb-1"><button class="btn rounded-circle border-0" style="width:38px;height:38px;background:#20a060;color:white;" onclick="addChildRow()"><i class="bi bi-plus"></i></button></div>
                    </div>
                    <h6 class="fw-900 mb-3" style="color:#20a060;">ACCOUNT SECURITY</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4"><label class="profile-label">USERNAME</label><input type="text" class="profile-input" value="<?php echo htmlspecialchars($username); ?>"></div>
                        <div class="col-md-4"><label class="profile-label">NEW PASSWORD</label><input type="password" class="profile-input" placeholder="Leave blank to keep current"></div>
                        <div class="col-md-4"><label class="profile-label">CONFIRM PASSWORD</label><input type="password" class="profile-input" placeholder="Confirm new password"></div>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="button" class="btn fw-800 py-3 w-50 rounded-pill" data-bs-dismiss="modal" style="background:#ef4444;color:white;"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                        <button type="button" class="btn fw-800 py-3 w-50 rounded-pill" onclick="document.getElementById('profilePage1').style.display='block';document.getElementById('profilePage2').style.display='none';" style="background:#164a36;color:white;"><i class="bi bi-chevron-left me-1"></i> Back</button>
                        <button type="button" class="btn fw-800 py-3 w-50 rounded-pill" onclick="TrackUI.show('Profile updated! (Demo mode — changes are not saved.)', 'Profile Updated', 'primary', 'OK')" style="background:#20a060;color:white;"><i class="bi bi-floppy me-2"></i>Save Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.profile-label { display:block; font-size:0.72rem; font-weight:800; letter-spacing:0.8px; color:#1e293b; margin-bottom:6px; }
.profile-input { width:100%; padding:13px 18px; border:2px solid #e8f5ee; border-radius:14px; background:#f8fdf9; font-size:0.9rem; color:#1e293b; outline:none; transition:0.2s; appearance:auto; }
.profile-input:focus { border-color:#20a060; background:#fff; box-shadow:0 0 0 3px rgba(32,160,96,0.1); }
.profile-input::placeholder { color:#aeb8c4; }
</style>
<script>
function addChildRow() {
    const c = document.getElementById('childrenRows');
    const r = document.createElement('div'); r.className = 'col-12 mt-2';
    r.innerHTML = `<div class="row g-3 align-items-center"><div class="col-md-5"><input type="text" class="profile-input" placeholder="Child's name"></div><div class="col-md-3"><input type="number" class="profile-input" placeholder="Age"></div><div class="col-md-3"><select class="profile-input"><option>None</option><option>Education</option><option>Health</option></select></div><div class="col-md-1"><button class="btn rounded-circle border-0" style="width:38px;height:38px;background:#ef4444;color:white;" onclick="this.closest('.col-12').remove()"><i class="bi bi-dash"></i></button></div></div>`;
    c.appendChild(r);
}
document.getElementById('profileInfoModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('profilePage1').style.display = 'block';
    document.getElementById('profilePage2').style.display = 'none';
});
</script>

<!-- ANNOUNCEMENT MODAL -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">

            <!-- Filter Pills Header -->
            <div class="modal-header border-0 px-4 py-3 d-flex align-items-center justify-content-between" style="background: #164a36;">
                <div class="d-flex gap-2 flex-wrap">
                    <button class="ann-filter-pill active" data-filter="all">All</button>
                    <button class="ann-filter-pill" data-filter="General">General</button>
                    <button class="ann-filter-pill" data-filter="Events">Events</button>
                    <button class="ann-filter-pill" data-filter="Meetings">Meetings</button>
                    <button class="ann-filter-pill" data-filter="Deadlines">Deadlines</button>
                    <button class="ann-filter-pill" data-filter="Sector News">Sector News</button>
                </div>
                <button type="button" class="btn rounded-circle d-flex align-items-center justify-content-center border-0" data-bs-dismiss="modal" style="width:38px;height:38px;background:rgba(255,255,255,0.15);color:white;flex-shrink:0;">
                    <i class="bi bi-x-lg fw-bold"></i>
                </button>
            </div>

            <!-- Announcement Cards -->
            <div class="modal-body p-4" style="height:480px; overflow-y:auto;">
                <?php foreach($dashboard_announcements as $ann): ?>
                <div class="ann-card mb-3 p-4" data-cat="<?php echo $ann['cat']; ?>" style="background:#fff; border:1px solid #e8ecf0; border-radius:18px; box-shadow:0 2px 12px rgba(0,0,0,0.05); transition:0.3s;">
                    <div class="d-flex align-items-start gap-3">
                        <!-- Avatar -->
                        <div style="width:52px;height:52px;border-radius:14px;background:#20a060;color:white;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.9rem;flex-shrink:0;">
                            <?php echo $ann['avatar']; ?>
                        </div>
                        <!-- Content -->
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-800 mb-0" style="color:#1e293b;font-size:1rem;"><?php echo htmlspecialchars($ann['title']); ?></h6>
                                <span style="background:#f0fdf4;color:#20a060;border:1.5px solid #bbf7d0;border-radius:20px;padding:3px 12px;font-size:0.7rem;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap;margin-left:12px;"><?php echo $ann['cat']; ?></span>
                            </div>
                            <p class="text-muted small mb-2" style="line-height:1.6;"><?php echo htmlspecialchars($ann['desc']); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="bi bi-person me-1"></i><?php echo $ann['author']; ?></span>
                                <span class="text-muted small"><i class="bi bi-calendar3 me-1"></i><?php echo $ann['date']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.ann-filter-pill {
    background: rgba(255,255,255,0.12);
    color: rgba(255,255,255,0.8);
    border: 1.5px solid rgba(255,255,255,0.2);
    border-radius: 50px;
    padding: 6px 18px;
    font-weight: 700;
    font-size: 0.82rem;
    cursor: pointer;
    transition: 0.2s;
}
.ann-filter-pill:hover {
    background: rgba(255,255,255,0.2);
    color: white;
}
.ann-filter-pill.active {
    background: #20a060;
    color: white;
    border-color: #20a060;
}
.ann-card:hover {
    border-color: #20a060 !important;
    box-shadow: 0 8px 24px rgba(32,160,96,0.12) !important;
    transform: translateY(-2px);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.ann-filter-pill').forEach(function(pill) {
        pill.addEventListener('click', function() {
            document.querySelectorAll('.ann-filter-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('.ann-card').forEach(function(card) {
                card.style.display = (filter === 'all' || card.dataset.cat === filter) ? '' : 'none';
            });
        });
    });
});
</script>

<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header border-0 p-4" style="background: rgba(22, 74, 54, 0.95) !important; border-bottom: 1px solid rgba(22, 74, 54, 0.3) !important; color: white;">
                <div class="d-flex align-items-center">
                    <div class="icon-square bg-success bg-opacity-10 text-success fs-5 me-3 mb-0" style="width: 40px; height: 40px; display: flex; align-items:center; justify-content:center; border-radius:10px;"><i class="bi bi-pencil-square"></i></div>
                    <h5 class="modal-title fw-800 text-white m-0" style="letter-spacing: -1px;">Account Settings</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
            </div>
            <form action="update_profile.php" method="POST">
                <div class="modal-body p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted opacity-75 mb-2" style="letter-spacing: 0.5px;">FIRST NAME</label>
                            <input type="text" name="first_name" class="form-control rounded-3 py-2 border-opacity-25" value="<?php echo htmlspecialchars($first_name); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted opacity-75 mb-2" style="letter-spacing: 0.5px;">LAST NAME</label>
                            <input type="text" name="last_name" class="form-control rounded-3 py-2 border-opacity-25" value="<?php echo htmlspecialchars($last_name); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted opacity-75 mb-2" style="letter-spacing: 0.5px;">USERNAME</label>
                            <input type="text" name="username" class="form-control rounded-3 py-2 border-opacity-25" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted opacity-75 mb-2" style="letter-spacing: 0.5px;">PASSWORD</label>
                            <input type="password" name="new_password" class="form-control rounded-3 py-2 border-opacity-25" placeholder="Leave blank to keep current">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0" style="background: rgba(22, 74, 54, 1) !important; color: white;">
                    <button type="button" class="btn fw-bold py-2 px-4 rounded-3 border-0" style="background: #206970; color: white;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold py-2 px-5 rounded-3 shadow-lg border-0" style="background: #20a060;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>



<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    AOS.init({ once: true, duration: 800 });
    
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('update') === 'success') {
            if(typeof TrackUI !== 'undefined') TrackUI.toast('Profile updated successfully.', 'success');
            else alert('Profile updated successfully.');
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    });
</script>
</body>
</html>
</html>