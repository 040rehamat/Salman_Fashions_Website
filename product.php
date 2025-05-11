<?php
include 'db.php';
include 'header.php';

$product_id = $_GET['id'] ?? 0;
if (!$product_id) {
    echo "Product not found.";
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit;
}

$review_stmt = $conn->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
$review_stmt->bind_param("i", $product_id);
$review_stmt->execute();
$reviews = $review_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title><?= htmlspecialchars($product['name']) ?> | Salman Fashions</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: var(--bg-color, #fafafa);
      transition: background 0.3s, color 0.3s;
    }

    .product-detail-container {
      max-width: 1000px;
      margin: auto;
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      padding: 2rem;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      animation: fadeIn 0.4s ease-in-out;
    }

    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .product-detail-image {
      flex: 1 1 350px;
      overflow: hidden;
      border-radius: 12px;
    }

    .product-detail-image img {
      width: 100%;
      transition: transform 0.4s;
      border-radius: 12px;
    }

    .product-detail-image:hover img {
      transform: scale(1.05);
    }

    .product-detail-info {
      flex: 1 1 300px;
    }

    .product-detail-info h2 {
      margin-top: 0;
      font-size: 2rem;
    }

    .price {
      font-size: 1.7rem;
      font-weight: bold;
      color: #e91e63;
      margin: 0.7rem 0;
    }

    .buy-btns {
      display: flex;
      gap: 1rem;
      margin: 1.5rem 0;
    }

    .buy-btns button {
      padding: 0.8rem 1.8rem;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      font-size: 1rem;
      font-weight: 600;
      transition: background 0.3s;
    }

    .add-to-cart {
      background: #212121;
      color: white;
    }

    .add-to-cart:hover {
      background: #333;
    }

    .buy-now {
      background: #ff5722;
      color: white;
    }

    .buy-now:hover {
      background: #e64a19;
    }

    /* Reviews */
    .reviews {
      max-width: 900px;
      margin: 3rem auto;
      padding: 2rem;
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .reviews h3 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }

    .review {
      background: #f9f9f9;
      padding: 1rem;
      border-radius: 10px;
      margin-bottom: 1rem;
    }

    .review strong {
      color: #333;
    }

    .star-rating {
      color: gold;
      font-size: 1rem;
    }

    body.dark-mode {
      background-color: #121212;
      color: #eee;
    }

    body.dark-mode .product-detail-container,
    body.dark-mode .reviews {
      background-color: #1e1e1e;
      color: #f1f1f1;
    }

    body.dark-mode .review {
      background-color: #2a2a2a;
    }
  </style>
</head>
<body>

<div class="product-detail-container">
  <div class="product-detail-image">
    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
  </div>
  <div class="product-detail-info">
    <h2><?= htmlspecialchars($product['name']) ?></h2>
    <p class="price">₹<?= htmlspecialchars($product['price']) ?></p>
    <p><?= htmlspecialchars($product['description'] ?? 'No description available.') ?></p>

    <form action="add_to_cart.php" method="POST">
      <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
      <input type="number" name="quantity" value="1" min="1" required>
      <div class="buy-btns">
        <button type="submit" class="add-to-cart">🛒 Add to Cart</button>
        <button type="submit" formaction="checkout.php" class="buy-now">⚡ Buy Now</button>
      </div>
    </form>
  </div>
</div>

<!-- Reviews Section -->
<div class="reviews">
  <h3>🗣️ Customer Reviews</h3>
  <?php if ($reviews->num_rows > 0): ?>
    <?php while ($review = $reviews->fetch_assoc()): ?>
      <div class="review">
        <strong><?= htmlspecialchars($review['username'] ?? 'Anonymous') ?></strong>
        <div class="star-rating"><?= str_repeat('★', $review['rating']) ?><?= str_repeat('☆', 5 - $review['rating']) ?></div>
        <p><?= htmlspecialchars($review['review_text'] ?? '') ?></p>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No reviews yet. Be the first to review!</p>
  <?php endif; ?>
</div>

</body>
</html>
