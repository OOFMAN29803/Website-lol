<?php

// Define the file path for the JSON data
$jsonFilePath = 'modules.json';

// Check if the file exists
if (file_exists($jsonFilePath)) {
    // Read and decode the JSON data
    $jsonData = json_decode(file_get_contents($jsonFilePath), true);

    // Start HTML output
    echo "<div id='output'>";
    foreach ($jsonData as $module) {
        echo "<div>";
        echo "<p><strong>Container:</strong> " . htmlspecialchars($module['divContainer']) . "</p>";
        echo "<p><strong>Type:</strong> " . htmlspecialchars($module['type']) . "</p>";
        echo "<p><strong>Question Name:</strong> " . htmlspecialchars($module['questionName']) . "</p>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<p>No modules found.</p>";
}

?>
