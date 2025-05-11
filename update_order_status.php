<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];

    $valid_statuses = ['Pending', 'Shipped', 'Delivered', 'Cancelled'];

    if (in_array($status, $valid_statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);

        if ($stmt->execute()) {
            header("Location: admin_orders.php?success=1");
        } else {
            echo "Failed to update status.";
        }

        $stmt->close();
    } else {
        echo "Invalid status.";
    }
}

$conn->close();
?>
