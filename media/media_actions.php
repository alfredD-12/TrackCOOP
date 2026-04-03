<?php
session_start();
include '../auth/db_connect.php';

// Security: Only Admin can manage media
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../index.php?error=unauthorized");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_media'])) {
    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $category      = mysqli_real_escape_string($conn, $_POST['category']);
    $activity_date = mysqli_real_escape_string($conn, $_POST['activity_date']);
    $uploaded_by   = $_SESSION['user_id'];

    // File Upload handling
    $target_dir = "../uploads/media/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . '_' . basename($_FILES["media_file"]["name"]);
    $target_file = $target_dir . $file_name;
    $db_path = "uploads/media/" . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'media.php';
    // Remove existing query params if any
    $redirect_url = strtok($redirect_url, '?');

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["media_file"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        header("Location: " . $redirect_url . "?upload=not_image");
        exit();
    }

    // Check file size (limit to 5MB)
    if ($_FILES["media_file"]["size"] > 5000000) {
        header("Location: " . $redirect_url . "?upload=large_file");
        exit();
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        header("Location: " . $redirect_url . "?upload=invalid_format");
        exit();
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["media_file"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO media_activities (title, description, file_path, category, uploaded_by, activity_date) 
                    VALUES ('$title', '$description', '$db_path', '$category', '$uploaded_by', '$activity_date')";
            
            if (mysqli_query($conn, $sql)) {
                header("Location: " . $redirect_url . "?upload=success");
            } else {
                header("Location: " . $redirect_url . "?upload=db_error");
            }
        } else {
            header("Location: " . $redirect_url . "?upload=error");
        }
    }
}

// Action for deleting media
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'media.php';
    $redirect_url = strtok($redirect_url, '?');
    
    // First get the file path to delete the physical file
    $res = mysqli_query($conn, "SELECT file_path FROM media_activities WHERE id = $id");
    if ($row = mysqli_fetch_assoc($res)) {
        $full_path = "../" . $row['file_path'];
        if (file_exists($full_path)) {
            unlink($full_path);
        }
        
        mysqli_query($conn, "DELETE FROM media_activities WHERE id = $id");
        header("Location: " . $redirect_url . "?delete=success");
    }
}

// Action for editing media details
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_media'])) {
    $id            = intval($_POST['edit_media']);
    $title         = mysqli_real_escape_string($conn, $_POST['title']);
    $description   = mysqli_real_escape_string($conn, $_POST['description']);
    $category      = mysqli_real_escape_string($conn, $_POST['category']);
    $activity_date = mysqli_real_escape_string($conn, $_POST['activity_date']);
    
    $redirect_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'media.php';
    $redirect_url = strtok($redirect_url, '?');

    $sql = "UPDATE media_activities SET 
            title = '$title', 
            description = '$description', 
            category = '$category', 
            activity_date = '$activity_date' 
            WHERE id = $id";
            
    if (mysqli_query($conn, $sql)) {
        header("Location: " . $redirect_url . "?updated=success");
    } else {
        header("Location: " . $redirect_url . "?updated=error");
    }
}
?>
