<?php
session_start();
if (isset($_SESSION['username'])) {
    // If user is already logged in, redirect to the index page
    header('Location: index.php');
    exit();
}

// Check for error message
$error_message = isset($_GET['error']) ? $_GET['error'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PACster</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="login-container">
        <h2>PACster: Login</h2>

        <?php $error_message = $error_message ?? ' '; ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div> <!-- Error message div -->
        <br>

        <form action="login_handler.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>
    </div>
</body>
</html>