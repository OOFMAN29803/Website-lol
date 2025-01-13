function AllOneLabsOpen() {
var container = document.getElementById("AllOneLabsContainerOpen")
container.style.width = "50%"
containerP.style.fontSize = "25px"
container.style.position = "fixed"
container.style.height = "80vh"
container.style.top = "5%";
container.style.left = "0";
}

function AllOneLabsClose() {
var container = document.getElementById("AllOneLabsContainerOpen")
var containerP = document.getElementById("infoLab")
containerP.textContent = "AllOneLabs explores cutting edge papers and technologies that are in really early stages or something that is only shown to be a proof of concept. These things will include things like Self-Learning-AI concepts, Leading AI advancements, Software to aid people, Video Generation, Image generation AND SO MUCH MORE."
container.style.width = " 47.5%"
containerP.style.fontSize = "18px"
container.style.position = "initial"
container.style.height = "30vh"
container.style.top = "initial";
container.style.left = "initial";
}

if (navigator.userAgent.match(/iPhone/i)   || navigator.userAgent.match(/iPad/i)  || navigator.userAgent.match(/Android/i)) { 
window.location.href = "indexMobile.html"
 } 
 
 