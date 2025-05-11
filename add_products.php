<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input to prevent SQL injection
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $cat = mysqli_real_escape_string($conn, $_POST['category']);

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = $_FILES['image']['name'];
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageSize = $_FILES['image']['size'];

        // Check file type and size (optional)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $imageType = mime_content_type($imageTmp);

        if (in_array($imageType, $allowedTypes)) {
            // Create a unique file name to avoid overwriting
            $imageNewName = uniqid('', true) . '.' . pathinfo($imageName, PATHINFO_EXTENSION);
            $imageUploadPath = 'uploads/' . $imageNewName; // Make sure 'uploads' folder is writable

            // Check if uploads folder exists and is writable
            if (!is_dir('uploads/')) {
                mkdir('uploads/', 0755, true); // Create folder if it doesn't exist
            }

            // Move the uploaded file to the desired location
            if (move_uploaded_file($imageTmp, $imageUploadPath)) {
                // Insert product data into database
                $sql = "INSERT INTO products (name, description, price, image, category)
                        VALUES ('$name', '$desc', '$price', '$imageUploadPath', '$cat')";
                if ($conn->query($sql)) {
                    echo "Product added successfully!";
                } else {
                    echo "Error: " . $conn->error;
                }
            } else {
                echo "Error uploading image.";
            }
        } else {
            echo "Invalid image format. Only JPEG, PNG, and GIF are allowed.";
        }
    } else {
        echo "No image uploaded or an error occurred with the file.";
    }
}
?>
