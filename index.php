<?php
// TrackCOOP Landing Page - Ultimate Animated Light Version
include 'auth/db_connect.php';
// Optimized for Nasugbu Farmers and Fisherfolks Agriculture Cooperative (NFFAC)
// UI/UX Principles: Jakob Nielsen's 10 Usability Heuristics Applied

$remembered_user = "";
if (isset($_COOKIE['remember_user'])) {
    $remembered_user = $_COOKIE['remember_user'];
}

$alert_msg = "";
if(isset($_GET['login'])) {
    if($_GET['login'] == 'pending') $alert_msg = "Your account is still PENDING for Admin approval.";
    elseif($_GET['login'] == 'wrong_password') $alert_msg = "Incorrect password. Please retry again.";
    elseif($_GET['login'] == 'not_found') $alert_msg = "Username not found. Please register first.";
    elseif($_GET['login'] == 'captcha_failed') $alert_msg = "Captcha verification failed. Please try again.";
}
if(isset($_GET['register'])) {
    if($_GET['register'] == 'success') $alert_msg = "Registration Successful! Please wait for Admin approval.";
    elseif($_GET['register'] == 'weak_password') $alert_msg = "Weak Password: Use 8-15 chars with letters, numbers, and symbols.";
    elseif($_GET['register'] == 'password_mismatch') $alert_msg = "Passwords do not match. Please ensure both fields are identical.";
}

// Fetch latest 6 announcements for inline section (Static demo data)
$ann_rows = [
    ['title' => 'Welcome to TrackCOOP', 'content' => 'TrackCOOP is now live! Join our community and start tracking your cooperative activities.', 'category' => 'General', 'created_at' => '2026-04-02', 'first_name' => 'Admin', 'last_name' => 'User'],
    ['title' => 'New Share Capital System', 'content' => 'Our new share capital tracking system is now available. All members can monitor their contributions.', 'category' => 'Sector News', 'created_at' => '2026-04-01', 'first_name' => 'Bookkeeper', 'last_name' => ''],
    ['title' => 'Member Meeting Notice', 'content' => 'General assembly meeting will be held on April 15, 2026. All members are invited to attend and participate in important discussions.', 'category' => 'Meetings', 'created_at' => '2026-03-31', 'first_name' => 'Admin', 'last_name' => 'User'],
    ['title' => 'Harvest Festival 2026', 'content' => 'Join us for the annual Harvest Festival on April 20, 2026. Celebrate the season with members and enjoy traditional Filipino food.', 'category' => 'Events', 'created_at' => '2026-04-05', 'first_name' => 'Admin', 'last_name' => 'User'],
    ['title' => 'Share Capital Deadline', 'content' => 'Reminder: Share capital contributions must be submitted by April 30, 2026. Late submissions will not be processed.', 'category' => 'Deadlines', 'created_at' => '2026-04-01', 'first_name' => 'Bookkeeper', 'last_name' => ''],
    ['title' => 'Member Meeting Notice', 'content' => 'General assembly meeting will be held on April 15, 2026. All members are invited to attend.', 'category' => 'Meetings', 'created_at' => '2026-03-20', 'first_name' => 'Admin', 'last_name' => 'User'],
];

