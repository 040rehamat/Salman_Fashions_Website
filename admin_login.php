<?php
ob_start();


session_start();
include 'db.php';
include 'header.php';
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validate the admin credentials
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Verify password (assuming passwords are hashed)
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];  // Set session to keep track of the logged-in admin
            header("Location: dashboard.php"); // Redirect to the dashboard
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "No admin found with this username.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Salman Fashions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            color: #333;
        }

        

        /* Login Container Styling */
        .login-container {
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 30px;
            text-align: center;
            margin-top: 40px; /* Space below the header */
            margin-left: auto;
            margin-right: auto;
        }

        .login-container h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #333;
            font-weight: 600;
        }

        .login-container label {
            display: block;
            text-align: left;
            margin: 12px 0 8px;
            font-size: 1rem;
            color: #555;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .login-container input[type="text"]:focus,
        .login-container input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }

        .login-container input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 12px;
            font-size: 1rem;
            border: none;
            border-radius: 6px;
            width: 100%;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .login-container input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error {
            color: #ff4d6d;
            margin-top: 15px;
            font-size: 0.9rem;
        }

        .login-container .icon {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 20px;
        }

    </style>
</head>
<body>

<!-- Header Section -->


<!-- Login Form -->
<div class="login-container">
    <div class="icon">
        <i class="fa-solid fa-right-to-bracket"></i>
    </div>
    <h2>Admin Login</h2>
    <form method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <input type="submit" value="Login">
    </form>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

</div>

</body>
<?php ob_end_flush(); ?>

</html>
