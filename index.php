
<?php
include 'db.php';
include 'header.php';

$category = $_GET['category'] ?? 'all';
$perPage = 8; // Number of products per page

// Get the current page from URL (default to page 1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

// Fetch total number of products
if ($category === 'all') {
    $sql_count = "SELECT COUNT(*) AS total FROM products";
    $stmt_count = $conn->prepare($sql_count);
} else {
    $sql_count = "SELECT COUNT(*) AS total FROM products WHERE category = ?";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("s", $category);
}
$stmt_count->execute();
$count_result = $stmt_count->get_result();
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $perPage);

// Fetch products with pagination
if ($category === 'all') {
    $sql = "SELECT * FROM products LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $offset, $perPage);
} else {
    $sql = "SELECT * FROM products WHERE category = ? LIMIT ?, ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $category, $offset, $perPage);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link rel="manifest" href="manifest.json" />

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Salman Fashions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <!-- Adding Google Font for Stylish Text -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      if (localStorage.getItem("mode") === "dark") {
        document.body.classList.add("dark-mode");
      }
    });

    function toggleDarkMode() {
      document.body.classList.toggle("dark-mode");
      localStorage.setItem("mode", document.body.classList.contains("dark-mode") ? "dark" : "light");
    }
  </script>

  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #f7ccf8;
      color: #0d0d0e;
      transition: background 0.3s, color 0.3s;
    }

    body.dark-mode {
      background-color: #121212;
      color: #f1f1f1;
    }

    header {
      background-color: #fc9cc4;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom-left-radius: 30px;
      border-bottom-right-radius: 30px;
    }

    header h1 {
      margin: 0;
      font-size: 2rem;
      font-weight: 600;
      letter-spacing: 1px;
    }

    nav {
      display: flex;
      gap: 1.5rem;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      transition: all 0.3s ease;
    }

    nav a:hover {
      background-color: #212121;
      color: #f1f1f1;
    }

    .menu-toggle {
      display: none;
      font-size: 1.5rem;
      color: white;
      cursor: pointer;
    }

    .search-bar {
      display: flex;
      justify-content: center;
      margin-top: 1.5rem;
    }

    .search-bar input {
      padding: 0.8rem;
      border-radius: 50px;
      border: none;
      width: 50%;
      margin-right: 0.5rem;
    }

    .search-bar button {
      padding: 0.8rem 1rem;
      border: none;
      background-color: #eea270;
      color: white;
      border-radius: 50px;
      cursor: pointer;
    }

    main {
      padding: 2rem;
      max-width: 1200px;
      margin: auto;
    }

    #hero {
      text-align: center;
      padding: 2rem;
      background: linear-gradient(135deg, #fadd87, #fa7b90);
      border-radius: 30px;
      color: white;
      margin-bottom: 2rem;
    }

    .categories {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 1rem;
      margin: 1rem 0;
    }

    .category {
      background-color: white;
      border-radius: 30px;
      padding: 0.7rem 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s ease;
      cursor: pointer;
      font-weight: 500;
    }
    .category.active {
  background-color: #212121;
  color: white;
  transform: scale(1.08);
}

    .category:hover {
      transform: scale(1.05);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .products {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-top: 1rem;
    }

    .product {
      background-color: white;
      padding: 1rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      text-align: center;
      transition: transform 0.3s ease;
    }

    .product:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .product img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      border-radius: 12px;
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
      border-radius: 30px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .product button:hover {
      background-color: #444;
    }

    input[type="number"] {
      width: 50px;
      padding: 4px;
      text-align: center;
      border-radius: 10px;
    }

    footer {
      text-align: center;
      padding: 1rem;
      background-color: #212121;
      color: white;
      margin-top: 2rem;
      border-top-left-radius: 30px;
      border-top-right-radius: 30px;
    }

    /* Dark Mode Specific Styles */
    .dark-mode .category {
      background-color: #1e1e1e;
      color: #f1f1f1;
    }

    .dark-mode .category:hover,
    .dark-mode .product:hover {
      background-color: #333;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    .dark-mode .product {
      background-color: #1e1e1e;
      color: #f1f1f1;
    }

    .dark-mode .search-bar input,
    .dark-mode .search-bar button {
      background-color: #333;
      color: #f1f1f1;
    }

    .dark-mode .search-bar button {
      background-color: #ff8a65;
    }

    /* Responsive Design for Mobile */
    @media (max-width: 768px) {
      nav {
        display: none;
        flex-direction: column;
        gap: 1rem;
        background: #fc9cc4;
        position: absolute;
        top: 60px;
        right: 10px;
        padding: 1rem;
        border-radius: 8px;
      }

      nav.active {
        display: flex;
      }

      .menu-toggle {
        display: block;
      }
    }
  </style>
</head>
<body>

<script>
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('sw.js').then(registration => {
      console.log('Service Worker registered with scope:', registration.scope);
    }).catch(error => {
      console.log('Service Worker registration failed:', error);
    });
  }
