<?php
session_start();
include('../auth/db_connect.php'); 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT first_name, middle_name, last_name, username, sector, role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $first_name = $user['first_name'];
    $middle_name = $user['middle_name'];
    $last_name = $user['last_name'];
    $username = $user['username'];
    $full_name = trim($user['first_name'] . " " . $user['middle_name'] . " " . $user['last_name']);
    $user_role = $user['role'];
    $user_sector = !empty($user['sector']) ? $user['sector'] : "Not Assigned";
    
    $static_membership_type = "Regular Member"; 
    $static_share_capital = "5,450.00"; 
} else {
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
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
    <title>TrackCOOP | Member Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    
    <style>
        /* Dashboard Specific Styles */
        .member-header {
            background: linear-gradient(135deg, var(--track-bg) 0%, var(--track-beige) 100%);
            padding: 60px 0 40px;
            border-bottom: 1px solid rgba(229, 229, 192, 0.4);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .member-header h1 { color: var(--track-dark); letter-spacing: -1.5px; }

        .member-header::after {
            content: ''; position: absolute; top: -20%; right: -5%; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(32,160,96,0.08) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%; z-index: 0; pointer-events: none;
        }

        .footer-track { margin-top: auto; background-color: var(--track-beige); border-top: 1px solid #E5E5C0; padding: 40px 0; color: #4B5563; }
    </style>
    <link rel="stylesheet" href="../includes/footer.css">
</head>
<body>

<?php 
    $active_page = 'dashboard'; 
    $user_role = 'Member';
    $membership_type = $static_membership_type; 
    include('../includes/dashboard_navbar.php'); 
?>

<div class="member-header">
    <div class="container position-relative" style="z-index: 1;">
        <div class="row align-items-center">
            <div class="col-lg-8 fade-in-up">
                <div class="status-badge stagger-1">
                    <span class="spinner-grow spinner-grow-sm me-2 text-success" role="status" style="width: 10px; height: 10px;"></span>
                    System Live
                </div>
                <h1 class="display-3 fw-800 text-dark mb-2 stagger-2">Greetings, <?php echo htmlspecialchars($first_name); ?>!</h1>
                <p class="fs-5 text-muted mb-0 stagger-3">Welcome to the TrackCOOP portal. Manage your membership and sector activities here.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0 fade-in-up delay-1">
                <button class="btn-primary-coop shadow-sm" onclick="TrackUI.show('Do you want to generate your latest share capital statement?', 'Statement Request', 'primary', 'View Now', 'Cancel')">
                    <i class="bi bi-file-earmark-pdf me-2"></i> View Statement
                </button>
            </div>
        </div>
    </div>
</div>

<main class="container mb-5">
    <div class="row g-4 mb-5">
        <div class="col-md-4 fade-in-up delay-1">
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-square bg-success bg-opacity-10 text-success fs-4"><i class="bi bi-piggy-bank"></i></div>
                    <div class="text-success small fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Verified</div>
                </div>
                <p class="text-muted mb-2 fw-bold small text-uppercase mt-3" style="letter-spacing: 0.5px;">Total Share Capital</p>
                <h2 class="fw-800 mb-0 text-dark">PHP <?php echo $static_share_capital; ?></h2>
            </div>
        </div>

        <div class="col-md-4 fade-in-up delay-2">
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-square bg-primary bg-opacity-10 text-primary fs-4"><i class="bi bi-geo-alt"></i></div>
                </div>
                <p class="text-muted mb-2 fw-bold small text-uppercase mt-3" style="letter-spacing: 0.5px;">Assigned Sector</p>
                <h2 class="fw-800 mb-0 text-dark"><?php echo htmlspecialchars($user_sector); ?></h2>
                <div class="small text-muted mt-2">Nasugbu Agricultural Sector</div>
            </div>
        </div>

        <div class="col-md-4 fade-in-up delay-3">
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="icon-square bg-warning bg-opacity-10 text-warning fs-4"><i class="bi bi-award"></i></div>
                </div>
                <p class="text-muted mb-2 fw-bold small text-uppercase mt-3" style="letter-spacing: 0.5px;">Membership Level</p>
                <h2 class="fw-800 mb-0 text-dark"><?php echo htmlspecialchars($static_membership_type); ?></h2>
                <a href="#" class="text-success text-decoration-none small fw-bold mt-2 d-inline-block">View Benefits →</a>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header border-0 p-4" style="background: var(--track-beige) !important; border-bottom: 1px solid rgba(229, 229, 192, 0.8) !important;">
                <div class="d-flex align-items-center">
                    <div class="icon-square bg-success bg-opacity-10 text-success fs-5 me-3 mb-0" style="width: 40px; height: 40px;"><i class="bi bi-pencil-square"></i></div>
                    <h5 class="modal-title fw-800 text-dark" style="letter-spacing: -1px;">Edit Profile Settings</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <div class="modal-footer border-0 p-4 pt-0" style="background: var(--track-beige) !important; border-top: 1px solid rgba(229, 229, 192, 0.8) !important; padding-top: 24px !important;">
                    <button type="button" class="btn btn-light fw-bold py-2 px-4 rounded-3 border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-coop py-2 px-5 shadow-lg border-0">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<?php include('../includes/footer.php'); ?>

<script>
    AOS.init({ once: true, duration: 800 });
    
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