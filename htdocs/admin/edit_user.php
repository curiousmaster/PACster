<?php
// edit_user.php

session_start(); // Start the session

// Include the configuration file
include 'config.php';

// Connect to the database
$db = connectDB();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Determine the user ID to edit
$userId = isset($_GET['id']) ? $_GET['id'] : $_SESSION['id'];

// Check if the user is an admin
$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

// If the user is normal, they should only be able to edit themselves
if (!$isAdmin && $userId != $_SESSION['id']) {
    header('Location: index.php?error=access_denied');
    exit();
}

// Fetch the user details from the database
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user exists
if (!$user) {
    header('Location: index.php'); // Redirect if user not found
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only update the password
    if (isset($_POST['password']) && !empty($_POST['password'])) {
        $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

        $updateQuery = "UPDATE users SET password = :password WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':password', $newPassword);
        $updateStmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $updateStmt->execute();

        // Redirect after successful update
        header('Location: index.php?success=user_updated');
        exit();
    }
}

// Include header
include 'header.php';
?>

<div class="content">
    <div class="admin-container">
        <h3>Edit User</h3>

        <form method="post" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly> <!-- Make it read-only -->
            </div>

            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" readonly> <!-- Make it read-only -->
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <input type="text" id="role" name="role" value="<?php echo htmlspecialchars($user['role']); ?>" readonly> <!-- Make it read-only -->
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Update Password" class="submit-btn">
                <a href="index.php" class="cancel-btn">Cancel</a> <!-- Cancel button -->
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
include 'footer.php';
?>