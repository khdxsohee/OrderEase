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
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Body and Container Styles (if not already in style.css) */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5; /* Light grey background */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Align content to the top */
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 1200px; /* Wider container for more content */
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px; /* Softer rounded corners */
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1); /* More pronounced shadow */
        }

        .page-title {
            color: #1a237e; /* Darker blue for titles */
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.2em; /* Slightly larger title */
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-title i {
            margin-right: 15px; /* Space between icon and text */
            color: #007bff; /* Icon color */
            font-size: 1.2em; /* Icon size relative to text */
        }

        /* Message Styles */
        .message {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 8px;
            font-weight: 500;
            text-align: center;
            border: 1px solid transparent;
            animation: fadeIn 0.5s ease-out; /* Fade-in effect */
        }

        .message.success {
            background-color: #e6ffed; /* Light green */
            color: #1c7438; /* Dark green text */
            border-color: #b3dfc9;
        }

        .message.error {
            background-color: #ffe6e6; /* Light red */
            color: #990000; /* Dark red text */
            border-color: #f1b3b3;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Top Links (Add New & Logout) */
        .add-new-link, .logout-link { /* Combined styles for both links */
            display: inline-flex; /* For icon alignment */
            align-items: center;
            padding: 10px 22px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .add-new-link {
            background-color: #28a745; /* Green */
            color: white;
            margin-bottom: 25px;
        }
        .add-new-link:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        .add-new-link i {
            margin-right: 8px;
        }

        .logout-container { /* Parent for logout link to align right */
            text-align: right;
            margin-top: -60px; /* Adjust to align with add new button */
            margin-bottom: 30px;
        }
        .logout-link {
            background-color: #dc3545; /* Red */
            color: white;
        }
        .logout-link:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        .logout-link i {
            margin-right: 8px;
        }


        /* Dashboard Table Styles */
        .dashboard-table {
            width: 100%;
            border-collapse: separate; /* For rounded corners on rows */
            border-spacing: 0;
            margin-top: 30px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08); /* Stronger shadow */
            background-color: #fff;
            border-radius: 10px; /* Softer table corners */
            overflow: hidden; /* Ensures rounded corners are applied */
        }

        .dashboard-table th, .dashboard-table td {
            padding: 14px 18px; /* More padding */
            text-align: left;
            border-bottom: 1px solid #e0e0e0; /* Lighter border */
        }

        .dashboard-table th {
            background-color: #f5f7fa; /* Lighter header background */
            color: #4a4a4a; /* Darker grey text */
            font-weight: 700; /* Bolder headers */
            text-transform: uppercase;
            font-size: 0.85em; /* Slightly smaller font for headers */
        }

        .dashboard-table tbody tr:last-child td {
            border-bottom: none; /* Remove border from last row */
        }

        .dashboard-table tbody tr:hover {
            background-color: #f7fafd; /* Lighter hover effect */
            transform: translateY(-1px); /* Slight lift on hover */
            box-shadow: 0 3px 10px rgba(0,0,0,0.05); /* Subtle shadow on hover */
        }

        /* Actions Column */
        .dashboard-table .actions a {
            margin-right: 12px; /* More space between links */
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95em; /* Slightly larger text */
            transition: color 0.2s ease, transform 0.2s ease;
        }

        .dashboard-table .actions a:hover {
            color: #0056b3;
            transform: translateY(-1px);
        }

        .dashboard-table .actions a.delete-link {
            color: #dc3545; /* Red for delete */
        }
        .dashboard-table .actions a.delete-link:hover {
            color: #c82333;
        }
        .dashboard-table .actions i {
            margin-right: 5px; /* Space between icon and text */
            font-size: 0.9em;
        }


        /* Status Badges */
        .status-badge {
            padding: 6px 12px; /* More padding */
            border-radius: 20px; /* Pill shape */
            font-size: 0.8em; /* Slightly smaller font */
            font-weight: 700; /* Bolder text */
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block; /* For better padding */
        }

        /* Payment Status Colors */
        .status-badge.pending { background-color: #ffc107; color: #856404; } /* Amber, darker text */
        .status-badge.paid { background-color: #28a745; } /* Green */
        .status-badge.refunded { background-color: #dc3545; } /* Red */

        /* Delivery Status Colors */
        .status-badge.processing { background-color: #17a2b8; } /* Cyan */
        .status-badge.shipped { background-color: #6c757d; } /* Grey */
        .status-badge.out-for-delivery { background-color: #007bff; } /* Blue */
        .status-badge.delivered { background-color: #28a745; } /* Green (same as paid) */
        .status-badge.cancelled { background-color: #dc3545; } /* Red (same as refunded) */

        /* No Items Message */
        .message a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }
        .message a:hover {
            text-decoration: underline;
        }

        /* Confirmation Dialog Styles */
       .confirmation-dialog {
            display: none; /* Hidden by default - BAHUT ZAROORI HAI YEH */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5); /* Darker overlay */
            /* Yahahan se 'display: flex;' hata dein */
            justify-content: center; /* Flexbox properties rehne dein taake JavaScript show kare toh theek lage */
            align-items: center;
        }
        .confirmation-dialog-content {
            background-color: #ffffff;
            padding: 40px; /* More padding */
            border-radius: 15px; /* More rounded */
            box-shadow: 0 10px 40px rgba(0,0,0,0.25); /* Stronger, softer shadow */
            width: 90%;
            max-width: 450px; /* Slightly wider */
            text-align: center;
            animation: zoomIn 0.3s ease-out; /* Animation */
        }
        .confirmation-dialog-content h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.8em;
            font-weight: 700;
        }
        .confirmation-dialog-content p {
            color: #555;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .confirmation-dialog-content strong {
            color: #1a237e; /* Highlight tracking ID */
        }
        .confirmation-dialog-content button {
            padding: 12px 25px; /* Larger buttons */
            margin: 0 10px;
            border: none;
            border-radius: 8px; /* More rounded buttons */
            cursor: pointer;
            font-size: 1.05em;
            font-weight: 600;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }
        .confirmation-dialog-content .confirm-btn {
            background-color: #dc3545;
            color: white;
        }
        .confirmation-dialog-content .confirm-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .confirmation-dialog-content .cancel-btn {
            background-color: #6c757d;
            color: white;
        }
        .confirmation-dialog-content .cancel-btn:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        /* Animation for dialog */
        @keyframes zoomIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
                padding: 20px;
            }
            .dashboard-table th, .dashboard-table td {
                padding: 10px;
                font-size: 0.9em;
            }
            .dashboard-table .actions a {
                display: block; /* Stack actions on small screens */
                margin-bottom: 5px;
            }
            .page-title {
                font-size: 1.8em;
            }
            .page-title i {
                margin-right: 10px;
            }
            .add-new-link, .logout-link {
                width: calc(100% - 44px); /* Full width for small screens */
                text-align: center;
                justify-content: center;
                margin-bottom: 15px;
            }
            .logout-container {
                margin-top: 0; /* Remove negative margin on small screens */
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .dashboard-table {
                font-size: 0.8em;
            }
            .dashboard-table th, .dashboard-table td {
                padding: 8px;
            }
            .confirmation-dialog-content button {
                padding: 10px 15px;
                font-size: 0.9em;
            }
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
        <div class="logout-container">
            <p><a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></p>
        </div>

        <?php if (!empty($tracking_items)): ?>
            <div style="overflow-x: auto;"> <table class="dashboard-table">
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
            </div>
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
