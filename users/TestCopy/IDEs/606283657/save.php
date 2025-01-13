<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file_path = 'content.txt';
    $new_content = $_POST['content'] ?? '';

    // Overwrite the file with new content
    file_put_contents($file_path, $new_content);

    echo "Content updated successfully!";
} else {
    echo "Invalid request.";
}
?>
