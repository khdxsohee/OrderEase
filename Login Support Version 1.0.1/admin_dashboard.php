<?php
// Har admin page ke shuru mein yeh code add karein
session_start(); // Session start karein

// Agar user logged in nahi hai, toh login page par redirect karein
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'db_config.php'; // Database connection include karein
// ... rest of your existing PHP code for the page
// admin_dashboard.php
require_once 'db_config.php';

$sql = "SELECT tracking_id, customer_name, delivery_status, payment_status, last_updated FROM tracking_items ORDER BY last_updated DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard - Manage Tracking Items</h2>
        <p><a href="admin_add.php">Add New Tracking Item</a></p>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Tracking ID</th>
                        <th>Customer Name</th>
                        <th>Delivery Status</th>
                        <th>Payment Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['tracking_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['delivery_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                        <td><?php echo date("Y-m-d H:i", strtotime($row['last_updated'])); ?></td>
                        <td>
                            <a href="admin_update.php?tracking_id=<?php echo htmlspecialchars($row['tracking_id']); ?>">Edit</a> |
                            <a href="admin_delete.php?tracking_id=<?php echo htmlspecialchars($row['tracking_id']); ?>" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tracking items found. <a href="admin_add.php">Add one now!</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
$conn->close();
?>