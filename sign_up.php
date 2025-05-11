<?php
include 'db.php';
include 'header.php'; // Include navbar/header

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Please fill in all fields!";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Use email as a default username if the username field is not needed
        $username = $email; // Using email as username

        // Prepare and execute insert
        $stmt = $conn->prepare("INSERT INTO users ( name, email, password) VALUES ( ?, ?, ?)");
        $stmt->bind_param("sss",  $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $success_message = "Registration successful! You can now log in.";
        } else {
            $error_message = "Registration failed. Email might already exist.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign Up - Salman Fashions</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f7ccf8;
      margin: 0;
      padding: 0;
    }

    .signup-wrapper {
      display: flex;
      justify-content: center;
      padding: 80px 20px 50px;
    }

    .container {
      background-color: #fff;
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 420px;
    }

    h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #333;
    }

    .form-group {
      margin-bottom: 1.2rem;
    }

    .form-group label {
      display: block;
      font-weight: bold;
      margin-bottom: 0.4rem;
    }

    .form-group input {
      width: 100%;
      padding: 0.8rem;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 1rem;
    }

    .form-group button {
      width: 100%;
      padding: 0.9rem;
      background-color: #eea270;
      color: white;
      border: none;
      border-radius: 5px;
      font-size: 1.05rem;
      cursor: pointer;
    }

    .form-group button:hover {
      background-color: #e7883d;
    }

    .message {
      text-align: center;
      margin-top: 1rem;
      color: red;
    }

    .success-message {
      color: green;
    }

    .redirect {
      text-align: center;
      margin-top: 1.2rem;
    }

    .redirect a {
      color: #eea270;
      text-decoration: none;
      font-weight: bold;
    }

    .redirect a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="signup-wrapper">
    <div class="container">
      <h2>Create Your Account</h2>

      <?php if (isset($error_message)): ?>
        <p class="message"><?= $error_message ?></p>
      <?php elseif (isset($success_message)): ?>
        <p class="success-message"><?= $success_message ?></p>
        <div class="redirect">
          <a href="login.php">Log in now</a>
        </div>
      <?php endif; ?>

      <form action="sign_up.php" method="POST">
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" name="name" required>
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <div class="form-group">
          <button type="submit">Sign Up</button>
        </div>
      </form>

      <div class="redirect">
        <p>Already have an account? <a href="login.php">Log In</a></p>
      </div>
    </div>
  </div>
</body>
</html>
