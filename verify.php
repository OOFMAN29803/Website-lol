<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the base directory for users
$users_dir = __DIR__ . '/users';

// Get email and token from request
$email = $_GET['email'] ?? null;
$token = $_GET['token'] ?? null;

// Initialize response
$response = [
    'success' => false,
    'message' => 'Invalid email or token.'
];

// Find the user's folder
$found = false;
foreach (scandir($users_dir) as $username) {
    if ($username === '.' || $username === '..') continue; // Skip system folders

    $user_file = "$users_dir/$username/user.json";
    if (file_exists($user_file)) {
        $user_data = json_decode(file_get_contents($user_file), true);

        // Check if email and token match
        if ($user_data['email'] === $email && $user_data['session_token'] === $token) {
            $user_data['verified'] = true; // Mark user as verified

            // Save the updated user data
            file_put_contents($user_file, json_encode($user_data, JSON_PRETTY_PRINT));

            // Update the response
            $response = [
                'success' => true,
                'message' => 'User verified successfully.'
            ];
            $found = true;
            break;
        }
    }
}

// Show response
if ($response['success']) {
    echo "<p>You have been verified, thank you!</p>";
} else {
    echo "<p>" . $response['message'] . "</p>";
}
?>
