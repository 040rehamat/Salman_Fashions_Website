<?php
session_start();
include 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($product_id <= 0) {
    echo "Invalid product ID.";
    exit;
}

$product = $conn->query("SELECT name, image FROM products WHERE id = $product_id")->fetch_assoc();

if (!$product) {
    echo "Product not found in database.";
    exit;
}



$existing = $conn->query("SELECT * FROM reviews WHERE user_id=$user_id AND product_id=$product_id")->fetch_assoc();
$rating = $existing['rating'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Leave a Review - Salman Fashions</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #f1f1f1;
      color: #333;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
      padding: 40px 15px;
      transition: background 0.3s, color 0.3s;
    }
   
    .review-box {
      background: #ffffff;
      padding: 35px;
      border-radius: 15px;
      box-shadow: 0 15px 30px rgba(0,0,0,0.08);
      max-width: 550px;
      width: 100%;
      animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .product-info {
      text-align: center;
      margin-bottom: 25px;
    }
    .product-info img {
      max-height: 140px;
      border-radius: 12px;
      margin-bottom: 10px;
    }
    .product-info h3 {
      font-size: 22px;
      margin: 0;
    }
    .stars {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin: 20px 0;
      font-size: 26px;
    }
    .stars i {
      color: #ccc;
      cursor: pointer;
      transition: color 0.3s;
    }
    .stars i.selected {
      color: #f7b731;
    }
    textarea {
      width: 100%;
      padding: 14px;
      font-size: 15px;
      border-radius: 10px;
      border: 1px solid #ccc;
      margin-bottom: 20px;
      resize: none;
      font-family: 'Poppins', sans-serif;
    }
    button {
      background: #007bff;
      color: white;
      padding: 12px;
      width: 100%;
      font-size: 16px;
      font-weight: 500;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background 0.3s;
    }
    button:hover {
      background: #0056b3;
    }
    .edit-delete {
      text-align: center;
      margin-top: 20px;
    }
    .edit-delete button {
      margin: 6px;
      padding: 10px 18px;
      font-size: 14px;
      font-weight: 500;
      border-radius: 8px;
      border: none;
      cursor: pointer;
    }
    .edit {
      background: #ffc107;
      color: #333;
    }
    .edit:hover {
      background: #e0a800;
    }
    .delete {
      background: #dc3545;
      color: white;
    }
    .delete:hover {
      background: #c82333;
    }

    /* DARK MODE SUPPORT */
    @media (prefers-color-scheme: dark) {
      body {
        background: #121212;
        color: #ddd;
      }
      .review-box {
        background: #1f1f1f;
        box-shadow: 0 15px 30px rgba(255, 255, 255, 0.05);
      }
      textarea {
        background: #2a2a2a;
        border-color: #444;
        color: #fff;
      }
      .stars i {
        color: #555;
      }
      .stars i.selected {
        color: #ffcd3c;
      }
    }
  </style>
</head>
<body>
<br><br>

<div class="review-box">
  <div class="product-info">
    <img src="uploads/<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
    <h3><?= htmlspecialchars($product['name']) ?></h3>
  </div>

  <form id="reviewForm">
    <div class="stars" id="starRating">
      <?php
        for ($i = 1; $i <= 5; $i++) {
          $class = ($i <= $rating) ? 'fas fa-star selected' : 'far fa-star';
          echo "<i class='$class' data-value='$i'></i>";
        }
      ?>
    </div>

    <textarea name="review" rows="4" placeholder="Write your honest opinion..." required><?= htmlspecialchars($existing['review'] ?? '') ?></textarea>

    <input type="hidden" name="rating" value="<?= $rating ?>">
    <input type="hidden" name="product_id" value="<?= $product_id ?>">
    <input type="hidden" name="edit_mode" value="<?= $existing ? 1 : 0 ?>">

    <button type="submit"><?= $existing ? 'Update Review' : 'Submit Review' ?></button>

    <?php if ($existing): ?>
    <div class="edit-delete">
      <button type="button" class="edit" onclick="resetForm()">Edit Again</button>
      <button type="button" class="delete" onclick="deleteReview()">Delete</button>
    </div>
    <?php endif; ?>
  </form>
</div>

<script>
  const stars = document.querySelectorAll('#starRating i');
  const ratingInput = document.querySelector('input[name="rating"]');

  stars.forEach(star => {
    star.addEventListener('click', () => {
      const value = star.dataset.value;
      ratingInput.value = value;
      stars.forEach((s, i) => {
        s.classList.remove('selected', 'fas');
        s.classList.add('far');
        if (i < value) {
          s.classList.add('selected', 'fas');
          s.classList.remove('far');
        }
      });
    });
  });

  document.getElementById('reviewForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('review_handler.php', {
      method: 'POST',
      body: formData
    }).then(res => res.text()).then(data => {
      alert(data);
      location.reload();
    });
  });

  function resetForm() {
    document.querySelector('textarea').focus();
  }

  function deleteReview() {
    if (confirm("Delete your review?")) {
      fetch('delete_review.php?product_id=<?= $product_id ?>')
        .then(res => res.text())
        .then(data => {
          alert(data);
          location.reload();
        });
    }
  }
</script>

</body>
</html>
