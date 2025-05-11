<?php
session_start();
include 'db.php';
include 'header.php'; 

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id']; // ✅ Must be set

$sql = "SELECT orders.id, orders.product_id,products.name, products.price, orders.quantity, orders.payment_method, orders.status, orders.order_date 
        FROM orders 
        JOIN products ON orders.product_id = products.id 
        WHERE orders.user_id = ? 
        ORDER BY orders.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<br><br>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders - Salman Fashions</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f4f4;
      margin: 0;
      padding: 20px;
      color: #333;
    }

    /* Dark Mode Styles */
    body.dark-mode {
      background: #121212;
      color: #e0e0e0;
    }

    main {
      max-width: 800px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    body.dark-mode main {
      background: #1e1e1e;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    body.dark-mode h2 {
      color: #f1f1f1;
    }

    ul {
      list-style-type: none;
      padding: 0;
    }

    li {
      background: #f9f9f9;
      margin-bottom: 15px;
      padding: 15px;
      border-left: 5px solid #28a745;
      border-radius: 5px;
    }

    body.dark-mode li {
      background: #333;
      border-left: 5px solid #28a745;
    }

    a {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      background: #007bff;
      color: white;
      padding: 10px 20px;
      border-radius: 20px;
    }

    body.dark-mode a {
      background: #0056b3;
    }

    a:hover {
      background: #0056b3;
    }

    body.dark-mode a:hover {
      background: #007bff;
    }

    /* Optional: Add a toggle button for dark mode */
    .dark-mode-toggle {
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #444;
      color: #fff;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<main>
  <h2>🧾 My Orders</h2>

  <?php if ($result->num_rows > 0): ?>
    <ul>
    <?php while ($row = $result->fetch_assoc()): ?>
  <li>
    <strong><?= htmlspecialchars($row['name']) ?></strong> x <?= $row['quantity'] ?> 
    - ₹<?= number_format($row['price'] * $row['quantity'], 2) ?> 
    (<?= $row['payment_method'] ?>) - <?= date("d M Y", strtotime($row['order_date'])) ?>  
    <br>Status: 
    <span class="status status-<?= strtolower($row['status']) ?>">
      <?= $row['status'] ?>
    </span>

    <?php if ($row['status'] === 'Pending'): ?>
  <form action="cancel_order.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
    <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
    <button type="submit" style="margin-top: 10px; background-color: #f44336; color: white; border: none; padding: 6px 12px; border-radius: 5px;">Cancel Order</button>
  </form>
  <?php elseif ($row['status'] === 'Delivered'): ?>
  <div style="margin-top:10px;">
    <a href="review.php?product_id=<?= $row['product_id'] ?>" style="background-color:#ffc107; color:black; padding:6px 12px; border-radius:5px; margin-right:10px;">⭐ Leave a Review</a>

    <a href="gen_invoice.php?id=<?= $row['id'] ?>" target="_blank" style="background-color:#28a745; color:white; padding:6px 12px; border-radius:5px;">📄 Download Invoice</a>
  </div>
<?php endif; ?>


  </li>
<?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p style="text-align:center;">You haven't placed any orders yet.</p>
  <?php endif; ?>

  <div style="text-align:center;">
    <a href="index.php">🔙 Back to Home</a>
  </div>
</main>

<script>
  // Toggle Dark Mode
  function toggleDarkMode() {
    document.body.classList.toggle("dark-mode");
  }
</script>

</body>
</html>

<?php
// Review Section
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($product_id)) {
    $rating = $_POST['rating'];
    $review = $_POST['review'];

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, review) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $product_id, $rating, $review);
    $stmt->execute();
    $stmt->close();

    header("Location: my_orders.php?review=success");
    exit;
}

$stmt->close();
$conn->close();
?>

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
