<?php
// auth/register.php
include "db_connect.php"; // Since they are in the 'auth' folder together

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname  = mysqli_real_escape_string($conn, $_POST['fname']);
    $mname  = mysqli_real_escape_string($conn, $_POST['mname']);
    $lname  = mysqli_real_escape_string($conn, $_POST['lname']);
    $sector = mysqli_real_escape_string($conn, $_POST['sector']);
    $user   = mysqli_real_escape_string($conn, $_POST['username']);
    $password_raw = $_POST['password'];
    $confirm_raw  = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : "";

    // ── Server-Side Password Confirmation (Match Check) ──
    if ($password_raw !== $confirm_raw) {
        header("Location: ../index.php?register=password_mismatch&username=" . urlencode($user));
        exit();
    }

    // ── Server-Side Password Complexity Enforcement (8-15 chars, L, N, S) ──
    $pattern = '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,15}$/';
    if (!preg_match($pattern, $password_raw)) {
        header("Location: ../index.php?register=weak_password&username=" . urlencode($user));
        exit();
    }

    $pass   = password_hash($password_raw, PASSWORD_DEFAULT);

    // Default values for new members
    $role = 'Member';
    $status = 'Pending';

    $sql = "INSERT INTO users (first_name, middle_name, last_name, username, password, sector, role, status) 
        VALUES ('$fname', '$mname', '$lname', '$user', '$pass', '$sector', 'Member', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../index.php?register=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>