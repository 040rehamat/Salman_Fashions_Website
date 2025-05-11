<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';
include 'header.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Search Results - Salman Fashions</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f4f4f4;
      color: #333;
    }

    header {
      background-color: rgb(252, 156, 196);
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header a {
      color: white;
      text-decoration: none;
      margin-left: 1rem;
    }

    main {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 1rem;
    }

    .products {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }

    .product {
      background: white;
      padding: 1rem;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      text-align: center;
    }

    .product img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      border-radius: 8px;
    }

    .product h4 {
      margin: 1rem 0 0.5rem;
    }

    .product p {
      font-weight: bold;
      color: #444;
    }

    .product button {
      padding: 0.5rem 1rem;
      background-color: #212121;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    footer {
      text-align: center;
      padding: 1rem;
      background-color: #212121;
      color: white;
      margin-top: 2rem;
    }
  </style>
</head>
<body>



<main>
  <h2>Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
  <?php
  if ($query !== '') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
    $search = "%" . $query . "%";
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
          echo "<div class='products'>";
          while ($row = $result->fetch_assoc()) {
              echo "<div class='product'>";
              echo "<img src='{$row['image']}' alt='{$row['name']}' />";
              echo "<h4>{$row['name']}</h4>";
              echo "<p>₹{$row['price']}</p>";
              echo "<form action='add_to_cart.php' method='POST'>";
              echo "<input type='hidden' name='product_id' value='{$row['id']}' />";
              echo "<input type='number' name='quantity' value='1' min='1' required>";
              echo "<button type='submit'>Add to Cart</button>";
              echo "</form>";
              echo "</div>";
          }
          echo "</div>";
      } else {
          echo "<p>No results found. Try different keywords.</p>";
      }

      $stmt->close();
  } else {
      echo "<p>Please enter a search term.</p>";
  }

  $conn->close();
  ?>
</main>

<footer>
  <p>&copy; <?php echo date("Y"); ?> Salman Fashions. All rights reserved.</p>
</footer>

</body>
</html>
