<?php
// Public Announcements - No login required
include('../auth/db_connect.php');

// Static announcements data for public viewing
$static_announcements = [
    ['id' => 1, 'title' => 'Annual General Assembly 2024', 'content' => 'Join us for our annual general assembly on April 15, 2024. Light refreshments will be provided.', 'category' => 'General', 'first_name' => 'Administrator', 'last_name' => '', 'created_at' => '2024-03-20'],
    ['id' => 2, 'title' => 'New Member Registration Opened', 'content' => 'Registration for new members is now open. Fill out the form at the office to apply. Processing takes 2-3 weeks.', 'category' => 'Important', 'first_name' => 'Administrator', 'last_name' => '', 'created_at' => '2024-03-15'],
    ['id' => 3, 'title' => 'System Maintenance Notice', 'content' => 'System will be under maintenance on April 1st from 2 AM to 4 AM. Services will be temporarily unavailable.', 'category' => 'General', 'first_name' => 'Administrator', 'last_name' => '', 'created_at' => '2024-03-10'],
    ['id' => 4, 'title' => 'Share Capital Distribution', 'content' => 'Share capital distribution for Q1 2024 has been processed. Check your statement for details.', 'category' => 'Important', 'first_name' => 'Bookkeeper', 'last_name' => '', 'created_at' => '2024-03-01'],
];

// ── Search & Filter Logic
$filter_cat = isset($_GET['category']) ? trim($_GET['category']) : '';
$search     = isset($_GET['search'])   ? trim($_GET['search'])   : '';

// Filter announcements
$filtered_announcements = $static_announcements;
if ($filter_cat !== '') {
    $filtered_announcements = array_filter($filtered_announcements, function($a) use ($filter_cat) {
        return $a['category'] === $filter_cat;
    });
}
if ($search !== '') {
    $search_lower = strtolower($search);
    $filtered_announcements = array_filter($filtered_announcements, function($a) use ($search_lower) {
        return strpos(strtolower($a['title']), $search_lower) !== false || strpos(strtolower($a['content']), $search_lower) !== false;
    });
}

// Prepare data
$announcements = [];
foreach ($filtered_announcements as $ann) {
    $announcements[] = $ann;
}
$total = count($announcements);

// ── Category Counts for filter pills
$category_counts = ['General' => 2, 'Important' => 2];

