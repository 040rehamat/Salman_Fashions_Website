<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];

$stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("iii", $quantity, $user_id, $product_id);
$stmt->execute();

echo json_encode(['success' => 'Quantity updated']);
$stmt->close();
$conn->close();
?>
