// js/scripts.js

// Helper functions needed in PAC files
function dnsDomainIs(host, domain) {
    return (host.length >= domain.length && host.substring(host.length - domain.length) === domain);
}

function isPlainHostName(host) {
    return host.indexOf('.') === -1;
}

// Additional helper functions can be added here if needed

// Function to introduce a delay (optional, can be used if needed)
function delay(time) {
    return new Promise(resolve => setTimeout(resolve, time));
}

let pacScriptContent = ''; // Variable to store the loaded PAC file content
let currentPACFileName = 'proxy.pac'; // Variable to store the current PAC file name

/**
 * Function to load the selected PAC file.
 * Appends a timestamp to the PAC file URL to prevent caching.
 * Fetches the PAC file content and displays it in the PAC content textarea.
 */
function loadFile() {
    // Get the selected PAC file URL directly from the dropdown
    let selectElement = document.getElementById("idSFilePath");
    let filePath = selectElement.value;
    let selectedOption = selectElement.options[selectElement.selectedIndex];
    let fileName = selectedOption.text; // Get the PAC file name from the selected option

    if (filePath !== "") {
        // Generate a unique timestamp to append to the PAC file URL
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
                pacScriptContent = data; // Store the PAC file content for later use
                currentPACFileName = fileName; // Update the current PAC file name
                document.getElementById("testURLsButton").disabled = false; // Enable the Test button
                document.getElementById("resultsDiv").innerHTML = "<i>PAC file loaded successfully</i>";
                document.getElementById("pacContentDiv").value = data; // Display the PAC file content in textarea
                document.getElementById("downloadPACButton").disabled = false; // Enable the Download button
            })
            .catch(error => {
                console.error("Error loading PAC file:", error);
                document.getElementById("resultsDiv").innerHTML = "<i>Error loading PAC file</i>";
                document.getElementById("pacContentDiv").value = ""; // Clear PAC content on error
                document.getElementById("downloadPACButton").disabled = true; // Disable the Download button
            });
    } else {
        // If no PAC file is selected, disable the Test and Download buttons
        document.getElementById("testURLsButton").disabled = true;
        document.getElementById("downloadPACButton").disabled = true;
        document.getElementById("resultsDiv").innerHTML = "<i>No PAC file selected.</i>";
        document.getElementById("pacContentDiv").value = "";
    }
    return false; // Prevent form submission
}

/**
 * Function to extract the hostname from a given URL.
 * Returns an empty string if the URL is invalid.
 * @param {string} vURL - The URL to extract the hostname from.
 * @returns {string} - The hostname or an empty string if invalid.
 */
function getHost(vURL) {
    try {
        return new URL(vURL).hostname;
    } catch (error) {
        console.error("Invalid URL:", vURL);
        return "";
    }
}

/**
 * Function to test multiple URLs against the loaded PAC file.
 * Displays the results in the results div.
 */
function testURL() {
    let results = "";
    let urlList = document.getElementById("idURL").value.split("\n");
    let resultsDiv = document.getElementById("resultsDiv");

    resultsDiv.innerHTML = "<i>Testing URLs...</i>"; // Indicate that testing is in progress

    for (let i = 0; i < urlList.length; i++) {
        let url = urlList[i].trim();
        if (url !== "") {
            let host = getHost(url);
            if (host === "") {
                // Handle invalid URL
                results += `<tr><td>${url}</td><td>Invalid URL</td><td><i>Invalid URL</i></td></tr>`;
                continue;
            }
            let route = executePACFunction(url, host); // Determine the proxy route
            results += `<tr><td>${url}</td><td>${host}</td><td>${route}</td></tr>`;
        }
    }

    resultsDiv.innerHTML = `<table>
                                <tr>
                                    <th>URL</th>
                                    <th>Host</th>
                                    <th>Route</th>
                                </tr>
                                ${results}
                            </table>`; // Display the testing results as a table
    return false; // Prevent form submission
}

