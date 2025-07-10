CREATE TABLE `tracking_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tracking_id` VARCHAR(20) UNIQUE NOT NULL,
    `customer_name` VARCHAR(255) NOT NULL,
    `customer_address` TEXT NOT NULL,
    `products` TEXT NOT NULL, -- Stores JSON string of products and quantities
    `price` DECIMAL(10, 2) NOT NULL,
    `order_date` DATE NOT NULL,
    `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `payment_status` ENUM('Pending', 'Paid', 'Refunded') DEFAULT 'Pending',
    `delivery_status` ENUM('Pending', 'Processing', 'Shipped', 'Out for Delivery', 'Delivered', 'Cancelled') DEFAULT 'Pending'
);