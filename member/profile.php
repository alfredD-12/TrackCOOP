<?php
session_start();
include('../auth/db_connect.php'); 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Static user data with session info
$first_name = isset($_SESSION['fname']) ? $_SESSION['fname'] : "Member";
$middle_name = "Demo";
$last_name = "User";
$username = $user_id;
$full_name = isset($_SESSION['fname']) ? $_SESSION['fname'] : "Member";
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : "Member";
$user_sector = "Rice";
$static_membership_type = "Member";

// Try to fetch from database, but use static values if unavailable
@$query = "SELECT first_name, middle_name, last_name, username, sector, role FROM users WHERE id = ?";
@$stmt = $conn->prepare($query);
if ($stmt) {
    @$stmt->bind_param("i", $user_id);
    @$stmt->execute();
    @$result = $stmt->get_result();
    if ($user = @$result->fetch_assoc()) {
        $first_name = $user['first_name'];
        $middle_name = $user['middle_name'];
        $last_name = $user['last_name'];
        $username = $user['username'];
        $full_name = trim($user['first_name'] . " " . $user['middle_name'] . " " . $user['last_name']);
        $user_role = $user['role'];
        $user_sector = !empty($user['sector']) ? $user['sector'] : "Not Assigned";
    }
}

if ($user_role !== 'Member') {
    header("Location: unauthorized.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Information | TrackCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    
    <style>
        /* Profile Page Specific Styles */
        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-main);
            background: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)), url('../Home.jpeg') top center / 100% 100% no-repeat fixed;
            overflow-x: hidden;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .navbar {
            background-color: rgba(22, 74, 54, 0.95) !important;
            backdrop-filter: blur(10px);
            padding: 15px 0;
            border-bottom: 2px solid rgba(32, 160, 96, 0.3);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .profile-header {
            background: transparent !important;
            padding: 70px 0 50px;
            border-bottom: none;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
            z-index: 10;
        }
        .profile-header h1 { 
            color: #20a060 !important; 
            letter-spacing: -2px; 
            font-weight: 800 !important;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .profile-header p { 
            color: #ffffff !important; 
            font-weight: 400;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            opacity: 0.95;
        }

        /* ── Elite Profile Card ── */
        .card-custom {
            background: #ffffff !important;
            border: 2.5px solid #20a060 !important;
            border-radius: 30px !important;
            padding: 32px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
            position: relative;
            overflow: hidden;
            opacity: 1 !important;
        }

        .icon-square {
            width: 54px; height: 54px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            transition: 0.3s;
        }

        .info-box {
            padding: 24px;
            background: #f8fafc !important;
            border-radius: 20px !important;
            border: 1.5px solid #e2e8f0 !important;
            transition: all 0.3s ease;
        }
        .info-box:hover {
            border-color: #20a060 !important;
            background: #ffffff !important;
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.08);
            transform: translateY(-3px);
        }

        .btn-primary-coop {
            background: var(--track-green);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 28px;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.3);
        }
        .btn-primary-coop:hover {
            background: #1a8548;
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(32, 160, 96, 0.4);
            color: white;
        }

        .footer-track { margin-top: auto; background-color: #fdfdf8; border-top: 1px solid #E5E5C0; padding: 40px 0; color: #4B5563; }
    </style>
    <link rel="stylesheet" href="../includes/footer.css">
</head>
<body>

<?php 
    $active_page = 'profile'; 
    $user_role = 'Member';
    $membership_type = $static_membership_type; 
    include('../includes/dashboard_navbar.php'); 
?>

<div class="profile-header">
    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-center">
            <div class="col-lg-12 fade-in-up">
                <h1 class="display-4 fw-800 mb-0">Account Information</h1>
                <p class="fs-5 mb-0">Manage your account information and security settings here.</p>
            </div>
        </div>
    </div>
</div>

<main class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-custom p-4 p-md-5 fade-in-up delay-1">
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between mb-5">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <div class="icon-square bg-success bg-opacity-10 text-success fs-4 me-3 mb-0" style="width: 54px; height: 54px;"><i class="bi bi-person-badge-fill"></i></div>
                        <div>
                            <h3 class="fw-800 mb-1" style="letter-spacing: -1.2px;">Profile Overview</h3>
                            <p class="text-muted small mb-0 max-width-text">Maintain your digital identity by keeping your personal details and credentials up to date.</p>
                        </div>
                    </div>
                    <button class="btn btn-primary-coop px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="bi bi-pencil-square me-2"></i> Edit Account Details
                    </button>
                </div>

                <div class="row g-4">
                    <div class="col-md-6 fade-in-up delay-1">
                        <div class="info-box">
                            <small class="text-muted text-uppercase fw-800 d-block mb-2" style="font-size: 10px; letter-spacing: 1.5px;">Username</small>
                            <h5 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($username); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-6 fade-in-up delay-1">
                        <div class="info-box">
                            <small class="text-muted text-uppercase fw-800 d-block mb-2" style="font-size: 10px; letter-spacing: 1.5px;">Full Name</small>
                            <h5 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($full_name); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-6 fade-in-up delay-2">
                        <div class="info-box">
                            <small class="text-muted text-uppercase fw-800 d-block mb-2" style="font-size: 10px; letter-spacing: 1.5px;">Assigned Sector</small>
                            <h5 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($user_sector); ?></h5>
                        </div>
                    </div>
                    <div class="col-md-6 fade-in-up delay-2">
                        <div class="info-box">
                            <small class="text-muted text-uppercase fw-800 d-block mb-2" style="font-size: 10px; letter-spacing: 1.5px;">Member Category</small>
                            <h5 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($static_membership_type); ?></h5>
                        </div>
                    </div>
                </div>

                <div class="mt-5 p-4 rounded-4 border border-warning border-opacity-25 fade-in-up delay-3" style="background: rgba(255, 193, 7, 0.05);">
                     <div class="d-flex align-items-center">
                        <div class="icon-square bg-warning bg-opacity-10 text-warning mb-0 me-3" style="width: 40px; height: 40px; min-width: 40px;">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Security Reminder</h6>
                            <p class="text-muted small mb-0">Change your password regularly and never share your account credentials. TrackCOOP will never ask for your password via email or message.</p>
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- EDIT PROFILE MODAL -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header border-0 p-4" style="background: rgba(22, 74, 54, 0.95) !important; border-bottom: 1px solid rgba(22, 74, 54, 0.3) !important; color: white;">
                <div class="d-flex align-items-center">
                    <div class="icon-square bg-success bg-opacity-10 text-success fs-5 me-3 mb-0" style="width: 40px; height: 40px;"><i class="bi bi-pencil-square"></i></div>
                    <h5 class="modal-title fw-800 text-white" style="letter-spacing: -1px;">Edit Profile Settings</h5>
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
                            <label class="form-label small fw-800 text-muted opacity-75 mb-2" style="letter-spacing: 0.5px;">MIDDLE NAME</label>
                            <input type="text" name="middle_name" class="form-control rounded-3 py-2 border-opacity-25" value="<?php echo htmlspecialchars($middle_name); ?>">
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
                            <label class="form-label small fw-800 text-muted opacity-75 mb-2" style="letter-spacing: 0.5px;">AGRICULTURAL SECTOR</label>
                            <select name="sector" class="form-select rounded-3 py-2 border-opacity-25" required>
                                <option value="Rice" <?php echo ($user_sector == 'Rice') ? 'selected' : ''; ?>>Rice</option>
                                <option value="Corn" <?php echo ($user_sector == 'Corn') ? 'selected' : ''; ?>>Corn</option>
                                <option value="Fishery" <?php echo ($user_sector == 'Fishery') ? 'selected' : ''; ?>>Fishery</option>
                                <option value="Livestock" <?php echo ($user_sector == 'Livestock') ? 'selected' : ''; ?>>Livestock</option>
                                <option value="High Value Crops" <?php echo ($user_sector == 'High Value Crops') ? 'selected' : ''; ?>>High Value Crops</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted opacity-75 mb-2" style="letter-spacing: 0.5px;">SECURITY & PASSWORD</label>
                            <input type="password" name="new_password" class="form-control rounded-3 py-2 border-opacity-25" placeholder="Leave blank to keep current">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0" style="background: rgba(22, 74, 54, 0.95) !important; border-top: 1px solid rgba(22, 74, 54, 0.3) !important; padding-top: 24px !important; color: white;">
                    <button type="button" class="btn fw-bold py-2 px-4 rounded-3 border-0" style="background: #206970; color: white; transition: all 0.3s ease;" onmouseover="this.style.background='#20a060'; this.style.boxShadow='0 8px 20px rgba(32, 160, 96, 0.3)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#206970'; this.style.boxShadow='none'; this.style.transform='translateY(0)';" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-coop py-2 px-5 shadow-lg border-0">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('update') === 'success') {
            TrackUI.toast('Account information has been securely updated in our system.', 'success');
            // Clean URL
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        } else if (urlParams.get('update') === 'error') {
            TrackUI.toast('There was an error updating your profile. Please try again.', 'danger');
            const newUrl = window.location.pathname;
            window.history.replaceState({}, document.title, newUrl);
        }
    });
</script>
</body>
</html>
