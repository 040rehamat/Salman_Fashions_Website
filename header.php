<?php

$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Salman Fashions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
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
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    header h1 {
      margin: 0;
      font-size: 2rem;
      font-weight: 600;
      letter-spacing: 1px;
    }
    nav {
      display: flex;
      gap: 1.2rem;
      align-items: center;
    }
    nav a {
      color: white;
      text-decoration: none;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.5rem 1rem;
      border-radius: 20px;
      transition: all 0.3s ease;
    }
    nav a:hover {
      background-color: #212121;
      color: #f1f1f1;
    }

    .auth-menu {
      position: relative;
    }

    .auth-toggle {
      font-size: 1.8rem;
      color: white;
      cursor: pointer;
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 50%;
      padding: 10px;
      background-color: #fc9cc4;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      transition: all 0.3s ease;
    }

    .auth-toggle:hover {
      background-color: #f37b99;
    }

    .auth-dropdown {
      display: none;
      position: absolute;
      top: 45px;
      right: 0;
      background-color: #fc9cc4;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      flex-direction: column;
      min-width: 180px;
      z-index: 999;
    }

    .auth-dropdown a {
      padding: 0.7rem 1rem;
      color: white;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
      font-weight: 500;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .auth-dropdown a:last-child {
      border-bottom: none;
    }

    .auth-dropdown a:hover {
      background-color: #212121;
    }

    .auth-dropdown.show {
      display: flex;
    }
  </style>
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

    function toggleAuthMenu() {
      document.getElementById("authDropdown").classList.toggle("show");
    }

    window.onclick = function(e) {
      if (!e.target.closest(".auth-menu")) {
        document.getElementById("authDropdown")?.classList.remove("show");
      }
    }
  </script>
</head>
<body>

<header>
  <h1><i class="fa-solid fa-shirt"></i> Salman Fashions</h1>

  <nav>
    <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
    <a href="checkout.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
    

    <a href="my_orders.php"><i class="fa-solid fa-receipt"></i> Orders</a>
    <a href="transaction.php"><i class="fa-solid fa-receipt"></i> Transactions</a>
    <a href="#" onclick="toggleDarkMode()"><i class="fa-solid fa-moon"></i> Mode</a>

    <!-- Auth Dropdown -->
    <div class="auth-menu">
      <div class="auth-toggle" onclick="toggleAuthMenu()">
        <i class="fa-solid fa-user"></i>
      </div>
      <div class="auth-dropdown" id="authDropdown">
        <?php if ($isLoggedIn): ?>
          <a href="log_out.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        <?php else: ?>
          <a href="admin_login.php"><i class="fa-solid fa-gauge"></i>Admin Dashboard</a>


          <a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
          <a href="sign_up.php"><i class="fa-solid fa-user-plus"></i> Sign Up</a>
          <a href="log_out.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
  
</header>

</body>
</html>
