<?php
ob_start();
session_start();
include 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<div class='message error'>❌ You need to log in to view your transaction history.</div>";
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT o.id, o.product_id, o.quantity, o.payment_method, o.order_date, p.name AS product_name, p.price
        FROM orders o
        INNER JOIN products p ON o.product_id = p.id
        WHERE o.user_id = ? ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f4f7fa;
            --text-color: #333;
            --table-bg: #fff;
            --table-alt: #f2f2f2;
            --header-bg: #007bff;
            --header-color: white;
            --button-bg: #007bff;
            --button-hover: #0056b3;
        }

        body.dark {
            --bg-color: #121212;
            --text-color: #f4f4f4;
            --table-bg: #1f1f1f;
            --table-alt: #2c2c2c;
            --header-bg: #333;
            --header-color: #fff;
            --button-bg: #444;
            --button-hover: #666;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            padding: 30px;
            transition: background 0.3s, color 0.3s;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background-color: var(--table-bg);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: var(--text-color);
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: var(--header-bg);
            color: var(--header-color);
        }

        tr:nth-child(even) {
            background-color: var(--table-alt);
        }

        .button {
            text-decoration: none;
            background-color: var(--button-bg);
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            display: inline-block;
            margin-top: 20px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: var(--button-hover);
        }

        .message {
            text-align: center;
            padding: 15px;
            font-size: 16px;
            color: var(--text-color);
        }

        .dark-toggle {
            text-align: right;
            margin-bottom: 10px;
        }

        .dark-toggle button {
            background: var(--button-bg);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        .dark-toggle button:hover {
            background: var(--button-hover);
        }
    </style>
</head>
<body>



    <h2>Your Transaction History</h2>

    <?php
    if ($result->num_rows == 0) {
        echo "<div class='message'>No transactions found. You haven't placed any orders yet.</div>";
    } else {
        echo "<table>
                <tr>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price (₹)</th>
                    <th>Payment Method</th>
                    <th>Order Date</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            $total_price = $row['price'] * $row['quantity'];
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['product_name']}</td>
                    <td>{$row['quantity']}</td>
                    <td>₹{$total_price}</td>
                    <td>" . ucfirst($row['payment_method']) . "</td>
                    <td>" . date('d-m-Y H:i:s', strtotime($row['order_date'])) . "</td>
                  </tr>";
        }
        echo "</table>";
    }

    $stmt->close();
    $conn->close();
    ?>

    <a href="index.php" class="button">Go back to homepage</a>
</div>

<script>
    function toggleDarkMode() {
        document.body.classList.toggle("dark");
        localStorage.setItem("darkMode", document.body.classList.contains("dark") ? "enabled" : "disabled");
    }

    window.onload = function () {
        if (localStorage.getItem("darkMode") === "enabled") {
            document.body.classList.add("dark");
        }
    }
</script>

</body>
</html>
