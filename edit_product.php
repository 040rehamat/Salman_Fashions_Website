<?php
ob_start();
session_start();
include 'db.php';
include 'header.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

function safe_htmlspecialchars($value) {
    return htmlspecialchars(is_null($value) ? '' : (string)$value, ENT_QUOTES, 'UTF-8');
}

if (!isset($_GET['product_id'])) {
    die("Invalid request.");
}

$id = $_GET['product_id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Product - Salman Fashions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f9f9f9;
      padding: 2rem;
      color: #333;
      transition: background 0.3s, color 0.3s;
    }
    body.dark-mode {
      background-color: #121212;
      color: #f1f1f1;
    }
    .container {
      background: #fff;
      padding: 2rem;
      max-width: 600px;
      margin: auto;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    body.dark-mode .container {
      background: #1e1e1e;
    }
    h2 {
      text-align: center;
      margin-bottom: 1rem;
    }
    input, textarea {
      width: 100%;
      padding: 12px;
      margin: 0.75rem 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      background-color: #fafafa;
      transition: all 0.3s ease;
    }
    body.dark-mode input, body.dark-mode textarea {
      background: #2c2c2c;
      color: white;
      border-color: #444;
    }
    button {
      background: #007bff;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s ease;
      display: block;
      width: 100%;
      margin-top: 1rem;
    }
    button:hover {
      background: #0056b3;
    }
    #drop-area {
      padding: 1rem;
      text-align: center;
      border: 2px dashed #aaa;
      margin-top: 1rem;
      border-radius: 10px;
      background-color: #f1f1f1;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    #drop-area:hover {
      background-color: #e2e2e2;
    }
    label[for="fileElem"] {
      display: inline-block;
      margin-top: 0.5rem;
      background-color: #007bff;
      color: white;
      padding: 8px 16px;
      border-radius: 6px;
      cursor: pointer;
    }
    label[for="fileElem"]:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body class="<?= isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] == 'true' ? 'dark-mode' : '' ?>">

<div class="container">
  <h2>Edit Product</h2>
  <?php if (!$product): ?>
    <p style="color: red; text-align: center;">Product not found.</p>
  <?php else: ?>
  <form id="editForm" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= safe_htmlspecialchars($product['id']) ?>">
    <input type="text" name="name" value="<?= safe_htmlspecialchars($product['name']) ?>" required placeholder="Product Name">
    <input type="text" name="category" value="<?= safe_htmlspecialchars($product['category']) ?>" required placeholder="Category">
    <textarea name="description" required placeholder="Description"><?= safe_htmlspecialchars($product['description']) ?></textarea>
    <input type="number" name="price" value="<?= safe_htmlspecialchars($product['price']) ?>" required placeholder="Price">

    <div id="drop-area">
      <p>Drag & Drop Image or Click to Upload</p>
      <input type="file" name="image" id="fileElem" accept="image/*" style="display:none">
      <label for="fileElem">Choose Image</label>
    </div>

    <button type="submit">Save Changes</button>
  </form>
  <?php endif; ?>
</div>

<script>
  $('#editForm').submit(function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
      url: 'update_product.php',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res) {
        Swal.fire('Success!', 'Product updated successfully.', 'success');
      },
      error: function() {
        Swal.fire('Error', 'Failed to update product.', 'error');
      }
    });
  });

  const dropArea = document.getElementById("drop-area");
  const fileInput = document.getElementById("fileElem");

  dropArea.addEventListener("dragover", e => {
    e.preventDefault();
    dropArea.style.background = "#ddd";
  });

  dropArea.addEventListener("dragleave", () => {
    dropArea.style.background = "";
  });

  dropArea.addEventListener("drop", e => {
    e.preventDefault();
    fileInput.files = e.dataTransfer.files;
    dropArea.style.background = "";
  });

  dropArea.addEventListener("click", () => {
    fileInput.click();
  });
</script>
</body>
</html>
