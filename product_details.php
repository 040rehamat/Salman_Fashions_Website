<?php
// After displaying product info
include 'db.php';
include 'header.php';
 $stmt = $conn->prepare("SELECT r.rating, r.review, u.name AS username, r.created_at 
                        FROM reviews r 
                        JOIN users u ON r.user_id = u.id 
                        WHERE r.product_id = ?");

$stmt->bind_param("i", $product_id);
$stmt->execute();
$reviews = $stmt->get_result();
?>

<h3>📝 Product Reviews</h3>
<?php if ($reviews->num_rows > 0): ?>
    <?php while ($r = $reviews->fetch_assoc()): ?>
        <div style="margin-bottom:10px;">
            <strong><?= htmlspecialchars($r['username']) ?></strong> 
            <span>⭐ <?= $r['rating'] ?>/5</span><br>
            <em><?= htmlspecialchars($r['review']) ?></em><br>
            <small><?= date('d M Y', strtotime($r['created_at'])) ?></small>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No reviews yet.</p>
<?php endif; ?>
