<?php
session_start();
include 'db.php';

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
  echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
  exit;
}

// Validate POST data
if (!isset($_POST['id'], $_POST['name'], $_POST['category'], $_POST['description'], $_POST['price'])) {
  echo json_encode(['status' => 'error', 'message' => 'Missing fields']);
  exit;
}

$id = intval($_POST['id']);
$name = trim($_POST['name']);
$category = trim($_POST['category']);
$description = trim($_POST['description']);
$price = floatval($_POST['price']);

$imagePath = null;

// Handle file upload if image is sent
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
  $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
  $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

  if (!in_array($ext, $allowed)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid image format']);
    exit;
  }

  $newName = 'product_' . time() . '.' . $ext;
  $uploadDir = 'uploads/';
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  $uploadPath = $uploadDir . $newName;

  if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
    $imagePath = $uploadPath;
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Image upload failed']);
    exit;
  }
}

// Update query
if ($imagePath) {
  $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, description = ?, price = ?, image = ? WHERE id = ?");
  $stmt->bind_param("sssssi", $name, $category, $description, $price, $imagePath, $id);
} else {
  $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, description = ?, price = ? WHERE id = ?");
  $stmt->bind_param("ssssi", $name, $category, $description, $price, $id);
}

if ($stmt->execute()) {
  echo json_encode(['status' => 'success', 'message' => 'Product updated']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to update']);
}
