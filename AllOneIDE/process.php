<?php
// Check if a request is sent
if (isset($_POST['command'])) {
    $command = $_POST['command'];
    $data = isset($_POST['data']) ? $_POST['data'] : null; // Optional second parameter

    // Perform actions based on the command
    if ($command === 'writeFile') {
        $content = ""; // Example empty content
        $filePath = __DIR__ . "/$data"; // Use absolute path

        // Debugging: Log the file path
        error_log("Attempting to write to file: " . $filePath);

        // Write content to the file
        if (file_put_contents($filePath, $content)) {
            echo "$filePath was created successfully";
        } else {
            error_log("Failed to write to file: " . $filePath);
            echo "Failed to write to file. Something went wrong.";
        }
    } else {
        echo "Unknown command: " . htmlspecialchars($command);
    }
} else {
    echo "No command received.";
}
?>
