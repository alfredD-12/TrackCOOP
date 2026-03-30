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

    // Check if username is taken by someone else
    $check_query = "SELECT id FROM users WHERE username = ? AND id != ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("si", $username, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        header("Location: profile.php?update=error&msg=usertaken");
        exit();
    }

    // Update query construction
    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, username = ?, sector = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $first_name, $middle_name, $last_name, $username, $sector, $hashed_password, $user_id);
    } else {
        $query = "UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, username = ?, sector = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $first_name, $middle_name, $last_name, $username, $sector, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: profile.php?update=success");
    } else {
        header("Location: profile.php?update=error");
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: profile.php");
    exit();
}
?>
