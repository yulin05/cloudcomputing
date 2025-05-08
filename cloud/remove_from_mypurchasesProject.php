<?php
include 'db_connection.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $purchase_id = intval($_GET['id']); // Sanitizing input

    // Prepare statement for better security
    $stmt = $conn->prepare("DELETE FROM purchased_orders WHERE purchase_id = ?");
    $stmt->bind_param("i", $purchase_id);

    if ($stmt->execute()) {
        // Success - redirect with flag
        header("Location: mypurchasesProject.php?removed=1");
        exit;
    } else {
        // SQL error
        header("Location: mypurchasesProject.php?error=1");
        exit;
    }
    $stmt->close();
} else {
    // Invalid or missing ID
    header("Location: mypurchasesProject.php?error=2");
    exit;
}
?>
