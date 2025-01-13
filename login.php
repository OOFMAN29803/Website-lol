<?php
session_start();

$user_dir = "users/";
$profile_page = "profile.php";

// Check if cookies are set and validate them
if (isset($_COOKIE['username']) && isset($_COOKIE['session_token'])) {
    $username = preg_replace("/[^a-zA-Z0-9_-]/", "", $_COOKIE['username']);
    $user_file = $user_dir . $username . "/user.json";

    if (file_exists($user_file)) {
        $user_data = json_decode(file_get_contents($user_file), true);

        // Validate session token from cookies
        if (hash_equals($user_data['session_token'], $_COOKIE['session_token'])) {
            session_regenerate_id(true);
            $_SESSION['session_token'] = $user_data['session_token'];

            // Redirect to the profile page
            header("Location: $profile_page");
            exit();
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = preg_replace("/[^a-zA-Z0-9_-]/", "", $_POST['username']);
    $password = $_POST['password'];

    $user_file = $user_dir . $username . "/user.json";

    if (file_exists($user_file)) {
        $user_data = json_decode(file_get_contents($user_file), true);

        if (password_verify($password, $user_data['password'])) {
            session_regenerate_id(true);
            $user_data['session_token'] = bin2hex(random_bytes(32));

            $fp = fopen($user_file, 'w');
            if (flock($fp, LOCK_EX)) {
                fwrite($fp, json_encode($user_data, JSON_PRETTY_PRINT));
                fflush($fp);
                flock($fp, LOCK_UN);
            }
            fclose($fp);

            $_SESSION['session_token'] = $user_data['session_token'];

            setcookie("username", $username, [
                'expires'  => time() + 2592000,
                'path'     => '/',
                'secure'   => true, // Set to false if testing locally without HTTPS
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            setcookie("session_token", $user_data['session_token'], [
                'expires'  => time() + 2592000,
                'path'     => '/',
                'secure'   => true, // Set to false if testing locally without HTTPS
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            header("Location: $profile_page");
            exit();
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "User not found. Please sign up first.";
    }
}
?>



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
width: 30%;
background-color: #4F72FF;
border: 3px solid #334AA6;
border-radius: 20px;
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
<div class="inputHolder" id="inputHolder">
<center>
<form method="POST" action="">
<input placeholder="Username. . ."  type="text" name="username" required id="input1"><br>
<input placeholder="Password. . ." type="password" name="password" required id="input2"><br>
 <button type="submit" id="buttony">Log In</button><button id="buttony" onclick="window.location.href = 'signup.php'" style="margin-left: 5px;">Sign Up instead</button>
</form>
</div>

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

 }
</script>