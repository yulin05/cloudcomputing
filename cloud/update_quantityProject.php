<?php
include 'db_connection.php';

if (isset($_GET['id']) && is_numeric($_GET['id']) && isset($_GET['action'])) {
    $order_id = $_GET['id'];
    $action = $_GET['action'];
    
    // First, get the current order details with prepared statement
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $quantity = $order['quantity'];
        $price = $order['price'];
        
        // Update quantity based on action
        if ($action === 'increase') {
            $quantity += 1;
        } elseif ($action === 'decrease') {
            $quantity -= 1;
        } else {
            // Invalid action
            $stmt->close();
            header("Location: cartProject.php?error=5");
            exit;
        }
        
        // Calculate new total price
        $total_price = $price * $quantity;

        if ($quantity <= 0) {
            // Delete the order if quantity is zero or negative
            $update_stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
            $update_stmt->bind_param("i", $order_id);
        } else {
            // Update the order
            $update_stmt = $conn->prepare("UPDATE orders SET quantity = ?, total_price = ? WHERE order_id = ?");
            $update_stmt->bind_param("idi", $quantity, $total_price, $order_id);
        }
        
        if ($update_stmt->execute()) {
            $update_stmt->close();
            $stmt->close();
            // Redirect back to cart page
            header("Location: cartProject.php?updated=1");
            exit;
        } else {
            $update_stmt->close();
            $stmt->close();
            // Handle error
            header("Location: cartProject.php?error=3");
            exit;
        }
    } else {
        $stmt->close();
        // Order not found
        header("Location: cartProject.php?error=4");
        exit;
    }
} else {
    // Invalid parameters
    header("Location: cartProject.php?error=5");
    exit;
}
?>