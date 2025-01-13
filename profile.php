<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if session token is set
if (!isset($_SESSION['session_token'])) {
    // If not, redirect to login page
    header("Location: login.php");
    exit();
}

// Get the session token from the session
$session_token = $_SESSION['session_token'];

// Define the directory for storing user data
$users_dir = __DIR__ . "/users";

// Scan the users directory to find the matching user
$found_user = false;
foreach (scandir($users_dir) as $username) {
    if ($username === '.' || $username === '..') {
        continue;
    }

    $user_file = "$users_dir/$username/user.json";

    // Check if the user.json file exists
    if (file_exists($user_file)) {
        $user_data = json_decode(file_get_contents($user_file), true);

        // Check if the session token matches
        if ($user_data['session_token'] === $session_token) {
            $found_user = true;
            break;
        }
    }
}

if (!$found_user) {
    // If no user is found with the session token, redirect to login page
    header("Location: login.php");
    exit();
}

if ($user_data['verified'] === 'false') {
	header("Location: verificationWarning.html");
}
// Now you have the $user_data array with user info
$email = $user_data['email'];
?>
<!DOCTYPE HTML>
<html>
<head>
	<link rel="icon" href="profileImage.ico" type="image/x-icon"/></link>
<title><?php echo $username; ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');

body {
    padding: 10px;
}


p, h1, h2, small, svg {
    font-family: "Plus Jakarta Sans", sans-serif;
    font-optical-sizing: auto;
    font-style: normal;
}
h1 {
	font-weight: 800;
}
.AllOneAIContainer, 
.AllOneStorageContainer, 
.AllOneWebContainer, 
.AllOneLabsContainer, 
.AllOneUpdatesContainer,
.AllOneStoreContainer {
flex: 1; 
    height: auto;
    border-radius: 10px; 
    overflow: hidden;
	padding: 5px;
    text-align: center; 
    background-color: white; 
    box-sizing: border-box;
}

.AllOneAIContainer {
    background-color: #35DE6D;
    border: 3px solid #2DBD5D;

}

.AllOneLogoutContainer {
    width: 80%;
    background-color: #FF6161;
    border: 3px solid #C94D4D;
    border-radius: 10px;
	overflow: hidden;
}

.AllOneLogoutContainer:hover {
background-color: #D45D5D;
border: 3px solid #AD4C4C;
transition: 0.2s ease;
}

.AllOneTermsContainer {
    width: 80%;
    background-color: #7EE6C0;
    border: 3px solid #66BA9B;
    border-radius: 10px;
}

.AllOneTermsContainer:hover {
background-color: #52CC97;
border: 3px solid #48B384;
transition: 0.2s ease;
}

.AllOneDeleteContainer {
    width: 80%;
    background-color: #FF2424;
    border: 3px solid #C71C1C;
    border-radius: 10px;
	transition: 0.2s ease;
}
.AllOneDeleteContainer:hover {
background-color: #D12A2A;
border: 3px solid #B82525;
}

