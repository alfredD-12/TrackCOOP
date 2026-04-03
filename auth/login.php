<?php
session_start();

// Static Login Credentials for Testing
$static_users = [
    'admin' => ['password' => '123456789', 'role' => 'Admin', 'first_name' => 'Administrator'],
    'bookkeeper' => ['password' => '123456789', 'role' => 'Bookkeeper', 'first_name' => 'Bookkeeper'],
    'member' => ['password' => '123456789', 'role' => 'Member', 'first_name' => 'Member']
];

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['selected_role'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $selected_role = $_POST['selected_role'];

    // ── Cookie Handling (Remember Me) ──
    if (isset($_POST['remember-me'])) {
        setcookie('remember_user', $user, time() + (30 * 24 * 60 * 60), "/"); // 30 Days
    } else {
        if (isset($_COOKIE['remember_user'])) {
            setcookie('remember_user', '', time() - 3600, "/"); // Expired
        }
    }

    // Validate against static users
    if (isset($static_users[$user])) {
        $user_data = $static_users[$user];
        
        // Verify password and role matches
        if ($pass === $user_data['password'] && $user_data['role'] === $selected_role) {
            $_SESSION['user_id'] = $user; // Use username as ID for static auth
            $_SESSION['role'] = $user_data['role'];
            $_SESSION['fname'] = $user_data['first_name'];

            // Role-Based Redirection
            if ($user_data['role'] === 'Admin') {
                header("Location: ../admin/admin_dashboard.php");
            } elseif ($user_data['role'] === 'Bookkeeper') {
                header("Location: ../bookkeeper/bookkeeper_dashboard.php");
            } elseif ($user_data['role'] === 'Member') {
                header("Location: ../member/member_dashboard.php");
            } else {
                header("Location: ../index.php?login=invalid_role");
            }
            exit();
        } else {
            header("Location: ../index.php?login=wrong_password&username=" . urlencode($user));
        }
    } else {
        header("Location: ../index.php?login=not_found");
    }
}
?>