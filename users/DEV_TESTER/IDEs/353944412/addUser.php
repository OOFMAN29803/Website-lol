<?php
header('Content-Type: application/json');

// Example PHP logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['username'])) {
        $username = $input['username'];

        // Example response
        echo json_encode(["message" => "User $username added successfully"]);
    } else {
        echo json_encode(["error" => "Username not provided"]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Invalid request method"]);
}
?>
