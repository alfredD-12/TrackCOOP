<?php
session_start();
include '../auth/db_connect.php';

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Guest';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Static media activities data (Simulating DB results for demonstration)
$media_activities = [
    ['id' => 1, 'title' => 'Community Planting Day', 'description' => 'Members gathering for planting crops', 'category' => 'Events', 'activity_date' => '2024-03-20', 'first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'file_path' => 'event.png'],
    ['id' => 2, 'title' => 'Harvest Festival 2024', 'description' => 'Annual harvest celebration', 'category' => 'Harvesting', 'activity_date' => '2024-03-15', 'first_name' => 'Maria', 'last_name' => 'Santos', 'file_path' => 'agriculture.webp'],
    ['id' => 3, 'title' => 'Training Workshop', 'description' => 'Agricultural innovation workshop', 'category' => 'Training', 'activity_date' => '2024-03-10', 'first_name' => 'Pedro', 'last_name' => 'Garcia', 'file_path' => 'sector.jpg'],
    ['id' => 4, 'title' => 'General Assembly', 'description' => 'Cooperative year-end meeting.', 'category' => 'Meeting', 'activity_date' => '2024-03-05', 'first_name' => 'Lito', 'last_name' => 'Cruz', 'file_path' => 'meeting.jpg'],
    ['id' => 5, 'title' => 'Rice Field Inspection', 'description' => 'Monthly progress report.', 'category' => 'Livelihood', 'activity_date' => '2024-03-01', 'first_name' => 'Ana', 'last_name' => 'Reyes', 'file_path' => 'agriculture.webp'],
    ['id' => 6, 'title' => 'Main Office Hub', 'description' => 'Central service center.', 'category' => 'Events', 'activity_date' => '2024-02-28', 'first_name' => 'Admin', 'last_name' => 'Account', 'file_path' => 'Home.jpeg'],
];

$category_filter = isset($_GET['category']) ? $_GET['category'] : 'All';

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
    <link rel="stylesheet" href="../includes/dashboard_layout.css">
    
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
        <div class="ms-auto d-flex align-items-center gap-3">
             <div class="search-wrapper position-relative" style="width: 250px;">
                <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left: 15px;"></i>
                <input type="text" id="gallerySearch" class="form-control border-0 shadow-sm" placeholder="Search gallery..." style="padding-left: 40px; border-radius: 50px; background: #f1f5f9;">
            </div>
            <a href="../index.php" class="btn btn-outline-success btn-sm rounded-pill px-4 fw-bold">Back to Home</a>
        </div>
    </div>
</nav>

<div class="gallery-header">
    <div class="container text-center">
        <h1 class="fw-800 display-4 mb-3" style="letter-spacing: -2px; color: var(--track-dark);">Cooperative Activities</h1>
        <p class="text-muted fs-5 mb-5 mx-auto" style="max-width: 600px;">Experience the growth and milestones of Nasugbu's farmers and fisherfolks through our digital gallery.</p>
        
        <div class="d-flex flex-wrap justify-content-center mt-4">
            <button class="filter-btn active" data-filter="All">All Activities</button>
            <button class="filter-btn" data-filter="Training">Trainings</button>
            <button class="filter-btn" data-filter="Harvesting">Harvesting</button>
            <button class="filter-btn" data-filter="Meeting">Meetings</button>
            <button class="filter-btn" data-filter="Livelihood">Livelihood</button>
            <button class="filter-btn" data-filter="Events">Events</button>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4" id="galleryGrid">
        <?php foreach ($media_activities as $row): ?>
            <div class="col-lg-4 col-md-6 media-item" data-category="<?php echo htmlspecialchars($row['category']); ?>" data-search-matched="true">
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
                                <div class="uploader-avatar"><?php echo strtoupper(substr($row['first_name'], 0, 1)); ?></div>
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
        <?php endforeach; ?>
    </div>

    <div id="noResults" class="empty-state" style="display: none;">
        <i class="bi bi-images"></i>
        <h3>No media found</h3>
        <p>We couldn't find any activities matching your criteria.</p>
    </div>

    <!-- ELITE PAGINATION clustered to right -->
    <div class="pagination-elite mt-5">
        <span class="pagination-elite-label">ROWS PER PAGE</span>
        <select id="rowsPerPage" class="pagination-elite-select">
            <option value="3">3</option>
            <option value="6" selected>6</option>
            <option value="12">12</option>
        </select>
        <span id="paginationInfo" class="pagination-elite-info">1–6 of 6</span>
        <div class="pagination-elite-buttons">
            <button id="prevPage" class="pagination-elite-btn" title="Previous Page"><i class="bi bi-chevron-left"></i></button>
            <button id="nextPage" class="pagination-elite-btn" title="Next Page"><i class="bi bi-chevron-right"></i></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let currentPage = 1;
    let rowsPerPage = parseInt(document.getElementById('rowsPerPage').value);
    let activeFilter = 'All';

    function updatePagination() {
        const searchTerm = document.getElementById('gallerySearch').value.toLowerCase().trim();
        const items = Array.from(document.querySelectorAll('.media-item'));
        
        let visibleCount = 0;
        items.forEach(item => {
            const title = item.querySelector('.media-title').innerText.toLowerCase();
            const desc = item.querySelector('.media-desc').innerText.toLowerCase();
            const cat = item.dataset.category;
            
            const matchesSearch = title.includes(searchTerm) || desc.includes(searchTerm);
            const matchesFilter = activeFilter === 'All' || cat === activeFilter;
            
            if (matchesSearch && matchesFilter) {
                item.setAttribute('data-search-matched', 'true');
                visibleCount++;
            } else {
                item.setAttribute('data-search-matched', 'false');
                item.style.display = 'none';
            }
        });

        const matchedItems = items.filter(i => i.getAttribute('data-search-matched') === 'true');
        const totalPages = Math.ceil(matchedItems.length / rowsPerPage);
        
        if (currentPage > totalPages) currentPage = totalPages || 1;
        if (currentPage < 1) currentPage = 1;

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        matchedItems.forEach((item, index) => {
            if (index >= start && index < end) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });

        // Toggle Empty State
        document.getElementById('noResults').style.display = matchedItems.length === 0 ? 'block' : 'none';
        document.getElementById('galleryGrid').style.display = matchedItems.length === 0 ? 'none' : 'flex';

        // Update Info
        const infoStart = matchedItems.length === 0 ? 0 : start + 1;
        const infoEnd = Math.min(end, matchedItems.length);
        document.getElementById('paginationInfo').textContent = `${infoStart}–${infoEnd} of ${matchedItems.length}`;
        
        // Buttons
        document.getElementById('prevPage').disabled = (currentPage === 1);
        document.getElementById('nextPage').disabled = (currentPage === totalPages || totalPages === 0);
    }

    // Listeners
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            activeFilter = this.dataset.filter;
            currentPage = 1;
            updatePagination();
        });
    });

    document.getElementById('gallerySearch').addEventListener('input', () => {
        currentPage = 1;
        updatePagination();
    });

    document.getElementById('rowsPerPage').addEventListener('change', function() {
        rowsPerPage = parseInt(this.value);
        currentPage = 1;
        updatePagination();
    });

    document.getElementById('prevPage').addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            updatePagination();
        }
    });

    document.getElementById('nextPage').addEventListener('click', () => {
        const matchedCount = document.querySelectorAll('.media-item[data-search-matched="true"]').length;
        if (currentPage < Math.ceil(matchedCount / rowsPerPage)) {
            currentPage++;
            updatePagination();
        }
    });

    // Initial load
    document.addEventListener('DOMContentLoaded', updatePagination);
</script>

<?php 
if (isset($msg) && $msg != "") {
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof TrackUI !== 'undefined') {
            TrackUI.show('$msg', 'System Success', 'primary', 'Okay');
        } else {
            alert('$msg');
        }
    });
    </script>";
}
?>
</body>
</html>
