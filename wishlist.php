<?php
session_start();
include 'db.php'; // Database connection
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;

if (!$isLoggedIn) {
  header("Location: login.php");
  exit();
}

// Fetch wishlist items
$wishlist = $conn->query("SELECT w.id as wid, p.* FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.user_id = $user_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Wishlist - Salman Fashions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="style.css" />
  <style>
    .wishlist-container {
      max-width: 1000px;
      margin: 2rem auto;
      padding: 1rem;
    }

    .wishlist-item {
      display: flex;
      align-items: center;
      background: #fff;
      padding: 1rem;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      margin-bottom: 1rem;
      transition: all 0.3s ease;
    }

    body.dark-mode .wishlist-item {
      background-color: #222;
      color: #fff;
    }

    .wishlist-item img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 10px;
      margin-right: 1rem;
    }

    .wishlist-actions button {
      margin-right: 0.5rem;
    }

    .btn {
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 20px;
      background-color: #fc9cc4;
      color: #fff;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background-color: #e57aaa;
    }
  </style>
</head>
<body class="<?php echo (isset($_COOKIE['mode']) && $_COOKIE['mode'] === 'dark') ? 'dark-mode' : ''; ?>">

<?php include 'header.php'; // Reuse your header ?>

<div class="wishlist-container">
  <h2><i class="fa-solid fa-heart"></i> My Wishlist</h2>
  <?php while ($item = $wishlist->fetch_assoc()): ?>
    <div class="wishlist-item">
      <img src="uploads/<?= $item['image'] ?>" alt="<?= $item['name'] ?>">
      <div style="flex: 1;">
        <h4><?= $item['name'] ?></h4>
        <p>₹<?= $item['price'] ?></p>
        <div class="wishlist-actions">
          <form method="POST" action="move_to_cart.php" style="display:inline-block">
            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
            <button class="btn" type="submit"><i class="fa-solid fa-cart-plus"></i> Move to Cart</button>
          </form>
          <form method="POST" action="remove_wishlist.php" style="display:inline-block">
            <input type="hidden" name="wishlist_id" value="<?= $item['wid'] ?>">
            <button class="btn" type="submit"><i class="fa-solid fa-trash"></i> Remove</button>
          </form>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>

<script>
  // Persist dark mode on wishlist page
  document.addEventListener("DOMContentLoaded", () => {
    if (localStorage.getItem("mode") === "dark") {
      document.body.classList.add("dark-mode");
    }
  });
</script>
</body>
</html>
