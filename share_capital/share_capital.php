<?php
session_start();
include('../auth/db_connect.php'); // Inclusion kept for path consistency but connection not used below

// Static Identity for Full-Static Demo
$user_id   = 101; 
$user_role = 'Bookkeeper';
$full_name = "Bookkeeper";

// ── Handle Add Capital (Admin/Bookkeeper) ──── (Demo Mode)
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_capital'])) {
    // Simulated successful addition
    $msg = "success";
}

// ── Summary Stats ──── (Static demo data)
$total_capital   = 180200.00;
$member_count    = 16;
$avg_capital     = 11262.50;
$monthly_capital = 45250.00;

// ── Per Member Capital Balance ──── (Standardized 5-Sector Static Data)
$static_members_cap = [
    ['id' => 1, 'first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'username' => 'juan123', 'sector' => 'Rice', 'status' => 'Approved', 'balance' => 5450.00, 'tx_count' => 3],
    ['id' => 2, 'first_name' => 'Maria', 'last_name' => 'Santos', 'username' => 'maria456', 'sector' => 'Corn', 'status' => 'Approved', 'balance' => 8200.00, 'tx_count' => 5],
    ['id' => 3, 'first_name' => 'Pedro', 'last_name' => 'Garcia', 'username' => 'pedro789', 'sector' => 'Fishery', 'status' => 'Approved', 'balance' => 3100.00, 'tx_count' => 2],
    ['id' => 4, 'first_name' => 'Rosa', 'last_name' => 'Lopez', 'username' => 'rosa101', 'sector' => 'Livestock', 'status' => 'Approved', 'balance' => 6000.00, 'tx_count' => 4],
    ['id' => 5, 'first_name' => 'Alex', 'last_name' => 'Reyes', 'username' => 'alexreyes', 'sector' => 'High Value Crops', 'status' => 'Approved', 'balance' => 12400.00, 'tx_count' => 8],
    ['id' => 6, 'first_name' => 'Elena', 'last_name' => 'Bautista', 'username' => 'elenab', 'sector' => 'Rice', 'status' => 'Approved', 'balance' => 15400.00, 'tx_count' => 12],
    ['id' => 7, 'first_name' => 'Roberto', 'last_name' => 'Mendoza', 'username' => 'robert_m', 'sector' => 'Corn', 'status' => 'Approved', 'balance' => 9200.00, 'tx_count' => 6],
    ['id' => 8, 'first_name' => 'Liza', 'last_name' => 'Pascual', 'username' => 'lizap', 'sector' => 'Fishery', 'status' => 'Approved', 'balance' => 4300.00, 'tx_count' => 3],
    ['id' => 9, 'first_name' => 'Fernando', 'last_name' => 'Villanueva', 'username' => 'fernandv', 'sector' => 'Livestock', 'status' => 'Approved', 'balance' => 18500.00, 'tx_count' => 15],
    ['id' => 10, 'first_name' => 'Carmen', 'last_name' => 'Gonzales', 'username' => 'carmeng', 'sector' => 'High Value Crops', 'status' => 'Approved', 'balance' => 7200.00, 'tx_count' => 4],
    ['id' => 11, 'first_name' => 'Antonio', 'last_name' => 'Torres', 'username' => 'antoniot', 'sector' => 'Rice', 'status' => 'Approved', 'balance' => 3300.00, 'tx_count' => 2],
    ['id' => 12, 'first_name' => 'Sofia', 'last_name' => 'Aquino', 'username' => 'sofia_a', 'sector' => 'Corn', 'status' => 'Approved', 'balance' => 21000.00, 'tx_count' => 18],
    ['id' => 13, 'first_name' => 'Miguel', 'last_name' => 'Dizon', 'username' => 'migueld', 'sector' => 'Fishery', 'status' => 'Approved', 'balance' => 11000.00, 'tx_count' => 9],
    ['id' => 14, 'first_name' => 'Gina', 'last_name' => 'Castro', 'username' => 'ginac', 'sector' => 'Livestock', 'status' => 'Approved', 'balance' => 15400.00, 'tx_count' => 10],
    ['id' => 15, 'first_name' => 'Andres', 'last_name' => 'Soriano', 'username' => 'andress', 'sector' => 'High Value Crops', 'status' => 'Approved', 'balance' => 25000.00, 'tx_count' => 22],
    ['id' => 16, 'first_name' => 'Isabel', 'last_name' => 'Flores', 'username' => 'isabelf', 'sector' => 'Rice', 'status' => 'Approved', 'balance' => 14750.00, 'tx_count' => 11],
];
$members_cap = $static_members_cap;

// ── Member list for modal dropdown ──
$member_list = [
    ['id' => 1, 'first_name' => 'Juan', 'last_name' => 'Dela Cruz'],
    ['id' => 2, 'first_name' => 'Maria', 'last_name' => 'Santos'],
    ['id' => 3, 'first_name' => 'Pedro', 'last_name' => 'Garcia'],
    ['id' => 4, 'first_name' => 'Rosa', 'last_name' => 'Lopez'],
    ['id' => 5, 'first_name' => 'Alex', 'last_name' => 'Reyes'],
    ['id' => 12, 'first_name' => 'Sofia', 'last_name' => 'Aquino'],
    ['id' => 15, 'first_name' => 'Andres', 'last_name' => 'Soriano'],
];

// ── All Transactions for Full History Modal ── (Static demo data)
$all_tx_rows = [
    ['id' => 1, 'amount' => 5450.00, 'transaction_type' => 'deposit', 'reference_no' => 'REF001', 'notes' => 'Initial contribution', 'created_at' => '2024-01-20', 'uid' => 1, 'first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'sector' => 'Rice', 'rec_fname' => 'Bookkeeper', 'rec_lname' => ''],
    ['id' => 2, 'amount' => 3750.00, 'transaction_type' => 'deposit', 'reference_no' => 'REF002', 'notes' => 'Monthly contribution', 'created_at' => '2024-02-15', 'uid' => 2, 'first_name' => 'Maria', 'last_name' => 'Santos', 'sector' => 'Corn', 'rec_fname' => 'Bookkeeper', 'rec_lname' => ''],
    ['id' => 3, 'amount' => 8200.00, 'transaction_type' => 'deposit', 'reference_no' => 'REF003', 'notes' => 'Quarterly contribution', 'created_at' => '2024-03-10', 'uid' => 3, 'first_name' => 'Pedro', 'last_name' => 'Garcia', 'sector' => 'Fishery', 'rec_fname' => 'Bookkeeper', 'rec_lname' => ''],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share Capital Overview | TRACKCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    <style>
        :root {
            --track-green: #206970;
            --track-green-light: #e9f5ee;
            --track-dark: #1a1a1a;
            --track-bg: #f8fafc;
            --track-beige: #F5F5DC;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --text-main: #212529;
            --text-muted: #555555;
        }

        * { box-sizing: border-box; }

        @keyframes fadeInUpCustom {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        body { 
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            color: var(--text-main);
            background: linear-gradient(rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.55)), url('../Home.jpeg') top center / 100% 100% no-repeat fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ── Navbar ── */
        .navbar {
            background-color: #164a36 !important;
            padding: 15px 0;
            border-bottom: 1px solid rgba(22, 74, 54, 0.3);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            animation: fadeInUpCustom 0.8s ease-out;
        }
        .navbar-brand { font-weight: 800; font-size: 1.5rem; letter-spacing: -0.8px; color: #ffffff !important; }
        .navbar-brand span { color: #20a060; }
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 600; font-size: 0.95rem; margin: 0 10px;
            padding: 8px 0 !important; position: relative;
            transition: var(--transition-smooth); display: flex; align-items: center; gap: 6px;
        }
        .navbar-nav .nav-link::after {
            content: ''; position: absolute; bottom: 0; left: 0;
            width: 0; height: 2px; background-color: var(--track-green); transition: width 0.3s ease;
        }
        .navbar-nav .nav-link:hover::after,
        .navbar-nav .nav-link.active::after { width: 100%; }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active { color: #20a060 !important; background: transparent !important; }
        .logout-btn {
            border: 2px solid #dc2626; background: #dc2626; color: white;
            width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 12px; transition: var(--transition-smooth); text-decoration: none;
        }
        .logout-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(220, 38, 38, 0.6); }

        /* ── Page Header ── */
        .page-header {
            background: transparent;
            padding: 10px 0 10px;
            border-bottom: none;
            margin-bottom: 10px;
            position: relative; overflow: hidden;
            animation: fadeInUpCustom 0.8s cubic-bezier(0.16,1,0.3,1) both;
            color: #ffffff !important;
        }
        .page-header h1 { color: #20a060 !important; font-weight: 900 !important; }
        .page-header p { color: #ffffff !important; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
        .page-header::before {
            content: ''; position: absolute; top: -50%; right: -10%; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(32,160,96,0.05) 0%, transparent 70%);
            filter: blur(40px); animation: float 10s infinite alternate;
        }
        @keyframes float { from { transform: translate(0,0); } to { transform: translate(-30px, 20px); } }

        .badge-platform {
            background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(8px);
            color: var(--track-green); font-weight: 800; padding: 8px 16px;
            border-radius: 50px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.5px;
            display: inline-flex; align-items: center; margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(32,160,96,0.1); border: 1.5px solid rgba(32,160,96,0.15);
        }
        .pulse-dot {
            width: 8px; height: 8px; background-color: var(--track-green); border-radius: 50%;
            margin-right: 10px; position: relative;
        }
        .pulse-dot::after {
            content: ''; position: absolute; width: 100%; height: 100%; top: 0; left: 0;
            background: inherit; border-radius: inherit; animation: pulse-ring 1.5s infinite;
        }
        @keyframes pulse-ring { 
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(3); opacity: 0; }
        }

        /* ── Elite 3.0 Stat Cards ── */
        .stat-card {
            border: 1px solid rgba(255, 255, 255, 0.4); 
            border-radius: 24px; 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            padding: 20px; 
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
            position: relative; 
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .stat-card:hover { 
            transform: translateY(-12px) scale(1.03); 
            box-shadow: 0 40px 80px -15px rgba(32,160,96,0.15); 
            border-color: rgba(32,160,96,0.3); 
            background: #ffffff;
        }
        .stat-card::after {
            content: ''; position: absolute; bottom: 0; left: 0; width: 100%; height: 6px;
            background: var(--card-brand, var(--track-green));
            opacity: 0.1;
        }

        .icon-box {
            width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;
            border-radius: 18px; margin-bottom: 24px; transition: all 0.5s ease;
            box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05);
        }
        .stat-card:hover .icon-box { 
            transform: scale(1.2) rotate(12deg); 
            box-shadow: 0 15px 30px -5px rgba(0,0,0,0.1);
        }

        .stat-label {
            font-size: 0.75rem; 
            font-weight: 800; 
            text-transform: uppercase; 
            letter-spacing: 1.2px; 
            color: #94a3b8;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 1.85rem; 
            font-weight: 900; 
            color: #1e293b;
            letter-spacing: -1px;
            margin: 0;
            line-height: 1;
        }

        /* ── Inspiration Design System ── */
        .table-card {
            border: 1px solid rgba(226, 232, 240, 0.8); 
            border-radius: 28px; background: #ffffff;
            padding: 32px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
        }
        
        .table-elite { border-collapse: separate; border-spacing: 0 0; margin-top: 0; width: 100%; }
        .table-elite thead th { 
            background: transparent; border: none; padding: 15px 20px; 
            font-size: 0.7rem; font-weight: 800; color: #1a1a1a; 
            text-transform: uppercase; letter-spacing: 1.5px;
            border-bottom: 2px solid #f1f5f9;
        }
        .table-elite tbody tr { 
            background: white; transition: all 0.2s ease;
            border-bottom: 1px solid #f1f5f9;
        }
        .table-elite tbody tr:hover { 
            background: #f8fafc !important;
        }
        .table-elite tbody td { 
            padding: 20px 20px; border-bottom: 1px solid #f1f5f9;
            vertical-align: middle; color: var(--text-main); font-weight: 500;
        }
        
        /* Member/Document column icon */
        .icon-box-doc {
            width: 42px; height: 42px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; font-weight: 800; margin-right: 18px; flex-shrink: 0;
            background: #20a060; color: #ffffff; /* Solid Green High-Contrast */
            box-shadow: 0 4px 10px rgba(32, 160, 96, 0.2);
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        /* Pills from Inspiration */
        .badge-status-pill {
            padding: 6px 14px; border-radius: 8px; font-size: 0.7rem; 
            font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .badge-green-elite { background: #dcfce7; color: #15803d; }
        .badge-yellow-elite { background: #fef9c3; color: #854d0e; }
        .badge-red-elite    { background: #fee2e2; color: #b91c1c; }

        /* Created By / Sector style */
        .avatar-box-elite {
            width: 36px; height: 36px; border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 0.85rem; margin-right: 12px;
            background: #10b981; color: white;
        }
        .info-dual { display: flex; flex-direction: column; line-height: 1.2; }
        .info-dual .primary { font-weight: 700; color: #1e293b; font-size: 0.95rem; }
        .info-dual .secondary { font-size: 0.78rem; color: #94a3b8; font-weight: 600; }

        /* Action Buttons (Minimalist) */
        .btn-doc-action {
            width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; transition: 0.2s;
            background: transparent; border: none; color: #3b82f6;
            font-size: 0.9rem; cursor: pointer;
        }
        .btn-doc-action:hover { background: #eff6ff; transform: scale(1.1); }
        .btn-doc-action.btn-delete { color: #ef4444; }
        .btn-doc-action.btn-delete:hover { background: #fff1f2; }
        .btn-doc-action.btn-contribute { color: #20a060; }
        .btn-doc-action.btn-contribute:hover { background: #f0fdf4; }
        
        /* Pagination Footer */
        .pagination-footer-elite {
            display: flex; align-items: center; justify-content: flex-end;
            padding-top: 30px; margin-top: 10px;
            gap: 35px; font-size: 0.85rem; color: #64748b; font-weight: 700;
        }
        .rows-per-page { display: flex; align-items: center; gap: 12px; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1px; }
        .rows-per-page select {
            border: 1px solid #e2e8f0; border-radius: 6px; padding: 5px 10px;
            background: white; font-weight: 800; outline: none; cursor: pointer; color: #1e293b;
        }
        .page-chevrons { display: flex; gap: 8px; }

        /* Hide Horizontal Scrollbar but keep functionality */
        .table-responsive::-webkit-scrollbar { display: none; }
        .table-responsive {
            -ms-overflow-style: none; /* IE and Edge */
            scrollbar-width: none; /* Firefox */
        }
        .btn-chevron {
            width: 35px; height: 35px; border: 1px solid #e2e8f0; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            background: white; color: #64748b; transition: 0.2s;
        }
        .btn-chevron:hover:not(:disabled) { background: #f8fafc; color: #1e293b; border-color: #cbd5e1; }
        .btn-chevron:disabled { opacity: 0.3; cursor: not-allowed; }

        /* ── Progress Indicators ── */
        .capital-bar { height: 6px; background: #f1f5f9; border-radius: 10px; overflow: hidden; margin-top: 8px; }
        .capital-bar-fill { height: 100%; background: var(--track-green); border-radius: 10px; transition: width 1s ease-out; }

        /* ── Modal ── */
        .modal-content { border: none; border-radius: 20px; box-shadow: 0 25px 60px rgba(0,0,0,0.15); }
        .modal-header { background: rgba(22, 74, 54, 0.95); border-bottom: 2px solid rgba(22, 74, 54, 0.3); border-radius: 20px 20px 0 0; padding: 24px 28px; color: white; }
        .modal-title { font-weight: 800; font-size: 1.3rem; letter-spacing: -0.5px; color: white; gap: 10px; display: flex; align-items: center; }
        .modal-title i { color: var(--track-green); }
        .modal-body { padding: 28px; }
        .modal-footer { background: rgba(22, 74, 54, 0.95); border-top: 1px solid rgba(22, 74, 54, 0.3); border-radius: 0 0 20px 20px; padding: 20px 28px; color: white; }
        .form-label { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; }
        .form-control, .form-select {
            border-radius: 12px; padding: 12px 16px; border: 1.5px solid #e5e5c0;
            background-color: #fdfdf8; transition: 0.3s; font-size: 0.95rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--track-green); box-shadow: 0 0 0 4px rgba(32,160,96,0.12);
            background-color: #fff;
        }
        .btn-track { background: #20a060; color: white; border: none; border-radius: 12px; padding: 12px 28px; font-weight: 700; transition: var(--transition-smooth); box-shadow: 0 4px 14px rgba(32, 160, 96, 0.3); }
        .btn-track:hover { background: #1a8548; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(32, 160, 96, 0.4); color: white; }
        .btn-cancel { background: #206970; color: white; border: none; border-radius: 12px; padding: 12px 24px; font-weight: 600; transition: 0.3s; }
        .btn-cancel:hover { background: #20a060; color: white; transform: translateY(-2px); }

        /* ── Select2 Custom Theme (Beige TrackCOOP) ── */
        .select2-container--default .select2-selection--single {
            border: 1.5px solid #e5e5c0 !important;
            border-radius: 12px !important;
            height: 48px !important;
            background-color: #fdfdf8 !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 48px !important;
            padding-left: 16px !important;
            color: var(--text-main);
            font-size: 0.95rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 10px !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--track-green) !important;
            box-shadow: 0 0 0 4px rgba(32,160,96,0.12) !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: var(--track-green) !important;
            box-shadow: 0 0 0 4px rgba(32,160,96,0.12) !important;
            outline: none !important;
        }
        .select2-dropdown {
            border: 1.5px solid #e5e5c0 !important;
            border-radius: 14px !important;
            box-shadow: 0 12px 40px rgba(0,0,0,0.1) !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1.5px solid #e5e5c0;
            border-radius: 8px;
            padding: 8px 12px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.9rem;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--track-green);
            outline: none;
            box-shadow: 0 0 0 3px rgba(32,160,96,0.1);
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--track-green) !important;
            color: white !important;
        }
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: var(--track-green-light) !important;
            color: #1a5c38 !important;
            font-weight: 700;
        }
        .select2-results__option {
            font-size: 0.92rem;
            padding: 10px 14px;
        }
        .select2-search--dropdown { padding: 10px 12px 6px; }

        /* ── Shimmering Progress Bar ── */
        .capital-bar { height: 8px; border-radius: 99px; background: #f1f5f9; overflow: hidden; position: relative; }
        .capital-bar-fill { 
            height: 100%; border-radius: 99px; 
            background: linear-gradient(90deg, #20a060, #1a8548, #2ecc71); 
            background-size: 200% 100%;
            animation: shimmer 2s infinite linear;
            box-shadow: 0 0 10px rgba(46, 204, 113, 0.4);
            transition: width 1.5s cubic-bezier(0.16,1,0.3,1); 
        }
        @keyframes shimmer { 0% { background-position: 100% 0; } 100% { background-position: -100% 0; } }

        /* ── Sector badge ── */
        .sector-badge { background: var(--track-beige); color: var(--track-dark); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; border: 1px solid rgba(229,229,192,0.8); }

        /* ── Stagger Animations (mirrors admin_dashboard) ── */
        .fade-in-up { opacity: 0; animation: fadeInUpCustom 0.8s cubic-bezier(0.16,1,0.3,1) forwards; }
        .delay-1 { animation-delay: 0.1s; } .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; } .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        /* ── Button portal style (matches admin) ── */
        .btn-portal {
            background: #20a060; color: white; border-radius: 12px; padding: 12px 24px;
            font-weight: 700; border: none; box-shadow: 0 8px 20px rgba(32,160,96,0.2);
            transition: var(--transition-smooth); display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-portal:hover { transform: translateY(-3px); background: #1a8548; box-shadow: 0 12px 25px rgba(32,160,96,0.3); color: white; }

        /* --- Elite 3.0 Pagination Design --- */
        .pagination-elite-3 {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 20px;
            padding: 16px 24px;
            border-top: 1px solid #f1f5f9;
            background: #ffffff;
            border-radius: 0 0 24px 24px;
        }
        .pagination-label {
            font-size: 0.7rem;
            font-weight: 800;
            color: #94a3b8;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        .pagination-select-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .pagination-select {
            appearance: none;
            -webkit-appearance: none;
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 4px 32px 4px 12px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #1e293b;
            cursor: pointer;
            transition: var(--transition-smooth);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            min-width: 65px;
        }
        .pagination-select:hover { border-color: var(--track-green); }

        .pagination-range {
            font-size: 0.85rem;
            font-weight: 700;
            color: #64748b;
            min-width: 100px;
            text-align: center;
        }

        .pagination-nav {
            display: flex;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            background: #ffffff;
        }
        .pagination-nav-btn {
            width: 42px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }
        .pagination-nav-btn:first-child { border-right: 1.5px solid #e2e8f0; }
        .pagination-nav-btn:hover:not(:disabled) { background: #f8fafc; color: var(--track-green); }
        .pagination-nav-btn:disabled { opacity: 0.3; cursor: not-allowed; }

        .search-input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--track-green);
            font-size: 1.1rem;
            z-index: 5;
        }

        .search-input {
            width: 100%;
            height: 52px;
            padding: 0 20px 0 50px;
            border-radius: 14px;
            border: 2px solid var(--track-beige);
            background: white;
            transition: var(--transition-smooth);
            font-weight: 600;
            color: var(--track-dark);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--track-green);
            box-shadow: 0 8px 25px rgba(32, 160, 96, 0.15) !important;
            transform: translateY(-2px);
        }
    </style>
</head>
<div class="sidebar-layout">
    <?php 
        $active_page = 'share_capital';
        $user_role = $_SESSION['role'];
        $membership_type = $user_role;
        $full_name = htmlspecialchars($full_name);
        include('../includes/dashboard_sidebar.php'); 
    ?>

    <div class="main-content-wrapper">


<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container position-relative" style="z-index:1;">
            <div class="col-md-12 d-flex flex-column flex-md-row justify-content-end align-items-md-center gap-3">
                <div class="search-input-group mb-0 position-relative" style="max-width: 400px; width: 100%;">
                    <i class="bi bi-search"></i>
                    <input type="text" id="memberCapitalSearch" class="search-input" placeholder="Search">
                </div>
                <button class="btn-portal" data-bs-toggle="modal" data-bs-target="#addCapitalModal">
                    <i class="bi bi-plus-circle-fill"></i> Record Capital
                </button>
            </div>
    </div>
</div>

<div class="container pb-3">

    <!-- Flash Message -->
    <?php if ($msg === 'success'): ?>
        <div class="alert alert-success fw-bold rounded-4 mb-2" role="alert"><i class="bi bi-check-circle-fill me-2"></i> Capital contribution recorded successfully.</div>
    <?php elseif ($msg === 'error'): ?>
        <div class="alert alert-danger fw-bold rounded-4 mb-2" role="alert"><i class="bi bi-exclamation-octagon-fill me-2"></i> Error saving record. Please try again.</div>
    <?php elseif ($msg === 'invalid'): ?>
        <div class="alert alert-warning fw-bold rounded-4 mb-2" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i> Invalid data submitted. Please check the form.</div>
    <?php endif; ?>


    <!-- MEMBER CAPITAL TABLE -->
    <div class="table-card" data-aos="fade-up" data-aos-delay="100">
        <div class="table-responsive">
            <table class="table table-elite align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 250px;">NAME</th>
                        <th style="min-width: 200px;">SECTOR</th>
                        <th style="min-width: 150px;">BALANCE</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($members_cap && count($members_cap) > 0):
                        $max_balance = 1;
                        $rows_data = $members_cap;
                        if (is_array($rows_data)) {
                            foreach ($rows_data as $rc) {
                                if ($rc['balance'] > $max_balance) $max_balance = $rc['balance'];
                            }
                        }
                        foreach ($rows_data as $rc):
                            $initials = strtoupper(substr($rc['first_name'],0,1) . substr($rc['last_name'],0,1));
                            $bar_pct  = ($max_balance > 0) ? round(($rc['balance'] / $max_balance) * 100) : 0;
                    ?>
                    <tr class="member-capital-row" data-search-matched="true" onclick="showMemberHistory(<?php echo $rc['id']; ?>, '<?php echo htmlspecialchars($rc['first_name'] . ' ' . $rc['last_name'], ENT_QUOTES); ?>')" style="cursor:pointer;">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="icon-box-doc">
                                    <?php echo strtoupper(substr($rc['first_name'], 0, 1)); ?>
                                </div>
                                <div class="info-dual">
                                    <span class="primary member-name"><?php echo htmlspecialchars($rc['first_name'] . ' ' . $rc['last_name']); ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="info-dual">
                                <span class="primary member-sector"><?php echo htmlspecialchars($rc['sector']); ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-800 text-dark" style="font-size: 1.05rem;">₱<?php echo number_format($rc['balance'], 2); ?></div>
                        </td>
                        <td class="text-end" onclick="event.stopPropagation();">
                            <div class="d-flex gap-1 justify-content-end align-items-center">
                                <button class="btn-doc-action" title="View History">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn-doc-action btn-contribute" title="Quick Add Capital">
                                    <i class="bi bi-plus-circle"></i>
                                </button>
                                <button class="btn-doc-action btn-delete" title="Delete record">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted small">
                            <i class="bi bi-wallet2 d-block fs-1 opacity-25 mb-3"></i>
                            No share capital records found.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- ELITE 3.0 PAGINATION -->
        <div class="pagination-elite-3">
            <div class="pagination-select-wrapper">
                <span class="pagination-label">ROWS PER PAGE</span>
                <select id="rowsPerPageSelect" class="pagination-select" onchange="changePageSize()">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
            </div>
            <div class="pagination-range" id="paginationRangeDisplay">
                1-5 of 15
            </div>
            <div class="pagination-nav">
                <button class="pagination-nav-btn" id="prevPageBtn" onclick="prevPage()">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="pagination-nav-btn" id="nextPageBtn" onclick="nextPage()">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- FULL HISTORY MODAL -->
<div class="modal fade" id="fullHistoryModal" tabindex="-1" aria-labelledby="fullHistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fullHistoryLabel">
                    <i class="bi bi-clock-history"></i>
                    <span id="historyModalTitle">Full Transaction History</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 0;">
                <!-- Filter bar inside modal -->
                <div style="padding: 20px 28px 0; border-bottom: 1px solid #f1f5f9; background: #f8fafc;">
                    <div class="d-flex gap-3 align-items-center flex-wrap pb-3">
                        <div class="position-relative flex-grow-1" style="max-width:260px;">
                            <i class="bi bi-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#aaa;"></i>
                            <input type="text" id="historySearch" class="form-control form-control-sm" placeholder="Search member..." style="padding-left:36px;border-radius:10px;" oninput="applyHistoryFilter()">
                        </div>
                        <select id="historyTypeFilter" class="form-select form-select-sm" style="max-width:180px;border-radius:10px;" onchange="applyHistoryFilter()">
                            <option value="all">All Types</option>
                            <option value="deposit">Deposits Only</option>
                            <option value="withdrawal">Withdrawals Only</option>
                        </select>
                        <small class="text-muted ms-auto" id="historyCount"></small>
                    </div>
                </div>

                <div class="table-responsive" style="max-height:480px;">
                    <table class="table align-middle mb-0" style="font-size:0.9rem;">
                        <thead style="position:sticky;top:0;background:#fff;z-index:10;">
                            <tr>
                                <th class="border-0 ps-4" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;padding-bottom:12px;padding-top:14px;">Member</th>
                                <th class="border-0" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;">Type</th>
                                <th class="border-0" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;">Amount</th>
                                <th class="border-0" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;">Reference</th>
                                <th class="border-0" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;">Notes</th>
                                <th class="border-0" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#6b7280;">Date</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <?php if (empty($all_tx_rows)): ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">No transactions recorded yet.</td></tr>
                            <?php else: ?>
                            <?php foreach ($all_tx_rows as $tx):
                                $ini = strtoupper(substr($tx['first_name'],0,1) . substr($tx['last_name'],0,1));
                            ?>
                            <tr class="history-row"
                                data-uid="<?php echo $tx['uid']; ?>"
                                data-name="<?php echo strtolower($tx['first_name'] . ' ' . $tx['last_name']); ?>"
                                data-type="<?php echo $tx['transaction_type']; ?>">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#20a060,#1a8548);color:white;font-weight:800;font-size:0.7rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                            <?php echo $ini; ?>
                                        </div>
                                        <span class="fw-600" style="color:#1a1a1a;"><?php echo htmlspecialchars($tx['first_name'] . ' ' . $tx['last_name']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($tx['transaction_type'] === 'deposit'): ?>
                                        <span style="background:#eefdf5;color:#27ae60;padding:4px 12px;border-radius:20px;font-size:0.72rem;font-weight:700;"><i class="bi bi-arrow-down-circle me-1"></i>Deposit</span>
                                    <?php else: ?>
                                        <span style="background:#fee2e2;color:#ef4444;padding:4px 12px;border-radius:20px;font-size:0.72rem;font-weight:700;"><i class="bi bi-arrow-up-circle me-1"></i>Withdrawal</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-800" style="color:<?php echo $tx['transaction_type'] === 'deposit' ? '#27ae60' : '#ef4444'; ?>;">
                                        <?php echo $tx['transaction_type'] === 'withdrawal' ? '−' : '+'; ?>₱<?php echo number_format($tx['amount'], 2); ?>
                                    </span>
                                </td>
                                <td><small class="badge bg-light text-dark border"><?php echo $tx['reference_no'] ? htmlspecialchars($tx['reference_no']) : '—'; ?></small></td>
                                <td><small class="text-muted"><?php echo $tx['notes'] ? htmlspecialchars($tx['notes']) : '—'; ?></small></td>
                                <td>
                                    <small class="fw-600" style="color:#374151;"><?php echo date('M d, Y', strtotime($tx['created_at'])); ?></small><br>
                                    <small class="text-muted" style="font-size:0.7rem;"><?php echo date('h:i A', strtotime($tx['created_at'])); ?></small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
                <div class="p-3 text-end" style="border-top:1px solid #f1f5f9; background:#f8fafc;">
                    <small class="text-muted float-start mt-2 ms-2">Total history records shown above.</small>
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Close History</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADD CAPITAL MODAL -->
<div class="modal fade" id="addCapitalModal" tabindex="-1" aria-labelledby="addCapitalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCapitalLabel"><i class="bi bi-plus-circle-fill"></i> Record Capital Contribution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" onsubmit="return TrackUI.confirmForm(event, 'Register this capital contribution/transaction to the member ledger?', 'Finance Entry', 'primary', 'Record Now', 'Back')">
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label">Member</label>
                            <select name="member_id" id="memberSelect" class="form-select" required>
                                <option value="">-- Select Member --</option>
                                <?php if ($member_list): foreach ($member_list as $ml): ?>
                                <option value="<?php echo $ml['id']; ?>"><?php echo htmlspecialchars($ml['first_name'] . ' ' . $ml['last_name']); ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Transaction Type</label>
                            <select name="transaction_type" class="form-select" required>
                                <option value="deposit">Deposit (Add Capital)</option>
                                <option value="withdrawal">Withdrawal (Deduct Capital)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Amount (₱)</label>
                            <input type="number" name="amount" class="form-control" min="1" step="0.01" placeholder="e.g. 500.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Reference No.</label>
                            <input type="text" name="reference_no" class="form-control" placeholder="e.g. OR-2024-001">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes / Remarks</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
                        </div>
                        <div class="col-12 mt-4 text-end pe-3 pb-2">
                            <button type="submit" name="add_capital" class="btn-track"><i class="bi bi-check-circle me-2"></i> Save Record</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    AOS.init({ duration: 700, once: true, easing: 'ease-out-cubic' });

    // ── Select2: Searchable member dropdown ────────────────────────────────────
    $(document).ready(function() {
        $('#memberSelect').select2({
            dropdownParent: $('#addCapitalModal'),
            placeholder: '-- Select Member --',
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 0
        });

        // Sync quickAdd with Select2
        window.quickAdd = function(memberId) {
            $('#memberSelect').val(memberId).trigger('change');
        };
    });

    // ── Show Full History (all members) ───────────────────────────────────────
    function filterHistory(mode) {
        document.getElementById('historyModalTitle').textContent = 'Full Transaction History';
        document.getElementById('historySearch').value = '';
        document.getElementById('historyTypeFilter').value = 'all';
        // show all rows
        document.querySelectorAll('.history-row').forEach(r => r.style.display = '');
        updateCount();
    }

    // ── Show History filtered to one member ───────────────────────────────────
    function showMemberHistory(uid, name) {
        document.getElementById('historyModalTitle').textContent = name + ' — Capital History';
        document.getElementById('historySearch').value = '';
        document.getElementById('historyTypeFilter').value = 'all';
        // filter by uid
        document.querySelectorAll('.history-row').forEach(r => {
            r.style.display = (r.dataset.uid == uid) ? '' : 'none';
        });
        updateCount();
    }

    // ── Live search + type filter inside modal ────────────────────────────────
    function applyHistoryFilter() {
        const search = document.getElementById('historySearch').value.toLowerCase().trim();
        const type   = document.getElementById('historyTypeFilter').value;

        document.querySelectorAll('.history-row').forEach(r => {
            const nameMatch = !search || r.dataset.name.includes(search);
            const typeMatch = type === 'all' || r.dataset.type === type;
            r.style.display = (nameMatch && typeMatch) ? '' : 'none';
        });

        // reset title to generic if searching
        if (search) {
            document.getElementById('historyModalTitle').textContent = 'Transaction History — Search Results';
        }
        updateCount();
    }

    function updateCount() {
        const visible = document.querySelectorAll('.history-row:not([style*="display: none"])').length;
        const total   = document.querySelectorAll('.history-row').length;
        document.getElementById('historyCount').textContent = visible + ' of ' + total + ' records';
    }

    // Init count on modal open
    document.getElementById('fullHistoryModal').addEventListener('shown.bs.modal', updateCount);

    // ── Real-Time Capital Search ─────────────────────────────────────────────
    function performCapitalSearch() {
        const term = document.getElementById('memberCapitalSearch').value.toLowerCase().trim();
        const rows = document.querySelectorAll('.member-capital-row');
        let found = 0;

        rows.forEach(row => {
            const name = row.querySelector('.member-name').textContent.toLowerCase();
            const sector = row.querySelector('.member-sector').textContent.toLowerCase();
            
            const match = name.includes(term) || sector.includes(term);
            row.dataset.searchMatched = match ? 'true' : 'false';
            if (match) found++;
        });

        // Handle Empty State
        const tbody = document.querySelector('.table-elite tbody');
        let noResults = document.getElementById('noCapitalResultsRow');

        if (found === 0) {
            if (!noResults) {
                noResults = document.createElement('tr');
                noResults.id = 'noCapitalResultsRow';
                noResults.className = 'no-results-row';
                noResults.innerHTML = `
                    <td colspan="4" class="text-center py-5">
                        <div class="opacity-25 mb-3"><i class="bi bi-wallet2" style="font-size: 3rem;"></i></div>
                        <h5 class="fw-bold text-muted">No balances found</h5>
                        <p class="text-muted small mb-0">We couldn't find any member matching your search criteria.</p>
                    </td>
                `;
                tbody.appendChild(noResults);
            }
        } else if (noResults) {
            noResults.remove();
        }
        
        currentPage = 1; // Reset to page 1 on search
        updatePagination();
    }

    // ── Pagination Logic ─────────────────────────────────────────────
    let currentPage = 1;
    let rowsPerPage = 5;

    function updatePagination() {
        const rows = document.querySelectorAll('.member-capital-row');
        const visibleRows = Array.from(rows).filter(row => row.dataset.searchMatched !== 'false');
        
        const totalRows = visibleRows.length;
        const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * rowsPerPage;
        const end = Math.min(start + rowsPerPage, totalRows);

        // First, hide all rows
        rows.forEach(r => r.style.display = 'none');

        // Then show only the current page's matching rows
        visibleRows.forEach((row, index) => {
            if (index >= start && index < end) {
                row.style.display = '';
            }
        });

        // Update Range Display
        const rangeDisplay = document.getElementById('paginationRangeDisplay');
        if (rangeDisplay) {
            const startDisplay = totalRows === 0 ? 0 : start + 1;
            const endDisplay = end;
            rangeDisplay.textContent = `${startDisplay}-${endDisplay} of ${totalRows}`;
        }

        // Disable/Enable Chevrons
        const prevBtn = document.getElementById('prevPageBtn');
        const nextBtn = document.getElementById('nextPageBtn');
        if (prevBtn) prevBtn.disabled = (currentPage === 1);
        if (nextBtn) nextBtn.disabled = (currentPage === totalPages || totalRows === 0);
    }

    function nextPage() {
        currentPage++;
        updatePagination();
    }

    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            updatePagination();
        }
    }

    function changePageSize() {
        const rowsSelect = document.getElementById('rowsPerPageSelect');
        if (rowsSelect) {
            rowsPerPage = parseInt(rowsSelect.value);
            currentPage = 1;
            updatePagination();
        }
    }

    // Initialization
    document.getElementById('memberCapitalSearch').addEventListener('input', performCapitalSearch);
    document.addEventListener('DOMContentLoaded', updatePagination);
</script>
    </div> <!-- .main-content-wrapper -->
</div> <!-- .sidebar-layout -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
