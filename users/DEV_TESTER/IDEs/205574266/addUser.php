<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['username'])) {
        $username = $input['username'];

        $file = 'allowedUsers.json';

        if (file_exists($file)) {
            $jsonData = json_decode(file_get_contents($file), true);
        } else {
            $jsonData = ["username" => []];
        }
        if (!in_array($username, $jsonData['username'])) {
            $jsonData['username'][] = $username;

            if (file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT))) {
                echo json_encode(["message" => "User $username added successfully"]);
            } else {
                echo json_encode(["error" => "Failed to save the username"]);
            }
        } else {
            echo json_encode(["message" => "User $username already exists"]);
        }
    } else {
        echo json_encode(["error" => "Username not provided"]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Invalid request method"]);
}
?>
