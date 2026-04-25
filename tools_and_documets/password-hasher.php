<?php
$password = "admin123";
$hashed = password_hash($password, PASSWORD_BCRYPT);
echo "Password: " . $password . "<br>";
echo "Hashed: " . $hashed;
?>

<!-- INSERT INTO `admins` (`username`, `password`, `email`) VALUES
    ('admin', '$2y$10$abc123def456ghi789jkl...', 'admin@shop.com'); 
-->