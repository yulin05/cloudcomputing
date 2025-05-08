<?php
include 'db_connection.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $order_id = $_GET['id'];
    
    // Delete the order using prepared statement
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        // Redirect back to cart page
        header("Location: cartProject.php?removed=1");
        exit;
    } else {
        // Handle error
        header("Location: cartProject.php?error=1");
        exit;
    }
    $stmt->close();
} else {
    // Invalid ID
    header("Location: cartProject.php?error=2");
    exit;
}
?>