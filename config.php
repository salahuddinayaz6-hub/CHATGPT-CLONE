<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'rsoa_rsoa276_77');
define('DB_USER', 'rsoa_rsoa276_77');
define('DB_PASS', '123456');
 
// API Configuration (for future use)
define('API_ENDPOINT', 'https://api.openai.com/v1/chat/completions');
define('API_KEY', 'your-api-key-here'); // Replace with actual API key
 
// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
 
// Timezone
date_default_timezone_set('Asia/Kolkata');
 
// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
// Function to get database connection
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch(PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return null;
    }
}
?>
