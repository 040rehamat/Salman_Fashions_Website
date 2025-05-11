<?php
session_start();
header('Content-Type: application/json');
include 'db.php';

// Check if the review form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['rating'], $_POST['review'])) {
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $review = trim($_POST['review']);
    
    // Check if review and rating are valid
    if ($rating < 1 || $rating > 5 || empty($review)) {
        echo json_encode(['success' => false, 'message' => 'Invalid review or rating']);
        exit;
    }

    // Insert the review into the database
    $stmt = $conn->prepare("INSERT INTO reviews (product_id, rating, review) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $product_id, $rating, $review);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Review submission failed', 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
