<?php
// logout.php
session_start(); // Session start karein

// Sabhi session variables ko remove karein
$_SESSION = array();

// Session cookie ko delete karein
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Session ko destroy karein
session_destroy();

// User ko login page par redirect karein
header("Location: login.php");
exit();
?>