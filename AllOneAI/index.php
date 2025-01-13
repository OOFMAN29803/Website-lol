<?php
session_start();

$username = ''; // Initialize to avoid undefined variable warnings

function load_user_data($username) {
    $user_file = "users/$username/user.json";
    if (file_exists($user_file)) {
        return json_decode(file_get_contents($user_file), true);
    }
    return null;
}

function verify_password($input_password, $stored_password) {
    return password_verify($input_password, $stored_password);
}

function regenerate_session_token(&$user_data, $username) {
    $user_data['session_token'] = bin2hex(random_bytes(32));
    file_put_contents("users/$username/user.json", json_encode($user_data, JSON_PRETTY_PRINT));
    return $user_data['session_token'];
}

function set_session_and_cookie($session_token) {
    $_SESSION['session_token'] = $session_token;
    setcookie("session_token", $session_token, [
        'expires' => time() + 2592000, // 1 month validity
        'path' => '/',
        'secure' => true, // Send only over HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? ''; // Assign the submitted username to $username
    $password = $_POST['password'] ?? '';
    $profile_page = "index.html";

    $user_data = load_user_data($username);

    if ($user_data && verify_password($password, $user_data['password'])) {
        $session_token = regenerate_session_token($user_data, $username);
        set_session_and_cookie($session_token);

        // Uncomment if redirection is needed
        // header("Location: $profile_page");
        // exit();
    } else {
        $error_message = "Invalid username or password. Please try again.";
		header("Location: $profile_page");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-16">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AllOneAI</title>
	<link rel="icon" href="Icons/logo.ico" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="styling.css">
</head>
<body>
<center>
    <!-- Display the username -->
    <div class="userHold"><p><?php echo htmlspecialchars($username); ?></p></div>
    <div id="infoButton" alt="infoButton" onclick="openDisclaimer()">🛈</div>
    <div id="modelSelector" onclick="modelChangerDiv()"><p style="margin-top: 10px;">AllOneLM Mini</p></div>
	<br>
		<div class="changerDiv" id="changerDiv"> 
        <div class="changerModelDiv">
            <div class="click12bButton">
                <p class="AllOne12bP">AllOneLM Mini (DEMO) (Using)</p>
            </div>
			</div>
			<br>
			<div class="changerModelDiv">
            <div class="click144MButton">
                <p class="AllOne12bP">AllOneLM (New) (Coming Soon)</p>
            </div>
            </div>
        </div>
	</div> 
    <input type="text" id="inputSentence" placeholder="Enter text here..."></input>
    <button id="submitButton">Submit</button>
	<div class="infoContainer" id="infoContainer"><p class="disclaimer">AllOneLM can generate innaccurate information, verify your results. This model is an early view of more efficient and better models that will work effectively. The sentences or prompts that you send through can at any time be collected, but aren't currently. by using the model you agree to collection of any data, although none is being collected right now.</p><button onclick="removeDisclaimer()" class="okButton">Ok</button></div>
	</center>
<script type="module">
   import { Client } from 'https://cdn.jsdelivr.net/npm/@gradio/client@latest/dist/index.min.js';

    let client;

    async function initClient() {
        client = await Client.connect("OOFMAN29803/AllOneAINew");
    }

    initClient();

    document.getElementById('submitButton').addEventListener('click', async () => {
        console.log("Button clicked");

        const inputField = document.getElementById('inputSentence');
        const inputSentence = inputField.value;

        inputField.value = '';

        const userInput = document.createElement("p");
        const userResultContainer = document.createElement("div");

        userResultContainer.style.width = "50%";
        userResultContainer.style.maxHeight = "70vh";
        userResultContainer.style.overflow = "auto";
        userResultContainer.style.backgroundColor = "#B0BDFF";
        userResultContainer.style.borderRadius = "10px";
        userResultContainer.style.padding = "4px";
        userResultContainer.style.marginTop = "20px";
        userResultContainer.style.float = "right";
        userResultContainer.style.marginBottom = "20px";
        userResultContainer.style.border = "3px solid #8993C7";
        userResultContainer.style.fontFamily = "'Plus Jakarta Sans', sans-serif";

        userInput.textContent = inputSentence;
        userResultContainer.appendChild(userInput);
        document.body.appendChild(userResultContainer);

        if (!inputSentence) {
            alert('Please enter a sentence.');
            return;
        }

        const resultContainer = document.createElement("div"); // Define resultContainer here
        resultContainer.className = 'glowing-container'; // Apply the glowing animation class

        document.body.appendChild(resultContainer);

        try {
            const result = await client.predict("/predict", { 		
                src_sentence: inputSentence, 
            });

            console.log('API Response:', result); // Log the full response for debugging

            // Make sure you are accessing the correct property in the API response
            const resultText = result.data[0] || ''; // Update this based on the actual response structure
            animateResult(resultText, resultContainer);
        } catch (error) {
            console.error('There was a problem with the API call:', error);
            const resultDiv = document.createElement("p");
            resultDiv.innerText = 'Error: ' + error.message;
            resultContainer.appendChild(resultDiv);
        }
    });

    function animateResult(resultText, resultContainer) {
        if (typeof resultText !== 'string') {
            resultText = JSON.stringify(resultText); // Convert to string if not already
        }

        const words = resultText.split(' ');
        words.forEach((word, index) => {
            const wordElement = document.createElement("span");
			console.log(resultText)
			if (resultText === "Error: Cannot read properties of undefined (reading 'predict')") {
				wordElement.textContent = "Sorry either the server is not working or you do not have internet";
            wordElement.className = 'word';
            wordElement.style.animationDelay = `${index * 0}s`; // Delay for each word
            wordElement.style.fontFamily = "'Plus Jakarta Sans', sans-serif"; // Apply the font
			wordElement.style.color = "red"
			wordElement.style.fontWeight = "700"
            resultContainer.appendChild(wordElement);
			} else{
			
			}
            wordElement.textContent = word;
            wordElement.className = 'word';
            wordElement.style.animationDelay = `${index * 0.1}s`; // Delay for each word
            wordElement.style.fontFamily = "'Plus Jakarta Sans', sans-serif"; // Apply the font

            resultContainer.appendChild(wordElement);
        });
    }
</script>
<script>
function modelChangerDiv() {
var modelDiv = document.getElementById("changerDiv")
var modelSelector = document.getElementById("modelSelector")
modelDiv.style.display = "block"
modelSelector.onclick = modelClose
}

function modelClose() {
var modelDiv2 = document.getElementById("changerDiv")
var modelSelector = document.getElementById("modelSelector")
modelDiv2.style.display = "none"
modelSelector.onclick = modelChangerDiv
}

function openDisclaimer() {
var container = document.getElementById("infoContainer")
container.style.display = "block"
}

function removeDisclaimer() {
var container = document.getElementById("infoContainer")
container.style.display = "none"
}
</script>
</body>
</html>
