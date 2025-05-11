<?php
// Start session if not already
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

header('Content-Type: text/html; charset=UTF-8');
include 'db.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add to Cart - Salman Fashions</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f7fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .message {
      padding: 2rem 3rem;
      background: white;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
      text-align: center;
    }
    .message h2 {
      color: #333;
    }
  </style>
</head>
<body>
<div class="message">
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Use prepared statement for safety
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);

    if ($stmt->execute()) {
      echo "<script>
        Swal.fire({
          icon: 'success',
          title: 'Added to Cart!',
          text: 'Product successfully added.',
          confirmButtonColor: '#007bff'
        }).then(() => window.history.back());
      </script>";
    } else {
      echo "<script>
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: 'Something went wrong while adding to cart.',
          confirmButtonColor: '#dc3545'
        }).then(() => window.history.back());
      </script>";
    }

  } else {
    echo "<script>
      Swal.fire({
        icon: 'warning',
        title: 'Login Required',
        text: 'Please log in to add items to your cart.',
        confirmButtonColor: '#ffc107'
      }).then(() => window.location.href = 'login.html');
    </script>";
  }
}
?>
</div>
</body>
</html>