</script>

<main>
  <section id="hero">
    <h2>Style that speaks for you</h2>
    <p>Explore our latest fashion collection</p>
    <div class="search-bar">
      <form action="search.php" method="GET">
        <input type="text" name="query" placeholder="Search for clothes, accessories, shoes..." />
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
      </form>
    </div>
  </section>

  <section id="explore">
    <h3>🧭 Explore Categories</h3>
    <div class="categories">
      <div class="category" onclick="filterCategory('all')"><i class="fa-solid fa-border-all"></i> All</div>
      <div class="category" onclick="filterCategory('Clothing')"><i class="fa-solid fa-shirt"></i> Clothing</div>
      <div class="category" onclick="filterCategory('Accessories')"><i class="fa-solid fa-gem"></i> Accessories</div>
      <div class="category" onclick="filterCategory('Footwear')"><i class="fa-solid fa-shoe-prints"></i> Footwear</div>
      <div class="category" onclick="filterCategory('Kids')"><i class="fa-solid fa-child-reaching"></i> Kids</div>
      <div class="category" onclick="filterCategory('Men')"><i class="fa-solid fa-user-tie"></i> Men</div>
      <div class="category" onclick="filterCategory('Women')"><i class="fa-solid fa-user-tie"></i> Women</div>
      </div>

      <div class="products">
  <?php
    if ($result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
        $product_id = $row['id'];

        $review_sql = "SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC";
        $review_stmt = $conn->prepare($review_sql);
        $review_stmt->bind_param("i", $product_id);
        $review_stmt->execute();
        $review_result = $review_stmt->get_result();
  ?>
    <div class="product">
      <a href="product.php?id=<?= $row['id'] ?>" class="product-link">
        <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" />
        <h4><?= htmlspecialchars($row['name']) ?></h4>
        <p>₹<?= htmlspecialchars($row['price']) ?></p>

        <div class="reviews">
          <h5>Reviews:</h5>
          <?php if ($review_result->num_rows > 0): ?>
            <?php while ($review = $review_result->fetch_assoc()): ?>
              <div class="review">
                <p><strong><?= htmlspecialchars($review['username'] ?? 'Anonymous') ?></strong> (<?= $review['rating'] ?>/5)</p>
                <p><?= htmlspecialchars($review['review_text'] ?? '') ?></p>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No reviews yet.</p>
          <?php endif; ?>
        </div>
      </a>

      <!-- Add to Cart Form -->
      <form action="add_to_cart.php" method="POST" class="add-to-cart">
        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
        <input type="number" name="quantity" value="1" min="1" required>
        <button type="submit">Add to Cart</button>
      </form>
    </div>
  <?php endwhile; else: ?>
    <p>No products available in this category.</p>
  <?php endif; ?>
</div>

     

     

  </section>
</main>

<footer>

  <p>&copy; 2025 Salman Fashions | All rights reserved.</p>
</footer>

<script>
  function toggleMenu() {
    document.getElementById("navbar").classList.toggle("active");
  }

  function filterCategory(category) {
    const url = new URL(window.location.href);
    url.searchParams.set("category", category);
    window.location.href = url;
  }

  document.addEventListener("DOMContentLoaded", () => {
    // Apply active class to selected category
    const urlParams = new URLSearchParams(window.location.search);
    const selectedCategory = urlParams.get("category") || "all";

    document.querySelectorAll(".category").forEach(cat => {
      if (cat.textContent.trim().toLowerCase().includes(selectedCategory.toLowerCase())) {
        cat.classList.add("active");
      }
    });
  });
</script>

</body>
</html>
