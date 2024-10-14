<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require 'config.php';

// Connect to SQLite database
$dbFile = __DIR__ . '/etc/users.db';
$db = new PDO('sqlite:' . $dbFile);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // Add a new user
    if ($action === 'add_user') {
        $username = $_POST['username'];
        $fullname = $_POST['fullname'];
        $role = $_POST['role'];
        $password = password_hash('password', PASSWORD_DEFAULT); // Default password

        $stmt = $db->prepare("INSERT INTO users (username, fullname, password, role, edited_by)
                              VALUES (:username, :fullname, :password, :role, :edited_by)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':edited_by', $_SESSION['username']);
        $stmt->execute();

        header('Location: admin.php');
        exit();
    }

    // Handle edit and delete cases similarly
}
