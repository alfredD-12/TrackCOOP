<?php
session_start();
include '../auth/db_connect.php';

// Security: Only Admin can manage media
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Fetch user name for navbar
$q = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$q->bind_param("i", $user_id);
$q->execute();
$full_name = "User";
if ($u = $q->get_result()->fetch_assoc()) {
    $full_name = $u['first_name'] . " " . $u['last_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Activity | TrackCOOP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../includes/footer.css">
    
    <style>
        :root { --track-green: #206970; --track-dark: #1e272e; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        
        .navbar { background: white; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 15px 0; }
        .navbar-brand { font-weight: 800; color: var(--track-dark); }
        .navbar-brand span { color: var(--track-green); }
        
        .page-header { background: white; padding: 40px 0; border-bottom: 1px solid rgba(0,0,0,0.05); margin-bottom: 40px; }
        
        .upload-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(32, 160, 96, 0.1);
            border-radius: 24px; padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.03);
        }

        .form-label { font-weight: 700; color: var(--track-dark); font-size: 0.9rem; margin-bottom: 10px; }
        .form-control, .form-select {
            padding: 12px 18px; border-radius: 14px; border: 1px solid #e2e8f0;
            font-size: 0.95rem; transition: all 0.3s ease;
        }
        .form-control:focus { border-color: var(--track-green); box-shadow: 0 0 0 4px rgba(32,160,96,0.1); }

        .image-preview-box {
            width: 100%; height: 250px; background: #f1f5f9;
            border: 2px dashed #cbd5e1; border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; color: #64748b; margin-top: 15px;
            overflow: hidden; position: relative;
        }
        .image-preview-box img { width: 100%; height: 100%; object-fit: cover; }
        
        .btn-track {
            background: #20a060;
            border: none; border-radius: 14px; padding: 12px 25px;
            color: white; font-weight: 700; box-shadow: 0 4px 14px rgba(32, 160, 96, 0.3);
            transition: all 0.3s ease;
        }
        .btn-track:hover { transform: translateY(-2px); background: #1a8548; box-shadow: 0 8px 20px rgba(32, 160, 96, 0.4); color: white; }

        .logout-btn { color: #ef4444; background: #fee2e2; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; transition: all 0.3s; text-decoration: none; }
        .logout-btn:hover { background: #ef4444; color: white; transform: rotate(90deg); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Track<span>COOP</span></a>
        <div class="ms-auto d-flex align-items-center">
            <div class="text-end me-3 d-none d-lg-block">
                <div class="small fw-bold lh-1"><?php echo htmlspecialchars($full_name); ?></div>
                <small class="text-muted"><?php echo htmlspecialchars($user_role); ?></small>
            </div>
            <a href="../auth/logout.php" class="logout-btn" title="Logout" 
               onclick="return TrackUI.confirmLink(event, 'Log out from the system?', 'Confirm Logout', 'logout')">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</nav>

<div class="page-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-800 display-6 mb-2">Upload Activity Photo</h1>
                <p class="text-muted mb-0">Share cooperative milestones and field activities with a professional touch.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="media.php" class="btn btn-outline-secondary rounded-pill px-4 fw-bold border-2">
                    <i class="bi bi-arrow-left me-2"></i> Back to Gallery
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="upload-card">
                <form action="media_actions.php" method="POST" enctype="multipart/form-data" id="uploadForm" onsubmit="return TrackUI.confirmForm(event, 'Publish this activity photo to the public gallery?', 'Upload Media', 'primary', 'Publish Now', 'Review')">
                    <input type="hidden" name="upload_media" value="1">
                    
                    <div class="mb-4">
                        <label class="form-label">Activity Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Rice Farming Workshop" required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" required>
                                <option value="Training">Training</option>
                                <option value="Harvesting">Harvesting</option>
                                <option value="Meeting">Coop Meeting</option>
                                <option value="Livelihood">Livelihood</option>
                                <option value="Other">Other Activities</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Activity Date</label>
                            <input type="date" name="activity_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Short Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Briefly describe what happened..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Select Photo</label>
                        <input type="file" name="media_file" id="media_file" class="form-control" accept="image/*" required onchange="previewImage(event)">
                        <div class="image-preview-box" id="previewArea">
                            <i class="bi bi-image fs-1 opacity-25"></i>
                            <span class="small mt-2">No Image Selected</span>
                        </div>
                    </div>

                    <div class="d-grid pt-3">
                        <button type="submit" class="btn btn-track py-3 fw-bold">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i> Upload Activity Photo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const previewArea = document.getElementById('previewArea');
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewArea.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
        }
        reader.readAsDataURL(file);
    }
}
</script>


</body>
</html>
