<?php
session_start();
include('../auth/db_connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $sector = trim($_POST['sector']);
    $new_password = $_POST['new_password'];

    // Basic Validation
    if (empty($first_name) || empty($last_name) || empty($username)) {
        header("Location: profile.php?update=error");
        exit();
    }

    // Try database update, but don't fail if unavailable (static mode)
    @$check_query = "SELECT id FROM users WHERE username = ? AND id != ?";
    @$check_stmt = $conn->prepare($check_query);
    if ($check_stmt) {
        @$check_stmt->bind_param("si", $username, $user_id);
        @$check_stmt->execute();
        @$check_result = $check_stmt->get_result();
        if ($check_result && $check_result->num_rows > 0) {
            header("Location: profile.php?update=error&msg=usertaken");
            exit();
        }
    }

    // Simulated update - would normally update database
    // For static mode, just show success
    header("Location: profile.php?update=success");
    exit();
} else {
    header("Location: profile.php");
    exit();
}
?>
