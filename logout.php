<?php
session_start();

// 1. Clear all session variables
$_SESSION = array();

// 2. If you want to kill the session cookie (completely clean slate)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destroy the session on the server
session_destroy();

// 4. Redirect back to the shop as a guest
header("Location: index.php");
exit();
?>