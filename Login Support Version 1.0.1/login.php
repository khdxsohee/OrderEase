<?php
// login.php
session_start(); // Session start karein

$message = "";

// Agar user pehle se logged in hai, toh dashboard par redirect kar den
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: admin_dashboard.php"); // Dashboard page ka naam yahan likhen
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once 'db_config.php'; // Db config file include karein admin credentials ke liye

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $message = "Please enter both username and password.";
    } elseif ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        // Password sahi hai, session set karein
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        header("Location: admin_dashboard.php"); // Login hone ke baad dashboard par bhej den
        exit();
    } else {
        $message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css"> <style>
        /* Login page specific styles */
        body {
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h2 {
            color: #1a237e;
            margin-bottom: 30px;
            font-size: 2em;
        }
        .login-container .message {
            background-color: #ffe0b2; /* Light orange for warnings */
            color: #e65100; /* Darker orange text */
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #ffcc80;
        }
        .login-container .message.error {
            background-color: #ffebee; /* Light red for errors */
            color: #c62828; /* Darker red text */
            border: 1px solid #ef9a9a;
        }
        .login-container label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 20px); /* Adjust for padding */
            padding: 12px 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if (!empty($message)): ?>
            <p class="message <?php echo strpos($message, 'Please') === 0 ? '' : 'error'; ?>"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form action="login.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>