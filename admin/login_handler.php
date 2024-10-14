<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        die('Username and password are required.');
    }

    // Connect to the database
    $db = connectDB();
    if (!$db) {
        die('Failed to connect to the database');
    }

    // Check if the user exists in the database
    $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // If user exists and the password is correct
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role']; // Ensure role is correctly set

        // Redirect to index (pactest) page
        header('Location: index.php');
        exit();
    } else {
        echo "Invalid username or password.";
    }
}
?>
