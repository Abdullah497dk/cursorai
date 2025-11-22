<?php
// Authentication Functions

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function isAdmin() {
    return isLoggedIn() && in_array($_SESSION['username'], ADMIN_USERNAMES);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: admin-login.php');
        exit();
    }
}

function login($userId, $username, $role) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>
