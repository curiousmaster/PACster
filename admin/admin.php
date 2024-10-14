<?php
// admin.php

session_start(); // Start the session

// Check if the user has admin role
if ($_SESSION['role'] !== 'admin') {
    // Redirect to an error page or the home page
    header('Location: index.php?error=access_denied');
    exit();
}

// Include the configuration file
include 'config.php';

// Connect to the database
$db = connectDB();

// Initialize sorting parameters
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'username'; // Default sort by username
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'asc' : 'desc';
$nextSortOrder = $sortOrder === 'asc' ? 'desc' : 'asc';

// Fetch users from the database
$query = "SELECT * FROM users ORDER BY $sortColumn $sortOrder";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
include 'header.php';
?>

<div class="content">
    <div class="admin-container">
        <h3>User List</h3>

        <!-- Button to Add User -->
        <div class="form-group">
            <a href="add_user.php" class="submit-btn">Add User</a>
        </div>

        <!-- Users table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>
                            <a href="?sort=username&order=<?php echo $nextSortOrder; ?>" class="sortable-header" order="<?php echo $nextSortOrder; ?>">
                                Username
                                <?php if ($sortColumn == 'username'): ?>
                                    <i class="fas <?php echo $sortOrder === 'asc' ? 'fa-angle-up' : 'fa-angle-down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?sort=fullname&order=<?php echo $nextSortOrder; ?>" class="sortable-header" order="<?php echo $nextSortOrder; ?>">
                                Full Name
                                <?php if ($sortColumn == 'fullname'): ?>
                                    <i class="fas <?php echo $sortOrder === 'asc' ? 'fa-angle-up' : 'fa-angle-down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?sort=role&order=<?php echo $nextSortOrder; ?>" class="sortable-header" order="<?php echo $nextSortOrder; ?>">
                                Role
                                <?php if ($sortColumn == 'role'): ?>
                                    <i class="fas <?php echo $sortOrder === 'asc' ? 'fa-angle-up' : 'fa-angle-down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="?sort=created&order=<?php echo $nextSortOrder; ?>" class="sortable-header" order="<?php echo $nextSortOrder; ?>">
                                Created
                                <?php if ($sortColumn == 'created'): ?>
                                    <i class="fas <?php echo $sortOrder === 'asc' ? 'fa-angle-up' : 'fa-angle-down'; ?>"></i>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td><?php echo htmlspecialchars($user['created']); ?></td>
                        <td>
                            <div class="button-group">
                                <form action="edit_user.php" method="get" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="action-btn edit-btn">Edit</button>
                                </form>

                                <?php if ($user['id'] != 1): ?>
                                <form action="delete_user.php" method="get" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="action-btn delete-btn">Delete</button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Include footer
include 'footer.php';
?>
