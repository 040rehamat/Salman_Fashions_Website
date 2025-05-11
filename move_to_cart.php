<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['product_id'])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

// Add to cart
$conn->query("INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)");

// Remove from wishlist
$conn->query("DELETE FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");

header("Location: wishlist.php");
exit();
