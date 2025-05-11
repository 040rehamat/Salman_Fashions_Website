<?php
session_start();
include 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='message error'>❌ Please log in to proceed with COD payment.</div>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details to pre-fill customer name
$user_sql = "SELECT name FROM users WHERE id = $user_id";
$user_result = mysqli_query($conn, $user_sql);
$user = mysqli_fetch_assoc($user_result);
$customer_name = $user['name'] ?? ''; // Set customer name if available
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash on Delivery (COD) Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }
        .container:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        label {
            font-size: 16px;
            margin-bottom: 8px;
            color: #333;
            display: block;
        }
        textarea, input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        textarea:focus, input[type="text"]:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 14px 30px;
            border-radius: 8px;
            border: none;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            font-size: 16px;
            color: #333;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Cash on Delivery (COD) Payment</h2>
    <form action="place_order.php" method="POST">
    <label for="customer_name">Customer Name:</label>
    <input type="text" name="customer_name" required placeholder="Enter your full name..." />

    <label for="address">Delivery Address:</label>
    <textarea name="address" required placeholder="Enter your full address here..."></textarea>

    <label for="contact">Contact Number:</label>
    <input type="text" name="contact" required placeholder="Enter your contact number..." />

    <input type="hidden" name="payment_method" value="COD">
    <button type="submit">Place Order</button>
</form>

</div>

</body>
</html>
