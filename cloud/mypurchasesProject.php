<?php
include 'db_connection.php';
date_default_timezone_set('Asia/Kuala_Lumpur');

// Fetch purchased orders
$sql = "SELECT * FROM purchased_orders ORDER BY purchase_date DESC";
$result = mysqli_query($conn, $sql);
$purchases = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $purchases[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golden Shop - My Purchases</title>
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
        
        .purchase-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .purchase-header {
            background-color: #f2f2f2;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
        }
        
        .purchase-date {
            font-weight: bold;
        }
        
        .purchase-details {
            padding: 20px;
        }
        
        .product-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .product-info:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 15px;
        }
        
        .product-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .product-quantity {
            color: #666;
            font-size: 14px;
        }
        
        .product-price {
            margin-left: auto;
            font-weight: bold;
        }
        
        .customer-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-top: 1px solid #ddd;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        
        .info-label {
            width: 100px;
            font-weight: bold;
        }
        
        .payment-info {
            background-color: #eaf7ea;
            padding: 15px;
            border-top: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
        }
        
        .payment-method {
            font-weight: bold;
        }
        
        .total-price {
            font-size: 18px;
            font-weight: bold;
            color: #2ecc71;
        }
        
        .no-purchases {
            text-align: center;
            padding: 40px 0;
            color: #666;
        }
        
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 0;
        }
        
        .empty-icon {
            font-size: 60px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .shop-now-btn {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        
        .shop-now-btn:hover {
            background-color: #45a049;
        }
        
        footer {
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
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
        <h1>My Purchase History</h1>
        
        <?php if (count($purchases) > 0): ?>
            <?php 
            // print_r($purchases);exit;
            // Group purchases by date and name to show them as orders
            $grouped_purchases = [];
            foreach ($purchases as $purchase) {
                $key = $purchase['purchase_date'] . '_' . $purchase['user_name'];
                if (!isset($grouped_purchases[$key])) {
                    $grouped_purchases[$key] = [
                        'purchase_date' => $purchase['purchase_date'],
                        'user_name' => $purchase['user_name'],
                        'user_address' => $purchase['user_address'],
                        'user_phone' => $purchase['user_phone'],
                        'payment_method' => $purchase['payment_method'],
                        'total' => 0,
                        'items' => []
                    ];
                }
                $grouped_purchases[$key]['items'][] = $purchase;
                $grouped_purchases[$key]['total'] += $purchase['total_price'];
            }
            ?>
            
            <?php foreach ($grouped_purchases as $order): ?>
                <div class="purchase-card">
                    <div class="purchase-header">
                        <div class="purchase-date">
                            Order ID: <span style="color: green;">#<?php echo $order['items'][0]['purchase_id']; ?></span><br><br>
                            Order Date: <?php 
    $date = new DateTime($order['purchase_date']);
    echo $date->format('F j, Y, g:i a'); 
?>
                        </div>
                        <div class="delete-order">
                            <a href="remove_from_mypurchasesProject.php?id=<?php echo $order['items'][0]['purchase_id']; ?>"
                            style="margin-left: 15px; color: #e74c3c; text-decoration: none;"
                            onclick="return confirm('Are you sure you want to remove this item?');">
                                Remove
                            </a>
                        </div>
                    </div>

                    <div class="purchase-details">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="product-info">
                                <?php if (!empty($item['photo_url'])): ?>
                                    <img src="<?php echo BASE_URL . $item['photo_url']; ?>" alt="<?php echo $item['product_name']; ?>" class="product-image">
                                <?php else: ?>
                                    <div class="product-image-placeholder"></div>
                                <?php endif; ?>
                                <div>
                                    <div class="product-name"><?php echo $item['product_name']; ?></div>
                                    <div class="product-quantity">Quantity: <?php echo $item['quantity']; ?> x RM <?php echo number_format($item['price'], 2); ?></div>
                                </div>
                                <div class="product-price">RM <?php echo number_format($item['total_price'], 2); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="customer-info">
                        <div class="info-row">
                            <div class="info-label">Name:</div>
                            <div><?php echo $order['user_name']; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Address:</div>
                            <div><?php echo $order['user_address']; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Phone:</div>
                            <div><?php echo $order['user_phone']; ?></div>
                        </div>
                    </div>

                    <div class="payment-info">
                        <div class="payment-method">Payment Method: <?php echo $order['payment_method']; ?></div>
                        <div class="total-price">Total: RM <?php echo number_format($order['total'], 2); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>

            
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <div class="no-purchases">You haven't made any purchases yet.</div>
                <a href="productpageProject.php" class="shop-now-btn">Shop Now</a>
            </div>
        <?php endif; ?>
    </div>
    
    <footer>
        <p>© 2025 Golden Shop - Your Graduation Destination</p>
        <p>Tel: +6012-3456789 | Email: info@graduationshop.com</p>
    </footer>
</body>
</html>