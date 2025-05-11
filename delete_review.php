<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$product_id = $_GET['product_id'];

$conn->query("DELETE FROM reviews WHERE user_id=$user_id AND product_id=$product_id");
echo "Review deleted.";
