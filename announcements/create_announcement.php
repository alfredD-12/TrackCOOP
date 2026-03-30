<?php
session_start();
include('../auth/db_connect.php');

// Security check: Admin and Bookkeeper only
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Admin', 'Bookkeeper'])) {
    header("Location: announcements.php?error=unauthorized");
    exit();
}

$user_id   = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$full_name = "User";

$q = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$q->bind_param("i", $user_id);
$q->execute();
if ($u = $q->get_result()->fetch_assoc()) {
    $full_name = $u['first_name'] . " " . $u['last_name'];
}

// ── Handle Form Submission ───────────────────────────────────────────────────
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_announcement'])) {
    $title           = trim($_POST['title']);
    $category        = trim($_POST['category']);
    $content         = trim($_POST['content']);
    $target_audience = trim($_POST['target_audience']);
    $status          = "Published";

    if (empty($title) || empty($content)) {
        $error = "Title and content are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO announcements (title, category, content, author_id, target_audience, status) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $title, $category, $content, $user_id, $target_audience, $status);
        
        if ($stmt->execute()) {
            header("Location: announcements.php?msg=created");
            exit();
        } else {
            $error = "Failed to save announcement. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Announcement | TrackCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --track-green: #20a060;
            --track-green-light: #e9f5ee;
            --track-dark: #1a1a1a;
            --track-bg: #f8fafc;
            --track-beige: #F5F5DC;
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --text-main: #212529;
            --text-muted: #555555;
        }

        body {
            background-color: var(--track-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            min-height: 100vh; display: flex; flex-direction: column;
        }

        /* ── Navbar ── */
        .navbar {
            background-color: rgba(245,245,220,0.95) !important;
            backdrop-filter: blur(10px); padding: 15px 0;
            border-bottom: 1px solid rgba(229,229,192,0.5);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .navbar-brand { 
            font-weight: 800; 
            font-size: 1.5rem; 
            letter-spacing: -1px; 
            color: var(--track-dark) !important; 
            text-decoration: none;
        }
        .navbar-brand span { color: var(--track-green); }

        /* ── Page Header ── */
        .page-header {
            background: linear-gradient(135deg, var(--track-bg) 0%, var(--track-beige) 100%);
            padding: 50px 0 36px; border-bottom: 1px solid rgba(229,229,192,0.4); margin-bottom: 40px;
        }

        /* ── Form Card ── */
        .form-card {
            background: white; border-radius: 20px; border: 1px solid rgba(226,232,240,0.8);
            padding: 36px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            border-top: 5px solid var(--track-green);
        }
        .form-label { font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
        .form-control, .form-select {
            border-radius: 12px; padding: 14px 18px; border: 1.5px solid #e2e8f0;
            background-color: #f8fafc; transition: 0.3s; font-size: 0.95rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--track-green); box-shadow: 0 0 0 4px rgba(32,160,96,0.12); background-color: #fff;
        }

        .btn-track { background: var(--track-green); color: white; border-radius: 12px; padding: 14px 28px; font-weight: 700; border: none; transition: var(--transition-smooth); }
        .btn-track:hover { background: #1a8548; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(32,160,96,0.3); color: white; }
        
        .btn-cancel { background: #f8fafc; color: var(--text-muted); border-radius: 12px; padding: 14px 28px; font-weight: 600; text-decoration: none; border: 1px solid #e2e8f0; transition: 0.3s; }
        .btn-cancel:hover { background: #fee2e2; color: #ef4444; border-color: #fee2e2; }

        .btn-back { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 12px; border: 1.5px solid #e5e5c0; background: white; color: var(--text-muted); text-decoration: none; transition: 0.3s; margin-right: 16px; }
        .btn-back:hover { border-color: var(--track-green); color: var(--track-green); background: var(--track-green-light); }
    </style>
    <link rel="stylesheet" href="../includes/footer.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="../admin/admin_dashboard.php">Track<span>COOP</span></a>
    </div>
</nav>

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="container">
        <div class="d-flex align-items-center mb-3">
            <a href="announcements.php" class="btn-back"><i class="bi bi-arrow-left fs-5"></i></a>
            <div class="badge bg-white text-success border-success border rounded-pill px-3 py-1 fw-bold text-uppercase small" style="font-size:0.7rem;">New Post</div>
        </div>
        <h1 class="fw-800 display-5 mb-1" style="letter-spacing:-1.5px;">Create Announcement</h1>
        <p class="fs-6 mb-0 text-muted">Compose a new announcement for the whole cooperative.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                <?php if ($error): ?>
                    <div class="alert alert-danger rounded-4 fw-bold mb-4"><i class="bi bi-exclamation-octagon-fill me-2"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row g-4">
                        <div class="col-md-8">
                            <label class="form-label">Announcement Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Annual General Assembly Meeting 2024" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="General">General</option>
                                <option value="Event">Event</option>
                                <option value="Meeting">Meeting</option>
                                <option value="Deadline">Deadline</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Target Audience</label>
                            <select name="target_audience" class="form-select">
                                <option value="All">All Users</option>
                                <option value="Members">Members Only</option>
                                <option value="Sectors">Specific Sector (WIP)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Content / Message <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control" rows="8" placeholder="Enter your announcement details here..." required></textarea>
                            <small class="text-muted mt-2 d-block">This will be visible to all assigned members on their dashboards.</small>
                        </div>
                        <div class="col-12 text-end mt-5">
                            <a href="announcements.php" class="btn-cancel me-3">Cancel</a>
                            <button type="submit" name="save_announcement" class="btn-track"><i class="bi bi-check-circle me-2"></i> Publish Announcement</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
