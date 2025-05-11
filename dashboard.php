<?php
ob_start();
include 'db.php';
include 'header.php';

// Fetch stats
$orders = $conn->query("SELECT COUNT(*) as total FROM orders")->fetch_assoc()['total'];
$users = $conn->query("SELECT COUNT(*) as total FROM admins")->fetch_assoc()['total'];
$products = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];

// Fetch orders and products
$ordersResult = $conn->query("SELECT o.id, o.user_id, o.product_id, o.quantity, o.payment_method, o.payment_id, o.address, o.contact, o.status, o.order_date, p.name AS product_name FROM orders o JOIN products p ON o.product_id = p.id ORDER BY o.order_date DESC");
$productsResult = $conn->query("SELECT * FROM products");
// Example: Get all products and display in table
$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);




?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - Salman Fashions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body>


<h2>📊 Admin Dashboard</h2>

<!-- Dashboard Stats -->
<div class="stats">
  <div class="card"><h3>Orders</h3><p><?= $orders ?></p></div>
  <div class="card"><h3>Users</h3><p><?= $users ?></p></div>
  <div class="card"><h3>Products</h3><p><?= $products ?></p></div>
</div>

<!-- Order Table -->
<h2>📦 All Orders</h2>
<table class="table">
  <tr>
    <th>Order ID</th><th>User ID</th><th>Product</th><th>Qty</th><th>Method</th>
    <th>Transaction/Reference_id</th><th>Address</th><th>Contact</th><th>Status</th><th>Date</th><th>Action</th>
  </tr>
  <?php while ($row = $ordersResult->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= $row['user_id'] ?></td>
      <td><?= $row['product_name'] ?></td>
      <td><?= $row['quantity'] ?></td>
      <td><?= $row['payment_method'] ?></td>
      <td><?= $row['payment_id'] ?></td>
      <td><?= $row['address'] ?></td>
      <td><?= $row['contact'] ?></td>
      <td><?= $row['status'] ?></td>
      <td><?= date('d M Y', strtotime($row['order_date'])) ?></td>
      <td>
        <form action="update_order_status.php" method="POST">
          <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
          <select name="status">
            <option <?= $row['status']=='Pending'?'selected':'' ?>>Pending</option>
            <option <?= $row['status']=='Shipped'?'selected':'' ?>>Shipped</option>
            <option <?= $row['status']=='Delivered'?'selected':'' ?>>Delivered</option>
          </select>
          <button type="submit">Update</button>
        </form>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

<!-- Product List -->
<h2>🛍️ Product List</h2>
<table class="table">
  <tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Actions</th></tr>
  <?php while($row = $productsResult->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['id']) ?></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td><?= htmlspecialchars($row['category']) ?></td>
      <td>₹<?= htmlspecialchars($row['price']) ?></td>
      <td>
      <button class="delete-btn" data-id="<?= $row['id'] ?>">Delete</button>

        <form method="GET" action="edit_product.php" style="display:inline;">
          <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
          <input type="submit" value="Edit" class="edit-btn">
        </form>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

<!-- Add Product Form -->
<div class="add-product">
  <h3>Add New Product</h3>
  <form action="add_products.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Product Name" required>
    <input type="text" name="category" placeholder="Category" required>
    <input name="description" placeholder="Description" required>
    <input type="number" name="price" placeholder="Price" required>
    <input type="file" name="image" required>
    <button type="submit">Add Product</button>
  </form>
</div>

<a href="?logout=true" class="logout-btn">Logout</a>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="admin.js"></script>

<?php
if (isset($_GET['logout'])) {
  session_destroy();
  header("Location: admin_login.php");
  exit();
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.dataset.id;
      const row = button.closest('tr');

      Swal.fire({
        title: 'Are you sure?',
        text: 'This product will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
      }).then(result => {
        if (result.isConfirmed) {
          fetch('delete_product.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'product_id=' + encodeURIComponent(id)
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              row.style.transition = 'opacity 0.5s ease';
              row.style.opacity = '0';
              setTimeout(() => row.remove(), 500);

              Swal.fire('Deleted!', 'Product has been removed.', 'success');
            } else {
              Swal.fire('Error!', data.message || 'Delete failed.', 'error');
              console.error(data.error);
            }
          })
          .catch(err => {
            Swal.fire('Error!', 'Something went wrong!', 'error');
            console.error('Fetch error:', err);
          });
        }
      });
    });
  });
});

</script>

</body>
</html>
<style>
body, html {
  margin: 0;
  padding: 0;
  font-family: 'Poppins', sans-serif;
  transition: background 0.3s, color 0.3s;
}
body.light-mode {
  background-color: #ffffff;
  color: #333;
}
body.dark-mode {
  background-color: #121212;
  color: #f1f1f1;
}

h2, h3 {
  text-align: center;
  margin: 1.5rem 0;
}
.toggle-btn {
  position: fixed;
  top: 20px;
  right: 20px;
  background-color: #007bff;
  color: white;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
}

/* Dashboard Cards */
.stats {
  display: flex;
  justify-content: center;
  gap: 2rem;
  flex-wrap: wrap;
  margin: 2rem;
}
.card {
  background: #f7f7f7;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  text-align: center;
  min-width: 180px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
body.dark-mode .card {
  background: #1f1f1f;
}
.card:hover {
  transform: translateY(-5px);
}

/* Tables */
.table {
  width: 90%;
  margin: 0 auto 3rem;
  border-collapse: collapse;
  background: #fff;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.table th, .table td {
  padding: 12px;
  border: 1px solid #ddd;
  text-align: center;
}
.table th {
  background: #343a40;
  color: white;
}
.table tr:nth-child(even) {
  background: #f9f9f9;
}
body.dark-mode .table tr:nth-child(even) {
  background: #2e2e2e;
}

/* Form */
.add-product {
  width: 80%;
  margin: 2rem auto;
  background: #f9f9f9;
  padding: 1.5rem;
  border-radius: 8px;
}
.add-product input, .add-product select {
  width: 100%;
  padding: 8px;
  margin: 8px 0;
}
.add-product button {
  background: #4CAF50;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
}

/* Buttons */
.edit-btn, .delete-btn {
  padding: 6px 12px;
  border: none;
  color: white;
  cursor: pointer;
  border-radius: 4px;
}
.edit-btn {
  background: #007bff;
}
.delete-btn {
  background: #dc3545;
}
.logout-btn {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background: #ff4d4d;
  color: white;
  padding: 10px 20px;
  border-radius: 5px;
  text-decoration: none;
}
</style>
<script>
  // Theme toggle
document.addEventListener('DOMContentLoaded', () => {
  const toggleButton = document.getElementById('toggleButton');
  const theme = localStorage.getItem('theme') || 'light';
  document.body.classList.add(theme + '-mode');
  toggleButton.innerText = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';

  toggleButton.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    document.body.classList.toggle('light-mode');
    const newTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
    localStorage.setItem('theme', newTheme);
    toggleButton.innerText = newTheme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
  });
});

// SweetAlert delete confirmation
