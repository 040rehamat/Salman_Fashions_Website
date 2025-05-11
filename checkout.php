<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT p.id AS product_id, p.name, p.price, p.image, c.quantity, (p.price * c.quantity) AS total_price 
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout - Salman Fashions</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f2f2f2;
      margin: 0;
      color: #333;
      transition: background 0.3s, color 0.3s;
    }
    body.dark-mode {
      background: #121212;
      color: #f2f2f2;
    }
    main {
      max-width: 1000px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    body.dark-mode main {
      background: #1f1f1f;
    }
    h2 {
      text-align: center;
      margin-bottom: 30px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }
    th {
      background: #343a40;
      color: white;
    }
    body.dark-mode th {
      background: #444;
    }
    body.dark-mode td {
      background: #2a2a2a;
      color: #ddd;
    }
    img {
      width: 60px;
      border-radius: 6px;
    }
    .qty-controls {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .qty-controls button {
      padding: 5px 10px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .qty-controls input {
      width: 40px;
      margin: 0 5px;
      text-align: center;
      padding: 5px;
    }
    .cart-btns {
      text-align: center;
      margin-top: 20px;
    }
    .cart-btns button {
      padding: 12px 24px;
      border: none;
      border-radius: 25px;
      font-size: 16px;
      margin: 5px;
      cursor: pointer;
    }
    .place-order {
      background: #28a745;
      color: white;
    }
    .empty-cart {
      background: #ffc107;
      color: #212529;
    }
    footer {
      text-align: center;
      margin-top: 30px;
      padding: 15px;
      background: #343a40;
      color: white;
      border-radius: 0 0 12px 12px;
    }
    .toast {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #28a745;
      color: white;
      padding: 12px 20px;
      border-radius: 6px;
      display: none;
      z-index: 9999;
      animation: slidein 0.4s ease;
    }
    @keyframes slidein {
      from { right: -100px; opacity: 0; }
      to { right: 20px; opacity: 1; }
    }

    /* 🌙 Dark Mode Toggle Button */
    .dark-mode-toggle {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 10px 15px;
      background: #343a40;
      color: white;
      border: none;
      border-radius: 25px;
      font-size: 14px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<!-- Dark Mode Button -->

<?php include 'header.php'; ?>

<main>
  <h2>🛒 Checkout - Your Cart</h2>

  <?php if (count($cart_items) > 0): ?>
    <form id="cart-form" onsubmit="return handleRedirect();">
    <table>
        <tr>
          <th>Image</th>
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Total</th>
          <th>Remove</th>
        </tr>
        <?php $total = 0; foreach ($cart_items as $row): $total += $row['total_price']; ?>
        <tr>
          <td><img src="<?= htmlspecialchars($row['image']) ?>" alt=""></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td>₹<?= number_format($row['price'], 2) ?></td>
          <td>
            <div class="qty-controls">
              <button type="button" onclick="changeQty(<?= $row['product_id'] ?>, -1)">-</button>
              <input type="number" name="quantities[<?= $row['product_id'] ?>]" id="qty-<?= $row['product_id'] ?>" value="<?= $row['quantity'] ?>" min="1">
              <button type="button" onclick="changeQty(<?= $row['product_id'] ?>, 1)">+</button>
            </div>
          </td>
          <td>₹<?= number_format($row['total_price'], 2) ?></td>
          <td><a href="remove_item.php?product_id=<?= $row['product_id'] ?>" onclick="return confirm('Remove this item?')">❌</a></td>
        </tr>
        <?php endforeach; ?>
      </table>

      <p><strong>Total: ₹<?= number_format($total, 2) ?></strong></p>

      <div class="payment-method" style="text-align:center;">
        
        <label><input type="radio" name="payment_method" value="COD" required> Cash on Delivery</label> &nbsp;&nbsp;
        <label><input type="radio" name="payment_method" value="Online" required> Online Payment</label>
      </div>

      <div class="cart-btns">
        <button type="button" class="empty-cart" onclick="emptyCart()">Empty Cart</button>
        
        <button type="submit" class="place-order">Place Order</button>
      </div>
    </form>
  <?php else: ?>
    <p style="text-align:center;">Your cart is empty.</p>
  <?php endif; ?>
</main>

<footer>
  <p>&copy; <?= date("Y") ?> Salman Fashions. All rights reserved.</p>
</footer>

<div class="toast" id="toastMsg">Updated successfully</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function changeQty(productId, change) {
  const input = document.getElementById('qty-' + productId);
  let qty = parseInt(input.value);
  qty = Math.max(1, qty + change);
  input.value = qty;
  updateCart(productId, qty);
}

function updateCart(productId, qty) {
  $.post('update_cart.php', { product_id: productId, quantity: qty }, function() {
    showToast("Cart updated");
    setTimeout(() => location.reload(), 800);
  });
}

function showToast(msg) {
  const toast = document.getElementById('toastMsg');
  toast.innerText = msg;
  toast.style.display = 'block';
  setTimeout(() => { toast.style.display = 'none'; }, 2000);
}

function emptyCart() {
  $.get('empty_cart.php', function() {
    location.reload();
  });
}

function validatePaymentMethod() {
  const selected = document.querySelector('input[name="payment_method"]:checked');
  if (!selected) {
    alert("Please select a payment method.");
    return false;
  }
  return true;
}

// 🌙 Dark Mode toggle + remember preference
function toggleDarkMode() {
  document.body.classList.toggle('dark-mode');
  localStorage.setItem('dark-mode', document.body.classList.contains('dark-mode') ? 'enabled' : 'disabled');
}

window.onload = () => {
  if (localStorage.getItem('dark-mode') === 'enabled') {
    document.body.classList.add('dark-mode');
  }
};
</script>
</body>
</html>
<script>
function handleRedirect() {
  const method = document.querySelector('input[name="payment_method"]:checked');
  if (!method) {
    alert("Please select a payment method.");
    return false;
  }

  const selectedMethod = method.value;

  // Redirect user to appropriate page
  if (selectedMethod === 'COD') {
    window.location.href = 'cod_payment.php';
  } else if (selectedMethod === 'Online') {
    window.location.href = 'online_payment.php';
  }

  return false; // prevent form submission
}
</script>
