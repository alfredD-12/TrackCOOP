<?php
// includes/navbar_member.php
// Variables expected: $active_page, $full_name, $static_membership_type
?>
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="member_dashboard.php">Track<span>COOP</span></a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="bi bi-list fs-1 text-success"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($active_page == 'dashboard') ? 'active' : ''; ?>" href="member_dashboard.php">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($active_page == 'announcements') ? 'active' : ''; ?>" href="../announcements/announcements.php">
                        <i class="bi bi-broadcast"></i> Announcements
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($active_page == 'profile') ? 'active' : ''; ?>" href="profile.php">
                        <i class="bi bi-person-badge"></i> Account Info
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block" style="line-height: 1.1;">
                    <span class="d-block fw-bold small text-dark"><?php echo htmlspecialchars($full_name); ?></span>
                    <small class="text-muted" style="font-size: 10px;"><?php echo htmlspecialchars($static_membership_type); ?></small>
                </div>
                
                <button class="btn btn-outline-success border-0 p-2 lh-1 rounded-circle" data-bs-toggle="modal" data-bs-target="#editProfileModal" title="Profile Settings">
                    <i class="bi bi-gear-fill fs-5" style="color: white;"></i>
                </button>

                <a href="../auth/logout.php" class="logout-btn" title="Logout"
                   onclick="return TrackUI.confirmLink(event, 'Log out from the system?', 'Confirm Logout', 'logout')">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</nav>
