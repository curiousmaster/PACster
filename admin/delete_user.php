<?php
session_start();

// Ensure only admin users can access this page
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Check if the user ID is provided
if (!isset($_GET['id'])) {
    header('Location: admin.php'); // Redirect to admin page if no user ID is provided
    exit();
}

$userId = $_GET['id'];

// Prevent deletion of the initial admin (id=1)
if ($userId == 1) {
    $_SESSION['error_message'] = "You cannot delete the initial admin user.";
    header('Location: admin.php');
    exit();
}

// Include the configuration file for database connection
require_once 'config.php';

// Delete the user from the database
$db = connectDB();
$stmt = $db->prepare("DELETE FROM users WHERE id = :id");
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);
$stmt->execute();

// Redirect back to the admin page after deletion
$_SESSION['success_message'] = "User deleted successfully.";
header('Location: admin.php');
exit();