/**
 * Function to execute the PAC file's FindProxyForURL function.
 * Dynamically creates a new function from the PAC script and invokes it.
 * @param {string} url - The URL to test.
 * @param {string} host - The hostname extracted from the URL.
 * @returns {string} - The proxy route determined by the PAC file.
 */
function executePACFunction(url, host) {
    let pacFunction;
    try {
        // Get the latest PAC script content from the textarea
        let currentPACContent = document.getElementById("pacContentDiv").value;

        // Create a new function from the PAC script
        pacFunction = new Function('url', 'host', currentPACContent + '; return FindProxyForURL(url, host);');
        return pacFunction(url, host); // Execute the PAC function
    } catch (error) {
        console.error("Error executing PAC function:", error);
        return "<i>Error in PAC file function</i>";
    }
}

/**
 * Function to download the loaded or edited PAC file with its original filename.
 */
function downloadPACFile() {
    let pacContent = document.getElementById("pacContentDiv").value;
    if (pacContent.trim() === "") {
        alert("No PAC file loaded to download.");
        return;
    }

    const blob = new Blob([pacContent], { type: 'application/x-ns-proxy-autoconfig' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = currentPACFileName; // Use the original PAC file name
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

/**
 * Function to toggle the editability of the PAC content textarea based on the checkbox.
 */
function toggleEdit() {
    const editToggle = document.getElementById("editToggle");
    const pacContentDiv = document.getElementById("pacContentDiv");
    pacContentDiv.readOnly = !editToggle.checked;
}

// Add event listener to the "Enable Edit" checkbox
document.addEventListener("DOMContentLoaded", function() {
    const editToggle = document.getElementById("editToggle");
    editToggle.addEventListener("change", toggleEdit);
});

/**
 * Mimics the shExpMatch() function from PAC files.
 *
 * @param {string} str - The string to be tested.
 * @param {string} shexp - The shell expression pattern to match against.
 * @returns {boolean} - Returns true if the string matches the shell expression; otherwise, false.
 */
function shExpMatch(str, shexp) {
    // Function to escape RegExp special characters except for *, ?, [, and ]
    function escapeRegex(s) {
        return s.replace(/[-\/\\^$+.|()]/g, '\\$&');
    }

    // Escape necessary characters in the shell expression
    let regexStr = escapeRegex(shexp);

    // Replace shell wildcards with RegExp equivalents
    regexStr = regexStr
        .replace(/\*/g, '.*')  // Replace '*' with '.*' (matches any number of characters)
        .replace(/\?/g, '.')    // Replace '?' with '.' (matches exactly one character)
        // Note: Character classes like [a-z] are preserved

    // Anchor the pattern to match the entire string
    regexStr = '^' + regexStr + '$';

    // Create a RegExp object with case-insensitive flag
    const regex = new RegExp(regexStr, 'i');

    // Test the string against the RegExp
    return regex.test(str);
}

/**
 * Converts a dotted-decimal IP string to a 32-bit number.
 * @param {string} ip - The IP address in dotted-decimal notation.
 * @returns {number} The numeric representation of the IP address.
 */
function ipToNumber(ip) {
    return ip.split('.').reduce((acc, octet) => {
        return (acc << 8) + parseInt(octet, 10);
    }, 0) >>> 0; // Ensure unsigned 32-bit integer
}

/**
 * Mimics the isInNet function from PAC files.
 * Checks if the host IP is within the specified subnet.
 * @param {string} hostIp - The host IP address.
 * @param {string} pattern - The subnet pattern (base IP of the subnet).
 * @param {string} mask - The subnet mask.
 * @returns {boolean} True if the host IP is in the specified subnet, false otherwise.
 */
function isInNet(hostIp, pattern, mask) {
    // Convert IP addresses and mask to 32-bit numbers
    const hostNum = ipToNumber(hostIp);
    const patternNum = ipToNumber(pattern);
    const maskNum = ipToNumber(mask);

    // Apply the mask to both the host IP and pattern, then compare
    return (hostNum & maskNum) === (patternNum & maskNum);
}

function dnsResolve(hostname) {
    return hostname;
}
