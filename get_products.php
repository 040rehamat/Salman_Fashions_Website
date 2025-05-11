<?php
include 'db.php';
include 'header.php';




$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sql = "SELECT * FROM products WHERE 1";
$params = [];

if (!empty($search)) {
    $sql .= " AND name LIKE ?";
    $params[] = "%$search%";
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

// Count total
$countSql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
$stmt = $conn->prepare($countSql);
if (!empty($params)) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$countResult = $stmt->get_result()->fetch_assoc();
$totalResults = $countResult['total'];
$totalPages = ceil($totalResults / $limit);

// Final query with limit
$sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;

$stmt = $conn->prepare($sql);
$types = str_repeat("s", count($params) - 2) . "ii";
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = [
        'id' => $row['id'],
        'name' => $row['name'],
        'price' => $row['price'],
        'category' => $row['category'],
        'image' => $row['image'],
    ];
}

echo json_encode([
    'status' => 'success',
    'currentPage' => $page,
    'totalPages' => $totalPages,
    'totalResults' => $totalResults,
    'products' => $products
]);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>🛍 Salman Fashions – Products</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 20px;
      background: linear-gradient(120deg, #fdfbfb 0%, #ebedee 100%);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #222;
    }

    .filters {
      display: flex;
      justify-content: center;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 30px;
    }

    .filters select, .filters input, .filters button {
      padding: 10px 14px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 15px;
    }

    .filters button {
      background-color: #007bff;
      color: #fff;
      border: none;
      cursor: pointer;
      transition: 0.3s;
    }

    .filters button:hover {
      background-color: #0056b3;
    }

    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      max-width: 1200px;
      margin: auto;
    }

    .product-card {
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease;
    }

    .product-card:hover {
      transform: translateY(-5px);
    }

    .product-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
    }

    .info {
      padding: 15px;
      text-align: center;
    }

    .info h3 {
      margin: 10px 0 5px;
      font-size: 18px;
      color: #333;
    }

    .info p {
      margin: 4px 0;
      font-size: 15px;
      color: #666;
    }

    .pagination {
      text-align: center;
      margin-top: 30px;
    }

    .pagination button {
      padding: 10px 14px;
      margin: 0 5px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s;
    }

    .pagination button:disabled {
      background-color: #adb5bd;
      cursor: default;
    }

    @media (max-width: 600px) {
      .product-card img {
        height: 160px;
      }
    }
  </style>
</head>
<body>

  <h2>🛒 Stylish Products Just for You</h2>

  <div class="filters">
    <select id="category">
      <option value="">All Categories</option>
      <option value="men">Men</option>
      <option value="women">Women</option>
      <option value="footwear">Footwear</option>
    </select>
    <input type="text" id="search" placeholder="Search by product name">
    <button onclick="loadProducts()">Search</button>
  </div>

  <div class="product-grid" id="productGrid"></div>
  <div class="pagination" id="pagination"></div>

  <script>
    let currentPage = 1;

    function loadProducts(page = 1) {
      const category = document.getElementById("category").value;
      const search = document.getElementById("search").value;
      const url = `getproducts.php?page=${page}&category=${encodeURIComponent(category)}&search=${encodeURIComponent(search)}`;

      fetch(url)
        .then(res => res.json())
        .then(data => {
          currentPage = data.currentPage;
          const grid = document.getElementById("productGrid");
          const pagination = document.getElementById("pagination");

          grid.innerHTML = "";
          pagination.innerHTML = "";

          if (data.products.length === 0) {
            grid.innerHTML = "<p style='text-align:center; width:100%; font-size:18px;'>🚫 No products found.</p>";
            return;
          }

          data.products.forEach(p => {
            const card = document.createElement("div");
            card.className = "product-card";
            card.innerHTML = `
              <img src="${p.image}" alt="${p.name}">
              <div class="info">
                <h3>${p.name}</h3>
                <p>₹${p.price}</p>
                <p>${p.category}</p>
              </div>`;
            grid.appendChild(card);
          });

          for (let i = 1; i <= data.totalPages; i++) {
            const btn = document.createElement("button");
            btn.textContent = i;
            if (i === currentPage) btn.disabled = true;
            btn.onclick = () => loadProducts(i);
            pagination.appendChild(btn);
          }
        });
    }

    loadProducts();
  </script>

</body>
</html>
