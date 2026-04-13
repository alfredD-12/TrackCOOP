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
        <a class="navbar-brand d-flex align-items-center" href="<?php echo $dashboard_url; ?>" style="margin-left: -25px;"><img src="<?php echo $base_path; ?>TrackCOOP Logo.svg" alt="TrackCOOP Logo" class="navbar-logo">Track<span>COOP</span></a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="bi bi-list fs-1 text-success"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <!-- Navigation consolidated to sidebar -->
            </ul>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block" style="line-height: 1.1;">
                    <span class="d-block fw-bold small text-white"><?php echo htmlspecialchars($full_name); ?></span>
                    <small class="text-white" style="font-size: 10px; opacity: 0.85;"><?php echo htmlspecialchars($membership_type); ?></small>
                </div>

                <?php if ($is_member): ?>
                    <button type="button" class="btn btn-outline-success border-0 p-2 lh-1 rounded-circle position-relative me-1" title="Announcements" data-bs-toggle="modal" data-bs-target="#announcementModal">
                        <i class="bi bi-bell-fill fs-5" style="color: white;"></i>
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
<?php if ($is_admin): ?>
<!-- PREDICTIVE INSIGHTS HUB MODAL -->
<div class="modal fade" id="dataCenterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-lg-down modal-xl animate-modal-zoom">
        <div class="modal-content overflow-hidden" style="border-radius: 28px; border: 1px solid rgba(0,0,0,0.1); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); background: #fdfdfd;">
            <div class="modal-header border-0 p-3 px-4 d-flex align-items-center justify-content-between shadow-sm" style="z-index: 10; background-color: rgba(22, 74, 54, 0.95); border-top-left-radius: 28px; border-top-right-radius: 28px; color: white;">
                <h5 class="modal-title mb-0" style="color: #20a060 !important; font-weight: 900 !important;">Member Risk Dashboard</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1);"></button>
            </div>
            <div class="modal-body p-0 bg-light" style="height: 80vh; min-height: 550px;">
                <iframe id="predictiveIframe" src="<?php echo $base_path; ?>predictive/predictive.php?view=modal" style="width:100%; height:100%; border:none;"></iframe>
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
