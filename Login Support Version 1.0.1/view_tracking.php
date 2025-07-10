<?php
// view_tracking.php
require_once 'db_config.php';

$tracking_data = null;
$message = "";

if (isset($_GET['tracking_id']) && !empty($_GET['tracking_id'])) {
    $tracking_id = $conn->real_escape_string($_GET['tracking_id']);
    $sql = "SELECT * FROM tracking_items WHERE tracking_id = '$tracking_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $tracking_data = $result->fetch_assoc();
    } else {
        $message = "No record found for tracking ID: " . htmlspecialchars($tracking_id);
    }
} else {
    $message = "Please enter a tracking ID.";
}
$conn->close();

// Function to get icon based on status (conceptual)
function getStatusIcon($status_type, $status_value) {
    $icons = [
        'payment' => [
            'Pending' => '<i class="fas fa-hourglass-half status-pending" title="Payment Pending"></i>',
            'Paid' => '<i class="fas fa-check-circle status-paid" title="Payment Paid"></i>',
            'Refunded' => '<i class="fas fa-undo-alt status-refunded" title="Payment Refunded"></i>'
        ],
        'delivery' => [
            'Pending' => '<i class="fas fa-box status-pending" title="Delivery Pending"></i>',
            'Processing' => '<i class="fas fa-cogs status-processing" title="Processing Order"></i>',
            'Shipped' => '<i class="fas fa-shipping-fast status-shipped" title="Order Shipped"></i>',
            'Out for Delivery' => '<i class="fas fa-truck status-out-for-delivery" title="Out for Delivery"></i>',
            'Delivered' => '<i class="fas fa-clipboard-check status-delivered" title="Delivered"></i>',
            'Cancelled' => '<i class="fas fa-times-circle status-cancelled" title="Order Cancelled"></i>'
        ]
    ];
    return $icons[$status_type][$status_value] ?? '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking Details</title>
    <link rel="stylesheet" href="style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> </head>
<body>
    <div class="container">
        <h2 class="page-title"><i class="fas fa-search-location"></i> Order Tracking Details</h2>
        <?php if ($tracking_data): ?>
            <div class="tracking-card">
                <div class="card-header">
                    <h3><i class="fas fa-id-card"></i> Tracking ID: <span class="tracking-id-value"><?php echo htmlspecialchars($tracking_data['tracking_id']); ?></span></h3>
                </div>
                <div class="card-body">
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-user"></i> Customer Name:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($tracking_data['customer_name']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-map-marker-alt"></i> Address:</span>
                        <span class="detail-value"><?php echo nl2br(htmlspecialchars($tracking_data['customer_address'])); ?></span>
                    </div>
                    <div class="detail-item full-width">
                        <span class="detail-label"><i class="fas fa-box-open"></i> Products:</span>
                        <ul class="product-list">
                            <?php
                            if (!empty($tracking_data['products'])) {
                                $products_decoded = json_decode($tracking_data['products'], true);
                                if ($products_decoded && is_array($products_decoded)) {
                                    foreach ($products_decoded as $product) {
                                        echo '<li><i class="fas fa-tag"></i> ' . htmlspecialchars($product['name']) . ' (Qty: ' . htmlspecialchars($product['qty']) . ')</li>';
                                    }
                                } else {
                                    echo '<li><i class="fas fa-exclamation-circle"></i> N/A (Invalid product data)</li>';
                                }
                            } else {
                                echo '<li><i class="fas fa-times-circle"></i> No products listed</li>';
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-dollar-sign"></i> Price:</span>
                        <span class="detail-value">PKR <?php echo number_format($tracking_data['price'], 2); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-calendar-alt"></i> Order Date:</span>
                        <span class="detail-value"><?php echo date("F j, Y", strtotime($tracking_data['order_date'])); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label"><i class="fas fa-clock"></i> Last Updated:</span>
                        <span class="detail-value"><?php echo date("F j, Y, g:i a", strtotime($tracking_data['last_updated'])); ?></span>
                    </div>
                    <div class="detail-item status-item">
                        <span class="detail-label"><i class="fas fa-wallet"></i> Payment Status:</span>
                        <span class="detail-value status-text"><?php echo htmlspecialchars($tracking_data['payment_status']); ?></span>
                        <span class="status-icon"><?php echo getStatusIcon('payment', $tracking_data['payment_status']); ?></span>
                    </div>
                    <div class="detail-item status-item">
                        <span class="detail-label"><i class="fas fa-truck-loading"></i> Delivery Status:</span>
                        <span class="detail-value status-text"><?php echo htmlspecialchars($tracking_data['delivery_status']); ?></span>
                        <span class="status-icon"><?php echo getStatusIcon('delivery', $tracking_data['delivery_status']); ?></span>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="message error"><i class="fas fa-exclamation-triangle"></i> <?php echo $message; ?></p>
        <?php endif; ?>
        <p class="back-link"><a href="track.php"><i class="fas fa-arrow-left"></i> Track another order</a></p>
    </div>
</body>
</html>