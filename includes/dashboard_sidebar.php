<?php
// includes/dashboard_sidebar.php
// Variables expected: $active_page, $full_name, $membership_type, $user_role

$is_admin = (isset($user_role) && $user_role === 'Admin');
$is_bookkeeper = (isset($user_role) && $user_role === 'Bookkeeper');

// Define Dashboard URL based on role
$dashboard_url = '../member/member_dashboard.php';
if ($is_admin) $dashboard_url = '../admin/admin_dashboard.php';
elseif ($is_bookkeeper) $dashboard_url = '../bookkeeper/bookkeeper_dashboard.php';

$base_path = '../'; 
?>

<aside class="dashboard-sidebar">
    <div class="sidebar-header">
        <a href="<?php echo $dashboard_url; ?>" class="sidebar-logo">
            <img src="<?php echo $base_path; ?>TrackCOOP Logo.svg" alt="TRACKCOOP Logo">
            <span>TRACK<span>COOP</span></span>
        </a>
    </div>

    <nav class="sidebar-nav">
        <a href="<?php echo $dashboard_url; ?>" class="nav-item <?php echo ($active_page == 'dashboard') ? 'active' : ''; ?>">
            <i class="bi bi-grid-fill"></i>
            <span>Dashboard</span>
        </a>

        <?php if ($is_admin): ?>
            <a href="../membership/members.php" class="nav-item <?php echo ($active_page == 'members') ? 'active' : ''; ?>">
                <i class="bi bi-people-fill"></i>
                <span>Members</span>
            </a>
            <a href="../sectors/sectors.php" class="nav-item <?php echo ($active_page == 'sectors') ? 'active' : ''; ?>">
                <i class="bi bi-diagram-3-fill"></i>
                <span>Sectors</span>
            </a>
            <a href="../documents/documents.php" class="nav-item <?php echo ($active_page == 'documents') ? 'active' : ''; ?>">
                <i class="bi bi-folder-fill"></i>
                <span>Documents</span>
            </a>
        <?php endif; ?>

        <?php if ($is_bookkeeper): ?>
            <a href="../share_capital/share_capital.php" class="nav-item <?php echo ($active_page == 'share_capital') ? 'active' : ''; ?>">
                <i class="bi bi-wallet2"></i>
                <span>Share Capital</span>
            </a>
        <?php endif; ?>

        <?php if ($user_role !== 'Member'): ?>
        <a href="../announcements/announcements.php" class="nav-item <?php echo ($active_page == 'announcements') ? 'active' : ''; ?>">
            <i class="bi bi-megaphone-fill"></i>
            <span>Announcements</span>
        </a>
        <?php endif; ?>

        <?php if ($is_admin): ?>
            <a href="../admin/gallery.php" class="nav-item <?php echo ($active_page == 'gallery') ? 'active' : ''; ?>">
                <i class="bi bi-images"></i>
                <span>Gallery</span>
            </a>
            <a href="../reports/reports.php" class="nav-item <?php echo ($active_page == 'reports') ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-bar-graph-fill"></i>
                <span>Reports</span>
            </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile-summary">
            <div class="user-avatar-mini">
                <?php echo strtoupper(substr($full_name, 0, 1)); ?>
            </div>
            <div class="user-details-mini">
                <span class="user-name-mini"><?php echo htmlspecialchars($full_name); ?></span>
                <span class="user-role-mini"><?php echo htmlspecialchars($membership_type); ?></span>
            </div>
        </div>
        <a href="../auth/logout.php" class="sidebar-logout-btn" 
           onclick="TrackUI.confirmLink(event, 'Log out from the system?', 'Confirm Logout', 'logout')">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</aside>

<style>
/* Dashboard Sidebar Styles - Matching "SkyDoc" Aesthetic with TrackCOOP Green */
.dashboard-sidebar {
    width: 250px;
    height: 100vh;
    background: #164a36; /* Deep forest green as requested */
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    z-index: 1100;
    border-right: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.sidebar-header {
    padding: 30px 15px;
}

.sidebar-logo {
    display: flex;
    align-items: center;
    gap: 0;
    text-decoration: none;
}

.sidebar-logo img {
    height: 38px;
    width: auto;
    margin-right: -15px !important;
}

.sidebar-logo span {
    color: #ffffff;
    font-weight: 800;
    font-size: 1.25rem;
    letter-spacing: -1.5px;
}

.sidebar-logo span span {
    color: #20a060;
}

.sidebar-nav {
    flex: 1;
    padding: 0 15px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 14px 20px;
    color: rgba(255, 255, 255, 0.6) !important;
    text-decoration: none !important;
    font-weight: 600;
    font-size: 0.95rem;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.nav-item i {
    font-size: 1.2rem;
}

.nav-item:hover {
    background: rgba(255, 255, 255, 0.05);
    color: #ffffff !important;
}

.nav-item.active {
    background: #fcd34d !important; /* Gold/Yellow from reference image */
    color: #1a1f26 !important;
    box-shadow: 0 10px 20px rgba(252, 211, 77, 0.2);
}

.nav-item.active i {
    color: #1a1f26;
}

.sidebar-footer {
    padding: 30px 20px;
    margin-top: auto;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.user-profile-summary {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar-mini {
    width: 38px;
    height: 38px;
    background: #20a060;
    color: white;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.9rem;
}

.user-details-mini {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.user-name-mini {
    color: #ffffff;
    font-weight: 700;
    font-size: 0.85rem;
    display: block;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-role-mini {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.7rem;
    font-weight: 600;
}

.sidebar-logout-btn {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444 !important;
    text-decoration: none !important;
    transition: all 0.3s ease;
}

.sidebar-logout-btn:hover {
    background: #ef4444;
    color: #ffffff !important;
    box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
}

@media (max-width: 992px) {
    .dashboard-sidebar {
        transform: translateX(-100%);
    }
    .dashboard-sidebar.active {
        transform: translateX(0);
    }
}
</style>
