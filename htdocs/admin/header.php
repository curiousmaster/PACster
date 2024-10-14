<?php
// header.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PACster</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome -->
</head>
<body>

<header>
    <div class="navbar">
        <h2>PAC Tester</h2>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li> <!-- Redirect to index.php -->
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin.php">Manage Users</a></li> <!-- Labeled Manage Users, shown only for admin -->
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </div>
</header>
