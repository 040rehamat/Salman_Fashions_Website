<?php
include 'db.php';
$id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// Get reviews
$reviews_stmt = $conn->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
$reviews_stmt->bind_param("i", $id);
$reviews_stmt->execute();
$reviews = $reviews_stmt->get_result();
?>

<div class="gallery">
  <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" style="width:100%; border-radius: 12px;" />
</div>

<h2><?= htmlspecialchars($product['name']) ?></h2>
<p><strong>Price:</strong> ₹<?= $product['price'] ?></p>
<p><?= htmlspecialchars($product['description'] ?? 'No description available.') ?></p>

<!-- Star Rating -->
<p><strong>Rate this product:</strong></p>
<form action="submit_review.php" method="POST">
  <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
  <input type="text" name="username" placeholder="Your name" required>
  <textarea name="review_text" placeholder="Your review" required></textarea>
  <select name="rating" required>
    <option value="">Rate</option>
    <?php for ($i = 1; $i <= 5; $i++): ?>
      <option value="<?= $i ?>"><?= str_repeat("★", $i) ?></option>
    <?php endfor; ?>
  </select>
  <button type="submit">Submit Review</button>
</form>

<!-- Show Reviews -->
<h4>Reviews:</h4>
<?php if ($reviews->num_rows > 0): ?>
  <?php while($review = $reviews->fetch_assoc()): ?>
    <p><strong><?= htmlspecialchars($review['username']) ?>:</strong>
    <?= str_repeat("★", $review['rating']) ?> - <?= htmlspecialchars($review['review_text']) ?></p>
  <?php endwhile; ?>
<?php else: ?>
  <p>No reviews yet.</p>
<?php endif; ?>

<!-- Add to Cart -->
<form action="add_to_cart.php" method="POST">
  <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
  <input type="number" name="quantity" value="1" min="1" required>
  <button type="submit">Add to Cart</button>
</form>

<!-- Buy Now -->
<form action="checkout.php" method="POST" style="margin-top: 1rem;">
  <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
  <input type="hidden" name="quantity" value="1" />
  <button type="submit">Buy Now</button>
</form>
