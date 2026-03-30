<?php
session_start();
include "db_connect.php"; // Ensure path is correct relative to login.php

if (isset($_POST['username']) && isset($_POST['password'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Retrieve details including role and status
    $stmt = $conn->prepare("SELECT id, first_name, password, role, status FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($pass, $row['password'])) {
            
            // Check if status is Approved for new members
            if ($row['status'] === 'Approved') {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['fname'] = $row['first_name'];

                // Role-Based Redirection based on Folder Structure
                if ($row['role'] === 'Admin') {
                    header("Location: ../admin/admin_dashboard.php");
                } elseif ($row['role'] === 'Bookkeeper') {
                    header("Location: ../bookkeeper/bookkeeper_dashboard.php");
                } elseif ($row['role'] === 'Member') {
                    header("Location: ../member/member_dashboard.php");
                } else {
                    header("Location: ../index.php?login=invalid_role");
                }
                exit(); // Important to stop the script after the header redirect
            } else {
                // Handle pending applications
                header("Location: ../index.php?login=pending");
            }
        } else {
            header("Location: ../index.php?login=wrong_password&username=" . urlencode($user));
        }
    } else {
        header("Location: ../index.php?login=not_found");
    }
}
?>