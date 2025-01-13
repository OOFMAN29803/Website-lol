<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$currentProduct = "AllOneStorage";  
$redirectSignup = "../signup.php";  

if (!isset($_COOKIE["username"])) {
    header("Location: $redirectSignup");
    exit();
}

$username = htmlspecialchars($_COOKIE["username"], ENT_QUOTES, 'UTF-8');
$userFilePath = "../users/$username/user.json";

if (!file_exists($userFilePath)) {
    echo "User file not found.";
    exit();
}

$fileJson = json_decode(file_get_contents($userFilePath), true);

if ($fileJson === null) {
    echo "Couldn't decode user JSON.";
    exit();
}

$entriesFilePath = "../users/$username/entries.json";

if (!file_exists($entriesFilePath)) {
    $defaultData = ["entries" => []];
    file_put_contents($entriesFilePath, json_encode($defaultData));
}

$data = json_decode(file_get_contents($entriesFilePath), true);

if ($data === null) {
    echo "Couldn't decode entries JSON.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - AllOneIDE</title>
		<link rel="icon" href="logo.ico" type="image/x-icon"/></link>

	<style>
	@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');



body {
background-color: #38EBC1;
padding: 0px;
margin: 0px;
background-image: url(LandingPage.png);
background-size: cover;
    background-repeat: no-repeat;
    background-position: center;
    width: 100%;
    margin: 0;
	    box-sizing: border-box;

	overflow: auto;
	
}
p, a, h1, small, h2, textarea, button, div, input {
font-family: "Plus Jakarta Sans", sans-serif;
font-optical-sizing: auto;
font-style: normal;
}

h1 {
color: white;
}
#buttonContainer {
width: 98%;
margin-auto;
} 

.topMainContainer {
background-color: #3BC49B;
border: 3px solid #36B38D;
padding: 7px;
width: 42%;
flex: 1;
display: flex;
border-radius: 10px;
margin: auto;
flex-direction: column;
height: inherit;
}

.mainParentJoiningContainer {
display: flex;
flex-direction: row;
width: 98%;
gap: 15px;
height: 400px;
box-sizing: border-box;
}

.createFunction {
padding: 7px;
font-weight: 700;
border-radius: 5px;
background-color: #62F0C1;
border: 3px solid #2DB381;
display: block;
margin: auto;
font-size: 20px;
box-sizing: border-box;
}
.createFunction:hover {
	background-color: #3CCFA3;
	border: 3px solid #33B08B;
}

#buttonContainer {
width: 98%;
flex-wrap: wrap;
flex-direction: row;
gap: 10px;
display: flex;
padding: 7px;
border: 3px  solid #1F5C43;
margin: auto;
height: auto;
box-sizing: border-box;
background-color: #2B805E;
}

.h1Current {
background-color: #3BC49B;
border: 3px solid #36B38D;
padding: 7px;
border-radius: 10px;
width: 45%;
}

.UserClicks {
width: 15%;
height: inherit;
border-radius: 10px;
background-color: #3CC99A;
height: 300px;
border: 3px solid #30A17B;
color: white;
font-weight: 700;
position: relative;
padding: 2px;
overflow: hidden;
}

.informationHolder {
	position: absolute;
	bottom: 0;
	width: 100%;
	box-sizing: border-box;
	border: none;
	padding: 5px;
		border: none;
		background-color: #54BA91;
		border-top: 3px solid #30A17B;
		box-sizing: border-box;
left: 0;
}

.joinInput {
width: 30%;
font-size: 30px;
background-color: #ffffff;
border: 3px solid #5BEBB1;
padding: 5px;
border-radius: 10px;
outline: none;
margin: auto;
font-weight: 700;

}
	</style>
</head>
<body>
<br>
<br>
<center>
<div class="mainParentJoiningContainer">
<div class="topMainContainer">
<h1>Create New</h1>
<img src="IDEShow.png" style="width: 55%; margin: auto; border: 3px solid #66DEB6; border-radius: 10px; user-select: none;"></img>
<button class="createFunction" onclick="window.location.href= 'newIDE.php'">New IDE</button>
</div>
<div class="topMainContainer">
<h1>Join Existing</h1>
<br>
<div>
<input class="joinInput" id="joinInput" placeholder="Username..."></input>
<input class="joinInput" id="joinInputNumber" placeholder="Number..."></input>
</div>
<button class="createFunction" onclick="redirectFunction()">Join IDE</button>
<p>The Username is the users AllOneTech Username | Your link should include 9 digits | Example: 292383740</p>
</div>
</div>
<br>
<br>
<h1 class="h1Current">Current IDE's</h1>
    <div id="buttonContainer">
        <?php
        if (!empty($data["entries"])) {
            foreach ($data["entries"] as $item) {
                $safeItem = htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
                echo "<div class='UserClicks' onclick=\"window.location='../users/$username/IDEs/$safeItem'\"><p class='font-size: 20px;'>Preview | Preview</p><div class='informationHolder'>IDE #$safeItem</div></div>";
            }
        } else {
            echo "<p>No entries found.</p>";
        }
        ?>
    </div>
	<script>
	function redirectFunction() {
	var inputRedirect = document.getElementById("joinInput").value;
	var inputRedirectNumber = document.getElementById("joinInputNumber").value;
	window.location.href = "http://localhost/AllOneTech/users/" + inputRedirect + "/IDEs/" + inputRedirectNumber + "/pageverify.php" 
	}
	</script>
</body>
</html>
