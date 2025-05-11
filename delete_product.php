<?php
// Include database connection
include 'db.php';

// Check if the product ID is set
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Start a transaction for consistency
    $conn->begin_transaction();

    try {
        // First, delete the reviews related to this product (if any)
        $stmt = $conn->prepare("DELETE FROM reviews WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();

        // Then, delete the product itself
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        echo "Product deleted successfully!";
    } catch (Exception $e) {
        // If there is an error, roll back the transaction
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>
