<?php
// admin_update.php
require_once 'db_config.php';

$message = "";
$tracking_data = null; // Initialize to null

// --- Step 1: Fetch Existing Data if tracking_id is provided ---
if (isset($_GET['tracking_id']) && !empty($_GET['tracking_id'])) {
    $tracking_id = $conn->real_escape_string($_GET['tracking_id']);
    $sql = "SELECT * FROM tracking_items WHERE tracking_id = '$tracking_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $tracking_data = $result->fetch_assoc();
    } else {
        $message = "Tracking ID not found: " . htmlspecialchars($tracking_id);
    }
}

// --- Step 2: Handle Form Submission for Updates ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tracking_id = $conn->real_escape_string($_POST['tracking_id']);
    $customer_name = $conn->real_escape_string($_POST['customer_name']);
    $customer_address = $conn->real_escape_string($_POST['customer_address']);
    $price = $conn->real_escape_string($_POST['price']);
    $order_date = $conn->real_escape_string($_POST['order_date']);
    $payment_status = $conn->real_escape_string($_POST['payment_status']);
    $delivery_status = $conn->real_escape_string($_POST['delivery_status']);

    // Process products and quantity into JSON
    $products_array = [];
    if (isset($_POST['product_name']) && is_array($_POST['product_name'])) {
        foreach ($_POST['product_name'] as $index => $name) {
            $qty = isset($_POST['product_qty'][$index]) ? (int)$_POST['product_qty'][$index] : 0;
            // Only add if product name is not empty and quantity is valid
            if (!empty(trim($name)) && $qty > 0) {
                $products_array[] = [
                    'name' => trim($name),
                    'qty' => $qty
                ];
            }
        }
    }
    $products_json = json_encode($products_array);
    if ($products_json === false) {
        // Fallback or error handling for JSON encoding failure
        $products = "";
        $message .= " Error: Could not encode product data. ";
    } else {
        $products = $conn->real_escape_string($products_json);
    }

    $sql = "UPDATE tracking_items SET
            customer_name = '$customer_name',
            customer_address = '$customer_address',
            products = '$products',
            price = '$price',
            order_date = '$order_date',
            payment_status = '$payment_status',
            delivery_status = '$delivery_status'
            WHERE tracking_id = '$tracking_id'";

    if ($conn->query($sql) === TRUE) {
        $message = "Record updated successfully!";
        // Refresh data after update to show latest changes
        $sql = "SELECT * FROM tracking_items WHERE tracking_id = '$tracking_id'";
        $result = $conn->query($sql);
        $tracking_data = $result->fetch_assoc();
    } else {
        $message = "Error updating record: " . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Tracking Item</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <div class="container">
        <h2>Update Tracking Item</h2>
        <p class="message"><?php echo $message; ?></p>
        <?php if ($tracking_data): // Only show form if tracking data is found ?>
        <form action="admin_update.php" method="post">
            <label for="tracking_id">Tracking ID:</label>
            <input type="text" id="tracking_id" name="tracking_id" value="<?php echo htmlspecialchars($tracking_data['tracking_id']); ?>" readonly>

            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($tracking_data['customer_name']); ?>" required>

            <label for="customer_address">Customer Address:</label>
            <textarea id="customer_address" name="customer_address" required><?php echo htmlspecialchars($tracking_data['customer_address']); ?></textarea>

            <label>Products & Quantity:</label>
            <div id="products_container">
                <div class="product-entry">
                    <input type="text" name="product_name[]" placeholder="Product Name" required>
                    <input type="number" name="product_qty[]" placeholder="Quantity" min="1" value="1" required>
                    <button type="button" class="remove-product">Remove</button>
                </div>
            </div>
            <button type="button" id="add_product">Add Another Product</button>
            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($tracking_data['price']); ?>" required>

            <label for="order_date">Order Date:</label>
            <input type="date" id="order_date" name="order_date" value="<?php echo htmlspecialchars($tracking_data['order_date']); ?>" required>

            <label for="payment_status">Payment Status:</label>
            <select id="payment_status" name="payment_status">
                <option value="Pending" <?php echo ($tracking_data['payment_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="Paid" <?php echo ($tracking_data['payment_status'] == 'Paid') ? 'selected' : ''; ?>>Paid</option>
                <option value="Refunded" <?php echo ($tracking_data['payment_status'] == 'Refunded') ? 'selected' : ''; ?>>Refunded</option>
            </select>

            <label for="delivery_status">Delivery Status:</label>
            <select id="delivery_status" name="delivery_status">
                <option value="Pending" <?php echo ($tracking_data['delivery_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="Processing" <?php echo ($tracking_data['delivery_status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                <option value="Shipped" <?php echo ($tracking_data['delivery_status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                <option value="Out for Delivery" <?php echo ($tracking_data['delivery_status'] == 'Out for Delivery') ? 'selected' : ''; ?>>Out for Delivery</option>
                <option value="Delivered" <?php echo ($tracking_data['delivery_status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                <option value="Cancelled" <?php echo ($tracking_data['delivery_status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>

            <button type="submit">Update Item</button>
        </form>
        <?php else: ?>
            <p class="message error">Please provide a valid tracking ID to update, or the item was not found.</p>
        <?php endif; ?>
        <p><a href="admin_dashboard.php">Back to Admin Dashboard</a></p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productsContainer = document.getElementById('products_container');
            const addProductBtn = document.getElementById('add_product');

            // --- Function to add a new product entry ---
            function addProductEntry(name = '', qty = 1) {
                const newProductEntry = document.createElement('div');
                newProductEntry.classList.add('product-entry');
                newProductEntry.innerHTML = `
                    <input type="text" name="product_name[]" placeholder="Product Name" value="${name}" required>
                    <input type="number" name="product_qty[]" placeholder="Quantity" min="1" value="${qty}" required>
                    <button type="button" class="remove-product">Remove</button>
                `;
                productsContainer.appendChild(newProductEntry);
            }

            // --- Event Listener for "Add Another Product" button ---
            addProductBtn.addEventListener('click', function() {
                addProductEntry(); // Add an empty one
            });

            // --- Event Listener for "Remove" buttons (using event delegation) ---
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

            // --- PHP generated JavaScript for pre-filling existing products ---
            <?php
            if (isset($tracking_data) && $tracking_data && !empty($tracking_data['products'])) {
                $decoded_products = json_decode($tracking_data['products'], true);
                if ($decoded_products && is_array($decoded_products) && count($decoded_products) > 0) {
                    // Clear the default empty entry first ONLY IF there are products to load
                    echo 'productsContainer.innerHTML = "";'; // Clear existing default row

                    foreach ($decoded_products as $product) {
                        // Use addProductEntry function to create and populate rows
                        // Make sure to escape values for JavaScript string
                        echo 'addProductEntry(\'' . addslashes(htmlspecialchars($product['name'])) . '\', ' . (int)$product['qty'] . ');';
                    }
                }
            }
            ?>
        });
    </script>
</body>
</html>