<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start the session for any necessary checks

// Check if the request method is POST and username is provided
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'])) {
    $username = $_POST['username'];
    
    // Define the directory where user data is stored
    $users_dir = __DIR__ . "/users";
    $user_dir = "$users_dir/$username";

    // Confirm the user directory exists
    if (file_exists($user_dir) && is_dir($user_dir)) {
        // Function to delete the directory and all its contents
        function delete_directory($dir) {
            foreach (scandir($dir) as $file) {
                if ($file === '.' || $file === '..') continue;
                $filePath = "$dir/$file";
                if (is_dir($filePath)) {
                    delete_directory($filePath);
                } else {
                    unlink($filePath); // Delete file
                }
            }
            rmdir($dir); // Remove directory
        }

        // Delete user directory
        delete_directory($user_dir);
        echo "User '$username' deleted successfully.";
    } else {
        echo "User directory does not exist.";
    }
} else {
    echo "Invalid request.";
}
?>