// Session-aware User Detail logic (Static fallback)
$is_logged_in = false;
$logged_user_name = "";
$logged_user_role = "";
if (isset($_SESSION['user_id'])) {
    // Static user data fallback (in demo mode)
    $is_logged_in = true;
    $logged_user_name = isset($_SESSION['fname']) ? $_SESSION['fname'] : "User";
    $logged_user_role = isset($_SESSION['role']) ? $_SESSION['role'] : "Member";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TRACKCOOP | NFFAC Nasugbu</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="includes/dashboard_layout.css">
    <link rel="stylesheet" href="includes/footer.css">
    <!-- Visual Placeholder for reCAPTCHA (Test Phase) -->

    <style>
        :root {
            --track-green: #206970;
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
            background: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)), url('Home.jpeg') top center / 100% 100% no-repeat fixed;
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
            background-color: rgba(22, 74, 54, 0.95) !important;
            backdrop-filter: blur(10px);
            padding: 15px 0;
            border-bottom: 1px solid rgba(22, 74, 54, 0.3);
            transition: var(--transition-smooth);
            z-index: 1050;
        }

        .navbar .nav-link { 
            color: rgba(255, 255, 255, 0.8) !important; 
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
            color: #ffffff !important;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .navbar-brand span { color: #20a060; }

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
            color: #20a060 !important;
        }

        .btn-nav-login {
            background: #20a060;
            color: white !important;
            padding: 10px 24px !important;
            border-radius: 50px;
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
            background: #1b8a52;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(32, 160, 96, 0.4);
            color: white !important;
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
            background: rgba(22, 74, 54, 0.95) !important;
            padding: 60px 0 40px !important;
            border-top: 1px solid rgba(22, 74, 54, 0.3) !important;
            color: #ffffff !important;
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
            padding: 60px 0 60px; 
            min-height: 80vh;
            display: flex;
            align-items: center;
            color: white;
            background: transparent;
        }

        .hero-title {
            font-weight: 800;
            font-size: clamp(2.5rem, 6vw, 4rem);
            line-height: 1.1;
            margin-bottom: 25px;
            letter-spacing: -1px;
            color: #20a060;
            text-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }

        .stats-wrapper {
            margin-top: -100px;
            position: relative;
            z-index: 10;
        }

        .stat-card-modern {
            background: #ffffff;
            border: 3px solid #20a060;
            padding: 40px 30px;
            border-radius: 35px;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 35px rgba(32, 160, 96, 0.15);
            position: relative;
            overflow: hidden;
        }

        .stat-card-modern::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(32, 160, 96, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        .stat-card-modern:hover {
            transform: translateY(-12px) scale(1.02);
            background: #ffffff;
            border-color: var(--primary-green);
            box-shadow: 0 20px 60px rgba(32, 160, 96, 0.3);
        }

        .stat-val { 
            font-size: 3.2rem; 
            font-weight: 900; 
            display: block; 
            color: var(--primary-green);
            line-height: 1;
            margin-bottom: 8px;
            letter-spacing: -1px;
        }
        
        .stat-desc { 
            font-size: 0.8rem; 
            color: var(--text-muted); 
            text-transform: uppercase; 
            letter-spacing: 2.5px; 
            font-weight: 700;
        }



        .feature-card {
            border: 2px solid rgba(32, 160, 96, 0.25);
            background: #fff;
            padding: 50px 40px;
            border-radius: 32px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            box-shadow: 0 10px 35px rgba(32, 160, 96, 0.1);
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-green), transparent);
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .feature-card:hover {
            transform: translateY(-18px);
            box-shadow: 0 25px 60px rgba(32, 160, 96, 0.2);
            border-color: var(--primary-green);
        }
        .feature-card:hover::before {
            opacity: 1;
        }
        .icon-box {
            width: 80px; height: 80px; background: linear-gradient(135deg, #eef7f2 0%, #e0f0e8 100%); color: var(--primary-green);
            border-radius: 24px; display: flex; align-items: center; justify-content: center;
            font-size: 36px; margin-bottom: 35px; transition: all 0.4s ease; box-shadow: 0 8px 20px rgba(32, 160, 96, 0.1);
        }
        .feature-card:hover .icon-box { 
            background: linear-gradient(135deg, var(--primary-green) 0%, #1a8a6e 100%); 
            color: white; 
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 35px rgba(32, 160, 96, 0.3);
        }

        .contact-card {
            background: linear-gradient(135deg, #fff 0%, rgba(32, 160, 96, 0.02) 100%); 
            padding: 50px; 
            border-radius: 32px;
            border: 2px solid rgba(32, 160, 96, 0.15);
            box-shadow: 0 10px 35px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
        }
        .contact-card:hover {
            box-shadow: 0 20px 60px rgba(32, 160, 96, 0.15);
            border-color: rgba(32, 160, 96, 0.3);
        }
        .form-control, .form-select {
            background: #f8f9fa; border: 1px solid #eee; height: 55px;
            border-radius: 12px !important; transition: var(--transition-smooth);
        }
        .form-control:focus, .form-select:focus { background: #fff; border-color: var(--primary-green); box-shadow: 0 0 0 4px rgba(32, 160, 96, 0.1); }

        footer { 
            background: rgba(22, 74, 54, 0.95); 
            color: #ffffff !important; 
            padding: 80px 0 40px; 
            border-top: 1px solid rgba(22, 74, 54, 0.3);
        }
        .footer-brand { color: #ffffff !important; font-size: 1.5rem; font-weight: 800; text-decoration: none; }
        .footer-link { color: #ffffff !important; text-decoration: none; transition: 0.3s; display: block; margin-bottom: 12px; font-size: 0.9rem; }
        .footer-link:hover { color: #20a060; transform: translateX(5px); }

        /* --- Modal Styling --- */
        .modal-content { border-radius: 35px; border: none; overflow: hidden; box-shadow: 0 40px 100px rgba(0,0,0,0.25); }
        
        .modal-header-botanical {
            background-color: #143c2c;
            padding: 25px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            border: none;
        }

        .modal-header-botanical .btn-close-custom {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .modal-header-botanical .btn-close-custom:hover {
            background: #e63946 !important;
            color: white !important;
            transform: rotate(90deg);
            box-shadow: 0 0 15px rgba(230, 57, 70, 0.4);
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
            flex: 1;
            text-align: center;
        }

        /* --- Multi-step Registration --- */
        .reg-step { transition: all 0.3s ease; }
        .reg-step-1.d-none, .reg-step-2.d-none { display: none !important; }
        .section-title-pill { 
            color: #20a060; 
            font-weight: 800; 
            font-size: 0.85rem; 
            letter-spacing: 0.5px; 
            text-transform: uppercase;
            margin-bottom: 20px;
            display: inline-block;
        }
        .step-indicator {
            color: #20a060;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 15px;
        }
        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 4px;
            display: block;
            letter-spacing: 0.2px;
        }
        .form-control-botanical {
            border-radius: 12px !important;
            padding: 12px 18px;
            border: 1px solid #e2e8f0;
            font-size: 0.9rem;
            background-color: #fcfdfc;
            transition: all 0.2s ease;
        }
        .form-control-botanical:focus {
            border-color: #20a060;
            box-shadow: 0 0 0 3px rgba(32, 160, 96, 0.1);
            background-color: #ffffff;
        }
        .btn-reg-next {
            background: #20a060;
            color: white;
            border-radius: 50px;
            padding: 14px 28px;
            font-weight: 700;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-reg-next:hover {
            background: #1a8a52;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(32, 160, 96, 0.3);
        }
        .btn-reg-back {
            background: #20a060;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 14px 24px;
            font-weight: 700;
            transition: all 0.2s ease;
        }
        .btn-reg-back:hover {
            background: #1a8a52;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(32, 160, 96, 0.3);
        }
        .btn-reg-cancel {
            background: #e63946;
            color: white;
            border-radius: 50px;
            padding: 14px 24px;
            font-weight: 700;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-reg-cancel:hover {
            background: #d62d3a;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(230, 57, 70, 0.3);
        }

        /* Strength Meter Styles */
        .strength-meter {
            height: 6px;
            background-color: #e2e8f0;
            border-radius: 10px;
            margin-top: 8px;
            overflow: hidden;
            display: none; /* Hidden by default */
        }
        .strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .strength-text {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            margin-top: 4px;
            display: block;
            letter-spacing: 0.5px;
        }
        .st-weak { width: 33.33%; background-color: #ef4444; }
        .st-good { width: 66.66%; background-color: #f59e0b; }
        .st-strong { width: 100%; background-color: #20a060; }
        .text-weak { color: #ef4444; }
        .text-good { color: #f59e0b; }
        .text-strong { color: #20a060; }
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
            background: #e63946;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 700;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 20px rgba(230, 57, 70, 0.2);
        }
        .btn-cancel-modal:hover {
            background: #d62828;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(230, 57, 70, 0.4);
            color: white;
        }
        
        .btn-login-modal {
            background: #20a060;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-weight: 700;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 20px rgba(32, 160, 96, 0.2);
        }
        .btn-login-modal:hover {
            background: #1b8550;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(32, 160, 96, 0.4);
            color: white;
        }
        .btn-cancel-modal:hover {
            background: #20a060;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(32, 160, 96, 0.3);
        }

        .sticky-alert { z-index: 1100; position: fixed; width: 100%; top: 0; background: var(--accent-gold); border-radius: 0; border: none; font-weight: 600; }

        /* --- Password Strength Bar --- */
        .strength-container { margin-top: 8px; }
        .strength-bar { 
            height: 6px; width: 100%; background: #e2e8f0; border-radius: 10px; 
            overflow: hidden; display: flex; transition: var(--transition-smooth);
        }
        .strength-segment { height: 100%; transition: width 0.4s ease, background-color 0.4s ease; width: 0%; }
        .strength-text { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; margin-top: 4px; display: block; }
        
        .strength-weak { background-color: #ef4444; width: 33.33%; }
        .strength-fair { background-color: #f59e0b; width: 66.66%; }
        .strength-strong { background-color: #22c55e; width: 100%; }
        
        .text-weak { color: #ef4444; }
        .text-fair { color: #f59e0b; }
        .text-strong { color: #22c55e; }

        /* Dynamic Children Fields Styling */
        .btn-add-child {
            background: #20a060;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-weight: 700;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            box-shadow: 0 2px 5px rgba(32, 160, 96, 0.3);
        }
        .btn-add-child:hover {
            background: #1b8550;
            transform: scale(1.1);
        }
        .child-row {
            animation: slideFade 0.3s ease-out;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .child-row:last-child {
            border-bottom: none;
            padding-bottom: 0px;
            margin-bottom: 10px;
        }
        .btn-remove-child {
            color: #ef4444;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding-top: 25px;
        }
        .btn-remove-child:hover {
            color: #b91c1c;
            transform: scale(1.1);
        }
    </style>
<style>
    /* Sticky Footer Logic */
    html, body {
        height: 100%;
        margin: 0;
    }
    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    main {
        flex: 1 0 auto;
    }
    .footer-track {
        flex-shrink: 0;
    }
</style>
</head>
<body>

    <!-- Note: Yellow alert bar removed. Notifications now handled by Premium Modal below. -->

    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#home" style="padding-left: 0; margin-left: -20px;">
                <img src="TrackCOOP Logo.svg" alt="TRACKCOOP Logo" class="navbar-logo">
                TRACK<span style="color: #20a060;">COOP</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="bi bi-list fs-1 text-success"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Centered Links -->
                <ul class="navbar-nav mx-auto align-items-center">
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
                            <i class="bi bi-bell-fill me-1" style="color:#ffffff;"></i> Announcements
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mx-2" href="#contact">
                            <i class="bi bi-question-circle me-1"></i> FAQ
                        </a>
                    </li>
                </ul>

                <!-- Right-aligned Dashboard/Login -->
                <ul class="navbar-nav align-items-center">
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
                            <button class="btn btn-nav-login" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="bi bi-key-fill me-1"></i> Login
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
                    <h1 class="hero-title">Intelligent <br>Management for Nasugbu Farmers.</h1>
                    <p class="text-white opacity-75 mb-5 fs-5 text-shadow" style="text-shadow: 0 2px 10px rgba(0,0,0,0.5);">Empowering our local agriculture cooperative with a centralized digital system for document tracking and membership growth.</p>
                </div>
                <div class="col-lg-5 text-center" data-aos="fade-left" data-aos-delay="300">
                    <div class="d-flex flex-column gap-3 align-items-center justify-content-center">
                        <button class="btn btn-lg px-5 py-3 shadow-lg rounded-pill fw-bold w-100 text-white" style="max-width: 320px; background: #20a060; border: none; transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 10px 25px rgba(32,160,96,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';" data-bs-toggle="modal" data-bs-target="#registerModal">Become a Member</button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container stats-wrapper mb-5 mb-lg-60px">
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
    <section class="py-60px" id="gallery" style="background: transparent;">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5" data-aos="fade-up">
                <div class="text-center text-md-start mb-4 mb-md-0">
                    <h2 class="fw-bold fs-1 mb-2" style="letter-spacing: -1.5px; color: #20a060; font-weight: 900 !important; text-shadow: 0 4px 15px rgba(0,0,0,0.5);">Cooperative Activities</h2>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#galleryModal" class="btn rounded-pill px-5 py-3 fw-bold text-white b-gallery-btn" style="background: #20a060 !important; border: 2px solid #20a060 !important; box-shadow: 0 4px 15px rgba(32, 160, 96, 0.3);">
                        Browse Full Archive <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <div class="row g-4">
                <?php
                // Fetch top 3 latest activities
                // Static media activities (Demo data)
                $media_activities = [
                    ['id' => 1, 'title' => 'Team Building Event 2026', 'category' => 'Events', 'activity_date' => '2026-03-15', 'file_path' => 'event.png', 'description' => 'Our cooperative members gathered for an exciting team building activity to strengthen bonds and improve collaboration. This event featured various activities designed to enhance teamwork and communication among participants.'],
                    ['id' => 2, 'title' => 'Harvest Season Activity', 'category' => 'Agriculture', 'activity_date' => '2026-03-10', 'file_path' => 'agriculture.webp', 'description' => 'A successful harvest season engaging our farmers. Our cooperative members gathered for an exciting activity to strengthen bonds and celebrate a bountiful yield.'],
                    ['id' => 4, 'title' => 'Member Conference 2026', 'category' => 'Meetings', 'activity_date' => '2026-02-28', 'file_path' => 'meeting.jpg', 'description' => 'Annual strategic member conference to discuss future goals, financial reviews, and cooperative developments for the upcoming year.'],
                ];
                if (count($media_activities) > 0): 
                    foreach ($media_activities as $m): ?>
                        <div class="col-lg-4" data-aos="zoom-in">
                            <div class="gallery-card-premium bg-white shadow-sm" style="border-radius: 20px; overflow: hidden; border: 3px solid #20a060; display: flex; flex-direction: column; height: 100%; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)';" onmouseout="this.style.transform='translateY(0)';" data-title="<?php echo htmlspecialchars($m['title']); ?>" data-date="<?php echo date('F d, Y', strtotime($m['activity_date'])); ?>" data-category="<?php echo htmlspecialchars($m['category']); ?>" data-img="<?php echo htmlspecialchars($m['file_path']); ?>" data-desc="<?php echo htmlspecialchars($m['description']); ?>" onclick="openActivityDetail(this)">
                                <div class="card-img-wrapper" style="height: 220px; position: relative; background: url('<?php echo htmlspecialchars($m['file_path']); ?>') center/cover no-repeat;">
                                    <span class="category-tag bg-white fw-bold px-3 py-1 rounded-pill shadow-sm" style="position: absolute; top: 15px; left: 15px; font-size: 0.8rem; pointer-events: none; color: #20a060;"><?php echo htmlspecialchars($m['category']); ?></span>
                                </div>
                                <div class="card-content p-4 d-flex flex-column" style="flex: 1; pointer-events: none;">
                                    <h4 class="fw-bold mb-3" style="color: #20a060; font-size: 1.35rem;"><?php echo htmlspecialchars($m['title']); ?></h4>
                                    <p class="text-dark small mb-0 mt-auto" style="font-size: 0.95rem;"><i class="bi bi-calendar me-2"></i><?php echo date('F d, Y', strtotime($m['activity_date'])); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; 
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
        background: white; border-radius: 32px; border: 2px solid rgba(32, 160, 96, 0.25); 
        overflow: hidden; height: 100%; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex; flex-direction: column; box-shadow: 0 10px 35px rgba(32, 160, 96, 0.12); position: relative;
    }
    .gallery-card-premium::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 5px;
        background: linear-gradient(90deg, var(--primary-green), transparent); opacity: 0; 
        transition: opacity 0.4s ease; pointer-events: none;
    }
    .gallery-card-premium:hover { 
        transform: translateY(-18px); 
        box-shadow: 0 30px 70px rgba(32, 160, 96, 0.25); 
        border-color: var(--primary-green); 
    }
    .gallery-card-premium:hover::before { opacity: 1; }
    .card-img-wrapper { height: 260px; width: 100%; overflow: hidden; position: relative; background: linear-gradient(135deg, #f5f5f5, #e8e8e8); }
    .card-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275); filter: brightness(0.95); }
    .gallery-card-premium:hover .card-img-wrapper img { transform: scale(1.12); filter: brightness(1); }
    .category-tag { position: absolute; top: 20px; left: 20px; background: rgba(255,255,255,0.95); backdrop-filter: blur(15px); padding: 8px 18px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; color: #20a060; border: 1.5px solid rgba(32, 160, 96, 0.2); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
    .card-content { padding: 35px; }
    .placeholder-style { border-style: dashed !important; border-width: 2px !important; background: rgba(32, 160, 96, 0.02) !important; }
    .b-gallery-btn { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); background: linear-gradient(135deg, #206970 0%, #1a5a55 100%) !important; color: white !important; border: none !important; box-shadow: 0 12px 30px rgba(32, 126, 112, 0.25) !important; font-weight: 700 !important; letter-spacing: 0.5px !important; }
    .b-gallery-btn:hover { background: linear-gradient(135deg, #20a060 0%, #1a8a6e 100%) !important; color: white !important; transform: translateY(-3px); box-shadow: 0 18px 45px rgba(32, 160, 96, 0.4) !important; }
    
    .hero-btn-filled { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); background: linear-gradient(135deg, #206970 0%, #1a5a55 100%) !important; color: white !important; border: 2px solid rgba(32, 126, 112, 0.3) !important; box-shadow: 0 12px 30px rgba(32, 126, 112, 0.25) !important; font-weight: 700 !important; letter-spacing: 0.5px !important; }
    .hero-btn-filled:hover { background: #20a060 !important; transform: translateY(-2px); box-shadow: 0 15px 30px rgba(32, 160, 96, 0.3) !important; }
    
    .hero-btn-outline { transition: all 0.3s ease; background: #206970 !important; color: white !important; border: none !important; box-shadow: 0 10px 20px rgba(32, 126, 112, 0.2) !important; }
    .hero-btn-outline:hover { background: #20a060 !important; transform: translateY(-2px); box-shadow: 0 15px 30px rgba(32, 160, 96, 0.3) !important; }
    
    .py-100px { padding-top: 100px !important; padding-bottom: 100px !important; }
    .mb-lg-100px { margin-bottom: 100px !important; }

    /* Announcement V2 - Feature Style (Matched to Prototype) */
    .ann-card-v2 {
        border: 3px solid #20a060;
        background: #ffffff;
        padding: 40px 30px;
        border-radius: 35px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        box-shadow: 0 10px 30px rgba(32, 160, 96, 0.1);
        cursor: pointer;
    }
    .ann-card-v2:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 25px 50px rgba(32, 160, 96, 0.2);
        border-color: #28c76f;
    }
    .ann-icon-box {
        width: 70px; height: 70px; background: #20a060; color: white;
        border-radius: 20px; display: flex; align-items: center; justify-content: center;
        font-size: 24px; font-weight: 800; margin-bottom: 25px; transition: 0.3s ease;
    }
    
    .ann-category-tag {
        position: absolute; top: 30px; right: 25px;
        font-size: 0.7rem; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.5px; color: white;
        background: #20a060; padding: 6px 16px; border-radius: 50px;
    }
    .ann-card-v2 .title { font-weight: 700; font-size: 1.25rem; margin-bottom: 12px; color: #20a060; line-height: 1.4; }
    .ann-card-v2 .snippet { color: #334155; font-size: 0.95rem; line-height: 1.6; margin-bottom: 25px; flex-grow: 1; }
    
    .ann-footer {
        border-top: 1px solid #f1f5f9; padding-top: 20px;
        display: flex; align-items: center; justify-content: space-between;
        font-size: 0.85rem; font-weight: 600; color: #64748b;
    }
    .ann-footer i { color: #20a060; font-size: 1.1rem; }
    .ann-footer .author { display: flex; align-items: center; gap: 6px; }
    .ann-footer .date { display: flex; align-items: center; gap: 6px; }

    /* FAQ & Message Center Custom Styles */
    .faq-wrapper-card {
        background: white;
        border: 3px solid #20a060;
        border-radius: 40px;
        padding: 40px;
        height: 100%;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    }
    .faq-accordion .accordion-item {
        border: 1.5px solid #e2e8f0;
        border-radius: 20px !important;
        margin-bottom: 15px;
        overflow: hidden;
        transition: 0.3s;
    }
    .faq-accordion .accordion-item:hover {
        border-color: #20a060;
        box-shadow: 0 5px 15px rgba(32, 160, 96, 0.1);
    }
    .faq-accordion .accordion-button {
        padding: 20px 25px;
        font-weight: 700;
        color: #20a060;
        background: white;
        box-shadow: none;
        font-size: 1.05rem;
    }
    .faq-accordion .accordion-button:not(.collapsed) {
        color: #20a060;
        background: #f8fafc;
    }
    .faq-accordion .accordion-button::after {
        background-size: 1.2rem;
        filter: hue-rotate(100deg) brightness(0.6);
    }
    .faq-accordion .accordion-body {
        padding: 0 25px 25px;
        color: #475569;
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    .message-wrapper-card {
        background: white;
        border: 3px solid #20a060;
        border-radius: 40px;
        padding: 40px;
        height: 100%;
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
    }
    .message-wrapper-card .form-control {
        border: 1.5px solid #cbd5e1;
        border-radius: 15px;
        padding: 15px;
        font-size: 0.95rem;
    }
    .message-wrapper-card .form-control:focus {
        border-color: #20a060;
        box-shadow: 0 0 0 4px rgba(32, 160, 96, 0.1);
    }
    .btn-submit-green {
        background: #20a060 !important;
        color: white !important;
        font-weight: 700;
        border-radius: 50px;
        padding: 15px;
        transition: 0.3s;
        border: none;
    }
    .btn-submit-green:hover {
        background: #1b8a52 !important;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(32, 160, 96, 0.2);
    }

    /* Platform Features Styles */
    .feature-card {
        background: #ffffff;
        border: 3px solid #20a060;
        padding: 40px 30px;
        border-radius: 35px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        box-shadow: 0 10px 30px rgba(32, 160, 96, 0.1);
    }
    .feature-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 50px rgba(32, 160, 96, 0.2);
        border-color: #28c76f;
    }
    .feature-card .icon-box {
        width: 70px; height: 70px; background: #20a060; color: white;
        border-radius: 20px; display: flex; align-items: center; justify-content: center;
        font-size: 24px; font-weight: 800; margin-bottom: 25px; transition: 0.3s ease;
    }
    .feature-card h4 { font-weight: 700; font-size: 1.25rem; margin-bottom: 12px; color: #20a060; }
    </style>

    <section class="py-100px mt-5" id="features" style="background: transparent;">
        <div class="container py-60px">
            <div class="text-start mb-5" data-aos="fade-up">
                <h2 class="fw-bold fs-1" style="color: #20a060; letter-spacing: -1.5px; font-weight: 900 !important; text-shadow: 0 4px 15px rgba(0,0,0,0.5);">Platform Features</h2>
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
    <section id="announcements" class="py-100px" style="background: transparent;">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5" data-aos="fade-up">
                <div class="text-start mb-4 mb-md-0">
                    <h2 class="fw-bold fs-1 mb-3" style="color: #20a060; letter-spacing: -1.5px; font-weight: 900 !important; text-shadow: 0 4px 15px rgba(0,0,0,0.5);">Cooperative Announcements</h2>
                </div>
                <div>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#announcementsModal" class="btn rounded-pill px-5 py-3 fw-bold text-white b-gallery-btn" style="background: #20a060 !important; border: 2px solid #20a060 !important; box-shadow: 0 4px 15px rgba(32, 160, 96, 0.3); transition: all 0.3s ease;">
                        View All Announcements
                    </button>
                </div>
            </div>

            <?php if (!empty($ann_rows)): ?>
            <div class="row g-4 justify-content-center">
                <?php foreach (array_slice($ann_rows, 0, 3) as $i => $ann): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo 100 + ($i * 100); ?>">
                    <div class="ann-card-v2" style="cursor: pointer;" 
                         onclick="openAnnouncementDetail(this)"
                         data-title="<?php echo htmlspecialchars($ann['title']); ?>"
                         data-category="<?php echo htmlspecialchars($ann['category'] ?? 'General'); ?>"
                         data-date="<?php echo date('M j, Y', strtotime($ann['created_at'])); ?>"
                         data-author="<?php echo htmlspecialchars($ann['first_name'] . ' ' . $ann['last_name']); ?>"
                         data-content="<?php echo htmlspecialchars($ann['content']); ?>">
                        <span class="ann-category-tag"><?php echo htmlspecialchars($ann['category'] ?? 'General'); ?></span>
                        
                        <div class="ann-icon-box">
                            <?php echo strtoupper(substr($ann['first_name'], 0, 1) . substr($ann['last_name'], 0, 1)); ?>
                        </div>
                        
                        <h4 class="title"><?php echo htmlspecialchars($ann['title']); ?></h4>
                        <p class="snippet"><?php echo htmlspecialchars(mb_strimwidth($ann['content'], 0, 100, '...')); ?></p>
                        
                        <div class="ann-footer">
                            <span class="author"><i class="bi bi-person me-1"></i> <?php echo htmlspecialchars($ann['first_name'] . ' ' . $ann['last_name']); ?></span>
                            <span class="date"><i class="bi bi-calendar4-event me-1"></i> <?php echo date('M j, Y', strtotime($ann['created_at'])); ?></span>
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

    <section class="py-100px" id="contact" style="background: transparent !important;">
        <div class="container py-60px">
            <div class="text-start mb-5" data-aos="fade-right">
                <h2 class="fw-bold" style="color: #20a060 !important; font-weight: 900; font-size: 2.8rem; letter-spacing: -1.5px; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">Frequently Asked Questions</h2>
            </div>
            
            <div class="row g-4 overflow-hidden">
                <!-- FAQ Accordion Column -->
                <div class="col-lg-7" data-aos="fade-right" data-aos-delay="200">
                    <div class="faq-wrapper-card">
                        <div class="accordion faq-accordion" id="faqAccordion">
                            <!-- FAQ 1 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        Who can register in TrackCOOP?
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Farmers and fisherfolks connected to NFFAC Nasugbu can register. Select the correct sector during signup so your account profile is properly categorized.
                                    </div>
                                </div>
                            </div>
                            <!-- FAQ 2 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        Why can't I log in right after registering?
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        All new accounts require administrative verification. Please wait for an SMS or email notification once your account has been reviewed and approved.
                                    </div>
                                </div>
                            </div>
                            <!-- FAQ 3 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        What password format is required?
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        For security, your password must be at least 8 characters long and include a mix of uppercase letters, lowercase letters, and numbers.
                                    </div>
                                </div>
                            </div>
                            <!-- FAQ 4 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                        How do I know the latest cooperative updates?
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        You can check the "Cooperative Announcements" section on the homepage or log in to your dashboard to view more detailed management news.
                                    </div>
                                </div>
                            </div>
                            <!-- FAQ 5 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                        Who should I contact for account concerns?
                                    </button>
                                </h2>
                                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        For urgent concerns, you can use the message form on this page or visit our Official Coop Office during working hours.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Form Column -->
                <div class="col-lg-5" data-aos="fade-left" data-aos-delay="400">
                    <div class="message-wrapper-card">
                        <div class="text-start mb-4">
                            <h3 class="fw-bold" style="color: #20a060; letter-spacing: -0.5px;">Send us a Message</h3>
                            <p class="text-muted small" style="text-align: justify;">Need more help? Send your question and our team will get back to you.</p>
                        </div>
                        <form action="#" method="POST">
                            <div class="row g-3">
                                <div class="col-md-4"><input type="text" class="form-control" placeholder="First Name" required></div>
                                <div class="col-md-4"><input type="text" class="form-control" placeholder="Middle Name"></div>
                                <div class="col-md-4"><input type="text" class="form-control" placeholder="Last Name" required></div>
                                <div class="col-12"><input type="email" class="form-control" placeholder="Email Address" required></div>
                                <div class="col-12"><textarea class="form-control" rows="5" placeholder="How can we help?" required></textarea></div>
                                <div class="col-12 pt-2">
                                    <button type="submit" class="btn btn-submit-green w-100">Submit Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NFFAC Official Footer Section -->

    <!-- --- DEDICATED LOGIN MODAL --- -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
            <div class="modal-content border-0">
                <div class="modal-header-botanical position-relative" style="padding: 25px; display: flex; justify-content: center;">
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 20px; right: 20px; background: rgba(0,0,0,0.1); border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: none; color: #ffffff;">
                        <i class="bi bi-x-lg" style="font-size: 1rem;"></i>
                    </button>
                    <div class="d-flex align-items-center gap-0">
                        <img src="TrackCOOP Logo.svg" alt="Logo" style="height: 42px; width: auto; margin-right: -14px;">
                        <div class="modal-brand m-0" style="font-weight: 800; font-size: 1.6rem; letter-spacing: -1.5px; color: #ffffff;">
                            TRACK<span style="color: #20a060;">COOP</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 pt-5">
                    <form action="auth/login.php" method="POST" onsubmit="return TrackUI.confirmForm(event, 'Proceed to login to your cooperative account?', 'Authentication', 'primary', 'Login', 'Back')">
                        <div class="mb-3">
                            <label class="small fw-bold mb-2 text-dark" style="letter-spacing: 0.5px;">USERNAME</label>
                            <input type="text" id="loginUsername" name="username" class="form-control px-4" placeholder="Enter username" required value="<?php echo !empty($remembered_user) ? htmlspecialchars($remembered_user) : (isset($_GET['username']) ? htmlspecialchars($_GET['username']) : ''); ?>">
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold mb-2 text-dark" style="letter-spacing: 0.5px;">PASSWORD</label>
                            <div class="password-toggle-wrapper">
                                <input type="password" id="loginPassword" name="password" class="form-control px-4" placeholder="Enter password" required maxlength="15" style="padding-right: 45px;">
                                <button type="button" class="password-toggle-btn" onclick="togglePassVisibility('loginPassword', 'loginEye')" style="right: 15px;">
                                    <i id="loginEye" class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold mb-2 text-dark" style="letter-spacing: 0.5px;">SELECT ROLE</label>
                            <select id="loginRole" name="selected_role" class="form-select px-4" required onchange="autoFillCredentials(this.value)">
                                <option value="">-- Choose Role --</option>
                                <option value="Admin">Administrator</option>
                                <option value="Bookkeeper">Bookkeeper</option>
                                <option value="Member">Member</option>
                            </select>
                        </div>

                        <div class="form-check mb-4 ps-1">
                            <input class="form-check-input" type="checkbox" name="remember-me" id="rememberMe" <?php echo !empty($remembered_user) ? 'checked' : ''; ?> style="margin-left: 0; margin-right: 10px; cursor: pointer;">
                            <label class="form-check-label small fw-bold text-muted" for="rememberMe" style="cursor: pointer; vertical-align: middle;">Remember Me</label>
                        </div>

                        <div class="mb-4 d-flex justify-content-center">
                            <div class="fake-recaptcha d-flex align-items-center justify-content-between p-3 border-1 rounded-3 shadow-sm" style="width: 100%; background: #fdfdfd; border: 1px solid #e2e8f0; border-radius: 12px !important;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check m-0">
                                        <input class="form-check-input" type="checkbox" id="fakeRobotCheck" required style="width: 24px; height: 24px; cursor: pointer; border-color: #cbd5e1; border-radius: 4px;">
                                    </div>
                                    <span class="small fw-bold text-muted" style="font-size: 0.85rem;">I'm not a robot</span>
                                </div>
                                <div class="text-center" style="line-height: 1;">
                                    <img src="https://www.gstatic.com/recaptcha/api2/logo_48.png" alt="reCAPTCHA" style="width: 28px; opacity: 0.9;">
                                    <div class="text-muted" style="font-size: 0.55rem; font-weight: 700; margin-top: 2px;">reCAPTCHA</div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mb-4">
                            <p class="small text-muted mb-0">Don't have an account? <a href="javascript:void(0)" onclick="TrackUI.switchModals('#loginModal', '#registerModal')" class="fw-bold text-success text-decoration-none" style="color: #20a060 !important;">Register here</a></p>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-cancel-modal w-100 py-3" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="submit" class="btn btn-login-modal w-100 py-3">
                                    <i class="bi bi-key-fill me-2"></i>Login
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- --- DEDICATED REGISTER MODAL (MULTI-STEP) --- -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 850px;">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header-botanical position-relative" style="padding: 25px; display: flex; justify-content: center;">
                    <button type="button" class="btn-close-custom" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 20px; right: 20px; background: rgba(0,0,0,0.1); border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: none; color: #ffffff;">
                        <i class="bi bi-x-lg" style="font-size: 1rem;"></i>
                    </button>
                    <div class="d-flex align-items-center gap-0">
                        <img src="TrackCOOP Logo.svg" alt="Logo" style="height: 42px; width: auto; margin-right: -14px;">
                        <div class="modal-brand m-0" style="font-weight: 800; font-size: 1.6rem; letter-spacing: -1.5px; color: #ffffff;">
                            TRACK<span style="color: #20a060;">COOP</span>
                        </div>
                    </div>
                </div>

                <div class="p-2 px-md-3">
                    <form action="auth/register.php" id="registrationForm" method="POST">
                        
                        <!-- Page 1: PERSONAL DETAILS -->
                        <div id="regStep1" class="reg-step reg-step-1">
                            <div class="step-indicator mb-1">Page 1 of 2</div>
                            <div class="section-title-pill mb-3">PERSONAL DETAILS</div>
                            
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label-custom">FIRST NAME</label>
                                    <input type="text" name="fname" class="form-control form-control-botanical" placeholder="First name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">MIDDLE NAME</label>
                                    <input type="text" name="mname" class="form-control form-control-botanical" placeholder="Middle name">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label-custom">LAST NAME</label>
                                    <input type="text" name="lname" class="form-control form-control-botanical" placeholder="Last name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">EMAIL</label>
                                    <input type="email" name="email" class="form-control form-control-botanical" placeholder="Email address" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label-custom">PHONE</label>
                                    <div class="input-group">
                                        <span class="input-group-text form-control-botanical border-end-0" style="background-color: #f1f5f9; color: #475569; font-weight: 700; border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important;">+63</span>
                                        <input type="text" name="phone" class="form-control form-control-botanical border-start-0" placeholder="9123456789" required maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '');" style="border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">STATUS</label>
                                    <select name="status" class="form-select form-control-botanical" required>
                                        <option value="">-- Select Status --</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label-custom">PLACE OF BIRTH</label>
                                    <input type="text" name="pob" class="form-control form-control-botanical" placeholder="Place of birth" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">BIRTHDAY</label>
                                    <input type="date" name="dob" class="form-control form-control-botanical" required>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label-custom">CURRENT HOME</label>
                                    <select name="barangay" class="form-select form-control-botanical" required>
                                        <option value="">-- Select Barangay --</option>
                                        <option value="Aga">Aga</option>
                                        <option value="Balayag-Manoc">Balayag-Manoc</option>
                                        <option value="Banilad">Banilad</option>
                                        <option value="Barangay 1 (Pob.)">Barangay 1 (Pob.)</option>
                                        <option value="Barangay 2 (Pob.)">Barangay 2 (Pob.)</option>
                                        <option value="Barangay 3 (Pob.)">Barangay 3 (Pob.)</option>
                                        <option value="Barangay 4 (Pob.)">Barangay 4 (Pob.)</option>
                                        <option value="Barangay 5 (Pob.)">Barangay 5 (Pob.)</option>
                                        <option value="Barangay 6 (Pob.)">Barangay 6 (Pob.)</option>
                                        <option value="Barangay 7 (Pob.)">Barangay 7 (Pob.)</option>
                                        <option value="Barangay 8 (Pob.)">Barangay 8 (Pob.)</option>
                                        <option value="Barangay 9 (Pob.)">Barangay 9 (Pob.)</option>
                                        <option value="Barangay 10 (Pob.)">Barangay 10 (Pob.)</option>
                                        <option value="Barangay 11 (Pob.)">Barangay 11 (Pob.)</option>
                                        <option value="Barangay 12 (Pob.)">Barangay 12 (Pob.)</option>
                                        <option value="Barangay 13 (Pob.)">Barangay 13 (Pob.)</option>
                                        <option value="Barangay 14 (Pob.)">Barangay 14 (Pob.)</option>
                                        <option value="Barangay 15 (Pob.)">Barangay 15 (Pob.)</option>
                                        <option value="Barangay 16 (Pob.)">Barangay 16 (Pob.)</option>
                                        <option value="Barangay 17 (Pob.)">Barangay 17 (Pob.)</option>
                                        <option value="Barangay 18 (Pob.)">Barangay 18 (Pob.)</option>
                                        <option value="Barangay 19 (Pob.)">Barangay 19 (Pob.)</option>
                                        <option value="Bayo">Bayo</option>
                                        <option value="Bucana">Bucana</option>
                                        <option value="Bulihan">Bulihan</option>
                                        <option value="Bungahan">Bungahan</option>
                                        <option value="Calauag">Calauag</option>
                                        <option value="Catandaan">Catandaan</option>
                                        <option value="Cogunan">Cogunan</option>
                                        <option value="Dacanlao">Dacanlao</option>
                                        <option value="Kaylaway">Kaylaway</option>
                                        <option value="Looc">Looc</option>
                                        <option value="Lumbangan">Lumbangan</option>
                                        <option value="Mataas na Pulo">Mataas na Pulo</option>
                                        <option value="Mulat">Mulat</option>
                                        <option value="Natipuan">Natipuan</option>
                                        <option value="Pantalan">Pantalan</option>
                                        <option value="Papaya">Papaya</option>
                                        <option value="Putat">Putat</option>
                                        <option value="Reparo">Reparo</option>
                                        <option value="Tala">Tala</option>
                                        <option value="Wawa">Wawa</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-custom">WORK/OCCUPATION</label>
                                    <input type="text" name="occupation" class="form-control form-control-botanical" placeholder="Occupation" required>
                                </div>
                            </div>
                            
                            <div class="row g-2 mt-3">
                                <div class="col-6">
                                    <button type="button" class="btn-reg-cancel w-100" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="btn-reg-next w-100" onclick="TrackUI.showRegStep(2)">
                                        Next <i class="bi bi-chevron-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Page 2: FAMILY & SECURITY (Hidden by default) -->
                        <div id="regStep2" class="reg-step reg-step-2 d-none">
                            <div class="step-indicator">Page 2 of 2</div>
                            
                            <div class="section-title-pill mb-3">FAMILY INFORMATION</div>
                            <!-- Parents & Spouse Row -->
                            <div class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <label class="form-label-custom small">FATHER'S NAME</label>
                                    <input type="text" name="father_name" class="form-control form-control-botanical" placeholder="Father's name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom small">MOTHER'S NAME</label>
                                    <input type="text" name="mother_name" class="form-control form-control-botanical" placeholder="Mother's name">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom small">WIFE'S NAME</label>
                                    <input type="text" name="wife_name" class="form-control form-control-botanical" placeholder="Wife's name (if married)">
                                </div>
                            </div>

                            <!-- Dynamic Children Container -->
                            <div class="section-title-pill mb-2">CHILDREN INFORMATION</div>
                            <div id="childrenContainer">
                                <div class="child-row row g-2">
                                    <div class="col-md-5 col-12">
                                        <label class="form-label-custom small">CHILD'S NAME</label>
                                        <input type="text" name="child_names[]" class="form-control form-control-botanical" placeholder="Child's name">
                                    </div>
                                    <div class="col-md-2 col-4">
                                        <label class="form-label-custom small">AGE</label>
                                        <input type="number" name="child_ages[]" class="form-control form-control-botanical" placeholder="Age">
                                    </div>
                                    <div class="col-md-4 col-6">
                                        <label class="form-label-custom small">BENEFIT</label>
                                        <select name="child_benefits[]" class="form-select form-control-botanical">
                                            <option value="">None</option>
                                            <option value="SSS">SSS</option>
                                            <option value="PhilHealth">PhilHealth</option>
                                            <option value="Pag-IBIG">Pag-IBIG</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1 col-2 text-center d-flex align-items-center justify-content-center">
                                        <button type="button" class="btn-add-child" onclick="TrackUI.addChildRow()" title="Add another child" style="margin-top: 18px;">
                                            <i class="bi bi-plus-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4"></div>

                            <div class="section-title-pill mb-3">ACCOUNT SECURITY</div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label-custom">USERNAME</label>
                                    <input type="text" name="username" class="form-control form-control-botanical" placeholder="Create username" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">PASSWORD</label>
                                    <div class="position-relative">
                                        <input type="password" id="regPasswordV2" name="password" class="form-control form-control-botanical" 
                                               placeholder="Create password" required maxlength="15" 
                                               oninput="checkPasswordStrengthV2(this.value)">
                                        <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer text-muted" 
                                           onclick="togglePassVisibility('regPasswordV2', this.id)" id="eyeRegV2"></i>
                                    </div>
                                    <span id="strengthTextV2" class="strength-text" style="display: none;">Weak</span>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label-custom">CONFIRM PASSWORD</label>
                                    <div class="position-relative">
                                        <input type="password" id="regConfirmPasswordV2" name="confirm_password" class="form-control form-control-botanical" 
                                               placeholder="Confirm password" required maxlength="15" oninput="checkPasswordMatchV2()">
                                        <i class="bi bi-eye position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer text-muted" 
                                           onclick="togglePassVisibility('regConfirmPasswordV2', this.id)" id="eyeConfRegV2"></i>
                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-between align-items-center mt-0">
                                    <span id="strengthTextV2" class="strength-text" style="display: none; margin-left: 2px;">Weak</span>
                                    <div id="matchStatusV2" class="fw-bold" style="font-size: 0.65rem; display: none;"></div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required style="cursor: pointer;">
                                    <label class="form-check-label small text-muted" for="termsCheck" style="cursor: pointer;">
                                        I agree to the <a href="#" class="text-success fw-bold text-decoration-none">Terms and Conditions</a>
                                    </label>
                                </div>
                                <p class="small text-muted mb-0">Already have an account? <a href="javascript:void(0)" onclick="TrackUI.switchModals('#registerModal', '#loginModal')" class="fw-bold text-success text-decoration-none">Login here</a></p>
                            </div>

                            <div class="row g-2">
                                <div class="col-4">
                                    <button type="button" class="btn-reg-cancel w-100" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle me-1"></i> Cancel
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button type="button" class="btn-reg-back w-100" onclick="TrackUI.showRegStep(1)">
                                        <i class="bi bi-chevron-left me-2"></i>Back
                                    </button>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="btn-reg-next w-100">
                                        <i class="bi bi-check2-circle me-2"></i>Register
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
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
    font-weight: 800 !important; background: #206970 !important; color: white !important;
    border: none !important; box-shadow: 0 10px 20px rgba(32, 126, 112, 0.2) !important; transition: all 0.3s ease !important;
}
#trackGlobalConfirmModal .btn-confirm:hover {
    background: #20a060 !important; transform: translateY(-3px); box-shadow: 0 15px 25px rgba(32, 160, 96, 0.3) !important;
}
#trackGlobalConfirmModal .btn-cancel {
    padding: 14px 20px !important; border-radius: 18px !important;
    font-weight: 700 !important; color: white !important; background: #206970 !important; border: none !important; transition: all 0.3s ease !important; box-shadow: 0 10px 20px rgba(32, 126, 112, 0.2) !important;
}
#trackGlobalConfirmModal .btn-cancel:hover {
    background: #20a060 !important; color: white !important; transform: translateY(-3px); box-shadow: 0 15px 25px rgba(32, 160, 96, 0.3) !important;
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
        <div class="modal-content" style="border-radius: 40px; border: none; overflow: hidden; box-shadow: 0 50px 100px rgba(0,0,0,0.2); height: 75vh;">
            <div class="modal-header border-0 py-4 px-4 d-flex align-items-center position-relative w-100" style="background: rgba(22, 74, 54, 0.95) !important; border-bottom: 1px solid rgba(22, 74, 54, 0.3) !important; color: white;">
                <div class="w-100 d-flex flex-wrap gap-2 justify-content-center">
                    <button type="button" class="modal-filter-btn active" onclick="filterModalGallery('All', this)">All</button>
                    <button type="button" class="modal-filter-btn" onclick="filterModalGallery('Events', this)">Events</button>
                    <button type="button" class="modal-filter-btn" onclick="filterModalGallery('Agriculture', this)">Agriculture</button>
                    <button type="button" class="modal-filter-btn" onclick="filterModalGallery('Meetings', this)">Meetings</button>
                    <button type="button" class="modal-filter-btn" onclick="filterModalGallery('Sector News', this)">Sector News</button>
                </div>
                <button type="button" class="btn btn-light rounded-circle shadow-sm detail-close-btn" data-bs-dismiss="modal" style="position: absolute; right: 30px; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; padding: 0; outline: none; border: none; z-index: 2;">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
            <div class="modal-body px-4 pb-5">
                <div class="row g-4 mt-2" id="modalGalleryGrid">
                    <?php
                    // Static archive activities (Demo data)
                    $archive_activities = [
                        ['id' => 1, 'title' => 'Team Building Event 2026', 'category' => 'Events', 'activity_date' => '2026-03-15', 'file_path' => 'event.png', 'description' => 'Our cooperative members gathered for an exciting team building activity to strengthen bonds and improve collaboration. This event featured various activities designed to enhance teamwork and communication among participants.'],
                        ['id' => 2, 'title' => 'Harvest Season Activity', 'category' => 'Agriculture', 'activity_date' => '2026-03-10', 'file_path' => 'agriculture.webp', 'description' => 'A successful harvest season engaging our farmers. Our cooperative members gathered for an exciting activity to strengthen bonds and celebrate a bountiful yield.'],
                        ['id' => 4, 'title' => 'Member Conference 2026', 'category' => 'Meetings', 'activity_date' => '2026-02-28', 'file_path' => 'meeting.jpg', 'description' => 'Annual strategic member conference to discuss future goals, financial reviews, and cooperative developments for the upcoming year.'],
                        ['id' => 5, 'title' => 'Sector Launch - Rice', 'category' => 'Sector News', 'activity_date' => '2026-02-20', 'file_path' => 'sector.jpg', 'description' => 'Official launch of the Rice sector operations, marking a significant milestone for our agricultural cooperative framework.'],
                        ['id' => 6, 'title' => 'Annual General Meeting', 'category' => 'Meetings', 'activity_date' => '2026-02-10', 'file_path' => 'meeting.jpg', 'description' => 'Annual general assembly of cooperative members to review financial reports and elect new board officials.'],
                    ];
                    if (count($archive_activities) > 0):
                        foreach ($archive_activities as $row): ?>
                            <div class="col-lg-4 col-md-6 modal-gallery-item" data-category="<?php echo htmlspecialchars($row['category']); ?>">
                                <div class="gallery-card-premium bg-white shadow-sm" style="border-radius: 20px; overflow: hidden; border: 2px solid #20a060; display: flex; flex-direction: column; height: 100%; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)';" onmouseout="this.style.transform='translateY(0)';" data-title="<?php echo htmlspecialchars($row['title']); ?>" data-date="<?php echo date('M d, Y', strtotime($row['activity_date'])); ?>" data-category="<?php echo htmlspecialchars($row['category']); ?>" data-img="<?php echo htmlspecialchars($row['file_path']); ?>" data-desc="<?php echo htmlspecialchars($row['description']); ?>" onclick="openActivityDetail(this)">
                                    <div class="card-img-wrapper" style="height: 180px; position: relative; background: url('<?php echo htmlspecialchars($row['file_path']); ?>') center/cover no-repeat; border-bottom: 2px solid #20a060;">
                                        <span class="category-tag bg-white text-success fw-bold px-3 py-1 rounded-pill shadow-sm" style="position: absolute; top: 15px; left: 15px; font-size: 0.8rem; pointer-events: none;"><?php echo htmlspecialchars($row['category']); ?></span>
                                    </div>
                                    <div class="card-content p-3 d-flex flex-column" style="flex: 1; pointer-events: none;">
                                        <h6 class="fw-bold mb-1" style="font-size: 0.95rem; color: #20a060;"><?php echo htmlspecialchars($row['title']); ?></h6>
                                        <p class="text-dark mb-0 mt-auto" style="font-size: 0.75rem;"><i class="bi bi-calendar-event me-1"></i><?php echo date('M d, Y', strtotime($row['activity_date'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;
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
                    padding: 8px 24px; border-radius: 50px; border: none;
                    background: white; color: #475569; font-weight: 700; font-size: 0.85rem;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
                }
                .modal-filter-btn.active, .modal-filter-btn:hover {
                    background: #20a060; color: white;
                    box-shadow: 0 10px 20px rgba(32, 160, 96, 0.25);
                }
            </style>
            <script>
                function filterModalGallery(category, btn) {
                    document.querySelectorAll('#galleryModal .modal-filter-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
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

<!-- --- PREMIUM ANNOUNCEMENTS ARCHIVE MODAL --- -->
<div class="modal fade" id="announcementsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 40px; border: none; overflow: hidden; box-shadow: 0 50px 100px rgba(0,0,0,0.2); height: 80vh;">
            <div class="modal-header border-0 py-4 px-4 d-flex align-items-center position-relative w-100" style="background: rgba(22, 74, 54, 0.95) !important; color: white;">
                <div class="w-100 d-flex flex-wrap gap-2 justify-content-center">
                    <button type="button" class="modal-filter-btn ann-filter active" onclick="filterAnnouncements('All', this)">All</button>
                    <button type="button" class="modal-filter-btn ann-filter" onclick="filterAnnouncements('General', this)">General</button>
                    <button type="button" class="modal-filter-btn ann-filter" onclick="filterAnnouncements('Events', this)">Events</button>
                    <button type="button" class="modal-filter-btn ann-filter" onclick="filterAnnouncements('Meetings', this)">Meetings</button>
                    <button type="button" class="modal-filter-btn ann-filter" onclick="filterAnnouncements('Deadlines', this)">Deadlines</button>
                    <button type="button" class="modal-filter-btn ann-filter" onclick="filterAnnouncements('Sector News', this)">Sector News</button>
                </div>
                <button type="button" class="btn btn-light rounded-circle shadow-sm detail-close-btn" data-bs-dismiss="modal" style="position: absolute; right: 30px; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; padding: 0; outline: none; border: none; z-index: 2;">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="d-flex flex-column gap-3" id="modalAnnouncementsList">
                    <?php foreach ($ann_rows as $row): ?>
                    <div class="modal-ann-row-item" data-category="<?php echo htmlspecialchars($row['category'] ?? 'General'); ?>">
                        <div class="ann-horizontal-card bg-white p-4 d-flex align-items-center gap-4 position-relative" style="border-radius: 25px; border: 1.5px solid #edf2f7; box-shadow: 0 4px 12px rgba(0,0,0,0.03); cursor: pointer;"
                             onclick="openAnnouncementDetail(this)"
                             data-title="<?php echo htmlspecialchars($row['title']); ?>"
                             data-category="<?php echo htmlspecialchars($row['category'] ?? 'General'); ?>"
                             data-date="<?php echo date('M j, Y', strtotime($row['created_at'])); ?>"
                             data-author="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                             data-content="<?php echo htmlspecialchars($row['content']); ?>">
                            <div class="ann-horizontal-icon" style="width: 80px; height: 80px; background: #20a060; color: white; border-radius: 20px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 800;">
                                <?php echo strtoupper(substr($row['first_name'], 0, 1) . substr($row['last_name'], 0, 1)); ?>
                            </div>
                            <div class="flex-grow-1 text-start pe-5">
                                <h5 class="fw-bold mb-2" style="color: #1a332a; font-size: 1.2rem;"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="text-muted small mb-3 lh-base" style="max-width: 90%;"><?php echo htmlspecialchars($row['content']); ?></p>
                                <div class="ann-row-footer d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: #f1f5f9 !important;">
                                    <span class="small fw-600 text-muted"><i class="bi bi-person me-1 text-success"></i> <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></span>
                                    <span class="small fw-600 text-muted"><i class="bi bi-calendar4-event me-1 text-success"></i> <?php echo date('M j, Y', strtotime($row['created_at'])); ?></span>
                                </div>
                            </div>
                            <span class="badge rounded-pill px-3 py-2" style="position: absolute; top: 25px; right: 30px; background: #eef7f2; color: #20a060; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                <?php echo htmlspecialchars($row['category'] ?? 'General'); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <script>
                function filterAnnouncements(category, btn) {
                    document.querySelectorAll('.ann-filter').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const items = document.querySelectorAll('.modal-ann-row-item');
                    items.forEach(item => {
                        const itemCat = item.getAttribute('data-category');
                        if (category === 'All' || itemCat === category) {
                            item.style.display = 'block';
                            item.style.animation = 'fadeInUp 0.3s ease forwards';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                }
            </script>
        </div>
    </div>
</div>

<style>
    .modal-ann-row-item { animation: fadeInUp 0.4s ease forwards; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- --- ANNOUNCEMENT DETAIL MODAL --- -->
<div class="modal fade" id="announcementDetailModal" tabindex="-1" aria-hidden="true" style="z-index: 2000;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 35px; border: none; overflow: hidden; box-shadow: 0 40px 100px rgba(0,0,0,0.3);">
            <!-- Modal Header (Dark Green with Integrated Icon) -->
            <div class="modal-header border-0 py-4 px-4 d-flex align-items-center justify-content-between" style="background: rgba(22, 74, 54, 0.95); color: white;">
                <div class="d-flex align-items-center gap-3">
                    <div id="annDetailInitialBox" class="d-flex align-items-center justify-content-center" style="width: 55px; height: 55px; background: #20a060; color: white; border-radius: 12px; font-size: 22px; font-weight: 800; border: 2px solid rgba(255,255,255,0.2);">
                        A
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0" id="annDetailTitle" style="color: #20a060; font-size: 1.3rem; letter-spacing: -0.5px;">Announcement Title</h4>
                        <span id="annDetailCategory" class="text-white-50 small fw-bold text-uppercase" style="letter-spacing: 1px;">CATEGORY</span>
                    </div>
                </div>
                <button type="button" class="btn ann-detail-close-btn rounded-circle p-0 d-flex align-items-center justify-content-center" data-bs-dismiss="modal" style="width: 44px; height: 44px;">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
            
            <!-- Modal Body (Clean Content) -->
            <div class="modal-body p-4 p-md-5 bg-white">
                <div id="annDetailContent" class="text-dark lh-lg" style="font-size: 1.1rem; color: #334155 !important;">
                    Announcement content goes here...
                </div>
            </div>
            
            <!-- Modal Footer (Status Bar Style) -->
            <div class="modal-footer border-0 p-4 pt-0 bg-white">
                <div class="w-100 d-flex justify-content-between align-items-center py-3 border-top" style="border-color: #f1f5f9 !important;">
                    <div class="text-muted small fw-bold d-flex align-items-center gap-2" style="color: #20a060 !important;">
                        <i class="bi bi-person fs-5"></i> <span id="annDetailAuthor">Admin Name</span>
                    </div>
                    <div class="text-muted small fw-bold d-flex align-items-center gap-2" style="color: #20a060 !important;">
                         <span id="annDetailDate">April 02, 2026</span> <i class="bi bi-calendar4-event fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openAnnouncementDetail(element) {
        if(!element) return;
        
        // Read data attributes
        const title = element.getAttribute('data-title');
        const category = element.getAttribute('data-category');
        const date = element.getAttribute('data-date');
        const author = element.getAttribute('data-author');
        const content = element.getAttribute('data-content');

        // Extract Initial (First letter of author)
        const initial = author ? author.charAt(0).toUpperCase() : 'A';

        // Populate modal fields
        const t = document.getElementById('annDetailTitle');
        const c = document.getElementById('annDetailCategory');
        const d = document.getElementById('annDetailDate');
        const a = document.getElementById('annDetailAuthor');
        const con = document.getElementById('annDetailContent');
        const ib = document.getElementById('annDetailInitialBox');

        if(t) t.textContent = title;
        if(c) c.textContent = category;
        if(d) d.textContent = date;
        if(a) a.textContent = author;
        if(con) con.innerHTML = content.replace(/\n/g, '<br>');
        if(ib) ib.textContent = initial;

        // Trigger Modal
        const modalEl = document.getElementById('announcementDetailModal');
        if(modalEl) {
            const annModal = bootstrap.Modal.getOrCreateInstance(modalEl);
            annModal.show();
        }
    }
</script>

<!-- --- ACTIVITY DETAIL MODAL --- -->
<style>
    .detail-close-btn {
        transition: all 0.2s ease-in-out;
        color: #20a060 !important;
    }
    .detail-close-btn:hover {
        color: white !important; 
        background-color: #dc3545 !important; /* Solid Red BG */
        transform: scale(1.1);
    }
</style>
<div class="modal fade" id="activityDetailModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,0.3);">
            <!-- Top Image Half -->
            <div id="detailModalImage" style="height: 350px; background: url('') center/cover no-repeat; position: relative;">
                <!-- Close Button -->
                <button type="button" class="btn btn-light rounded-circle shadow-sm detail-close-btn" data-bs-dismiss="modal" style="position: absolute; right: 20px; top: 20px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; padding: 0; outline: none; border: none;">
                    <i class="bi bi-x-lg fs-5"></i>
                </button>
            </div>
            <!-- Bottom Text Half -->
            <div class="modal-body p-4 p-md-5 bg-white text-start">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-1 gap-2">
                    <h3 class="fw-bold mb-0" id="detailModalTitle" style="color: #20a060;">Activity Title</h3>
                    <div class="text-muted" style="font-size: 0.95rem; display: flex; align-items: center; white-space: nowrap; font-weight: 500; color: #64748b !important;">
                        <i class="bi bi-calendar3 me-2" style="color: #74B816;"></i> <span id="detailModalDate">January 01, 2026</span>
                    </div>
                </div>
                <h6 class="mb-4" id="detailModalCategory" style="color: #20a060; font-weight: 600; font-size: 0.9rem;">Category</h6>
                <p class="text-dark mb-0" id="detailModalDesc" style="line-height: 1.8; font-size: 1rem; color: #334155 !important;">
                    Activity description goes here.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    function openActivityDetail(element) {
        // Read data attributes
        const title = element.getAttribute('data-title');
        const dateStr = element.getAttribute('data-date');
        const category = element.getAttribute('data-category');
        const img = element.getAttribute('data-img');
        const desc = element.getAttribute('data-desc');

        // Populate detail modal
        document.getElementById('detailModalTitle').textContent = title;
        document.getElementById('detailModalDate').textContent = dateStr;
        document.getElementById('detailModalCategory').textContent = category;
        document.getElementById('detailModalDesc').textContent = desc;
        document.getElementById('detailModalImage').style.backgroundImage = `url('${img}')`;

        // Show modal (we hide gallery if needed, but z-index handles stacking for now)
        const detailModal = new bootstrap.Modal(document.getElementById('activityDetailModal'));
        detailModal.show();
    }
</script>

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
    },
    switchModals(fromId, toId) {
        const fromModal = bootstrap.Modal.getInstance(document.querySelector(fromId));
        if (fromModal) fromModal.hide();
        setTimeout(() => {
            const toModal = new bootstrap.Modal(document.querySelector(toId));
            toModal.show();
        }, 400);
    },
    showRegStep(step) {
        document.querySelectorAll('.reg-step').forEach(s => s.classList.add('d-none'));
        document.getElementById(`regStep${step}`).classList.remove('d-none');
    },
    addChildRow() {
        const container = document.getElementById('childrenContainer');
        const newRow = document.createElement('div');
        newRow.className = 'child-row row g-2 mt-2';
        newRow.innerHTML = `
            <div class="col-md-5 col-12">
                <input type="text" name="child_names[]" class="form-control form-control-botanical" placeholder="Child's name">
            </div>
            <div class="col-md-2 col-4">
                <input type="number" name="child_ages[]" class="form-control form-control-botanical" placeholder="Age">
            </div>
            <div class="col-md-4 col-6">
                <select name="child_benefits[]" class="form-select form-control-botanical">
                    <option value="">None</option>
                    <option value="SSS">SSS</option>
                    <option value="PhilHealth">PhilHealth</option>
                    <option value="Pag-IBIG">Pag-IBIG</option>
                </select>
            </div>
            <div class="col-md-1 col-2 text-center d-flex align-items-center justify-content-center">
                <div class="btn-remove-child" onclick="this.closest('.child-row').remove()" title="Remove child" style="padding-top:0;">
                    <i class="bi bi-dash-circle-fill fs-5"></i>
                </div>
            </div>
        `;
        container.appendChild(newRow);
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
                const myModal = new bootstrap.Modal(document.getElementById('loginModal'));
                myModal.show();
            }
        });
    }
});
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<style>
    .ann-detail-close-btn {
        transition: all 0.2s ease-in-out;
        border: 2px solid rgba(255,255,255,0.4) !important;
        color: white !important;
    }
    .ann-detail-close-btn:hover {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
        transform: scale(1.1) rotate(90deg);
        box-shadow: 0 0 15px rgba(220, 53, 69, 0.4);
    }
</style>

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

        // --- PASSWORD STRENGTH LOGIC ---
        function checkPasswordStrength(password) {
            const container = document.getElementById('strengthContainer');
            const bar = document.getElementById('strengthBar');
            const text = document.getElementById('strengthText');
            const reqText = document.getElementById('passReqText');

            if (password.length > 0) {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
                return;
            }

            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-zA-Z]/.test(password) && /\d/.test(password)) strength++;
            if (/[\W_]/.test(password)) strength++;

            // Reset classes
            bar.className = 'strength-segment';
            text.className = 'strength-text';

            if (strength === 1 || password.length < 8) {
                bar.classList.add('strength-weak');
                text.textContent = 'Bad Password';
                text.classList.add('text-weak');
            } else if (strength === 2) {
                bar.classList.add('strength-fair');
                text.textContent = 'Good Password';
                text.classList.add('text-fair');
            } else if (strength === 3) {
                bar.classList.add('strength-strong');
                text.textContent = 'Strong Password';
                text.classList.add('text-strong');
            }
        }

        function checkPasswordMatch() {
            const pass = document.getElementById('regPassword').value;
            const confirm = document.getElementById('regConfirmPassword').value;
            const status = document.getElementById('matchStatus');

            if (confirm.length === 0) {
                status.style.display = 'none';
                return;
            }

            status.style.display = 'block';
            if (pass === confirm) {
                status.textContent = 'Passwords Match';
                status.style.color = '#22c55e';
            } else {
                status.textContent = 'Passwords do not match';
                status.style.color = '#ef4444';
            }
        }

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

        // --- AUTO-FILL CREDENTIALS BASED ON ROLE ---
        function autoFillCredentials(role) {
            const usernameField = document.getElementById('loginUsername');
            const passwordField = document.getElementById('loginPassword');
            
            const credentials = {
                'Admin': { username: 'admin', password: '123456789' },
                'Bookkeeper': { username: 'bookkeeper', password: '123456789' },
                'Member': { username: 'member', password: '123456789' }
            };
            
            if (credentials[role]) {
                usernameField.value = credentials[role].username;
                passwordField.value = credentials[role].password;
            } else {
                usernameField.value = '';
                passwordField.value = '';
            }
        }

        // --- NEW MULTI-STEP PASSWORD LOGIC ---
        function checkPasswordStrengthV2(password) {
            const text = document.getElementById('strengthTextV2');
            
            if (password.length === 0) {
                text.style.display = 'none';
                return;
            }

            text.style.display = 'block';

            const hasLetters = /[a-zA-Z]/.test(password);
            const hasNumbers = /\d/.test(password);
            const hasSymbols = /[\W_]/.test(password);

            // Reset classes
            text.className = 'strength-text';

            if (hasLetters && hasNumbers && hasSymbols) {
                // STRONG: Letters + Numbers + Symbols
                text.textContent = 'Strong Password';
                text.classList.add('text-strong');
            } else if (hasLetters && (hasNumbers || hasSymbols)) {
                // GOOD: Letters + (Numbers OR Symbols)
                text.textContent = 'Good Password';
                text.classList.add('text-good');
            } else {
                // WEAK: Letters only (or any other basic combo)
                text.textContent = 'Weak Password';
                text.classList.add('text-weak');
            }
        }

        function checkPasswordMatchV2() {
            const pass = document.getElementById('regPasswordV2').value;
            const confirm = document.getElementById('regConfirmPasswordV2').value;
            const status = document.getElementById('matchStatusV2');

            if (confirm.length === 0) {
                status.style.display = 'none';
                return;
            }

            status.style.display = 'block';
            if (pass === confirm) {
                status.textContent = 'Passwords Match';
                status.style.color = '#20a060';
            } else {
                status.textContent = 'Passwords do not match';
                status.style.color = '#ef4444';
            }
        }

        // --- VERTICAL CENTERING SCROLL LOGIC ---
        document.querySelectorAll('a.nav-link[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (!targetElement) return;

                e.preventDefault();
                
                // Calculate position to center the section
                const elementRect = targetElement.getBoundingClientRect();
                const absoluteElementTop = elementRect.top + window.pageYOffset;
                const elementHeight = targetElement.offsetHeight;
                const viewportHeight = window.innerHeight;
                
                // Pinned Centering Logic:
                // Attempts to center the section, but ensures the TOP is never hidden
                const navbarHeight = 90; // Approx navbar height + buffer
                let offsetPosition = absoluteElementTop - (viewportHeight / 2) + (elementHeight / 2);
                
                // If the section is too tall and centering hides the top, stick to top
                if (offsetPosition > absoluteElementTop - navbarHeight) {
                    offsetPosition = absoluteElementTop - navbarHeight;
                }

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                // Update active state in navbar
                document.querySelectorAll('.nav-link').forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
<?php include('includes/footer.php'); ?>
</body>
</html>