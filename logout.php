<?php
session_start();

// Clear session token
if (isset($_SESSION['session_token'])) {
    unset($_SESSION['session_token']);
}

// Expire the session token cookie
if (isset($_COOKIE['session_token'])) {
    setcookie('session_token', '', time() - 2592000, '/', '', true, true); // Expire the cookie
}
if (isset($_COOKIE['username'])) {
    setcookie('username', '', time() - 2592000, '/', '', true, true); // Expire the cookie
}

// Destroy the session
session_destroy();

// Confirm logout
?>
<html>
<head>Logged Out</head>
<style>
body {
background-color: #D6FFF3;
padding: 0px;
margin: 0px;
}
p, button, a, h1, small, h2, svg {
font-family: "Plus Jakarta Sans", sans-serif;
font-optical-sizing: auto;
font-style: normal;
}
</style>
<body>
<p>You have been logged out. Thank you for using AllOneTech services!</p>
</body>
</html>