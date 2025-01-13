var numberModules = 0
var currentUse = 0
var questionTitle = 0
var smallerTitle = 0
var topTitle = 0
 
 function addModuleShort() {
	 numberModules = numberModules + 1
	 var titlePUsed = document.getElementById("titleInputP").value
	 var holdingContainer = document.getElementById("mainInvisContainer")
    currentUse = "short"
	questionTitle = titlePUsed
	 const answerShort = document.createElement("input")
	 const containerDiv = document.createElement("div")
	 const pTitle = document.createElement("p")
	 const buttonDelete = document.createElement("button")
	 buttonDelete.textContent = "🗑"
	buttonDelete.onclick = function () {
    const parent = buttonDelete.parentElement;
    parent.remove();
};
	 pTitle.textContent = titlePUsed
	 pTitle.style.fontSize = "25px"
	 pTitle.style.fontWeight = "600"
	containerDiv.id = "containerDiv" + numberModules
	 containerDiv.classList.add("containerDiv")
	 answerShort.style.fontSize = "15px";
	FDKJHKDS3746d876(numberModules, currentUse, questionTitle);
	 holdingContainer.appendChild(containerDiv)
	  	 containerDiv.appendChild(pTitle)
 	 containerDiv.appendChild(answerShort)
 	 	 containerDiv.appendChild(buttonDelete)

 } 
 


document.getElementById('addModuleButton').onclick = () => {
    addModuleToJSON('This is a new module!', 'Delete Module');
};
 
 function addModuleLong() {
	 numberModules = numberModules + 1
	 var titlePUsed = document.getElementById("titleInputP").value
	 var holdingContainer = document.getElementById("mainInvisContainer")
	 const pTitle = document.createElement("p")
	 const buttonDelete = document.createElement("button")
	 	buttonDelete.onclick = function () {
    const parent = buttonDelete.parentElement;
    parent.remove();
};
	 buttonDelete.textContent = "🗑"
		questionTitle = titlePUsed
	     currentUse = "long"
	console.log(numberModules + "devContainer" + currentUse)
	 pTitle.textContent = titlePUsed
	 pTitle.style.fontSize = "25px"
	 pTitle.style.fontWeight = "600"
	 const answerShort = document.createElement("textarea")
	 const containerDiv = document.createElement("div")
	 containerDiv.id = "containerDiv" + numberModules
	 containerDiv.classList.add("containerDiv")
	 answerShort.style.fontSize = "15px";
	FDKJHKDS3746d876(numberModules, currentUse, questionTitle);
	 holdingContainer.appendChild(containerDiv)
	 	  	 containerDiv.appendChild(pTitle)

 	 containerDiv.appendChild(answerShort)
	 containerDiv.appendChild(buttonDelete)
	 	FDKJHKDS3746d876(numberModules, currentUse);

	 

 }
 
  function FDKJHKDS3746d876(numberModules, currentUse, questionTitle) {
    const newModule = {
        divContainer: "divContainer" + numberModules,
        type: currentUse,
		questionName: questionTitle
    };

    fetch('addModule.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(newModule)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Module added successfully:', data);
        } else {
            console.error('Error adding module:', data.error);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
}

function callPublic() {
	smallerTitle = document.getElementById("")
}

function topBar(topTitle, smallerTitle) {
    const newModule = {
        TopName: topTitle,
		smallParagraph: smallerTitle
    };

    fetch('addToTop.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(newModule)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Module added successfully:', data);
        } else {
            console.error('Error adding module:', data.error);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
}
