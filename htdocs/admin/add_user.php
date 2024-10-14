<?php
session_start();

// Ensure only admin users can access this page
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$title = "Add User";
require_once 'header.php';
?>

<div class="content">
    <div class="container">
        <h3>Add New User</h3>

        <form action="add_user_handler.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>

            <div class="form-group">
                <label for="fullname">Full Name:</label>
                <input type="text" name="fullname" id="fullname" required>
            </div>

            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role" id="role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>

            <!-- Submit and Cancel buttons -->
            <div class="form-group">
                <input type="submit" value="Add User" class="submit-btn">
                <!-- Cancel button to go back to the admin page -->
                <a href="admin.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>
