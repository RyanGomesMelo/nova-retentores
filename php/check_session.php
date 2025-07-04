<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Function to get current user info
function getCurrentUser() {
    global $conn;
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    return null;
}

// Function to check if user has permission
function hasPermission($permission) {
    $user = getCurrentUser();
    if ($user) {
        // Add your permission logic here
        return true; // For now, all admins have all permissions
    }
    return false;
} 