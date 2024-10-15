<?php
<<<<<<< HEAD
define('COMPANY_NAME','<CHANGEME>');
=======
define('COMPANY_NAME','<CHANGEME>'); // Company branding
>>>>>>> ee809491094fb27c6afc4155659afd72810bdfaa

// Initial Configuration for PAC File Tester
<<<<<<< HEAD
$baseURL = '<URL_TO_PACFILES>'; // Url to P
=======
$baseURL = '<CHANGEME>'; // URL to pacfiles
>>>>>>> ee809491094fb27c6afc4155659afd72810bdfaa

// Path to the SQLite3 database file
define('DB_PATH', '<CHANGEME>/users.db'); // Full path to users.db

// Base path for PAC files
define('BASE_PATH', '<CHANGEME>'); // Full directory path to pac files

// Function to connect to the SQLite3 database
function connectDB() {
    static $db;
    if ($db === null) {
        // Ensure that the database file exists
        if (!file_exists(DB_PATH)) {
            die("Error: Database file not found at " . DB_PATH);
        }

        try {
            // Connect to the SQLite database
            $db = new PDO('sqlite:' . DB_PATH);
            // Set error mode to exception to catch issues
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // If connection fails, output an error message
            die("Error connecting to the database: " . $e->getMessage());
        }
    }
    return $db;
}

// Security settings
define('SESSION_TIMEOUT', 3600); // Session timeout (1 hour)

// Password settings
define('PASSWORD_MIN_LENGTH', 8); // Minimum password length

// Role definitions for RBAC (user roles)
define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');

// Prevent modification or deletion of the initial admin user
define('INITIAL_ADMIN', 'admin');

// Add other configuration settings as needed
