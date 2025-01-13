let compiletype = "html"; 
let pyodide;

async function loadPyodideAndRun() {
  const outputElement = document.getElementById('terminal');
  outputElement.textContent = "Loading Pyodide...";
  console.log("Starting Pyodide load...");

    // Load the Pyodide runtime
    pyodide = await loadPyodide(); 
    console.log("Pyodide loaded successfully.");
    outputElement.textContent = "Pyodide is loaded. Ready to run Python!";
    console.error("Failed to load Pyodide:", error);
    outputElement.textContent = "Error loading Pyodide. Check the console for details.";
  
}


function sampleiFrame() {
    console.log("Starting sampleiFrame...");
    const span = document.getElementById("iframeTerminal");
    const space = document.getElementById("codingSpace").value;

    if (compiletype === "html") {
        console.log("Executing HTML code...");
        const htmlContent = `
        ${space}
        `;
        span.srcdoc = htmlContent;
    } else if (compiletype === "python") {
        console.log("Executing Python code...");
        runPythonExec(space);
    }
}

async function runPythonExec(space) {
  const outputElement = document.getElementById('terminal'); // Output area

  if (!pyodide) {
    outputElement.textContent = "Error: Pyodide is not loaded yet!";
    console.log("Attempted to run Python code, but Pyodide is not loaded.");
    return;
  }

  try {
    pyodide.runPython(`
      import sys
      from io import StringIO
      sys.stdout = StringIO()  # Redirect stdout
    `);

    await pyodide.runPythonAsync(space);


    const stdout = pyodide.runPython("sys.stdout.getvalue()");
    console.log("Python execution result:", stdout);
    outputElement.textContent = stdout || "Code executed successfully, no output.";
  } catch (error) {
    console.error("Error running Python code:", error);
    outputElement.textContent = `Error: ${error}`;
  }
}

function setiFrame() {
    var span = document.getElementById("iframeTerminal");
    let outputDiv = document.getElementById("terminal");

    var htmlContent = `
        <p style="font-family: 'Plus Jakarta Sans', sans-serif; font-optical-sizing: auto; font-style: normal;">Your content will show here</p>
    `;

    span.style.display = "block";
    span.srcdoc = htmlContent;
    console.log("success");
}

function fullscreen() {
    var iframe = document.getElementById("iframeTerminal");
    var fullButton = document.getElementById("buttonNoFullScreen");
    var space = document.getElementById("codingSpace");
    space.style.marginTop = "10px";
    fullButton.style.display = "block";
    iframe.style.float = "left";
    iframe.style.width = "98%";
    iframe.style.zIndex = "99282882";
    iframe.style.height = "97vh";
    iframe.style.position = "fixed";
}

function offscreen() {
    var iframe = document.getElementById("iframeTerminal");
    var fullButton = document.getElementById("buttonNoFullScreen");
    fullButton.style.display = "none";
    iframe.style.float = "left";
    iframe.style.width = "initial";
    iframe.style.zIndex = "99282882";
    iframe.style.height = "50vh";
    iframe.style.width = "46%";
    iframe.style.position = "initial";
}

function changePythony() {
    var span = document.getElementById("iframeTerminal");
    span.style.display = "none";
    var terminal = document.getElementById("terminal");
    terminal.style.display = "block";
    var space = document.getElementById("codingSpace");
    space.style.marginTop = "0px";
    var buttony = document.getElementById("buttonFull");
    buttony.style.display = "none";
    compiletype = "python";
}

function changeHTML() {
    var span = document.getElementById("iframeTerminal");
    span.style.display = "block";
    var terminal = document.getElementById("terminal");
    terminal.style.display = "none";
    var space = document.getElementById("codingSpace");
    space.style.marginTop = "0px";
    var buttony = document.getElementById("buttonFull");
    buttony.style.display = "initial";
    compiletype = "html";
}

function clearText() {
    console.log("cleared");
    const space = document.getElementById("codingSpace");
    space.value = "";
    console.log("cleared");
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href);
}
function userData() {
fetch('data.json')
  .then(response => response.json())
  .then(jsonData => {
    // Access the 'people' array
    const people = jsonData.username;

    // Loop through each person
    people.forEach(person => {
      console.log(`ID: ${people}`);


setiFrame();
loadPyodideAndRun();
changeHTML();
