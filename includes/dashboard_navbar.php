<?php
// includes/dashboard_navbar.php
// Variables expected: $active_page, $full_name, $membership_type, $user_role

$is_admin = (isset($user_role) && $user_role === 'Admin');
$is_bookkeeper = (isset($user_role) && $user_role === 'Bookkeeper');
$is_member = (isset($user_role) && $user_role === 'Member');

// Define Dashboard URL based on role
$dashboard_url = '../member/member_dashboard.php';
if ($is_admin) $dashboard_url = '../admin/admin_dashboard.php';
elseif ($is_bookkeeper) $dashboard_url = '../bookkeeper/bookkeeper_dashboard.php';

// Define base paths for relative links
$base_path = '../'; 
?>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo $dashboard_url; ?>">Track<span>COOP</span></a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="bi bi-list fs-1 text-success"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($active_page == 'dashboard') ? 'active' : ''; ?>" href="<?php echo $dashboard_url; ?>">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                </li>
                
                <?php if ($is_admin || $is_bookkeeper): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($active_page == 'members') ? 'active' : ''; ?>" href="../membership/members.php">
                            <i class="bi bi-people"></i> Members
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($active_page == 'share_capital') ? 'active' : ''; ?>" href="../share_capital/share_capital.php">
                            <i class="bi bi-wallet2"></i> Share Capital
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($active_page == 'sectors') ? 'active' : ''; ?>" href="../sectors/sectors.php">
                            <i class="bi bi-diagram-3"></i> Sectors
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($active_page == 'documents') ? 'active' : ''; ?>" href="../documents/documents.php">
                            <i class="bi bi-folder-check"></i> Documents
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?php echo ($active_page == 'announcements') ? 'active' : ''; ?>" href="../announcements/announcements.php">
                        <i class="bi bi-broadcast"></i> Announcements
                    </a>
                </li>

                <?php if ($is_member): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($active_page == 'profile') ? 'active' : ''; ?>" href="../member/profile.php">
                            <i class="bi bi-person-badge"></i> Account Info
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block" style="line-height: 1.1;">
                    <span class="d-block fw-bold small text-dark"><?php echo htmlspecialchars($full_name); ?></span>
                    <small class="text-muted" style="font-size: 10px;"><?php echo htmlspecialchars($membership_type); ?></small>
                </div>

                <?php if ($is_member): ?>
                    <button class="btn btn-outline-success border-0 p-2 lh-1 rounded-circle" data-bs-toggle="modal" data-bs-target="#editProfileModal" title="Profile Settings">
                        <i class="bi bi-gear-fill fs-5"></i>
                    </button>
                <?php endif; ?>

                <a href="../auth/logout.php" class="logout-btn" title="Logout"
                   onclick="TrackUI.confirmLink(event, 'Log out from the system?', 'Confirm Logout', 'logout')">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</nav>

<!-- TRACKCOOP SYSTEM MODALS -->
<?php if ($is_admin || $is_bookkeeper): ?>
<!-- PREDICTIVE INSIGHTS HUB MODAL -->
<div class="modal fade" id="dataCenterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-lg-down modal-xl animate-modal-zoom">
        <div class="modal-content overflow-hidden" style="border-radius: 28px; border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); background: #fdfdfd;">
            <div class="modal-header border-0 p-3 px-4 d-flex align-items-center justify-content-between shadow-sm" style="z-index: 10; background-color: var(--track-beige); border-top-left-radius: 28px; border-top-right-radius: 28px;">
                <div class="d-flex align-items-center">
                    <div class="live-dot-container me-3">
                        <span class="live-dot-pulse"></span>
                    </div>
                    <div>
                        <h5 class="modal-title fw-800 mb-0 d-flex align-items-center">
                            Predictive Insights Hub
                        </h5>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light" style="height: 80vh; min-height: 550px;">
                <iframe id="predictiveIframe" src="<?php echo $base_path; ?>predictive/predictive.php?view=modal" style="width:100%; height:100%; border:none;"></iframe>
            </div>
            <div class="modal-footer border-0 p-3 px-4 d-flex justify-content-end align-items-center shadow-sm" style="z-index: 10; background-color: var(--track-beige); border-bottom-left-radius: 28px; border-bottom-right-radius: 28px;">
                <div class="small text-muted fw-bold">
                    <i class="bi bi-shield-check text-success me-1"></i> TrackCOOP Data Guardian Active
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Premium Modal Zoom Animation */
    .animate-modal-zoom {
        transform: scale(0.95);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .modal.show .animate-modal-zoom {
        transform: scale(1);
        opacity: 1;
    }

    /* Live Indicator Pulse */
    .live-dot-container {
        width: 12px; height: 12px; position: relative;
    }
    .live-dot-pulse {
        width: 100%; height: 100%; background-color: var(--track-green);
        border-radius: 50%; display: block;
        animation: pulse-dot 1.5s infinite ease-out;
    }
    @keyframes pulse-dot {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(32, 160, 96, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(32, 160, 96, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(32, 160, 96, 0); }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dataCenterModal = document.getElementById('dataCenterModal');
        if (dataCenterModal) {
            dataCenterModal.addEventListener('show.bs.modal', function () {
                const iframe = document.getElementById('predictiveIframe');
                if (iframe) iframe.contentWindow.location.reload();
            });
        }
    });
</script>
<?php endif; ?>
