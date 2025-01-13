<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = "";

if (isset($_COOKIE["username"])) {
    $username = htmlspecialchars($_COOKIE["username"]);
} else {
    header("Location: ../signup.php");
    exit();
}

$namesAllowedFile = "allowedUsers.json";

if (file_exists($namesAllowedFile)) {
    $allowedUsers = json_decode(file_get_contents($namesAllowedFile), true); 

    if (in_array($username, $allowedUsers['username'])) {
        header("Location: index.html");
        exit();
    } else {
        echo "<p>You are not allowed, as you haven't been accepted.</p>";
        exit();
    }
} else {
    echo "Error 322, Severe Error, Contact Support";
    exit();
}

?>
<body>
<p>You are not allowed!</p>
</body>
