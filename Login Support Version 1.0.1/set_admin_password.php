<?php
// set_admin_password.php
// Is file ko sirf ek baar run karna hai password set karne ke liye.
// Run karne ke baad, is file ko delete kar dena ya server se hata dena.

$password_to_set = "admin"; // <--- YAHAN APNA PASANDIDA PASSWORD LIKHEN

// Password ko hash karein
$hashed_password = password_hash($password_to_set, PASSWORD_DEFAULT);

echo "Apka Hashed Password Hai: <br>";
echo "<strong>" . $hashed_password . "</strong><br><br>";
echo "Is Hashed Password ko 'db_config.php' ya kisi aur secure file mein save kar lenge.";
echo "<br><br><b>WARNING: Is file ko run karne ke baad delete karna na bhulein!</b>";
?>