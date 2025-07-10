<?php
// admin_add.php
require_once 'db_config.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tracking_id = $conn->real_escape_string($_POST['tracking_id']);
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $customer_address = $conn->real_escape_string($_POST['customer_address']);
    // Collect products and quantities
$products_array = [];
if (isset($_POST['product_name']) && is_array($_POST['product_name'])) {
    foreach ($_POST['product_name'] as $index => $name) {
        // Ensure corresponding quantity exists and is a number
        $qty = isset($_POST['product_qty'][$index]) ? (int)$_POST['product_qty'][$index] : 0;
        if (!empty(trim($name)) && $qty > 0) { // Only add if name is not empty and quantity is valid
            $products_array[] = [
                'name' => trim($name),
                'qty' => $qty
            ];
        }
    }
}
$products_json = json_encode($products_array);
if ($products_json === false) {
    // Handle JSON encoding error if necessary
    $products = ""; // Default to empty if encoding fails
    $message = "Error encoding products data.";
} else {
    $products = $conn->real_escape_string($products_json); // Store the JSON string
}

    $price = $conn->real_escape_string($_POST['price']);
    $order_date = $conn->real_escape_string($_POST['order_date']);
    $payment_status = $conn->real_escape_string($_POST['payment_status']);
    $delivery_status = $conn->real_escape_string($_POST['delivery_status']);

    $sql = "INSERT INTO tracking_items (tracking_id, customer_name, customer_address, products, price, order_date, payment_status, delivery_status)
            VALUES ('$tracking_id', '$customer_name', '$customer_address', '$products', '$price', '$order_date', '$payment_status', '$delivery_status')";

    if ($conn->query($sql) === TRUE) {
        $message = "New record created successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Tracking Item</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <div class="container">
        <h2>Add New Tracking Item</h2>
        <p class="message"><?php echo $message; ?></p>
        <form action="admin_add.php" method="post">
            <label for="tracking_id">Tracking ID:</label>
            <input type="text" id="tracking_id" name="tracking_id" required>

            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" required>

            <label for="customer_address">Customer Address:</label>
            <textarea id="customer_address" name="customer_address" required></textarea>

            <label>Products & Quantity:</label>
<div id="products_container">
    <div class="product-entry">
        <input type="text" name="product_name[]" placeholder="Product Name" required>
        <input type="number" name="product_qty[]" placeholder="Quantity" min="1" required>
        <button type="button" class="remove-product">Remove</button>
    </div>
</div>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            const productsContainer = document.getElementById('products_container');
            const addProductBtn = document.getElementById('add_product');

            addProductBtn.addEventListener('click', function() {
                const newProductEntry = document.createElement('div');
                newProductEntry.classList.add('product-entry');
                newProductEntry.innerHTML = `
                    <input type="text" name="product_name[]" placeholder="Product Name" required>
                    <input type="number" name="product_qty[]" placeholder="Quantity" min="1" required>
                    <button type="button" class="remove-product">Remove</button>
                `;
                productsContainer.appendChild(newProductEntry);
            });

            productsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-product')) {
                    // Ensure at least one product entry remains
                    if (productsContainer.children.length > 1) {
                        e.target.closest('.product-entry').remove();
                    } else {
                        alert('You must have at least one product entry.');
                    }
                }
            });

            // THIS PART IS ONLY FOR admin_update.php to pre-fill existing products
            // You need to generate this section conditionally using PHP
            // Example PHP loop for pre-filling (copy-paste from previous response if you haven't)
            <?php
            // For admin_update.php only: Pre-fill existing products
            if (isset($tracking_data) && $tracking_data && !empty($tracking_data['products'])) {
                $decoded_products = json_decode($tracking_data['products'], true);
                if ($decoded_products && is_array($decoded_products)) {
                    // Clear the default empty entry first
                    echo 'productsContainer.innerHTML = "";';
                    foreach ($decoded_products as $product) {
                        echo 'const existingProductEntry = document.createElement("div");';
                        echo 'existingProductEntry.classList.add("product-entry");';
                        echo 'existingProductEntry.innerHTML = `';
                        echo '    <input type="text" name="product_name[]" placeholder="Product Name" value="' . htmlspecialchars($product['name']) . '" required>';
                        echo '    <input type="number" name="product_qty[]" placeholder="Quantity" min="1" value="' . htmlspecialchars($product['qty']) . '" required>';
                        echo '    <button type="button" class="remove-product">Remove</button>';
                        echo '`;';
                        echo 'productsContainer.appendChild(existingProductEntry);';
                    }
                }
            }
            ?>
        });
    </script>
<button type="button" id="add_product">Add Another Product</button>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="order_date">Order Date:</label>
            <input type="date" id="order_date" name="order_date" required>

            <label for="payment_status">Payment Status:</label>
            <select id="payment_status" name="payment_status">
                <option value="Pending">Pending</option>
                <option value="Paid">Paid</option>
                <option value="Refunded">Refunded</option>
            </select>

            <label for="delivery_status">Delivery Status:</label>
            <select id="delivery_status" name="delivery_status">
                <option value="Pending">Pending</option>
                <option value="Processing">Processing</option>
                <option value="Shipped">Shipped</option>
                <option value="Out for Delivery">Out for Delivery</option>
                <option value="Delivered">Delivered</option>
                <option value="Cancelled">Cancelled</option>
            </select>

            <button type="submit">Add Item</button>
        </form>
        <p><a href="admin_dashboard.php">Back to Admin Dashboard</a></p>
    </div>
</body>
</html>