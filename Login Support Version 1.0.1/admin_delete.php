<?php
// admin_delete.php
session_start();

// Agar user logged in nahi hai, toh login page par redirect karein
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/db_config.php'; // Database connection include karein

$message = "";

if (isset($_GET['tracking_id']) && !empty($_GET['tracking_id'])) {
    $tracking_id = $conn->real_escape_string($_GET['tracking_id']);

    // Delete query
    $sql = "DELETE FROM tracking_items WHERE tracking_id = '$tracking_id'";

    if ($conn->query($sql) === TRUE) {
        $message = "Tracking item with ID " . htmlspecialchars($tracking_id) . " has been deleted successfully.";
        // Delete hone ke baad admin dashboard par wapas redirect kar den
        header("Location: admin_dashboard.php?message=" . urlencode($message));
        exit();
    } else {
        $message = "Error deleting record: " . $conn->error;
    }
} else {
    $message = "No tracking ID specified for deletion.";
}

$conn->close();

// Agar redirect nahi hua to message display karein
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Tracking Item</title>
    <link rel="stylesheet" href="../style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <h2 class="page-title"><i class="fas fa-trash-alt"></i> Delete Status</h2>
        <?php if (!empty($message)): ?>
            <p class="message <?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>
        <p class="back-link"><a href="admin_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a></p>
    </div>
</body>
</html>