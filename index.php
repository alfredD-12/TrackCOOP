<?php
// TrackCOOP Landing Page - Ultimate Animated Light Version
include 'auth/db_connect.php';
// Optimized for Nasugbu Farmers and Fisherfolks Agriculture Cooperative (NFFAC)
// UI/UX Principles: Jakob Nielsen's 10 Usability Heuristics Applied

$alert_msg = "";
if(isset($_GET['login'])) {
    if($_GET['login'] == 'pending') $alert_msg = "Your account is still PENDING for Admin approval.";
    elseif($_GET['login'] == 'wrong_password') $alert_msg = "Incorrect password. Please retry again.";
    elseif($_GET['login'] == 'not_found') $alert_msg = "Username not found. Please register first.";
}
if(isset($_GET['register']) && $_GET['register'] == 'success') {
    $alert_msg = "Registration Successful! Please wait for Admin approval.";
}

// Fetch latest 6 announcements for inline section
$pub_ann_result = $conn->query("
    SELECT a.title, a.content, a.category, a.created_at,
           COALESCE(u.first_name, 'NFFAC') AS first_name,
           COALESCE(u.last_name, 'Admin') AS last_name
    FROM announcements a
    LEFT JOIN users u ON a.author_id = u.id
    ORDER BY a.created_at DESC
    LIMIT 6
");
$ann_rows = ($pub_ann_result) ? $pub_ann_result->fetch_all(MYSQLI_ASSOC) : [];

// Session-aware User Detail logic
$is_logged_in = false;
$logged_user_name = "";
$logged_user_role = "";
if (isset($_SESSION['user_id'])) {
    $q_user = $conn->prepare("SELECT first_name, last_name, role FROM users WHERE id = ?");
    $q_user->bind_param("i", $_SESSION['user_id']);
    $q_user->execute();
    if ($u_info = $q_user->get_result()->fetch_assoc()) {
        $is_logged_in = true;
        $logged_user_name = $u_info['first_name'] . ' ' . $u_info['last_name'];
        $logged_user_role = $u_info['role'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrackCOOP | NFFAC Nasugbu</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="includes/footer.css">
    <link rel="stylesheet" href="includes/footer.css">

    <style>
        :root {
            --track-green: #20a060;
            --primary-green: #20a060;
            --dark-green: #1a8548;
            --track-green-light: #e9f5ee;
            --track-dark: #1a1a1a;
            --track-bg: #f8fafc;
            --track-beige: #F5F5DC;
            --text-main: #212529;
            --text-muted: #555555;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            background-color: #fff;
            overflow-x: hidden;
            line-height: 1.6;
        }

        html { scroll-behavior: smooth; }

        /* --- Custom Animations --- */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .floating-icon { animation: float 4s ease-in-out infinite; }

        @keyframes slideFade {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* --- Components --- */
        .navbar {
            background-color: rgba(245, 245, 220, 0.9) !important;
            backdrop-filter: blur(10px);
            padding: 15px 0;
            border-bottom: 1px solid rgba(229, 229, 192, 0.5);
            transition: var(--transition-smooth);
            z-index: 1050;
        }

        .navbar .nav-link { 
            color: var(--text-muted) !important; 
            font-weight: 600;
            font-size: 0.95rem;
            margin: 0 15px; 
            padding: 8px 0 !important;
            position: relative;
            transition: var(--transition-smooth);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.7rem;
            letter-spacing: -1.5px;
            color: var(--track-dark) !important;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .navbar-brand span { color: var(--track-green); }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--track-green);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        .nav-link:hover,
        .nav-link.active { 
            color: var(--track-dark) !important;
        }

        .btn-nav-login {
            background: var(--track-green);
            color: white !important;
            padding: 10px 24px !important;
            border-radius: 12px;
            font-weight: 700;
            transition: var(--transition-smooth);
            border: none;
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-nav-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(32, 160, 96, 0.3);
            background: var(--dark-green);
        }

        /* --- Password Toggle Styling --- */
        .password-toggle-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-toggle-btn {
            position: absolute;
            right: 15px;
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-smooth);
            z-index: 5;
        }

        .password-toggle-btn:hover {
            color: var(--track-green);
        }

        /* --- Footer --- */
        footer {
            background: var(--track-beige) !important;
            padding: 100px 0 40px !important;
            border-top: 1.5px solid rgba(229, 229, 192, 0.8) !important;
            color: var(--text-dark);
            position: relative;
            z-index: 10;
        }
        .footer-brand {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -1px;
            color: var(--text-dark) !important;
            text-decoration: none;
        }
        .footer-link {
            display: block;
            color: var(--text-muted);
            text-decoration: none;
            margin-bottom: 12px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: var(--transition-smooth);
        }
        .footer-link:hover {
            color: var(--primary-green);
            transform: translateX(5px);
        }
        .footer-bottom-border {
            border-top: 1px solid rgba(229, 229, 192, 0.8);
            margin-top: 50px;
            padding-top: 30px;
        }

        .hero {
            padding: 180px 0 100px; 
            background: radial-gradient(circle at top right, #f1f8f5, #ffffff);
            min-height: 90vh;
            display: flex;
            align-items: center;
        }

        .hero-title {
            font-weight: 800;
            font-size: clamp(2.5rem, 6vw, 4rem);
            line-height: 1.1;
            margin-bottom: 25px;
            letter-spacing: -1px;
            color: var(--text-dark);
        }

        .stats-wrapper {
            margin-top: -60px;
            position: relative;
            z-index: 10;
        }

        .stat-card-modern {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(32, 160, 96, 0.2);
            padding: 35px 25px;
            border-radius: 25px;
            text-align: center;
            transition: var(--transition-smooth);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        }

        .stat-card-modern:hover {
            transform: translateY(-10px);
            background: rgba(32, 160, 96, 0.05);
            border-color: var(--primary-green);
            box-shadow: 0 20px 40px rgba(32, 160, 96, 0.1);
        }

        .stat-val { 
            font-size: 2.8rem; 
            font-weight: 800; 
            display: block; 
            color: var(--primary-green);
            line-height: 1;
            margin-bottom: 5px;
        }
        
        .stat-desc { 
            font-size: 0.75rem; 
            color: var(--text-muted); 
            text-transform: uppercase; 
            letter-spacing: 2px; 
            font-weight: 600;
        }

        .status-badge {
            display: inline-flex; align-items: center; background: white; color: var(--primary-green);
            font-weight: 700; padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; 
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(32, 160, 96, 0.1); border: 1px solid rgba(32, 160, 96, 0.2);
        }

        .feature-card {
            border: 1px solid rgba(0,0,0,0.05);
            background: #fff;
            padding: 45px 35px;
            border-radius: 28px;
            transition: var(--transition-smooth);
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.06);
            border-color: var(--primary-green);
        }
        .icon-box {
            width: 70px; height: 70px; background: #eef7f2; color: var(--primary-green);
            border-radius: 20px; display: flex; align-items: center; justify-content: center;
            font-size: 32px; margin-bottom: 30px; transition: 0.5s ease;
        }
        .feature-card:hover .icon-box { background: var(--primary-green); color: white; }

        .contact-card {
            background: #fff; padding: 45px; border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }
        .form-control, .form-select {
            background: #f8f9fa; border: 1px solid #eee; height: 55px;
            border-radius: 12px !important; transition: var(--transition-smooth);
        }
        .form-control:focus, .form-select:focus { background: #fff; border-color: var(--primary-green); box-shadow: 0 0 0 4px rgba(32, 160, 96, 0.1); }

        footer { 
            background: var(--track-beige); 
            color: var(--text-dark); 
            padding: 80px 0 40px; 
            border-top: 1px solid rgba(0,0,0,0.05);
        }
        .footer-brand { color: var(--text-dark); font-size: 1.5rem; font-weight: 800; text-decoration: none; }
        .footer-link { color: var(--text-muted); text-decoration: none; transition: 0.3s; display: block; margin-bottom: 12px; font-size: 0.9rem; }
        .footer-link:hover { color: var(--primary-green); transform: translateX(5px); }

        /* --- Modal Styling --- */
        .modal-content { border-radius: 30px; border: none; overflow: hidden; }
        
        .modal-header-beige {
            background-color: var(--track-beige);
            padding: 20px 25px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .modal-header-beige .btn-close {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .nav-tabs-auth { 
            border: none; 
            background: #f1f5f9; 
            border-radius: 16px; 
            padding: 5px; 
            display: flex;
        }
        .nav-tabs-auth .nav-link { 
            border: none; 
            border-radius: 12px; 
            font-weight: 700; 
            color: #64748b; 
            transition: var(--transition-smooth); 
            padding: 12px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .nav-tabs-auth .nav-link.active { 
            background: white; 
            color: var(--track-green) !important; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); 
        }

        .tab-pane.active {
            animation: slideFade 0.4s ease-out;
        }

        .form-control:focus {
            border-color: var(--track-green) !important;
            box-shadow: 0 0 0 4px rgba(32, 160, 96, 0.15) !important;
            background: #fff;
        }

        .btn-cancel-modal {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
            padding: 12px 24px;
            border-radius: 14px;
            font-weight: 700;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-cancel-modal:hover {
            background: #f1f5f9;
            color: #1e293b;
            transform: translateY(-2px);
        }

        .sticky-alert { z-index: 1100; position: fixed; width: 100%; top: 0; background: var(--accent-gold); border-radius: 0; border: none; font-weight: 600; }
    </style>
</head>
<body>

    <!-- Note: Yellow alert bar removed. Notifications now handled by Premium Modal below. -->

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#home">
                Track<span>COOP</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bi bi-list fs-1 text-success"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="#home">
                            <i class="bi bi-house-door me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="#gallery">
                            <i class="bi bi-images me-1"></i> Gallery
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="#features">
                            <i class="bi bi-star me-1"></i> Features
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="#announcements">
                            <i class="bi bi-megaphone me-1"></i> Announcements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="#contact">
                            <i class="bi bi-envelope me-1"></i> Contact
                        </a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <?php if ($is_logged_in): ?>
                            <div class="d-flex align-items-center bg-white p-2 rounded-4 border border-success border-opacity-10 shadow-sm">
                                <div class="text-end me-3 d-none d-lg-block">
                                    <div class="small fw-800 text-dark lh-1"><?php echo htmlspecialchars($logged_user_name); ?></div>
                                    <small class="text-muted" style="font-size: 0.7rem;"><?php echo htmlspecialchars($logged_user_role); ?></small>
                                </div>
                                <?php 
                                    $dash_url = 'index.php';
                                    if ($logged_user_role === 'Admin') $dash_url = 'admin/admin_dashboard.php';
                                    elseif ($logged_user_role === 'Bookkeeper') $dash_url = 'bookkeeper/bookkeeper_dashboard.php';
                                    elseif ($logged_user_role === 'Member') $dash_url = 'member/member_dashboard.php';
                                ?>
                                <a href="<?php echo $dash_url; ?>" class="btn-nav-login py-2 px-3">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </a>
                                <a href="auth/logout.php" class="ms-2 text-danger fs-4 lh-1" 
                                   title="Logout" 
                                   onclick="TrackUI.confirmLink(event, 'Log out from the system?', 'Confirm Logout', 'logout')">
                                    <i class="bi bi-box-arrow-right"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <button class="btn btn-nav-login" data-bs-toggle="modal" data-bs-target="#authModal">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </button>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero pb-lg-100px" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7" data-aos="fade-right">
                    <div class="status-badge">
                        <span class="spinner-grow spinner-grow-sm me-2 text-success" role="status" style="width: 10px; height: 10px;"></span>
                        System Live
                    </div>
                    <h1 class="hero-title">Intelligent <br><span class="text-success">Management</span> for Nasugbu Farmers.</h1>
                    <p class="text-muted mb-5 fs-5">Empowering our local agriculture cooperative with a centralized digital system for document tracking and membership growth.</p>
                </div>
                <div class="col-lg-5 text-center" data-aos="fade-left" data-aos-delay="300">
                    <div class="d-flex flex-column gap-3 align-items-center justify-content-center">
                        <button class="btn btn-lg px-5 py-3 shadow-lg rounded-4 fw-bold w-75" style="background:var(--primary-green); color:white; border:none;" data-bs-toggle="modal" data-bs-target="#authModal">Explore Portal</button>
                        <a href="#announcements" class="btn btn-lg px-4 py-3 rounded-4 fw-bold w-75" style="border: 2px solid var(--primary-green); color:var(--primary-green);">Public Announcements</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container stats-wrapper mb-5 mb-lg-100px">
        <div class="row g-4 justify-content-center">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card-modern">
                    <span class="stat-val">100%</span>
                    <span class="stat-desc">Digital Tracking</span>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card-modern">
                    <span class="stat-val">2026</span>
                    <span class="stat-desc">System Year</span>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card-modern">
                    <span class="stat-val">5+</span>
                    <span class="stat-desc">Agri Sectors</span>
                </div>
            </div>
        </div>
    </div>

    <!-- --- STANDALONE ACTIVITIES GALLERY SHOWCASE --- -->
    <section class="py-100px" id="gallery" style="background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5" data-aos="fade-up">
                <div class="text-center text-md-start mb-4 mb-md-0">
                    <h2 class="fw-800 fs-1 mb-2" style="letter-spacing: -1.5px; color: #1e272e;">Cooperative Activities</h2>
                    <p class="text-muted mb-0">Discover the heart and soul of NFFAC Nasugbu through our official gallery.</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#galleryModal" class="btn btn-outline-success rounded-pill px-5 py-3 fw-bold border-2 b-gallery-btn">
                        Browse Full Archive <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <div class="row g-4">
                <?php
                // Fetch top 3 latest activities
                $media_query = mysqli_query($conn, "SELECT * FROM media_activities ORDER BY activity_date DESC LIMIT 3");
                if ($media_query && mysqli_num_rows($media_query) > 0): 
                    while ($m = mysqli_fetch_assoc($media_query)): ?>
                        <div class="col-lg-4" data-aos="zoom-in">
                            <div class="gallery-card-premium">
                                <div class="card-img-wrapper">
                                    <img src="<?php echo htmlspecialchars($m['file_path']); ?>" alt="<?php echo htmlspecialchars($m['title']); ?>">
                                    <span class="category-tag"><?php echo htmlspecialchars($m['category']); ?></span>
                                </div>
                                <div class="card-content">
                                    <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($m['title']); ?></h5>
                                    <p class="text-muted small mb-0"><i class="bi bi-calendar-event me-2"></i><?php echo date('F d, Y', strtotime($m['activity_date'])); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; 
                else: 
                    // PROFESSIONAL PLACEHOLDER CARDS (Visible if no data yet)
                    for($i=1; $i<=3; $i++): ?>
                        <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="<?php echo $i*100; ?>">
                            <div class="gallery-card-premium placeholder-style text-center">
                                <div class="card-img-wrapper d-flex align-items-center justify-content-center bg-light">
                                    <i class="bi bi-image fs-1 opacity-10"></i>
                                    <span class="category-tag opacity-50">Coming Soon</span>
                                </div>
                                <div class="card-content">
                                    <h5 class="fw-bold mb-2 opacity-50">Future Activity Showcase</h5>
                                    <p class="text-muted small mb-0 opacity-50">Documenting Cooperative Progress</p>
                                </div>
                            </div>
                        </div>
                    <?php endfor; 
                endif; ?>
            </div>
        </div>
    </section>

    <!-- CUSTOM GALLERY STYLING -->
    <style>
    .gallery-card-premium {
        background: white; border-radius: 32px; border: 1px solid rgba(0,0,0,0.05); 
        overflow: hidden; height: 100%; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex; flex-direction: column;
    }
    .gallery-card-premium:hover { transform: translateY(-15px); box-shadow: 0 40px 80px rgba(0,0,0,0.1); border-color: rgba(32, 160, 96, 0.2); }
    .card-img-wrapper { height: 260px; width: 100%; overflow: hidden; position: relative; }
    .card-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: 0.8s ease; }
    .gallery-card-premium:hover .card-img-wrapper img { transform: scale(1.15); }
    .category-tag { position: absolute; top: 20px; left: 20px; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); padding: 6px 16px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; color: #20a060; border: 1px solid rgba(32, 160, 96, 0.1); }
    .card-content { padding: 30px; }
    .placeholder-style { border-style: dashed !important; border-width: 2px !important; background: rgba(32, 160, 96, 0.02) !important; }
    .b-gallery-btn { transition: all 0.3s ease; box-shadow: 0 10px 20px rgba(32, 160, 96, 0.1); }
    .b-gallery-btn:hover { background: #20a060 !important; color: white !important; transform: scale(1.05); }
    
    .py-100px { padding-top: 100px !important; padding-bottom: 100px !important; }
    .mb-lg-100px { margin-bottom: 100px !important; }

    /* Announcement V2 - Feature Style */
    .ann-card-v2 {
        border: 1px solid rgba(0,0,0,0.05);
        background: #fff;
        padding: 40px 30px;
        border-radius: 28px;
        transition: var(--transition-smooth);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .ann-card-v2:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        border-color: var(--primary-green);
    }
    .ann-icon-box {
        width: 60px; height: 60px; background: #eef7f2; color: var(--primary-green);
        border-radius: 18px; display: flex; align-items: center; justify-content: center;
        font-size: 20px; font-weight: 800; margin-bottom: 25px; transition: 0.5s ease;
    }
    .ann-card-v2:hover .ann-icon-box { background: var(--primary-green); color: white; }
    
    .ann-category-tag {
        position: absolute; top: 30px; right: 30px;
        font-size: 0.65rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1px; color: var(--primary-green);
        background: rgba(32, 160, 96, 0.08); padding: 5px 12px; border-radius: 50px;
    }
    .ann-card-v2 .title { font-weight: 700; font-size: 1.15rem; margin-bottom: 15px; color: var(--text-dark); line-height: 1.4; }
    .ann-card-v2 .snippet { color: var(--text-muted); font-size: 0.9rem; line-height: 1.7; margin-bottom: 25px; flex-grow: 1; }
    
    .ann-footer {
        border-top: 1px solid #f1f5f9; padding-top: 15px;
        display: flex; align-items: center; justify-content: space-between;
        font-size: 0.75rem; font-weight: 600; color: #94a3b8;
    }
    .ann-footer .author { color: var(--text-dark); }
    </style>

    <section class="py-100px mt-5 border-top" id="features">
        <div class="container py-100px">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold fs-1">Platform Features</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="icon-box"><i class="bi bi-file-earmark-bar-graph"></i></div>
                        <h4 class="fw-bold">Document Analytics</h4>
                        <p class="text-muted">Secure digital archives with real-time tracking for every NFFAC cooperative document and report.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="icon-box"><i class="bi bi-people-fill"></i></div>
                        <h4 class="fw-bold">Member Lifecycle</h4>
                        <p class="text-muted">Automated registration process for farmers and fisherfolks with detailed sector monitoring.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="icon-box"><i class="bi bi-megaphone-fill"></i></div>
                        <h4 class="fw-bold">Smart Broadcast</h4>
                        <p class="text-muted">Instant announcements and policy updates delivered to all cooperative members across Nasugbu.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── INLINE ANNOUNCEMENTS SECTION ─────────────────────────────────── -->
    <section id="announcements" class="py-100px" style="background: #fff;">
        <div class="container py-5">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="status-badge"><i class="bi bi-megaphone-fill me-2"></i> Official Updates</span>
                <h2 class="fw-bold fs-1 mb-3">Cooperative <span class="text-success">Announcements</span></h2>
                <p class="text-muted fs-5 mx-auto" style="max-width:600px;">Stay updated with the latest news and important reminders from the NFFAC Nasugbu management.</p>
            </div>

            <?php if (!empty($ann_rows)): ?>
            <div class="row g-4 justify-content-center">
                <?php foreach ($ann_rows as $i => $ann): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo 100 + ($i * 100); ?>">
                    <div class="ann-card-v2">
                        <span class="ann-category-tag"><?php echo htmlspecialchars($ann['category'] ?? 'General'); ?></span>
                        
                        <div class="ann-icon-box">
                            <?php echo strtoupper(substr($ann['first_name'], 0, 1) . substr($ann['last_name'], 0, 1)); ?>
                        </div>
                        
                        <h4 class="title"><?php echo htmlspecialchars($ann['title']); ?></h4>
                        <p class="snippet"><?php echo htmlspecialchars(mb_strimwidth($ann['content'], 0, 130, '...')); ?></p>
                        
                        <div class="ann-footer">
                            <span class="author"><i class="bi bi-person me-1"></i> <?php echo htmlspecialchars($ann['first_name'] . ' ' . $ann['last_name']); ?></span>
                            <span><i class="bi bi-calendar3 me-1"></i> <?php echo date('M j, Y', strtotime($ann['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5" data-aos="fade-up">
                <div class="icon-box mx-auto" style="width:90px; height:90px;"><i class="bi bi-broadcast"></i></div>
                <h5 class="fw-bold">No Announcements Yet</h5>
                <p class="text-muted">Please check back soon for future updates.</p>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="py-100px bg-light" id="contact">
        <div class="container py-100px">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5" data-aos="fade-right">
                    <h2 class="fw-bold mb-4">Official Coop Office</h2>
                    <p class="text-muted mb-4 fs-5">Visit us for manual verification or cooperative inquiries at the heart of Nasugbu.</p>
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3"><i class="bi bi-geo-alt-fill text-success fs-4"></i></div>
                        <div><h6 class="fw-bold mb-0">Location</h6><p class="text-muted small mb-0">Camp Avejar, Nasugbu, Batangas</p></div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3"><i class="bi bi-clock-fill text-success fs-4"></i></div>
                        <div><h6 class="fw-bold mb-0">Working Hours</h6><p class="text-muted small mb-0">Mon - Fri: 8:00 AM - 5:00 PM</p></div>
                    </div>
                </div>
                <div class="col-lg-7" data-aos="fade-left">
                    <div class="contact-card">
                        <h4 class="fw-bold mb-4 text-center">Send us a message</h4>
                        <form action="#" method="POST">
                            <div class="row g-3">
                                <div class="col-md-4"><input type="text" class="form-control" placeholder="First Name" required></div>
                                <div class="col-md-4"><input type="text" class="form-control" placeholder="Middle Name"></div>
                                <div class="col-md-4"><input type="text" class="form-control" placeholder="Last Name" required></div>
                                <div class="col-12"><input type="email" class="form-control" placeholder="Email Address" required></div>
                                <div class="col-12"><textarea class="form-control" rows="4" placeholder="How can we help?" required></textarea></div>
                                <div class="col-12"><button type="submit" class="btn btn-nav-login w-100 py-3 rounded-4 fw-bold">Submit Message</button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NFFAC Official Footer Section -->

    <div class="modal fade" id="authModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header-beige">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="top: 25px; right: 25px;"></button>
                    <div class="modal-brand m-0" style="font-weight: 800; font-size: 1.3rem; letter-spacing: -1.2px; color: var(--track-dark);">
                        Track<span style="color: var(--track-green);">COOP</span>
                    </div>
                    <div class="icon-box-small" style="width: 40px; height: 40px; background: white; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 15px rgba(32, 160, 96, 0.1);">
                        <i class="bi bi-shield-lock-fill text-success fs-5"></i>
                    </div>
                </div>

                <div class="p-4">
                    <ul class="nav nav-tabs nav-tabs-auth mb-4" role="tablist">
                        <li class="nav-item w-50"><button class="nav-link active w-100" data-bs-toggle="tab" data-bs-target="#login-p">Login</button></li>
                        <li class="nav-item w-50"><button class="nav-link w-100" data-bs-toggle="tab" data-bs-target="#reg-p">Register</button></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="login-p">
                            <form action="auth/login.php" method="POST" onsubmit="return TrackUI.confirmForm(event, 'Proceed to login to your cooperative account?', 'Authentication', 'primary', 'Log-In', 'Back')">
                                <div class="mb-3">
                                    <label class="small fw-bold mb-2">USERNAME</label>
                                    <input type="text" name="username" class="form-control" placeholder="Enter username" required value="<?php echo isset($_GET['username']) ? htmlspecialchars($_GET['username']) : ''; ?>">
                                </div>
                                <div class="mb-4">
                                    <label class="small fw-bold mb-2">PASSWORD</label>
                                    <div class="password-toggle-wrapper">
                                        <input type="password" id="loginPassword" name="password" class="form-control" placeholder="Enter password" required style="padding-right: 45px;">
                                        <button type="button" class="password-toggle-btn" onclick="togglePassVisibility('loginPassword', 'loginEye')">
                                            <i id="loginEye" class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 mt-4">
                                    <button type="button" class="btn btn-cancel-modal w-100" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </button>
                                    <button type="submit" class="btn btn-nav-login w-100 py-3 fw-bold">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Log-In
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="reg-p">
                            <form action="auth/register.php" method="POST" onsubmit="return TrackUI.confirmForm(event, 'Proceed with your account registration? Please verify your details first.', 'New Account Registry', 'primary', 'Register Now', 'Review')">
                                <div class="mb-3">
                                    <label class="small fw-bold mb-2">FULL NAME</label>
                                    <div class="row g-2">
                                        <div class="col-4"><input type="text" name="fname" class="form-control" placeholder="First" required></div>
                                        <div class="col-4"><input type="text" name="mname" class="form-control" placeholder="Middle"></div>
                                        <div class="col-4"><input type="text" name="lname" class="form-control" placeholder="Last" required></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="small fw-bold mb-2">ACCOUNT DETAILS</label>
                                    <div class="row g-2">
                                        <div class="col-6"><input type="text" name="username" class="form-control" placeholder="Username" required></div>
                                        <div class="col-6">
                                            <div class="password-toggle-wrapper">
                                                <input type="password" id="regPassword" name="password" class="form-control" placeholder="Password" required style="padding-right: 45px;">
                                                <button type="button" class="password-toggle-btn" onclick="togglePassVisibility('regPassword', 'regEye')">
                                                    <i id="regEye" class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="small fw-bold mb-2">AGRICULTURAL SECTOR</label>
                                    <select name="sector" class="form-select" required>
                                        <option value="" selected disabled>Select your sector...</option>
                                        <option value="Rice">Rice</option>
                                        <option value="Corn">Corn</option>
                                        <option value="Fishery">Fishery</option>
                                        <option value="Livestock">Livestock</option>
                                        <option value="High Value Crops">High Value Crops</option>
                                    </select>
                                </div>

                                <div class="d-flex gap-3 mt-4">
                                    <button type="button" class="btn btn-cancel-modal w-100" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </button>
                                    <button type="submit" class="btn btn-nav-login w-100 py-3 fw-bold">
                                        <i class="bi bi-person-plus-fill me-2"></i>Create
                                    </button>
                                </div>
                            </form>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- --- PREMIUM MODAL SYSTEM (INTERNAL TO PRESERVE DESIGN) --- -->
<style>
#trackGlobalConfirmModal .modal-content {
    background: rgba(255, 255, 255, 0.8) !important;
    backdrop-filter: blur(25px) saturate(200%) !important;
    -webkit-backdrop-filter: blur(25px) !important;
    border: 1px solid rgba(32, 160, 96, 0.1) !important;
    border-radius: 32px !important;
    box-shadow: 0 40px 100px rgba(0, 0, 0, 0.1) !important;
    overflow: visible !important; 
}
#trackGlobalConfirmModal .icon-circle {
    width: 84px !important; height: 84px !important;
    box-shadow: 0 15px 35px rgba(32, 160, 96, 0.15);
    border: 6px solid white !important; background: white !important;
    transform: translateY(-50%); position: absolute; top: 0; left: 50%; margin-left: -42px;
}
#trackGlobalConfirmModal .modal-body { padding: 60px 24px 32px !important; }
#trackGlobalConfirmModal .btn-confirm {
    padding: 14px 20px !important; border-radius: 18px !important;
    font-weight: 800 !important; background: linear-gradient(135deg, var(--primary-green), #1a8548) !important;
    border: none !important; box-shadow: 0 10px 20px rgba(32, 160, 96, 0.2) !important;
}
#trackGlobalConfirmModal .btn-cancel {
    padding: 14px 20px !important; border-radius: 18px !important;
    font-weight: 700 !important; color: #64748b !important; background: #f1f5f9 !important; border: none !important;
}
</style>

<div class="modal fade" id="trackGlobalConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 10000;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content">
            <div id="confirmIconContainer" class="icon-circle d-flex align-items-center justify-content-center">
                <i class="bi bi-question-circle" style="font-size: 2.8rem; color: var(--track-green);"></i>
            </div>
            <div class="modal-body text-center">
                <h4 class="fw-800 mb-2" id="confirmTitle" style="color: var(--track-dark); letter-spacing: -1.2px;">Confirm Action</h4>
                <p class="text-muted mb-0 px-2" id="confirmMessage" style="font-size: 0.95rem; line-height: 1.6;"></p>
                <div class="d-flex gap-3 mt-4 pt-2">
                    <button type="button" class="btn btn-cancel w-100" data-bs-dismiss="modal">Back</button>
                    <button type="button" id="confirmButton" class="btn btn-confirm w-100 text-white">Continue</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- --- PREMIUM GALLERY ARCHIVE MODAL --- -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 40px; border: none; overflow: hidden; box-shadow: 0 50px 100px rgba(0,0,0,0.2);">
            <div class="modal-header border-0 pb-3 px-4 pt-4 text-center d-block" style="background: var(--track-beige) !important; border-bottom: 1px solid rgba(229, 229, 192, 0.8) !important;">
                <h2 class="fw-800 mb-0" style="color: #1e272e; letter-spacing: -2px;">Cooperative Gallery</h2>
                <p class="text-muted small mb-0">Our shared milestones and activities archive</p>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="position: absolute; right: 30px; top: 30px;"></button>
            </div>
            <div class="modal-body px-4 pb-5">
                <!-- MODAL FILTER BUTTONS -->
                <div class="d-flex flex-wrap justify-content-center mb-4 gap-2">
                    <button type="button" class="modal-filter-btn active" onclick="filterModalGallery('All', this)">All</button>
                    <button type="button" class="modal-filter-btn" onclick="filterModalGallery('Training', this)">Trainings</button>
                    <button type="button" class="modal-filter-btn" onclick="filterModalGallery('Harvesting', this)">Harvesting</button>
                    <button type="button" class="modal-filter-btn" onclick="filterModalGallery('Meeting', this)">Meetings</button>
                    <button type="button" class="modal-filter-btn" onclick="filterModalGallery('Livelihood', this)">Livelihood</button>
                </div>

                <div class="row g-4 mt-2" id="modalGalleryGrid">
                    <?php
                    // Fetch ALL activities for the modal
                    $archive_query = mysqli_query($conn, "SELECT * FROM media_activities ORDER BY activity_date DESC");
                    if ($archive_query && mysqli_num_rows($archive_query) > 0):
                        while ($row = mysqli_fetch_assoc($archive_query)): ?>
                            <div class="col-lg-4 col-md-6 modal-gallery-item" data-category="<?php echo htmlspecialchars($row['category']); ?>">
                                <div class="gallery-card-premium">
                                    <div class="card-img-wrapper" style="height: 180px;">
                                        <img src="<?php echo htmlspecialchars($row['file_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                                        <span class="category-tag"><?php echo htmlspecialchars($row['category']); ?></span>
                                    </div>
                                    <div class="card-content p-3">
                                        <h6 class="fw-bold mb-1" style="font-size: 0.95rem;"><?php echo htmlspecialchars($row['title']); ?></h6>
                                        <p class="text-muted mb-0" style="font-size: 0.75rem;"><i class="bi bi-calendar-event me-1"></i><?php echo date('M d, Y', strtotime($row['activity_date'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile;
                    else: ?>
                        <div class="text-center py-5 w-100">
                            <i class="bi bi-images fs-1 opacity-25 d-block mb-3"></i>
                            <h5 class="text-muted">No activities have been uploaded yet.</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- MODAL SPECIFIC STYLES & SCRIPT -->
            <style>
                .modal-filter-btn {
                    padding: 10px 24px; border-radius: 50px; border: 1.5px solid #e2e8f0;
                    background: white; color: #64748b; font-weight: 700; font-size: 0.85rem;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    box-shadow: 0 4px 6px rgba(0,0,0,0.02);
                }
                .modal-filter-btn.active, .modal-filter-btn:hover {
                    background: #20a060; color: white; border-color: #20a060;
                    box-shadow: 0 10px 20px rgba(32, 160, 96, 0.25);
                    transform: translateY(-2px);
                }
            </style>
            <script>
                function filterModalGallery(category, btn) {
                    // Update active button state
                    document.querySelectorAll('.modal-filter-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    // Filter items
                    const items = document.querySelectorAll('.modal-gallery-item');
                    items.forEach(item => {
                        if (category === 'All' || item.getAttribute('data-category') === category) {
                            item.style.display = 'block';
                            item.style.animation = 'fadeInUp 0.4s ease forwards';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                }
            </script>

        </div>
    </div>
</div>

<script>
const TrackUI = {
    confirmModal: null, resolvePromise: null,
    init() { if (!this.confirmModal) this.confirmModal = new bootstrap.Modal(document.getElementById('trackGlobalConfirmModal'), { backdrop: 'static' }); },
    show(message, title = 'Notification', type = 'primary', btnConfirm = 'Okay', btnCancel = 'Close') {
        this.init();
        document.getElementById('confirmMessage').textContent = message;
        document.getElementById('confirmTitle').textContent = title;
        document.querySelector('.btn-cancel').textContent = btnCancel;
        const icon = document.querySelector('#confirmIconContainer i');
        const btn = document.getElementById('confirmButton');
        btn.textContent = btnConfirm;
        if (type === 'danger') { icon.className = 'bi bi-exclamation-triangle-fill'; icon.style.color = '#ef4444'; } 
        else { icon.className = 'bi bi-check-circle-fill'; icon.style.color = '#20a060'; }
        return new Promise((r) => {
            this.resolvePromise = r; this.confirmModal.show();
            const b = document.getElementById('confirmButton');
            const nb = b.cloneNode(true); b.parentNode.replaceChild(nb, b);
            nb.addEventListener('click', () => { this.confirmModal.hide(); this.resolvePromise(true); });
        });
    },
    async confirmForm(event, message, title = 'Submit', type = 'primary', btnC = 'Continue', btnX = 'Back') {
        event.preventDefault();
        const form = event.currentTarget;
        if (await this.show(message, title, type, btnC, btnX)) form.submit();
        return false;
    }
};

// AUTO-TRIGGER NOTIFICATION MODAL
document.addEventListener('DOMContentLoaded', () => {
    const alertMsg = "<?php echo $alert_msg; ?>";
    if (alertMsg) {
        let title = "System Notification";
        let type = "primary";
        let btnConfirm = "Okay";
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('register')) { 
            title = "Success!"; 
            type = "success"; 
        } else if (urlParams.has('login')) { 
            title = "Authentication Alert"; 
            type = "danger"; 
            if (urlParams.get('login') === 'wrong_password') {
                btnConfirm = "Retry Login";
            }
        }
        
        TrackUI.show(alertMsg, title, type, btnConfirm, 'Dismiss').then(confirmed => {
            if (confirmed && btnConfirm === "Retry Login") {
                const myModal = new bootstrap.Modal(document.getElementById('authModal'));
                myModal.show();
            }
        });
    }
});
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<?php include('includes/footer.php'); ?>
    <script>
        AOS.init({ duration: 1000, once: true });

        // --- SMART NAVIGATION MONITOR (SCROLLSPY) ---
        const sections = document.querySelectorAll("section[id], header[id]");
        const navLinks = document.querySelectorAll(".navbar-nav .nav-link");

        window.addEventListener("scroll", () => {
            let current = "";
            sections.forEach((section) => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= sectionTop - 150) {
                    current = section.getAttribute("id");
                }
            });

            navLinks.forEach((link) => {
                link.classList.remove("active");
                if (link.getAttribute("href") === `#${current}`) {
                    link.classList.add("active");
                }
            });
        });

        // Initialize active state on load
        window.dispatchEvent(new Event('scroll'));

        // --- PASSWORD TOGGLE LOGIC ---
        function togglePassVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("bi-eye");
                icon.classList.add("bi-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("bi-eye-slash");
                icon.classList.add("bi-eye");
            }
        }
    </script>
</body>
</html>