<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // reCAPTCHA Validation
    $recaptcha_token = $_POST['recaptcha_token'] ?? '';
    $secret_key = '6Ldb5qAqAAAAADdwSlbRW_R66ET9POEL0UfxCahC';

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = ['secret' => $secret_key, 'response' => $recaptcha_token];
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $response_data = json_decode($response, true);

if (!$response_data['success'] || $response_data['score'] < 0.5 || $response_data['action'] !== 'submit') {
    echo "reCAPTCHA validation failed. Please try again.";
    exit();
}

    // User Input
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate a unique session token
    $session_token = bin2hex(random_bytes(32));

    // Define the directory for storing user data
    $users_dir = __DIR__ . "/users";
    $user_dir = "$users_dir/$username";

    // Create users directory if it doesn't exist
    if (!file_exists($users_dir) && !mkdir($users_dir, 0775, true)) {
        die("Failed to create 'users' directory.");
    }

    // Check if username already exists
    if (file_exists($user_dir)) {
        echo "Username already taken, please choose another one.";
        exit();
    }

    // Create user directory
    if (!mkdir($user_dir)) {
        die("Failed to create user directory: $user_dir");
    }

    // Store user data
    $user_data = [
        'email' => $email,
        'password' => $hashed_password,
        'session_token' => $session_token,
        'storage' => "none",
        'subscription' => "free",
        'beta' => 'no',
        'verified' => 'false',
		'products' => []
    ];

    if (!file_put_contents("$user_dir/user.json", json_encode($user_data, JSON_PRETTY_PRINT))) {
        die("Failed to save user data.");
    }

    // Set session token
    $_SESSION['session_token'] = $session_token;

    // Send verification email
    sendEmail($username, $session_token, $email);

    // Redirect to profile
    header("Location: profile.php");
    exit();
}

function sendEmail($username, $session_token, $email) {
    $verifylink = "https://allonetech.site/verify.php?email=" . urlencode($email) . "&token=" . urlencode($session_token);
    $to = $email;
    $subject = "Verify to get access to AllOneTech services";

    $message = "
    <html>
    <head><title>Verify Your Email</title></head>
    <body>
        <p>Click the link below to verify your email:</p>
        <a href='$verifylink'>Verify Email</a>
        <p>Thank you for joining AllOneTech!</p>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: <no-reply@allonetech.site>\r\n";

    mail($to, $subject, $message, $headers);
}
?>
<head>
<title>Sign Up</title>
<script src="https://www.google.com/recaptcha/api.js?render=6Ldb5qAqAAAAAO7JPfwETu3r33JLkwIw-XZhEZX7"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');

body {
background-color: #B3E8FF;
padding: 0px;
margin: 0px;
font-family: "Plus Jakarta Sans", sans-serif;
  display: flex;
  align-items: center;
  justify-content: center;

}
p, button, a, h1, small, h2, div, input {
font-family: "Plus Jakarta Sans", sans-serif;
font-optical-sizing: auto;
font-style: normal;
}

.inputHolder {
width: 80%;
background-color: #4F72FF;
border: 3px solid #334AA6;
border-radius: 10px;
padding: 15px;
}

input {
width: 50%;
height: 40px;
border-radius: 5px;
background-color: #ffffff;
border: 3px solid #81E6C7;
outline: none;
transition: 0.2s ease;
margin-bottom: 10px;

}

button {
font-family: "Plus Jakarta Sans", sans-serif;
font-optical-sizing: auto;
font-style: normal;
height: 40px;
padding: 2px;
font-weight: 700;
border: 3px solid #5964C2;
border-radius: 5px;
background-color: #7583FF;
color: white;
font-size: 20px;
transition: 0.3s ease;
padding: 5px;
}

button:hover {
background-color: #5C67C9;
border: 3px solid #31376B;
}

input:hover {
background-color: #E0E0E0;
border: 3px solid #82C2BB;
}
</style>
</head>
<div style="width: 70%;">
<center>
<div class="inputHolder" id="inputHolder">
<center>
<form method="POST" action="" id="signup-form">
<input placeholder="Username. . ." type="text" name="username" required id="input1" maxlength="50"><br>
<input placeholder="Email. . ." type="email" name="email" required id="input2" maxlength="255"><br>
<input placeholder="Password. . ." type="password" name="password" required id="input3" maxlength="50"><br>
<input type="hidden" name="recaptcha_token" id="recaptcha-token">
  <button type="submit">Sign Up</button>
</form>

</div><br>

<div style="width: 90%; padding: 5px; border-radius: 10px; font-size: 20px; background-color: #4F72FF; border: 3px solid #334AA6; ">
<p>This site uses reCAPTCHA to verify you are a human.
<br>
When you press Sign Up, an email will be sent to your inbox using the email that was used in your input field.
</p>
</div>
</div>
</center>

<script>
if (navigator.userAgent.match(/iPhone/i)   || navigator.userAgent.match(/iPad/i)  || navigator.userAgent.match(/Android/i)) { 
var inputy = document.getElementById("inputHolder")
var buttony = document.getElementById("buttony")
buttony.style.width = "300px"
buttony.style.height = "80px"
buttony.style.fontSize = "30px"
inputy.style.width = "90%"
var input1 = document.getElementById("input1")
input1.style.width = "70%"
input1.style.height = "90px"
input1.style.fontSize = "50px"
var input2 = document.getElementById("input2")
input2.style.width = "70%"
input2.style.height = "90px"
input2.style.fontSize = "50px"
var input3 = document.getElementById("input3")
input3.style.width = "70%"
input3.style.height = "90px"
input3.style.fontSize = "50px"
 }
</script>
<script>
  grecaptcha.ready(function () {
    document.getElementById('signup-form').addEventListener('submit', function (e) {
      e.preventDefault(); // Stop the form from submitting immediately
      grecaptcha.execute('6Ldb5qAqAAAAAO7JPfwETu3r33JLkwIw-XZhEZX7', { action: 'submit' }).then(function (token) {
        // Add the token to the hidden field
        document.getElementById('recaptcha-token').value = token;
        // Now submit the form
        e.target.submit();
      });
    });
  });
</script>
