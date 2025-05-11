<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f9f9f9;
            padding: 30px;
            text-align: center;
        }
        .message {
            padding: 20px;
            border-radius: 8px;
            margin: 20px auto;
            width: 60%;
            font-size: 18px;
        }
        .success {
            background-color: #28a745;
            color: white;
        }
        .error {
            background-color: #dc3545;
            color: white;
        }
        a.button {
            text-decoration: none;
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<?php
if (isset($_SESSION['success'])) {
    echo "<div class='message success'><strong>✅ Success:</strong> {$_SESSION['success']}</div>";
    unset($_SESSION['success']);
} elseif (isset($_SESSION['error'])) {
    echo "<div class='message error'><strong>❌ Error:</strong> {$_SESSION['error']}</div>";
    unset($_SESSION['error']);
} else {
    echo "<div class='message'>No recent order placed.</div>";
}
?>

<a href="index.php" class="button">Continue Shopping</a>

</body>
</html>
