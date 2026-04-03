<?php
session_start();
include '../auth/db_connect.php';

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Guest';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Static media activities data
$static_media = [
    ['id' => 1, 'title' => 'Community Planting Day', 'description' => 'Members gathering for planting crops', 'category' => 'Events', 'activity_date' => '2024-03-20', 'first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'file_path' => 'uploads/event1.jpg'],
    ['id' => 2, 'title' => 'Harvest Festival 2024', 'description' => 'Annual harvest celebration', 'category' => 'Activities', 'activity_date' => '2024-03-15', 'first_name' => 'Maria', 'last_name' => 'Santos', 'file_path' => 'uploads/harvest1.jpg'],
    ['id' => 3, 'title' => 'Training Workshop', 'description' => 'Agricultural innovation workshop', 'category' => 'Training', 'activity_date' => '2024-03-10', 'first_name' => 'Pedro', 'last_name' => 'Garcia', 'file_path' => 'uploads/training1.jpg'],
];

$category_filter = isset($_GET['category']) ? $_GET['category'] : 'All';
$media_activities = $static_media;
if ($category_filter !== 'All') {
    $media_activities = array_filter($media_activities, function($m) use ($category_filter) {
        return $m['category'] === $category_filter;
    });
}

// Messages from actions
$msg = "";
if (isset($_GET['upload']) && $_GET['upload'] == 'success') $msg = "Activity photo uploaded successfully!";
if (isset($_GET['delete']) && $_GET['delete'] == 'success') $msg = "Media entry deleted successfully!";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Gallery | TrackCOOP</title>
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

        .gallery-header { 
            padding: 80px 0 60px; 
            background: linear-gradient(135deg, #f1f8f5 0%, #ffffff 100%);
            border-bottom: 1px solid rgba(32, 160, 96, 0.1);
        }

        .filter-btn {
            padding: 10px 24px; border-radius: 50px; border: 1px solid rgba(32, 160, 96, 0.2);
            background: white; color: var(--track-dark); font-weight: 600; font-size: 0.85rem;
            transition: all 0.3s ease; text-decoration: none; display: inline-block; margin: 0 5px 10px;
        }
        .filter-btn:hover, .filter-btn.active { 
            background: var(--track-green); color: white; border-color: var(--track-green);
            box-shadow: 0 8px 15px rgba(32, 160, 96, 0.2);
        }

        .media-card {
            background: white; border-radius: 28px; border: none;
            overflow: hidden; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(0,0,0,0.04); height: 100%;
            position: relative;
        }
        .media-card:hover { transform: translateY(-10px); box-shadow: 0 20px 50px rgba(0,0,0,0.08); }
        
        .media-img-container { width: 100%; height: 240px; overflow: hidden; position: relative; }
        .media-img-container img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s ease; }
        .media-card:hover img { transform: scale(1.1); }

        .category-badge {
            position: absolute; top: 15px; left: 15px;
            background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(8px);
            padding: 5px 15px; border-radius: 50px; font-size: 0.75rem;
            font-weight: 800; color: var(--track-green); border: 1px solid rgba(32, 160, 96, 0.2);
        }

        .media-body { padding: 25px; }
        .media-title { font-weight: 800; font-size: 1.1rem; color: var(--track-dark); margin-bottom: 8px; letter-spacing: -0.5px; }
        .media-desc { font-size: 0.85rem; color: #64748b; line-height: 1.6; margin-bottom: 15px; }
        
        .media-footer { border-top: 1px solid #f1f5f9; padding-top: 15px; display: flex; justify-content: space-between; align-items: center; }
        .uploader-info { display: flex; align-items: center; font-size: 0.75rem; color: #94a3b8; }
        .uploader-avatar { width: 24px; height: 24px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 8px; font-weight: bold; color: var(--track-green); }

        .btn-delete { color: #ef4444; border: none; background: #fee2e2; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
        .btn-delete:hover { background: #ef4444; color: white; }

        .empty-state { padding: 100px 0; text-align: center; color: #94a3b8; }
        .empty-state i { font-size: 4rem; margin-bottom: 20px; display: block; opacity: 0.3; }
        
        .btn-upload {
            background: linear-gradient(135deg, var(--track-green), #1a8548);
            border: none; border-radius: 50px; padding: 12px 30px;
            color: white; font-weight: 700; box-shadow: 0 10px 20px rgba(32, 160, 96, 0.2);
            transition: all 0.3s ease;
        }
        .btn-upload:hover { background: linear-gradient(135deg, #20a060, #1a8548); transform: translateY(-2px); box-shadow: 0 15px 25px rgba(32, 160, 96, 0.3); color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Track<span>COOP</span></a>
        <div class="ms-auto">
            <a href="../index.php" class="btn btn-outline-success btn-sm rounded-pill px-4 fw-bold">Back to Home</a>
        </div>
    </div>
</nav>

<div class="gallery-header">
    <div class="container text-center">
        <h1 class="fw-800 display-4 mb-3" style="letter-spacing: -2px; color: var(--track-dark);">Cooperative Activities</h1>
        <p class="text-muted fs-5 mb-5 mx-auto" style="max-width: 600px;">Experience the growth and milestones of Nasugbu's farmers and fisherfolks through our digital gallery.</p>
        
        <div class="d-flex flex-wrap justify-content-center mt-4">
            <a href="?category=All" class="filter-btn <?php echo $category_filter === 'All' ? 'active' : ''; ?>">All Activities</a>
            <a href="?category=Training" class="filter-btn <?php echo $category_filter === 'Training' ? 'active' : ''; ?>">Trainings</a>
            <a href="?category=Harvesting" class="filter-btn <?php echo $category_filter === 'Harvesting' ? 'active' : ''; ?>">Harvesting</a>
            <a href="?category=Meeting" class="filter-btn <?php echo $category_filter === 'Meeting' ? 'active' : ''; ?>">Meetings</a>
            <a href="?category=Livelihood" class="filter-btn <?php echo $category_filter === 'Livelihood' ? 'active' : ''; ?>">Livelihood</a>
        </div>
    </div>
</div>

<div class="container py-5">
    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="media-card">
                        <div class="media-img-container">
                            <img src="../<?php echo htmlspecialchars($row['file_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                            <div class="category-badge"><?php echo htmlspecialchars($row['category']); ?></div>
                        </div>
                        <div class="media-body">
                            <h5 class="media-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                            <p class="media-desc small"><?php echo htmlspecialchars($row['description']); ?></p>
                            
                            <div class="media-footer">
                                <div class="uploader-info">
                                    <div class="uploader-avatar"><?php echo substr($row['first_name'], 0, 1); ?></div>
                                    <span><?php echo date('M d, Y', strtotime($row['activity_date'])); ?></span>
                                </div>
                                <?php if ($user_role === 'Admin'): ?>
                                    <a href="media_actions.php?delete_id=<?php echo $row['id']; ?>" class="btn-delete" 
                                       onclick="return TrackUI.confirmLink(event, 'Permanently delete this activity photo?', 'Delete Media', 'danger', 'Delete Now', 'Keep It')">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i class="bi bi-images"></i>
            <h3>No media found</h3>
            <p>We haven't shared any activities in this category yet. Check back soon!</p>
        </div>
    <?php endif; ?>
</div>

<?php 
// Show modals for messages
if ($msg != "") {
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        TrackUI.show('$msg', 'System Success', 'primary', 'Okay');
    });
    </script>";
}
?>

<?php include '../includes/footer.php'; ?>
</body>
</html>
