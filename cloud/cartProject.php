<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db_connection.php';
date_default_timezone_set('Asia/Kuala_Lumpur');

// Fetch all orders
$sql = "SELECT * FROM orders";
$result = mysqli_query($conn, $sql);
$orders = [];
$total = 0;

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
        $total += $row['total_price'];
    }
}

// Handle payment form submission
$message = '';
$redirect = false;
$success = false; // Initialize success variable

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceed_payment'])) {
    $user_name = mysqli_real_escape_string($conn, $_POST['user_name']);
    $user_address = mysqli_real_escape_string($conn, $_POST['user_address']);
    $user_phone = mysqli_real_escape_string($conn, $_POST['user_phone']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    // Process each order item
    $success = true;
    foreach ($orders as $order) {
        // Use prepared statement for insertion
        $stmt = $conn->prepare("INSERT INTO purchased_orders (product_name, quantity, price, total_price, photo_url, user_name, user_address, user_phone, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siddsssss", $order['product_name'], $order['quantity'], $order['price'], $order['total_price'], $order['photo_url'], $user_name, $user_address, $user_phone, $payment_method);
        
        if (!$stmt->execute()) {
            $success = false;
            $message = "Error processing order: " . mysqli_error($conn);
            break;
        }
        $stmt->close();
    }
    
    if ($success) {
        // Clear the orders table
        mysqli_query($conn, "TRUNCATE TABLE orders");
        $message = "Purchase completed successfully!";
        $redirect = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golden Shop - Your Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5dc;
        }
        
        header {
            background-color: #000;
            color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            text-transform: uppercase;
        }
        
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        
        th, td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        tfoot td {
            font-weight: bold;
        }
        
        .empty-cart {
            text-align: center;
            padding: 30px;
            font-size: 18px;
            color: #666;
        }
        
        form {
            margin-top: 30px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        button {
            grid-column: span 2;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
            margin-top: 10px;
        }
        
        button:hover {
            background-color: #45a049;
        }
        
        footer {
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
        
        .continue-shopping {
            display: inline-block;
            background-color: #2196F3;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        
        .continue-shopping:hover {
            background-color: #0b7dda;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Golden Shop</div>
        <nav>
            <a href="homepageProject.php">Home</a>
            <a href="productpageProject.php">Product</a>
            <a href="cartProject.php">Cart</a>
            <a href="myorderProject.php">Order List</a>
            <a href="mypurchasesProject.php">Purchased History</a>
        </nav>
    </header>

    <div class="container">
        <h1>Your Shopping Cart</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>"><?php echo $message; ?></div>
            <?php if ($redirect): ?>
                <script>
                    setTimeout(function() {
                        window.location.href = 'myorderProject.php';
                    }, 2000);
                </script>
            <?php endif; ?>
        <?php endif; ?>
        
        <?php if (isset($_GET['removed']) && $_GET['removed'] == 1): ?>
            <div class="message success">Item has been removed from your cart.</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
            <div class="message success">Cart has been updated successfully.</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="message error">
                <?php 
                    $error_code = $_GET['error'];
                    switch ($error_code) {
                        case 1:
                            echo "Error removing item from cart.";
                            break;
                        case 2:
                            echo "Invalid item ID.";
                            break;
                        case 3:
                            echo "Error updating quantity.";
                            break;
                        case 4:
                            echo "Order not found.";
                            break;
                        case 5:
                            echo "Invalid operation.";
                            break;
                        default:
                            echo "An error occurred.";
                    }
                ?>
            </div>
        <?php endif; ?>

        <?php if (count($orders) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <?php if (!empty($order['photo_url'])): ?>
                                        <img src="<?php echo BASE_URL . $order['photo_url']; ?>" alt="<?php echo $order['product_name']; ?>" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                    <?php endif; ?>
                                    <?php echo $order['product_name']; ?>
                                </div>
                            </td>
                            <td>RM <?php echo number_format($order['price'], 2); ?></td>
                            <td>
                                <?php echo $order['quantity']; ?>

                                <a href="update_quantityProject.php?id=<?php echo $order['order_id']; ?>&action=decrease" 
                                style="margin-left: 5px; text-decoration: none;">
                                    <button style="background-color: #ff4d4d; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;">-</button>
                                </a>

                                <a href="update_quantityProject.php?id=<?php echo $order['order_id']; ?>&action=increase" 
                                style="margin-left: 5px; text-decoration: none;">
                                    <button style="background-color: #4CAF50; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;">+</button>
                                </a>
                            </td>
                            <td>
                                RM <?php echo number_format($order['total_price'], 2); ?>
                                <a href="remove_from_cartProject.php?id=<?php echo $order['order_id']; ?>" style="margin-left: 15px; color: #e74c3c; text-decoration: none;" onclick="return confirm('Are you sure you want to remove this item?');">Remove</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right;">Grand Total:</td>
                        <td>RM <?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
            
            <form method="POST">
                <div>
                    <label for="user_name">Full Name:</label>
                    <input type="text" id="user_name" name="user_name" required>
                
                    <label for="user_address">Shipping Address:</label>
                    <input type="text" id="user_address" name="user_address" required>
                
                    <label for="user_phone">Phone Number:</label>
                    <input type="text" id="user_phone" name="user_phone" required>
                
                    <label for="payment_method">Payment Method:</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="Credit Card">Credit Card</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>
                
                <button type="submit" name="proceed_payment">Complete Purchase</button>
            </form>
        <?php else: ?>
            <div class="empty-cart">
                <p>Your cart is empty!</p>
                <a href="productpageProject.php" class="continue-shopping">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>© 2025 Golden Shop - Your Graduation Destination</p>
        <p>Tel: +6012-3456789 | Email: info@goldenshop.com</p>
    </footer>
</body>
</html>