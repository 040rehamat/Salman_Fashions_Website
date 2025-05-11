<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['wishlist_id'])) {
  header("Location: login.php");
  exit();
}

$wishlist_id = $_POST['wishlist_id'];
$conn->query("DELETE FROM wishlist WHERE id = $wishlist_id");

header("Location: wishlist.php");
exit();
