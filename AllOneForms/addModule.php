<?php

// Set response headers for JSON
header('Content-Type: application/json');

// Get the JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Define the file path where the JSON data will be saved
$jsonFilePath = 'modules.json';

// Validate the input data
if (isset($data['divContainer'], $data['type'], $data['questionName'])) {
    $divContainer = htmlspecialchars($data['divContainer']);
    $type = htmlspecialchars($data['type']);
    $questionName = htmlspecialchars($data['questionName']); // Handle the new field

    // Prepare the data to be saved
    $newEntry = [
        'divContainer' => $divContainer,
        'type' => $type,
        'questionName' => $questionName // Include the new field
    ];

    // Load existing data from the JSON file if it exists
    if (file_exists($jsonFilePath)) {
        $existingData = json_decode(file_get_contents($jsonFilePath), true);
        if (!is_array($existingData)) {
            $existingData = [];
        }
    } else {
        $existingData = [];
    }

    // Add the new entry to the existing data
    $existingData[] = $newEntry;

    // Save the updated data back to the JSON file
    if (file_put_contents($jsonFilePath, json_encode($existingData, JSON_PRETTY_PRINT))) {
        $response = [
            'success' => true,
            'message' => 'Module added and saved successfully!',
            'data' => $newEntry
        ];
    } else {
        $response = [
            'success' => false,
            'error' => 'Failed to save data to JSON file.'
        ];
    }
} else {
    // If required fields are missing, return an error
    $response = [
        'success' => false,
        'error' => 'Invalid input. All fields (divContainer, type, questionName) are required.'
    ];
}

// Send the response
echo json_encode($response);

// Optional: Log the input data for debugging purposes
file_put_contents('debug.log', print_r($data, true));