// ── Pinned announcement (latest)
$pinned = !empty($announcements) ? $announcements[0] : ['title' => 'Welcome', 'content' => 'Welcome to TrackCOOP announcements', 'first_name' => 'System', 'created_at' => date('Y-m-d')];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Announcements | TrackCOOP NFFAC</title>
    <meta name="description" content="Official public announcements from the Nasugbu Farmers and Fisherfolks Agriculture Cooperative (NFFAC). Stay updated with cooperative news and updates.">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --track-green: #27ae60;
            --track-green-dark: #1a8548;
            --track-green-light: #eefdf5;
            --track-dark: #1a1a1a;
            --track-beige: #f5f5dc;
            --track-beige-dark: #eaeacc;
            --track-bg: #f8fafc;
            --text-main: #212529;
            --text-muted: #64748b;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes heroPulse {
            0%, 100% { transform: scale(1); opacity: 0.06; }
            50% { transform: scale(1.1); opacity: 0.1; }
        }
        @keyframes cardEntrance {
            from { opacity: 0; transform: translateY(20px) scale(0.99); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--track-bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Navbar ── */
        .pub-navbar {
            background: rgba(245, 245, 220, 0.97) !important;
            backdrop-filter: blur(12px);
            padding: 14px 0;
            border-bottom: 1px solid rgba(229, 229, 192, 0.6);
            box-shadow: 0 2px 16px rgba(0,0,0,0.04);
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: fadeInUp 0.6s ease-out;
        }
        .pub-navbar .navbar-brand {
            font-weight: 800;
            font-size: 1.45rem;
            letter-spacing: -1px;
            color: var(--track-dark) !important;
            text-decoration: none;
        }
        .pub-navbar .navbar-brand span { color: #27ae60; }

        .btn-back-home {
            background: white;
            border: 1.5px solid rgba(39, 174, 96, 0.25);
            color: #27ae60;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        .btn-back-home:hover {
            background: #27ae60;
            color: white;
            border-color: #27ae60;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(39, 174, 96, 0.25);
        }

        /* ── Hero Section ── */
        .page-hero {
            background: linear-gradient(135deg, #f0faf4 0%, #f1f8f4 100%);
            padding: 80px 0 60px;
            border-bottom: 1px solid rgba(39, 174, 96, 0.1);
            position: relative;
            overflow: hidden;
        }
        .page-hero::before {
            content: '';
            position: absolute;
            top: -30%; right: -10%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(32,160,96,0.07) 0%, transparent 70%);
            animation: heroPulse 6s ease-in-out infinite;
        }
        .page-hero::after {
            content: '';
            position: absolute;
            bottom: -20%; left: -5%;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(245,245,220,0.5) 0%, transparent 70%);
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: white;
            border: 1px solid rgba(39, 174, 96, 0.2);
            color: #27ae60;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 6px 16px;
            border-radius: 50px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(39, 174, 96, 0.1);
        }
        .hero-badge .badge-dot {
            width: 7px; height: 7px;
            background: var(--track-green);
            border-radius: 50%;
            animation: heroPulse 2s ease-in-out infinite;
        }
        .page-hero h1 {
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 800;
            letter-spacing: -1.5px;
            color: var(--track-dark);
            margin-bottom: 16px;
        }
        .page-hero p {
            color: var(--text-muted);
            font-size: 1.05rem;
            max-width: 560px;
        }

        /* ── Pinned Feature Card ── */
        .pinned-card {
            background: linear-gradient(135deg, #27ae60 0%, #1a8548 100%);
            border-radius: 24px;
            padding: 40px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(39, 174, 96, 0.25);
            animation: cardEntrance 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .pinned-card::before {
            content: '';
            position: absolute;
            top: -40%; right: -10%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        }
        .pinned-card .pinned-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 50px;
            margin-bottom: 18px;
            backdrop-filter: blur(5px);
        }
        .pinned-card h2 {
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.8px;
            margin-bottom: 12px;
        }
        .pinned-card p {
            opacity: 0.85;
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 24px;
        }
        .pinned-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            font-size: 0.85rem;
            opacity: 0.8;
        }

        /* ── Filter & Search Bar ── */
        .filter-section {
            background: white;
            border: 1px solid #e8f0ea;
            border-radius: 20px;
            padding: 20px 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            margin-bottom: 30px;
        }
        .search-input-group {
            position: relative;
        }
        .search-input-group i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
        .search-input-group input {
            padding-left: 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            font-size: 0.95rem;
            transition: var(--transition-smooth);
            background: #f8fafc;
        }
        .search-input-group input:focus {
            border-color: #27ae60;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.12);
            outline: none;
            background: white;
        }
        .filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 2px solid #e5e5c0;
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            transition: var(--transition-smooth);
            background: white;
        }
        .filter-pill:hover, .filter-pill.active {
            background: #27ae60;
            border-color: #27ae60;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(39, 174, 96, 0.2);
        }
        .filter-pill .count-badge {
            background: rgba(0,0,0,0.1);
            color: inherit;
            font-size: 0.72rem;
            padding: 1px 7px;
            border-radius: 50px;
        }
        .filter-pill.active .count-badge {
            background: rgba(255,255,255,0.25);
        }

        /* ── Announcement Cards ── */
        .announce-card {
            background: white;
            border: 1px solid #eef2f5;
            border-radius: 20px;
            padding: 28px;
            margin-bottom: 18px;
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
            animation: cardEntrance 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .announce-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(32, 160, 96, 0.1);
            border-color: rgba(32, 160, 96, 0.2);
        }
        .announce-card::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 4px;
            background: var(--track-green);
            border-radius: 20px 0 0 20px;
            opacity: 0;
            transition: var(--transition-smooth);
        }
        .announce-card:hover::before { opacity: 1; }

        .category-tag {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 50px;
            background: var(--track-green-light);
            color: var(--track-green);
            border: 1px solid rgba(32, 160, 96, 0.15);
        }
        .announce-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--track-dark);
            letter-spacing: -0.3px;
            margin: 12px 0 8px;
            line-height: 1.3;
        }
        .announce-body {
            color: var(--text-muted);
            font-size: 0.93rem;
            line-height: 1.75;
        }
        .announce-meta {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 0.83rem;
            color: var(--text-muted);
        }
        .author-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .author-avatar {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, var(--track-green), var(--track-green-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.72rem;
            font-weight: 800;
        }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            animation: fadeInUp 0.6s ease-out;
        }
        .empty-icon {
            width: 90px; height: 90px;
            background: var(--track-green-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        /* ── Count Bar ── */
        .results-bar {
            font-size: 0.88rem;
            color: var(--text-muted);
            font-weight: 600;
            padding: 0 4px;
            margin-bottom: 16px;
        }
        .results-bar strong { color: var(--track-dark); }

        /* ── Footer ── */
        .pub-footer {
            background: var(--track-dark);
            color: rgba(255,255,255,0.6);
            padding: 36px 0;
            text-align: center;
            font-size: 0.88rem;
            margin-top: auto;
        }
        .pub-footer a { color: var(--track-green); text-decoration: none; }
        .pub-footer a:hover { text-decoration: underline; }
        .pub-footer .brand { font-weight: 800; color: white; }
        .pub-footer .brand span { color: var(--track-green); }

        main { flex: 1; }
    </style>
</head>
<body>

<!-- ── NAV ─────────────────────────────────────────────────────────── -->
<nav class="pub-navbar navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Track<span>COOP</span></a>
        <div class="ms-auto">
            <a href="../index.php" class="btn-back-home">
                <i class="bi bi-arrow-left-short fs-5"></i> Back to Home
            </a>
        </div>
    </div>
</nav>

<!-- ── HERO ─────────────────────────────────────────────────────────── -->
<section class="page-hero">
    <div class="container position-relative" style="z-index:2;">
        <div class="col-lg-8">
            <div class="hero-badge">
                <span class="badge-dot"></span> Live Updates
            </div>
            <h1><i class="bi bi-bell-fill me-2" style="color:var(--track-green);"></i>Cooperative <span style="color:var(--track-green);">Announcements</span></h1>
            <p class="mb-0">Stay informed with the latest news, advisories, and official updates from the Nasugbu Farmers and Fisherfolks Agriculture Cooperative (NFFAC).</p>
        </div>
    </div>
</section>

<!-- ── MAIN CONTENT ──────────────────────────────────────────────────── -->
<main>
    <div class="container py-5">
        <div class="row g-4">

            <!-- Left: Main Feed -->
            <div class="col-lg-8">

                <?php if ($pinned && $filter_cat === '' && $search === ''): ?>
                <!-- Pinned / Latest Card -->
                <div class="pinned-card mb-4" data-aos="fade-up">
                    <div class="pinned-badge">
                        <i class="bi bi-pin-fill"></i> Latest Announcement
                    </div>
                    <h2><?php echo htmlspecialchars($pinned['title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars(mb_strimwidth($pinned['content'], 0, 220, '...'))); ?></p>
                    <div class="pinned-meta">
                        <span><i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($pinned['first_name'] . ' ' . $pinned['last_name']); ?></span>
                        <span><i class="bi bi-calendar3 me-1"></i><?php echo date('F j, Y', strtotime($pinned['created_at'])); ?></span>
                        <?php if (!empty($pinned['category'])): ?>
                        <span><i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($pinned['category']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Results bar -->
                <div class="results-bar">
                    Showing <strong><?php echo $total; ?></strong> announcement<?php echo $total !== 1 ? 's' : ''; ?>
                    <?php if ($search): ?> for "<strong><?php echo htmlspecialchars($search); ?></strong>"<?php endif; ?>
                    <?php if ($filter_cat): ?> in <strong><?php echo htmlspecialchars($filter_cat); ?></strong><?php endif; ?>
                </div>

                <!-- Cards Feed -->
                <?php if ($total > 0): ?>
                    <?php $delay = 0; foreach ($announcements as $row): $delay += 60; ?>
                    <div class="announce-card" data-aos="fade-up" data-aos-delay="<?php echo min($delay, 300); ?>">
                        <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                            <span class="category-tag">
                                <i class="bi bi-tag-fill"></i>
                                <?php echo htmlspecialchars($row['category'] ?? 'General'); ?>
                            </span>
                            <span class="text-muted small fw-bold">
                                <i class="bi bi-calendar3 me-1 opacity-60"></i>
                                <?php echo date('M d, Y', strtotime($row['created_at'])); ?>
                            </span>
                        </div>

                        <div class="announce-title"><?php echo htmlspecialchars($row['title']); ?></div>
                        <div class="announce-body"><?php echo nl2br(htmlspecialchars($row['content'])); ?></div>

                        <div class="announce-meta">
                            <div class="author-chip">
                                <div class="author-avatar">
                                    <?php echo strtoupper(substr($row['first_name'], 0, 1)); ?>
                                </div>
                                <span class="fw-bold text-dark"><?php echo htmlspecialchars($row['first_name']); ?></span>
                            </div>
                            <span class="text-muted small">
                                <i class="bi bi-clock-history me-1 opacity-60"></i>
                                <?php echo date('g:i A', strtotime($row['created_at'])); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>

                <?php else: ?>
                <div class="empty-state" data-aos="fade-up">
                    <div class="empty-icon">
                        <i class="bi bi-megaphone" style="font-size:2.5rem;color:var(--track-green);opacity:0.5;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">No Announcements Found</h5>
                    <p class="text-muted">
                        <?php echo $search ? "No results matching \"" . htmlspecialchars($search) . "\"." : "No announcements have been published yet. Check back soon!"; ?>
                    </p>
                    <?php if ($search || $filter_cat): ?>
                    <a href="public_announcements.php" class="btn rounded-pill px-4 fw-bold mt-2" style="background: var(--track-green); color: white;">Clear Filters</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right: Sidebar -->
            <div class="col-lg-4">

                <!-- Search Box -->
                <div class="filter-section mb-4" data-aos="fade-up" data-aos-delay="100">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-search me-2 text-success"></i>Search Announcements</h6>
                    <form method="GET" action="public_announcements.php">
                        <div class="search-input-group mb-3">
                            <i class="bi bi-search fs-6"></i>
                            <input type="text" name="search" class="form-control py-2"
                                placeholder="Search title or content..."
                                value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <?php if ($filter_cat): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($filter_cat); ?>">
                        <?php endif; ?>
                        <button type="submit" class="btn w-100 fw-bold rounded-pill" style="background:var(--track-green);color:white;">
                            <i class="bi bi-search me-2"></i>Search
                        </button>
                    </form>
                </div>

                <!-- Category Filter -->
                <?php if (!empty($category_counts)): ?>
                <div class="filter-section" data-aos="fade-up" data-aos-delay="150">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-funnel me-2 text-success"></i>Filter by Category</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="public_announcements.php<?php echo $search ? '?search='.urlencode($search) : ''; ?>"
                           class="filter-pill <?php echo $filter_cat === '' ? 'active' : ''; ?>">
                            All <span class="count-badge"><?php echo array_sum($category_counts); ?></span>
                        </a>
                        <?php foreach ($category_counts as $cat => $cnt): ?>
                        <a href="public_announcements.php?category=<?php echo urlencode($cat); ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>"
                           class="filter-pill <?php echo $filter_cat === $cat ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($cat); ?>
                            <span class="count-badge"><?php echo $cnt; ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<!-- ── FOOTER ────────────────────────────────────────────────────────── -->
<footer class="pub-footer">
    <div class="container">
        <div class="brand mb-1">Track<span>COOP</span></div>
        <p class="mb-1">Official platform of the Nasugbu Farmers and Fisherfolks Agriculture Cooperative (NFFAC).</p>
        <p class="mb-0">&copy; <?php echo date('Y'); ?> TrackCOOP. All rights reserved. &nbsp;&bull;&nbsp; <a href="../index.php">Home</a></p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 600, easing: 'ease-out-cubic' });
</script>
</body>
</html>
