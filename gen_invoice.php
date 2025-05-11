<?php
ob_start();
require('fpdf.php');
require('db.php');

// Logo path
$logo_path = 'logo.png';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid order ID.");
}

$order_id = intval($_GET['id']);

// Fetch order details
$order_sql = "SELECT * FROM orders WHERE id = $order_id";
$order_result = mysqli_query($conn, $order_sql);
if (!$order_result || mysqli_num_rows($order_result) === 0) die("Order not found.");
$order = mysqli_fetch_assoc($order_result);

// Check if customer_name exists
$customer = isset($order['customer_name']) && !empty($order['customer_name']) ? htmlspecialchars($order['customer_name'], ENT_QUOTES, 'UTF-8') : 'N/A';

// Order date and total amount
$date = date("d M Y", strtotime($order['order_date'] ?? ''));
$address = $order['address'] ?? 'Not provided';
$total_amount = number_format($order['total_amount'] ?? 0, 2);

// Fetch product details
$product_id = $order['product_id'];
$product_query = "SELECT name, price FROM products WHERE id = $product_id";
$product_result = mysqli_query($conn, $product_query);
$product = mysqli_fetch_assoc($product_result);

$product_name = $product['name'] ?? 'Product';
$product_price = $product['price'] ?? 0;
$quantity = $order['quantity'] ?? 1;
$subtotal = $product_price * $quantity;

// PDF generation
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Logo
if (file_exists($logo_path)) {
    $pdf->Image($logo_path, 10, 10, 30);
}
$pdf->Cell(0, 10, 'Salman Fashions - Invoice', 0, 1, 'R');
$pdf->Ln(15);

// Customer Info
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 8, 'Customer Name: ' . $customer, 0, 1);
$pdf->Cell(100, 8, 'Order Date: ' . $date, 0, 1);
$pdf->Cell(100, 8, 'Shipping Address: ' . $address, 0, 1);
$pdf->Ln(8);

// Product Table
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(90, 8, 'Product', 1);
$pdf->Cell(30, 8, 'Qty', 1, 0, 'C');
$pdf->Cell(40, 8, 'Price (₹)', 1, 0, 'R');
$pdf->Cell(30, 8, 'Total (₹)', 1, 1, 'R');

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(90, 8, $product_name, 1);
$pdf->Cell(30, 8, $quantity, 1, 0, 'C');
$pdf->Cell(40, 8, number_format($product_price, 2), 1, 0, 'R');
$pdf->Cell(30, 8, number_format($subtotal, 2), 1, 1, 'R');

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(160, 10, 'Total Amount', 1);
$pdf->Cell(30, 10, '₹ ' . number_format($subtotal, 2), 1, 1, 'R');

$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 11);
$pdf->Cell(0, 8, 'Thank you for shopping with Salman Fashions!', 0, 1, 'C');

ob_end_clean();
$pdf->Output('D', 'invoice_' . $order_id . '.pdf');
exit;
?>
