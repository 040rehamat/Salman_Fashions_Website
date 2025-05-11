<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("❌ Please log in to place an order.");
}

$user_id = $_SESSION['user_id'];
$customer_name = $_POST['customer_name'] ?? '';
$address = $_POST['address'] ?? '';
$contact = $_POST['contact'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'COD';
$status = 'Pending';

// Validate inputs
if ( empty($address) || empty($contact)) {
    die("❌ All fields are required.");
}

// 🔢 Calculate total
$total_amount = 0;
$cart_items = mysqli_query($conn, "SELECT c.product_id, c.quantity, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $user_id");
if (mysqli_num_rows($cart_items) === 0) {
    die("🛒 Cart is empty.");
}

while ($row = mysqli_fetch_assoc($cart_items)) {
    $total_amount += $row['price'] * $row['quantity'];
}

// Insert into orders
$stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, payment_method, payment_id, address, contact, status, order_date, customer_name, total_amount) VALUES (?, ?, ?, ?, '', ?, ?, ?, NOW(), ?, ?)");
$success = true;

mysqli_data_seek($cart_items, 0); // rewind result
while ($item = mysqli_fetch_assoc($cart_items)) {
    $stmt->bind_param("iiisssssd", $user_id, $item['product_id'], $item['quantity'], $payment_method, $address, $contact, $status, $customer_name, $total_amount);
    if (!$stmt->execute()) {
        $success = false;
        break;
    }
}

if ($success) {
    // Clear cart
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");
    echo "<div class='message success'>✅ Order placed successfully!</div>";
} else {
    echo "<div class='message error'>❌ Failed to place order: " . $stmt->error . "</div>";
}
?>
