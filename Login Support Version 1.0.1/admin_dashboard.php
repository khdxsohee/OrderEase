<?php
// admin_dashboard.php
session_start();

// Inactivity Timeout Check
$inactive = 1800; // 30 minutes in seconds (30 * 60)
if (isset($_SESSION['timeout']) && ($_SESSION['timeout'] + $inactive < time())) {
    session_unset();
    session_destroy();
    header("Location: login.php?message=Session expired due to inactivity.");
    exit();
}
$_SESSION['timeout'] = time();

// Agar user logged in nahi hai, toh login page par redirect karein
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/db_config.php';

$message = "";
if (isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
}

$tracking_items = [];
$sql = "SELECT * FROM tracking_items ORDER BY last_updated DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tracking_items[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Dashboard specific styles */
        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }
        .dashboard-table th, .dashboard-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .dashboard-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
        }
        .dashboard-table tbody tr:hover {
            background-color: #f0f8ff;
        }
        .dashboard-table .actions a {
            margin-right: 10px;
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }
        .dashboard-table .actions a.delete-link {
            color: #dc3545;
        }
        .dashboard-table .actions a:hover {
            color: #0056b3;
        }
        .dashboard-table .actions a.delete-link:hover {
            color: #c82333;
        }
        .add-new-link {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        .add-new-link:hover {
            background-color: #218838;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85em;
            font-weight: 600;
            color: white;
        }
        .status-badge.pending { background-color: #ffc107; }
        .status-badge.paid { background-color: #28a745; }
        .status-badge.refunded { background-color: #dc3545; }
        .status-badge.processing { background-color: #17a2b8; }
        .status-badge.shipped { background-color: #6c757d; }
        .status-badge.out-for-delivery { background-color: #007bff; }
        .status-badge.delivered { background-color: #28a745; }
        .status-badge.cancelled { background-color: #dc3545; }

        .confirmation-dialog {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
        }
        .confirmation-dialog-content {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .confirmation-dialog-content h3 {
            margin-top: 0;
            color: #333;
        }
        .confirmation-dialog-content p {
            margin-bottom: 20px;
            color: #555;
        }
        .confirmation-dialog-content button {
            padding: 10px 20px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        .confirmation-dialog-content .confirm-btn {
            background-color: #dc3545;
            color: white;
        }
        .confirmation-dialog-content .confirm-btn:hover {
            background-color: #c82333;
        }
        .confirmation-dialog-content .cancel-btn {
            background-color: #6c757d;
            color: white;
        }
        .confirmation-dialog-content .cancel-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="page-title"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>

        <?php if (!empty($message)): ?>
            <p class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <p><a href="admin_add.php" class="add-new-link"><i class="fas fa-plus-circle"></i> Add New Tracking Item</a></p>
        <p style="text-align: right;"><a href="logout.php" style="color: #dc3545; text-decoration: none; font-weight: bold;"><i class="fas fa-sign-out-alt"></i> Logout</a></p>

        <?php if (!empty($tracking_items)): ?>
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Tracking ID</th>
                        <th>Customer Name</th>
                        <th>Price</th>
                        <th>Order Date</th>
                        <th>Payment Status</th>
                        <th>Delivery Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tracking_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['tracking_id']); ?></td>
                        <td><?php echo htmlspecialchars($item['customer_name']); ?></td>
                        <td>PKR <?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo date("Y-m-d", strtotime($item['order_date'])); ?></td>
                        <td><span class="status-badge <?php echo strtolower($item['payment_status']); ?>"><?php echo htmlspecialchars($item['payment_status']); ?></span></td>
                        <td><span class="status-badge <?php echo strtolower(str_replace(' ', '-', $item['delivery_status'])); ?>"><?php echo htmlspecialchars($item['delivery_status']); ?></span></td>
                        <td><?php echo date("Y-m-d H:i", strtotime($item['last_updated'])); ?></td>
                        <td class="actions">
                            <a href="admin_update.php?tracking_id=<?php echo urlencode($item['tracking_id']); ?>"><i class="fas fa-edit"></i> Edit</a>
                            <a href="#" class="delete-link" data-tracking-id="<?php echo urlencode($item['tracking_id']); ?>"><i class="fas fa-trash-alt"></i> Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="message">No tracking items found. <a href="admin_add.php">Add a new one?</a></p>
        <?php endif; ?>
    </div>

    <div id="confirmationDialog" class="confirmation-dialog">
        <div class="confirmation-dialog-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete tracking ID: <strong id="trackingIdToDelete"></strong>?</p>
            <button class="cancel-btn" onclick="hideConfirmationDialog()">Cancel</button>
            <button class="confirm-btn" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>

    <script>
        // JavaScript for delete confirmation
        let currentTrackingId = '';
        const confirmationDialog = document.getElementById('confirmationDialog');
        const trackingIdToDeleteSpan = document.getElementById('trackingIdToDelete');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        document.querySelectorAll('.delete-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default link behavior
                currentTrackingId = this.dataset.trackingId;
                trackingIdToDeleteSpan.textContent = decodeURIComponent(currentTrackingId);
                confirmationDialog.style.display = 'flex'; // Show the dialog
            });
        });

        confirmDeleteBtn.addEventListener('click', function() {
            if (currentTrackingId) {
                window.location.href = 'admin_delete.php?tracking_id=' + currentTrackingId;
            }
        });

        function hideConfirmationDialog() {
            confirmationDialog.style.display = 'none'; // Hide the dialog
            currentTrackingId = ''; // Reset
        }

        // Close dialog if clicked outside content
        confirmationDialog.addEventListener('click', function(e) {
            if (e.target === confirmationDialog) {
                hideConfirmationDialog();
            }
        });
    </script>
</body>
</html>
