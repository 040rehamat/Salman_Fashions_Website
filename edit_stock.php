<?php
include 'db.php';
include 'header.php';
// Edit stock form
$product_id = $_GET['id'];
$query = "SELECT * FROM products WHERE id = '$product_id'";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_stock = $_POST['stock'];
    $update_query = "UPDATE products SET stock = '$new_stock' WHERE id = '$product_id'";
    mysqli_query($conn, $update_query);
    header("Location: admin_dashboard.php");
}
?>