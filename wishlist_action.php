<?php
session_start();
include "db.php";
if (!isset($_SESSION['user_id'])) exit;

$userId = $_SESSION['user_id'];
$productId = intval($_POST['product_id']);

// Toggle wishlist
$check = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_id=$userId AND product_id=$productId");
if (mysqli_num_rows($check) > 0) {
  mysqli_query($conn, "DELETE FROM wishlist WHERE user_id=$userId AND product_id=$productId");
  echo 'removed';
} else {
  mysqli_query($conn, "INSERT INTO wishlist(user_id, product_id) VALUES($userId, $productId)");
  echo 'added';
}
?>
