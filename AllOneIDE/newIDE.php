<?php

session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if username is available in the cookie
if (!isset($_COOKIE["username"])) {
    header("Location: ../signup.php");
    exit();
}

$username = htmlspecialchars($_COOKIE["username"]);

// Function to copy files and directories recursively
function copyDirectory($source, $destination) {
    if (!is_dir($source)) {
        die("Source directory does not exist: $source");
    }

    if (!is_dir($destination)) {
        if (!mkdir($destination, 0755, true)) {
            die("Failed to create destination directory: $destination");
        }
    }

    $files = scandir($source);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $sourceFile = $source . '/' . $file;
            $destinationFile = $destination . '/' . $file;

            if (is_dir($sourceFile)) {
                copyDirectory($sourceFile, $destinationFile);
            } else {
                if (!copy($sourceFile, $destinationFile)) {
                    die("Failed to copy file: $sourceFile to $destinationFile");
                }
            }
        }
    }
}

$randomNumber = rand(100000000, 999999999);
$sourceDirectory = realpath('./IDEs/Example');
$destinationDirectory = "../users/$username/IDEs/$randomNumber";

copyDirectory($sourceDirectory, $destinationDirectory);

$allowedUsersFile = "$destinationDirectory/allowedUsers.json";
$allowedUsersData = [
    "username" => [$username]
];
file_put_contents($allowedUsersFile, json_encode($allowedUsersData, JSON_PRETTY_PRINT));

$entriesFile = "../users/$username/entries.json";
if (!file_exists($entriesFile)) {
    file_put_contents($entriesFile, json_encode(["entries" => []], JSON_PRETTY_PRINT));
}

$data = json_decode(file_get_contents($entriesFile), true);
$data['entries'][] = $randomNumber;
file_put_contents($entriesFile, json_encode($data, JSON_PRETTY_PRINT));

header("Location: ../users/$username/IDEs/$randomNumber/pageverify.php");
exit();

?>
