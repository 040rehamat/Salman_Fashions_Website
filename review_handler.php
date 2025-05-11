<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$rating = $_POST['rating'];
$review = $_POST['review'];
$edit_mode = $_POST['edit_mode'];

if ($edit_mode) {
    $stmt = $conn->prepare("UPDATE reviews SET rating=?, review=? WHERE user_id=? AND product_id=?");
    $stmt->bind_param("isii", $rating, $review, $user_id, $product_id);
} else {
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, review) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $product_id, $rating, $review);
}

$stmt->execute();
$stmt->close();
echo "Review saved!";
