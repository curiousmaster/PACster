// js/scripts.js

// Helper functions needed in PAC files
function dnsDomainIs(host, domain) {
    return (host.length >= domain.length && host.substring(host.length - domain.length) === domain);
}

function isPlainHostName(host) {
    return host.indexOf('.') === -1;
}

// More helper functions like isInNet, isValidIpAddress, etc. can be added here if needed

function newPACFileURL() {
    document.getElementById("idFilePath").value = document.getElementById("idSFilePath").value;
    return false;
}

function delay(time) {
    return new Promise(resolve => setTimeout(resolve, time));
}

let pacScriptContent = ''; // Store the PAC file content

function loadFile() {
    let filePath = document.getElementById("idFilePath").value;
    if (filePath !== "") {
        // Generate a random timestamp to append to the PAC file URL
        const timestamp = new Date().getTime();
        const urlWithTimestamp = `${filePath}?t=${timestamp}`;

        // Fetch the PAC file content with the appended timestamp
        fetch(urlWithTimestamp)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                pacScriptContent = data; // Save PAC file content for later use
                document.getElementById("testURLsButton").disabled = false;
                document.getElementById("resultsDiv").innerHTML = "<i>PAC file loaded successfully</i>";
            })
            .catch(error => {
                console.error("Error loading PAC file:", error);
                document.getElementById("resultsDiv").innerHTML = "<i>Error loading PAC file</i>";
            });
    }
    return false;
}

// Extract hostname from a URL
function getHost(vURL) {
    try {
        return new URL(vURL).hostname;
    } catch (error) {
        console.error("Invalid URL:", vURL);
        return "";
    }
}

// Test the PAC file by passing a URL to the FindProxyForURL function
function testURL() {
    let results = "";
    let urlList = document.getElementById("idURL").value.split("\n");
    let resultsDiv = document.getElementById("resultsDiv");

    resultsDiv.innerHTML = "<i>Testing URLs...</i>";
    for (let i = 0; i < urlList.length; i++) {
        let url = urlList[i].trim();
        if (url !== "") {
            let host = getHost(url);
            if (host === "") {
                results += `<dt>${url}</dt><dd>Invalid URL</dd><dd>Route: <i>Invalid URL</i></dd><br>`;
                continue;
            }
            let route = executePACFunction(url, host); // Call the PAC function simulation
            results += `<dt>${url}</dt><dd>Host: ${host}</dd><dd>Route: ${route}</dd><br>`;
        }
    }
    resultsDiv.innerHTML = "<dl>" + results + "</dl>";
    return false;
}

function executePACFunction(url, host) {
    // Simulate execution of the PAC file's FindProxyForURL
    let pacFunction;
    try {
        // Wrap PAC file content in a function and execute it
        pacFunction = new Function('url', 'host', pacScriptContent + '; return FindProxyForURL(url, host);');
        return pacFunction(url, host);
    } catch (error) {
        console.error("Error executing PAC function:", error);
        return "<i>Error in PAC file function</i>";
    }
}
