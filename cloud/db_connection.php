<?php
// Database connection settings for AWS RDS
define('DB_SERVER', 'cloud-db.ctymswg7pmeb.us-east-1.rds.amazonaws.com'); // AWS RDS endpoint
define('DB_USERNAME', 'admin');                // AWS RDS master username
define('DB_PASSWORD', 'cloud-password');       // AWS RDS password
define('DB_DATABASE', 'ecommerce');           // Your database name

// Define base URL for your EC2 instance's public IP or DNS
// You'll need to update this with your actual EC2 public DNS or IP
define('BASE_URL', '/');

// Display errors for debugging (you can remove this in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Create connection
try {
    // Connect to MySQL instance
    $conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
    
    // Check connection
    if (!$conn) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    // Set charset to ensure proper character handling
    mysqli_set_charset($conn, "utf8mb4");
    
} catch (Exception $e) {
    // Log the error and display a user-friendly message
    error_log("Database connection error: " . $e->getMessage());
    die("We're experiencing technical difficulties. Please try again later.");
}
?>