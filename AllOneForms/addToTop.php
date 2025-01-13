<?php

// Set response headers for JSON
header('Content-Type: application/json');

// Define the file paths
$jsonFilePath = 'modules.json';
$htmlFilePath = 'viewform.html';

// Get the raw JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate and process input
if (isset($data['TopName'], $data['smallParagraph']) || isset($data['divContainer'], $data['type'], $data['questionName'])) {
    // Handle the first format
    if (isset($data['TopName'], $data['smallParagraph'])) {
        $topName = htmlspecialchars($data['TopName']);
        $smallParagraph = htmlspecialchars($data['smallParagraph']);

        // Prepare the new entry
        $newEntry = [
            'TopName' => $topName,
            'smallParagraph' => $smallParagraph
        ];
    }

    // Handle the second format
    if (isset($data['divContainer'], $data['type'], $data['questionName'])) {
        $divContainer = htmlspecialchars($data['divContainer']);
        $type = htmlspecialchars($data['type']);
        $questionName = htmlspecialchars($data['questionName']);

        // Prepare the new entry
        $newEntry = [
            'divContainer' => $divContainer,
            'type' => $type,
            'questionName' => $questionName
        ];
    }

    // Load existing data from the JSON file if it exists
    if (file_exists($jsonFilePath)) {
        $existingData = json_decode(file_get_contents($jsonFilePath), true);
        if (!is_array($existingData)) {
            $existingData = [];
        }
    } else {
        $existingData = [];
    }

    // Add the new entry to the top of the array
    array_unshift($existingData, $newEntry);

    // Save the updated data back to the JSON file
    if (file_put_contents($jsonFilePath, json_encode($existingData, JSON_PRETTY_PRINT))) {
        // Generate the HTML form dynamically
        $htmlContent = generateHTMLForm($existingData);

        if (file_put_contents($htmlFilePath, $htmlContent)) {
            $response = [
                'success' => true,
                'message' => 'Data added successfully to the top of JSON and HTML form created!',
                'data' => $newEntry
            ];
        } else {
            $response = [
                'success' => false,
                'error' => 'Failed to save the HTML form.'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'error' => 'Failed to save data to JSON file.'
        ];
    }
} else {
    $response = [
        'success' => false,
        'error' => 'Invalid input. Fields required for one of the JSON formats.'
    ];
}

// Send the response
echo json_encode($response);

// Function to generate the HTML form
function generateHTMLForm($data) {
    $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Form</title>
    <style>
         @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');

body {
    padding: 0px;
    margin: 0px;
    background-image: url(LandingPage.png);
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    width: 100%;
    box-sizing: border-box;
    overflow: auto;
}

p, a, h1, small, h2, textarea, button, input {
    font-family: "Plus Jakarta Sans", sans-serif;
    font-optical-sizing: auto;
    font-style: normal;
}

button {
    padding: 5px;
    border: 3px solid #87B4D6;
    background-color: #A1D6FF;
    font-size: 25px;
    font-weight: 600;
    margin: 10px;
    border-radius: 10px;
}

.containerDiv {
    width: 60%;
    background-color: #DEFFF8;
    border: 3px solid #99E8C8;
    padding: 5px;
    margin-top: 20px;
    border-radius: 10px;
}

textarea {
    width: 52%;
    height: 100px;
    border: none;
    border: 3px solid #73A6FF;
    font-size: 15px;
    padding: 5px;
    border-radius: 10px;
    outline: none;
    box-sizing: border-box;
    resize: none;
}

.pTitleTop {
    font-size: 30px;
    font-weight: 600;
}
    </style>
</head>
<body>
HTML;

    foreach ($data as $entry) {
        if (isset($entry['TopName'], $entry['smallParagraph'])) {
            $html .= '<div class="containerDiv">
                <p class="pTitleTop">' . htmlspecialchars($entry['TopName']) . '</p>
                <form action="#" method="post">
                    <textarea name="' . htmlspecialchars($entry['TopName']) . '" placeholder="' . htmlspecialchars($entry['smallParagraph']) . '" required></textarea>
                    <button type="submit">Submit</button>
                </form>
            </div>';
        } elseif (isset($entry['divContainer'], $entry['type'], $entry['questionName'])) {
            $html .= '<div class="containerDiv">
                <p class="pTitleTop">' . htmlspecialchars($entry['questionName']) . '</p>
                <form action="#" method="post">
                    <textarea name="' . htmlspecialchars($entry['divContainer']) . '" placeholder="' . htmlspecialchars($entry['questionName']) . '" required></textarea>
                    <button type="submit">Submit</button>
                </form>
            </div>';
        }
    }

    $html .= '</body>
</html>';

    return $html;
}
