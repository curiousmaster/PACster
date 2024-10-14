<?php
session_start();

// Ensure only admin users can process this form
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $fullname = $_POST['fullname'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Connect to the database
    require_once 'config.php';
    $db = connectDB();

    if ($db) {
        $stmt = $db->prepare("INSERT INTO users (username, fullname, role, password, created) VALUES (?, ?, ?, ?, datetime('now'))");
        $stmt->execute([$username, $fullname, $role, $password]);

        // Redirect to admin dashboard or confirmation page
        header('Location: admin.php');
        exit();
    } else {
        echo "Error connecting to the database.";
    }
}
?>
