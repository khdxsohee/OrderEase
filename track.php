<?php
// track.php
// This is the public interface for customers
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order</title>
    <link rel="stylesheet" href="style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> </head>
<body>
    <div class="container">
        <h2>Track Your Order</h2>
        <form action="view_tracking.php" method="get">
            <label for="tracking_id">Enter Tracking ID:</label>
            <input type="text" id="tracking_id" name="tracking_id" placeholder="e.g., BBB236" required>
            <button type="submit">Track Order</button>
        </form>
    </div>
</body>
</html>