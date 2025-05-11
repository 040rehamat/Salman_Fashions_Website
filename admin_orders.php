<?php
include 'db.php';
include 'header.php';
$result = $conn->query("SELECT o.id, o.user_id, o.product_id, o.quantity, o.payment_method, o.payment_id, o.address, o.contact, o.status, o.order_date, p.name AS product_name FROM orders o JOIN products p ON o.product_id = p.id ORDER BY o.order_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f7fa;
            margin: 0;
            padding: 40px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }

        th {
            background-color: #343a40;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        select {
            padding: 5px 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-family: inherit;
        }

        button {
            padding: 5px 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        form {
            display: flex;
            gap: 8px;
            justify-content: center;
            align-items: center;
        }

        @media (max-width: 768px) {
            th, td {
                font-size: 13px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>

<h2>📦 All Orders</h2>

<table>
    <tr>
        <th>Order ID</th>
        <th>User ID</th>
        <th>Product</th>
        <th>Qty</th>
        <th>Method</th>
        <th>Payment ID</th>
        <th>Address</th>
        <th>Contact</th>
        <th>Status</th>
        <th>Date</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['user_id'] ?></td>
            <td><?= $row['product_name'] ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= $row['payment_method'] ?></td>
            <td><?= $row['payment_id'] ?></td>
            <td><?= $row['address'] ?></td>
            <td><?= $row['contact'] ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= date('d M Y', strtotime($row['order_date'])) ?></td>
            <td>
                <form action="update_order_status.php" method="POST">
                    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                    <select name="status">
                        <option <?= $row['status']=='Pending'?'selected':'' ?>>Pending</option>
                        <option <?= $row['status']=='Shipped'?'selected':'' ?>>Shipped</option>
                        <option <?= $row['status']=='Delivered'?'selected':'' ?>>Delivered</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
        </tr>
        

    <?php } ?>
</table>

</body>
</html>
<style>
.status {
    font-weight: bold;
    padding: 4px 10px;
    border-radius: 12px;
    display: inline-block;
    text-transform: uppercase;
}

.status-pending {
    background-color: #ffa50026;
    color: #ff9800;
    border: 1px solid #ff9800;
}

.status-shipped {
    background-color: #2196f326;
    color: #2196f3;
    border: 1px solid #2196f3;
}

.status-delivered {
    background-color: #4caf5026;
    color: #4caf50;
    border: 1px solid #4caf50;
}

.status-cancelled {
    background-color: #f4433626;
    color: #f44336;
    border: 1px solid #f44336;
}
</style>

