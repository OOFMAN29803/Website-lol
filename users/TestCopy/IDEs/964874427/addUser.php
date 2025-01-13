<?php
$jsonFile = 'allowedUsers.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data from the request
    $input = json_decode(file_get_contents('php://input'), true);
    $newUsername = $input['username'] ?? null;

    if (!$newUsername) {
        echo json_encode(["success" => false, "message" => "Username is required."]);
        exit;
    }

    if (!file_exists($jsonFile)) {
        echo json_encode(["success" => false, "message" => "JSON file not found."]);
        exit;
    }

    $jsonData = json_decode(file_get_contents($jsonFile), true);

    if (in_array($newUsername, $jsonData['allowedUsers'])) {
        echo json_encode(["success" => false, "message" => "Username already exists!"]);
        exit;
    }

    $jsonData['allowedUsers'][] = $newUsername;

    file_put_contents($jsonFile, json_encode($jsonData, JSON_PRETTY_PRINT));

    echo json_encode(["success" => true, "message" => "Username '$newUsername' added successfully."]);
    exit;
}

echo json_encode(["success" => false, "message" => "Invalid request method."]);
?>
