<?php
include '../auth/db_connect.php';

// Existing image paths from list_dir
$images = [
    "uploads/media/1774585217_Screenshot 2026-03-26 130120.png",
    "uploads/media/1774585412_Screenshot 2026-03-25 230755.png",
    "uploads/media/1774585605_Screenshot 2026-03-26 002307.png",
    "uploads/media/1774585951_Screenshot 2026-03-26 001356.png",
    "uploads/media/1774679735_Screenshot 2026-03-28 133022.png"
];

$activities = [
    ["Rice Harvest Festival 2024", "Annual community celebration for bountiful rice harvest and support.", "Harvesting"],
    ["Organic Fertilizer Workshop", "Training session for sustainable farming methods for community members.", "Training"],
    ["Sector Cooperatives Meeting", "Discussing resource allocation and irrigation projects for the coming moon.", "Meeting"],
    ["Community Livelihood Program", "Empowering local artisans and farmers through financial literacy.", "Community"],
    ["New Irrigation System Site Visit", "Checking the progress of the subsidized water management system.", "Other"],
    ["Corn Sustainability Seminar", "Enhancing the yield of corn crops via modern fertilization techniques.", "Training"],
    ["Seed Distribution Drive", "Distributing high-yield rice seeds to secondary sector members.", "Community"],
    ["Quarterly Financial Audit", "Verification of cooperative funds and project transparency.", "Meeting"],
    ["Calamity Fund Allocation", "Planning emergency response measures for members affected by the storm.", "Other"],
    ["Farm-to-Market Road Planning", "Coordinating with local government for better logistics.", "Meeting"]
];

echo "<h3>Seeding Gallery Data...</h3>";
$count = 0;

for ($i = 1; $i <= 20; $i++) {
    $act = $activities[array_rand($activities)];
    $img = $images[array_rand($images)];
    $title = $act[0] . " Part " . (($i % 3) + 1);
    $desc = $act[1];
    $cat = $act[2];
    $date = date('Y-m-d', strtotime("-" . rand(1, 120) . " days"));
    $user_id = 41; // Assuming Admin ID

    $sql = "INSERT INTO media_activities (title, description, file_path, category, uploaded_by, activity_date) 
            VALUES ('$title', '$desc', '$img', '$cat', $user_id, '$date')";
    
    if ($conn->query($sql)) {
        $count++;
    } else {
        echo "Error: " . $conn->error . "<br>";
    }
}

echo "Successfully added $count example records to the gallery database.<br>";
echo "<a href='gallery.php'>Back to Gallery</a>";
?>
