<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $user_id = $_SESSION['user_id'];

    // Only allow cancel if order is pending
    $check = $conn->prepare("SELECT status FROM orders WHERE id = ? AND user_id = ?");
    $check->bind_param("ii", $order_id, $user_id);
    $check->execute();
    $check_result = $check->get_result()->fetch_assoc();

    if ($check_result && $check_result['status'] == 'Pending') {
        $update = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ?");
        $update->bind_param("i", $order_id);
        $update->execute();
    }
}

header("Location: my_orders.php");
exit();
?>