.ExperimentLinkButton2 {
    width: 200px;
    margin-top: 15px;
    font-family: "Plus Jakarta Sans", sans-serif;
    font-optical-sizing: auto;
    font-style: normal;
    height: 48px;
    padding: 2px;
    font-weight: 700;
    border: 3px solid #4ABA7B;
    border-radius: 5px;
    background-color: #6ED497;
    font-size: 20px;
    transition: 0.2s ease;
    margin-top: 40px;
}
.ExperimentLinkButton2:hover {
    background: linear-gradient(270deg, #00008B, #FFC0CB);
    background-size: 600% 600%;
    border: 3px solid; /* Set border size */
    border-image: linear-gradient(270deg, #000066, #FFA6B2) 1; /* Border gradient */
    -webkit-animation: GradientFlow 15s ease infinite;
    -moz-animation: GradientFlow 15s ease infinite;
    animation: GradientFlow 15s ease infinite;
    border-radius: 10px solid;
    color: white;
}

@-webkit-keyframes GradientFlow {
    0% {
        background-position: 0% 50%;
        border-image-source: linear-gradient(270deg, #000066, #FFA6B2); /* Darker gradient for border */
    }
    50% {
        background-position: 100% 50%;
        border-image-source: linear-gradient(270deg, #FF9CB0, #000066); /* Border gradient flow */
    }
    100% {
        background-position: 0% 50%;
        border-image-source: linear-gradient(270deg, #000066, #FFA6B2); /* Darker gradient for border */
    }
}

@-moz-keyframes GradientFlow {
    0% {
        background-position: 0% 50%;
        border-image-source: linear-gradient(270deg, #000066, #FFA6B2); /* Darker gradient for border */
    }
    50% {
        background-position: 100% 50%;
        border-image-source: linear-gradient(270deg, #FF9CB0, #000066); /* Border gradient flow */
    }
    100% {
        background-position: 0% 50%;
        border-image-source: linear-gradient(270deg, #000066, #FFA6B2); /* Darker gradient for border */
    }
}

@keyframes GradientFlow {
    0% {
        background-position: 0% 50%;
        border-image-source: linear-gradient(270deg, #000066, #FFA6B2); /* Darker gradient for border */
    }
    50% {
        background-position: 100% 50%;
        border-image-source: linear-gradient(270deg, #FF9CB0, #000066); /* Border gradient flow */
    }
    100% {
        background-position: 0% 50%;
        border-image-source: linear-gradient(270deg, #000066, #FFA6B2); /* Darker gradient for border */
    }
}



.profileDiv {
width: 40%;
border-radius: 20px;

font-weight: 800;
padding: 1px;
}

.AllOneWebContainer {
	background-color: #A3B9FF;
	border: 3px solid #7F90C7;
}

.AllOneStoreContainer {
background-color: #FF936B;
border: 3px solid #CC7656;
}
.ExperimentLinkButton3 {
width: 70%;
margin-top: 25px;
font-family: "Plus Jakarta Sans", sans-serif;
font-optical-sizing: auto;
font-style: normal;
height: 48px;
padding: 2px;
font-weight: 700;
border: 3px solid #4347BF;
border-radius: 5px;
background-color: #5459F0;
font-size: 20px;
transition: 0.2s ease;
color: white;
}
.ExperimentLinkButton3:hover {
background-color: #4145BA;
border: 3px solid #3A3DA6;
}

.AllOneStorageContainer {
	border: 3px solid #575757;
	background-color: #F9FF42;

	
}
.ExperimentLinkButton4 {
width: 70%;
font-family: "Plus Jakarta Sans", sans-serif;
font-optical-sizing: auto;
font-style: normal;
height: 48px;
padding: 2px;
font-weight: 700;
border-radius: 5px;
background-color: #D9DE39;
border: 3px solid #000000;
font-size: 20px;
transition: 0.2s ease;
}

.ExperimentLinkButton {
width: 70%;
margin-top: 45px;
font-family: "Plus Jakarta Sans", sans-serif;
font-optical-sizing: auto;
font-style: normal;
height: 48px;
padding: 2px;
font-weight: 700;
border: 3px solid #D99393;
border-radius: 5px;
background-color: #FFADAD;
font-size: 20px;
transition: 0.2s ease;
}
.ExperimentLinkButton:hover {
background-color: #B07777;
border: 3px solid #A16D6D;
}

.ExperimentLinkButton2 {
width: 70%;
margin-top: 15px;
font-family: "Plus Jakarta Sans", sans-serif;
font-optical-sizing: auto;
font-style: normal;
height: 48px;
padding: 2px;
font-weight: 700;
border: 3px solid #4ABA7B;
border-radius: 5px;
background-color: #6ED497;
font-size: 20px;
transition: 0.2s ease;
margin-top: 40px;
}

.ExperimentLinkButton5 {
width: 70%;
margin-top: 15px;
font-family: "Plus Jakarta Sans", sans-serif;
font-optical-sizing: auto;
font-style: normal;
height: 48px;
padding: 2px;
font-weight: 700;
border: 3px solid #000000;
border-radius: 5px;
background-color: #FFFF4A;
font-size: 20px;
transition: 0.2s ease;
margin-top: 40px;
}

.ExperimentLinkButton2:hover {
  background: linear-gradient(270deg, #00008B, #FFC0CB);
  background-size: 600% 600%;
  border: 3px solid; /* Set border size */
  border-image: linear-gradient(270deg, #000066, #FFA6B2) 1; /* Border gradient */
  -webkit-animation: GradientFlow 15s ease infinite;
  -moz-animation: GradientFlow 15s ease infinite;
  animation: GradientFlow 15s ease infinite;
  border-radius: 10px solid;
  color: white;
}
.AllOneLabsContainer {
	background-color: #FF8F8F;
	border: 3px solid #C97171;

}

.AllOneUpdatesContainer {
	background-color: #FFADE4;
	border: 3px solid #DB95C4;


}

.ExperimentLinkButton6 {
width: 70%;
margin-top: 15px;
font-family: "Plus Jakarta Sans", sans-serif;
font-optical-sizing: auto;
font-style: normal;
height: 48px;
padding: 2px;
font-weight: 700;
border: 3px solid #B576C2;
border-radius: 5px;
background-color: #CF87DE;
font-size: 20px;
transition: 0.2s ease;
margin-top: 40px;
}

.ExperimentLinkButton6:hover {
border: 3px solid #D1AA7D;
background-color: #FFCF99;
}


@-webkit-keyframes GradientFlow {
  0% {
    background-position: 0% 50%;
    border-image-source: linear-gradient(270deg, #000066, #FFA6B2); /* Darker gradient for border */
  }
  50% {
    background-position: 100% 50%;
    border-image-source: linear-gradient(270deg, #FF9CB0, #000066); /* Border gradient flow */
  }
  100% {
    background-position: 0% 50%;
    border-image-source: linear-gradient(270deg, #000066, #FFA6B2); /* Darker gradient for border */
  }
}

@-moz-keyframes GradientFlow {
  0% {
    background-position: 0% 50%;
    border-image-source: linear-gradient(270deg, #000066, #FFA6B2); /* Darker gradient for border */
  }
  50% {
    background-position: 100% 50%;
    border-image-source: linear-gradient(270deg, #FF9CB0, #000066); /* Border gradient flow */
  }
  100% {
    background-position: 0% 50%;
    border-image-source: linear-gradient(270deg, #000066, #FFA6B2); /* Darker gradient for border */
  }
}

@keyframes GradientFlow {
  0% {
    background-position: 0% 50%;
    border-image-source: linear-gradient(270deg, #000066, #FFA6B2); /* Darker gradient for border */
  }
  50% {
    background-position: 100% 50%;
    border-image-source: linear-gradient(270deg, #FF9CB0, #000066); /* Border gradient flow */
  }
  100% {
    background-position: 0% 50%;
    border-image-source: linear-gradient(270deg, #000066, #FFA6B2); /* Darker gradient for border */
  }
}

#transformerDiv {
position: fixed;
background-color: #D6FFF3;
top: 0;
left: 0;
width: 0px;
transition: 0.2s ease;
}

.menuDiv {
width: 46px;
height: 46px;
border: 3px solid #62ADBD;
padding: 2px;
background-color: #73CCDE;
border-radius: 5px;
font-family: "Plus Jakarta Sans", sans-serif;
font-optical-sizing: auto;
font-style: normal;
font-weight: 800;
font-size: 30px;
text-align: center;
justify-content: center;
align-items: center;
color: white;
position: fixed;
right: 1%;
top: 2%;
}

.menuContainer {
width: 15%;
padding: 4px;
position: fixed;
background-color: #46E0B7;
border: 3px solid #38B392;
right: 1%;
top: 10%;
border-radius: 10px;
display: none;
overflow: hidden;
transition: 0.2s ease;
}

.titleP {
color: white; 
font-size: 20px;
font-weight: 700;
}

#deletePrompt {
width: 50%;
background-color: #FF8080;
border: 3px solid #D66C6C;
border-radius: 10px;
position: fixed;
top: 20%;
left: 25%;
padding: 4px;
transition: 0.3s ease;
height: auto;
display: none;
}

.parentHoldings {
    display: flex; 
    flex-wrap: nowrap; 
    justify-content: flex-start; 
    align-items: stretch; 
    gap: 10px; 
    overflow-x: auto;
    width: 100%; 
    padding: 10px;
    background-color: #5F82E3;
	border: 3px solid #435CA1;
    box-sizing: border-box;
    scrollbar-width: thin;
	border-radius: 5px;
}
	
.nameInput {
 background: linear-gradient(270deg, #426EFF, #9F7FEB, #FF9D73);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-size: 60px;
}

.ExperimentLinkButton7 {
width: 70%;
margin-top: 25px;
font-family: "Plus Jakarta Sans", sans-serif;
font-optical-sizing: auto;
font-style: normal;
height: 48px;
padding: 2px;
font-weight: 700;
border: 3px solid #D98566;
border-radius: 5px;
background-color: #FF9C78;
font-size: 20px;
transition: 0.2s ease;
color: white;
}
.ExperimentLinkButton7:hover {
background-color: #D47A46;
border: 3px solid #A86138;
}
</style>
</head>
<body>
<center>
<div id="transformerDiv"></div>
<div class="profileDiv"><p class="nameInput">Welcome, <?php echo htmlspecialchars($username); ?></p></div>
<br>
<h1>Welcome to the AllOneTech central hub.</h1>
<br>
<div class="parentHoldings">
<div class="AllOneAIContainer">
<h2><b style="color: white;">AllOneAI</b></h2>
<br>
<small class="infoLab">Easy to Use AI that is in a demo stage right now</small>
<br>
<button onclick="window.location.href = 'AllOneAI/index.php'" class="ExperimentLinkButton2">Chat Now</button>
</div>
<div class="AllOneLabsContainer">
<h2><b style="color: white;">AllOneLabs</b></h2>
<br>
<small class="infoLab">Cutting edge papers for New AI and technology stuff</small>
<br>
<button class="ExperimentLinkButton">Soon</button>
</div>
<div class="AllOneWebContainer">
<h2><b style="color: white;">AllOneWeb</b></h2>
<br>
<small class="infoLab">Web stuff like Notebook Revamped and other services on the cloud or things that could easily be done in browser</small>
<br>
<button class="ExperimentLinkButton3" onclick="window.location.href = 'AllOneWeb/NotebookRevamped.html'">Open</button>
</div>
<div class="AllOneStorageContainer">
<h2><b style="color: black;">AllOneStorage</b></h2>
<br>
<small class="infoLab">Upcoming Cloud storage for cheap stuff only $1.49 for 100GB estimated to launch in January</small>
<br>
<button class="ExperimentLinkButton5" onclick="window.location.href='#'">Coming Soon</button>
</div>
<div class="AllOneStoreContainer">
<h2><b style="color: black;">AllOneStore</b></h2>
<br>
<small class="infoLab">AllOneStore is a place where you can buy all the subscriptions to help you with everything you need.</small>
<br>
<button class="ExperimentLinkButton7" onclick="window.location.href='AllOneIDE/Home.php'">Coming Soon</button>
</div>
<div class="AllOneUpdatesContainer">
<h2><b style="color: black;">AllOneTech Updates</b></h2>
<br>
<small class="infoLab">This is where things like company reshufflings, hiring callouts, AI releases, important papers, and others are all released.</small>
<br>
<button class="ExperimentLinkButton6" onclick="window.location.href='Updates/index.html'">Open</button>
</div>
</div>
<h1>Paid Subscriptions</h1>
<div class="parentHoldings" style="height: 350px;">
<h1 style="margin: auto;">Subscriptions are coming soon. Stay Tuned...</h1>
</div>
<h1>Chosen Weekly Instagram Post</h1>
<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-permalink="https://www.instagram.com/p/DECcB4wSF5b/?utm_source=ig_embed&amp;utm_campaign=loading" data-instgrm-version="14" style=" background:#FFF; border:0; border-radius:3px; box-shadow:0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width:540px; min-width:326px; padding:0; width:99.375%; width:-webkit-calc(100% - 2px); width:calc(100% - 2px);"><div style="padding:16px;"> <a href="https://www.instagram.com/p/DECcB4wSF5b/?utm_source=ig_embed&amp;utm_campaign=loading" style=" background:#FFFFFF; line-height:0; padding:0 0; text-align:center; text-decoration:none; width:100%;" target="_blank"> <div style=" display: flex; flex-direction: row; align-items: center;"> <div style="background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 40px; margin-right: 14px; width: 40px;"></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 100px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 60px;"></div></div></div><div style="padding: 19% 0;"></div> <div style="display:block; height:50px; margin:0 auto 12px; width:50px;"><svg width="50px" height="50px" viewBox="0 0 60 60" version="1.1" xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g transform="translate(-511.000000, -20.000000)" fill="#000000"><g><path d="M556.869,30.41 C554.814,30.41 553.148,32.076 553.148,34.131 C553.148,36.186 554.814,37.852 556.869,37.852 C558.924,37.852 560.59,36.186 560.59,34.131 C560.59,32.076 558.924,30.41 556.869,30.41 M541,60.657 C535.114,60.657 530.342,55.887 530.342,50 C530.342,44.114 535.114,39.342 541,39.342 C546.887,39.342 551.658,44.114 551.658,50 C551.658,55.887 546.887,60.657 541,60.657 M541,33.886 C532.1,33.886 524.886,41.1 524.886,50 C524.886,58.899 532.1,66.113 541,66.113 C549.9,66.113 557.115,58.899 557.115,50 C557.115,41.1 549.9,33.886 541,33.886 M565.378,62.101 C565.244,65.022 564.756,66.606 564.346,67.663 C563.803,69.06 563.154,70.057 562.106,71.106 C561.058,72.155 560.06,72.803 558.662,73.347 C557.607,73.757 556.021,74.244 553.102,74.378 C549.944,74.521 548.997,74.552 541,74.552 C533.003,74.552 532.056,74.521 528.898,74.378 C525.979,74.244 524.393,73.757 523.338,73.347 C521.94,72.803 520.942,72.155 519.894,71.106 C518.846,70.057 518.197,69.06 517.654,67.663 C517.244,66.606 516.755,65.022 516.623,62.101 C516.479,58.943 516.448,57.996 516.448,50 C516.448,42.003 516.479,41.056 516.623,37.899 C516.755,34.978 517.244,33.391 517.654,32.338 C518.197,30.938 518.846,29.942 519.894,28.894 C520.942,27.846 521.94,27.196 523.338,26.654 C524.393,26.244 525.979,25.756 528.898,25.623 C532.057,25.479 533.004,25.448 541,25.448 C548.997,25.448 549.943,25.479 553.102,25.623 C556.021,25.756 557.607,26.244 558.662,26.654 C560.06,27.196 561.058,27.846 562.106,28.894 C563.154,29.942 563.803,30.938 564.346,32.338 C564.756,33.391 565.244,34.978 565.378,37.899 C565.522,41.056 565.552,42.003 565.552,50 C565.552,57.996 565.522,58.943 565.378,62.101 M570.82,37.631 C570.674,34.438 570.167,32.258 569.425,30.349 C568.659,28.377 567.633,26.702 565.965,25.035 C564.297,23.368 562.623,22.342 560.652,21.575 C558.743,20.834 556.562,20.326 553.369,20.18 C550.169,20.033 549.148,20 541,20 C532.853,20 531.831,20.033 528.631,20.18 C525.438,20.326 523.257,20.834 521.349,21.575 C519.376,22.342 517.703,23.368 516.035,25.035 C514.368,26.702 513.342,28.377 512.574,30.349 C511.834,32.258 511.326,34.438 511.181,37.631 C511.035,40.831 511,41.851 511,50 C511,58.147 511.035,59.17 511.181,62.369 C511.326,65.562 511.834,67.743 512.574,69.651 C513.342,71.625 514.368,73.296 516.035,74.965 C517.703,76.634 519.376,77.658 521.349,78.425 C523.257,79.167 525.438,79.673 528.631,79.82 C531.831,79.965 532.853,80.001 541,80.001 C549.148,80.001 550.169,79.965 553.369,79.82 C556.562,79.673 558.743,79.167 560.652,78.425 C562.623,77.658 564.297,76.634 565.965,74.965 C567.633,73.296 568.659,71.625 569.425,69.651 C570.167,67.743 570.674,65.562 570.82,62.369 C570.966,59.17 571,58.147 571,50 C571,41.851 570.966,40.831 570.82,37.631"></path></g></g></g></svg></div><div style="padding-top: 8px;"> <div style=" color:#3897f0; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:550; line-height:18px;">View this post on Instagram</div></div><div style="padding: 12.5% 0;"></div> <div style="display: flex; flex-direction: row; margin-bottom: 14px; align-items: center;"><div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(0px) translateY(7px);"></div> <div style="background-color: #F4F4F4; height: 12.5px; transform: rotate(-45deg) translateX(3px) translateY(1px); width: 12.5px; flex-grow: 0; margin-right: 14px; margin-left: 2px;"></div> <div style="background-color: #F4F4F4; border-radius: 50%; height: 12.5px; width: 12.5px; transform: translateX(9px) translateY(-18px);"></div></div><div style="margin-left: 8px;"> <div style=" background-color: #F4F4F4; border-radius: 50%; flex-grow: 0; height: 20px; width: 20px;"></div> <div style=" width: 0; height: 0; border-top: 2px solid transparent; border-left: 6px solid #f4f4f4; border-bottom: 2px solid transparent; transform: translateX(16px) translateY(-4px) rotate(30deg)"></div></div><div style="margin-left: auto;"> <div style=" width: 0px; border-top: 8px solid #F4F4F4; border-right: 8px solid transparent; transform: translateY(16px);"></div> <div style=" background-color: #F4F4F4; flex-grow: 0; height: 12px; width: 16px; transform: translateY(-4px);"></div> <div style=" width: 0; height: 0; border-top: 8px solid #F4F4F4; border-left: 8px solid transparent; transform: translateY(-4px) translateX(8px);"></div></div></div> <div style="display: flex; flex-direction: column; flex-grow: 1; justify-content: center; margin-bottom: 24px;"> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; margin-bottom: 6px; width: 224px;"></div> <div style=" background-color: #F4F4F4; border-radius: 4px; flex-grow: 0; height: 14px; width: 144px;"></div></div></a><p style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; line-height:17px; margin-bottom:0; margin-top:8px; overflow:hidden; padding:8px 0 7px; text-align:center; text-overflow:ellipsis; white-space:nowrap;"><a href="https://www.instagram.com/p/DECcB4wSF5b/?utm_source=ig_embed&amp;utm_campaign=loading" style=" color:#c9c8cd; font-family:Arial,sans-serif; font-size:14px; font-style:normal; font-weight:normal; line-height:17px; text-decoration:none;" target="_blank">A post shared by AllOneTech (@allonetechofficial)</a></p></div></blockquote> <script async src="//www.instagram.com/embed.js"></script>
<div class="menuDiv" onclick="toggleMenu()">=</div>
<div class="menuContainer" id="menuContainer">
<button class="AllOneLogoutContainer" onclick="window.location.href = 'logout.php'" style="margin-bottom: 10px;">
<p class="titleP">Logout</p>
</button>
<br>
<button class="AllOneTermsContainer" onclick="termtake()" style="margin-bottom: 10px;"><p class="titleP">Terms and Legal</p></button>
<br>
<button class="AllOneDeleteContainer" onclick="deleteprompt()" style="margin-bottom: 10px;"><p class="titleP">Delete Account</p></button>
<br>
<button class="AllOneTermsContainer" onclick="closeMenu()"><p class="titleP">Close</p></button>

</div>
<div id="deletePrompt">
<p>If you do delete your account, None of your data will stay and everything will be completely erased. If you have any subscriptions <b>UNSUBSCRIBE NOW!!!</b>
<br>
<br>
If you do forget to unubscribe, mistakes happen, it's all good. Contact allonetechofficial@outlook.com and we will respond within hopefully 5 days and any charges will be reversed. We are sorry to see you go but we respect your decision and we hope you come back soon!
</p>
<br>
<br>
<form method="POST" action="delete_user.php">
    <input type="hidden" name="username" value="USERNAME_TO_DELETE">
    <button type="submit" class="AllOneDeleteContainer" style="width: 30%;"><p class="titleP">Delete Account</p></button>
</form>
<br>
<button onclick="okprompt()" class="AllOneTermsContainer" style="width: 30%;"><p class="titleP">Keep Account</p></button>
</div>
</center>

<script>

function termtake() {
var transformerDiv = document.getElementById("transformerDiv")
transformerDiv.style.width = "100%"
transformerDiv.style.height = "100vh";
setTimeout(doSomething, 300);

function doSomething() {
window.location.href = "termsAndLegal.html"
}
}

function deleteprompt() {
var object = document.getElementById("deletePrompt")
object.style.display = "block"
}

function toggleMenu() {
  var menu = document.getElementById("menuDiv");
  var menucon = document.getElementById("menuContainer");
      menucon.style.display = "block";
	  }
	 
function closeMenu() {
  var menu = document.getElementById("menuDiv");
  var menucon = document.getElementById("menuContainer");
      menucon.style.display = "none";
	  }
</script>
</body>
</html>