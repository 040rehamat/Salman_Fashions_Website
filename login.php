<?php
session_start();
include 'db.php'; // Make sure this connects correctly

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $login_input = trim($_POST['login_input']);
  $password = $_POST['password'];

  // Check DB for username or email match
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR name = ?");
  $stmt->bind_param("ss", $login_input, $login_input);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Use password_verify() if passwords are hashed
    if (password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['name'] = $user['name'];

      header("Location: index.php");
      exit();
    } else {
      $error = "Invalid password.";
    }
  } else {
    $error = "No account found with that name or email.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Salman Fashions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #fc9cc4, #f7ccf8);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }
    .login-container {
      background-color: white;
      padding: 2rem 2.5rem;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      max-width: 400px;
      width: 100%;
    }
    .login-container h2 {
      text-align: center;
      color: #fc3c91;
      margin-bottom: 1.5rem;
    }
    label {
      font-weight: 500;
      color: #333;
      display: block;
      margin-bottom: 0.5rem;
    }
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 0.75rem 1rem;
      margin-bottom: 1.2rem;
      border: 1px solid #ccc;
      border-radius: 12px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }
    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #fc3c91;
      outline: none;
    }
    input[type="submit"] {
      width: 100%;
      padding: 0.75rem;
      background-color: #fc3c91;
      color: white;
      font-size: 1rem;
      font-weight: 600;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    input[type="submit"]:hover {
      background-color: #e2307d;
    }
    .error {
      color: red;
      margin-top: 1rem;
      text-align: center;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 1rem;
      text-decoration: none;
      color: #555;
      font-size: 0.9rem;
    }
    .back-link:hover {
      color: #000;
    }
  </style>
</head>
<body>

<div class="login-container">
  <h2><i class="fa-solid fa-right-to-bracket"></i> User Login</h2>
  <form method="POST">
    <label for="login_input">Name or Email</label>
    <input type="text" id="login_input" name="login_input" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <input type="submit" value="Login">
  </form>

  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

  <a href="sign_up.php" class="back-link">Don't have an account? Sign up</a>
</div>

</body>
</html>
