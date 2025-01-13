<?php
$file_path = 'content.txt';

if (file_exists($file_path)) {
    echo file_get_contents($file_path);
} else {
    echo "No content yet!";
}
?>
