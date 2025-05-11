<?php
session_start();
include 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='message error'>❌ Please log in to proceed with online payment.</div>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch total from cart
$totalAmount = 0;
$stmt = $conn->prepare("SELECT SUM(p.price * c.quantity) as total FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($totalAmount);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UPI Payment | Salman Fashions</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
        }
        .container {
            width: 90%;
            max-width: 500px;
            margin: 60px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        .qr-img {
            display: block;
            margin: 20px auto;
            width: 200px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .upi-id {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        label {
            margin-top: 20px;
            display: block;
            color: #333;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0 16px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 16px;
        }
        button {
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #218838;
        }
        .info {
            font-size: 14px;
            color: #555;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container">
<form action="place_order.php" method="POST">
    <input type="hidden" name="payment_method" value="UPI Manual">

    <label for="address">Delivery Address:</label>
    <input type="text" name="address" required placeholder="Enter your full address here...">

    <label for="contact">Contact Number:</label>
    <input type="text" name="contact" required placeholder="Enter your contact number...">

    <h2>Pay via UPI</h2>
    <img src="images/qr.jpg" alt="UPI QR Code" class="qr-img">

    <p class="upi-id">Pay to UPI ID: <strong>6362688954@ybl</strong></p>
    <p class="info">Use PhonePe, Google Pay, Paytm or any UPI app to scan and pay ₹<?= $totalAmount ?></p>
    
    <label for="payment_id">Enter UPI Transaction/Reference ID:</label>
    <input type="text" name="payment_id" required placeholder="e.g., UPI1234567890">

    <h3 style="text-align:center; margin: 20px 0 10px; color: #28a745;">After payment, click below:</h3>
    <button type="submit">✅ Place Order</button>
</form>


   

    
   

</div>
</body>
</html>
