<?php
// auth/register.php
include "db_connect.php"; // Since they are in the 'auth' folder together

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fname  = mysqli_real_escape_string($conn, $_POST['fname']);
    $mname  = mysqli_real_escape_string($conn, $_POST['mname']);
    $lname  = mysqli_real_escape_string($conn, $_POST['lname']);
    $sector = mysqli_real_escape_string($conn, $_POST['sector']);
    $user   = mysqli_real_escape_string($conn, $_POST['username']);
    $pass   = password_hash($_POST['password'], PASSWORD_DEFAULT);

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