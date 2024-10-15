<?php
// index.php

// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Include the configuration file
include 'config.php';

// Dynamically construct the absolute URL to the templates.d directory
$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$templatePath = $scriptDir . '/templates.d/template.pac';

// Function to get last commit info of a PAC file
function getLastCommitInfo($filePath) {
    $gitPath = '/usr/bin/git'; // Use the correct path to Git
    $command = "$gitPath log -1 --pretty=format:'%h - %an, %ar : %s' -- " . escapeshellarg($filePath) . " 2>&1";

    $output = [];
    $returnVar = 0;

    // Change to the directory containing the PAC file
    chdir(BASE_PATH); // Change directory to the base path

    // Execute the command
    exec($command, $output, $returnVar);

    // Debug output
    error_log("Executing command: $command");
    if ($returnVar !== 0) {
        error_log("Error executing command: $command");
        error_log("Return Var: $returnVar");
        error_log("Output: " . implode("\n", $output));
        return "Error retrieving commit info.";
    }

    // Parse the output
    $commitInfo = implode("\n", $output);
    list($info, $comment) = explode(' : ', $commitInfo, 2);

    // Prepare the HTML table output with URL
    $url = htmlspecialchars("{$filePath}"); // Create URL from the file path
    $htmlOutput = '<table>';
    $htmlOutput .= '<tr><th class="commit-info-header">Info</th><td>' . htmlspecialchars($info) . '</td></tr>';
    $htmlOutput .= '<tr><th class="commit-info-header">Comment</th><td>' . htmlspecialchars($comment) . '</td></tr>';
    $htmlOutput .= '<tr><th class="commit-info-header">URL</th><td>' . $url . '</td></tr>';
    $htmlOutput .= '</table>';

    return $htmlOutput;
}

// Get last commit info for the PAC template file
$lastCommitInfo = getLastCommitInfo(basename($templatePath)); // Use basename to remove path details

// Define the title and include the header
$title = "PAC Test Tool";
require_once 'header.php';
?>

<script>
    // Clear the results div
    function initResults() {
        const resultsDiv = document.getElementById("resultsDiv");
        let str = "<h4>Test Results</h4><table class='results-table'><thead><tr><th>PAC File</th><th>URL</th><th>Host</th><th>Route</th></tr></thead><tbody></tbody>"; // Reset the results
        resultsDiv.innerHTML = str;
    }
</script>

<div class="content">
    <div class="wrapper">
        <!-- First Container: PAC File Selection and Testing -->
        <div class="container" style="display: flex; flex-direction: column; height: 100%;">
            <h3>PAC File Tester</h3>

            <form name="loadFileForm" onsubmit="return false;" method="post">
                <div class="form-group">
                    <label for="idSFilePath">Select PAC Files</label>
                    <select name="sFilePath" id="idSFilePath" multiple onchange="loadFile()">
                        <option value="" disabled>Select PAC files</option>
                        <option value="" disabled>────────────────────────────────────────────────────────────────────────</option>
                        <option value="<?php echo $templatePath; ?>">Pac Template</option>
                        <option value="" disabled>────────────────────────────────────────────────────────────────────────</option>

                        <?php
                        // PHP: List PAC files in the parent directory
                        $directory = realpath(__DIR__ . '/..');
                        foreach (glob($directory . '/*.pac') as $file) {
                            $fileName = basename($file);
                            echo "<option value='{$baseURL}/{$fileName}'>{$fileName}</option>";
                        }
                        ?>
                    </select>
                </div>
            </form>

            <form name="testURLForm" onsubmit="return testURL()" method="post">
                <div class="form-group">
                    <label for="idURL">Test URL</label>
                    <textarea name="nURL" id="idURL" placeholder="Enter URLs to test, one per line..."></textarea>
                </div>
                <div class="form-group">
                    <input type="submit" value="Test" id="testURLsButton" disabled class="submit-btn">
                </div>
            </form>

            <!-- Commit Info Section Moved to Bottom -->
            <div class="last-commit-info" id="lastCommitInfo" style="margin-top: auto; display: none;"> <!-- Initially hidden -->
                <strong>Information:</strong>
                <div id="commitInfo"><?php
                        echo $lastCommitInfo;
                ?></div>
            </div>
        </div>

        <!-- Results Container -->
        <div class="container" style="display: flex; flex-direction: column; height: 100%;">
            <div id="resultsDiv" class="results-box"></div>
            <script>initResults()</script>
            <!-- Button to download results as CSV -->
            <div style="text-align: center; margin-top: 10px;">
                <button id="downloadResultsButton" class="submit-btn">Download CSV</button>
            </div>
        </div>

        <!-- Second Container: Display and Edit Loaded PAC File -->
        <div class="container">
            <h4>Loaded PAC File</h4>
            <textarea id="pacContentDiv" readonly></textarea>
            <div class="form-group">
                <button id="downloadPACButton" onclick="downloadPACFile()" disabled class="submit-btn">Download PAC</button>
                <label for="editToggle" class="checkbox-label">
                    <input type="checkbox" id="editToggle"> Enable Edit
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Include footer -->
<?php require_once 'footer.php'; ?>

<!-- Link to external JavaScript -->
<script src="js/scripts.js"></script>

<script>
// Global variable to hold the test results
let testResults = [];

// Load the selected PAC files and update commit info
function loadFile() {
    let selectElement = document.getElementById("idSFilePath");
    let selectedFiles = Array.from(selectElement.selectedOptions).map(option => option.value);

    // Show or hide last commit info based on selected files
    const lastCommitInfo = document.getElementById("lastCommitInfo");
    if (selectedFiles.length === 0 || selectedFiles.includes("<?php echo $templatePath; ?>")) {
        lastCommitInfo.style.display = 'none'; // Hide for Pac Template and no files selected
    } else {
        lastCommitInfo.style.display = 'block'; // Show for other files
    }

    if (selectedFiles.length > 0) {
        initResults(); // Clear results
        selectedFiles.forEach(file => {
            // Fetch PAC file content
            fetch(file)
                .then(response => response.text())
                .then(data => {
                    document.getElementById("pacContentDiv").value = data; // Load PAC content
                    document.getElementById("testURLsButton").disabled = false; // Enable Test button
                    document.getElementById("downloadPACButton").disabled = false; // Enable Download button

                    // Get last commit info for the selected PAC file
                    getLastCommitInfo(file);
                })
                .catch(error => {
                    console.error("Error loading PAC file:", error);
                });
        });
    }
}

// Function to fetch the last commit info from the server
function getLastCommitInfo(filePath) {
    fetch('get_commit_info.php?file=' + encodeURIComponent(filePath))
        .then(response => response.text())
        .then(data => {
            document.getElementById("commitInfo").innerHTML = data; // Update the commit info display as HTML
        })
        .catch(error => {
            console.error("Error fetching commit info:", error);
        });
}

/**
 * Function to test multiple URLs against the loaded PAC files.
 * Displays the results in the results div.
 */
function testURL() {
    let resultsDiv = document.getElementById("resultsDiv");

    var tabdef = "<h4>Test Results</h4>"; // Title for results
    tabdef += "<table class='results-table'><thead><tr><th>PAC File</th><th>URL</th><th>Host</th><th>Route</th></tr></thead><tbody>"; // Table headers

    // Clear previous results
    testResults = [];

    // Get the URLs from the input textarea
    let urlList = document.getElementById("idURL").value.split("\n");

    // Get selected PAC files
    let selectElement = document.getElementById("idSFilePath");
    let selectedFiles = Array.from(selectElement.selectedOptions).map(option => option.value);

    // Iterate through each selected PAC file
    selectedFiles.forEach(pacFile => {
        // Load the PAC file content for the current file
        fetch(pacFile)
            .then(response => response.text())
            .then(data => {
                // Set the PAC content for execution
                const pacFunction = new Function('url', 'host', data + '; return FindProxyForURL(url, host);');

                // Test each URL for this PAC file
                urlList.forEach(url => {
                    let trimmedUrl = url.trim();
                    if (trimmedUrl !== "") {
                        let host = getHost(trimmedUrl);
                        if (host === "") {
                            // Handle invalid URL
                            tabdef += '<tr><td>' + pacFile + '</td><td>' + trimmedUrl + '</td><td>Invalid URL</td><td><i>Invalid URL</i></td></tr>';
                        } else {
                            // Determine the proxy route using the current PAC file's function
                            let route = pacFunction(trimmedUrl, host);
                            tabdef += '<tr><td>' + pacFile + '</td><td>' + trimmedUrl + '</td><td>' + host + '</td><td>' + route + '</td></tr>';

                            // Save result to testResults array
                            testResults.push({ pacFile: pacFile, url: trimmedUrl, host: host, route: route });
                        }
                    }
                });

                // Update the results table after processing all URLs
                resultsDiv.innerHTML = tabdef + "</tbody></table>"; // Closing tags for the table
            })
            .catch(error => {
                console.error("Error loading PAC file:", error);
            });
    });

    return false; // Prevent form submission
}

// Function to download the test results as a CSV file
function downloadResultsAsCSV() {
    let csvContent = "PAC File;URL;Host;Route\n"; // Add headers

    // Add each result to the CSV content
    testResults.forEach(result => {
        csvContent += `${result.pacFile};${result.url};${result.host};\"${result.route}\"\n`;
    });

    // Create a link and trigger the download
    const encodedUri = encodeURI("data:text/csv;charset=utf-8," + csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "test_results.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Add event listener for download results button
document.getElementById("downloadResultsButton").addEventListener("click", downloadResultsAsCSV);
</script>

<style>
/* Add CSS for the results box */
.results-box {
    width: 100%; /* Full width */
    max-height: calc(100vh - 350px); /* Set a maximum height for the box */
    overflow-y: auto; /* Enable vertical scrolling */
    border: 1px solid #ddd; /* Border for the box */
    border-radius: 5px; /* Rounded corners */
    padding: 10px; /* Padding inside the box */
    background-color: #f9f9f9d0; /* Light background for readability */
}

/* Add CSS for results table */
.results-table {
    width: 100%;
    border-collapse: collapse;
}

.results-table th, .results-table td {
    padding: 8px;
    border: 1px solid #ddd;
    text-align: left;
}

.results-table th {
    background-color: #005FA5;
    color: white; /* Header text color */
}

.results-table tbody tr:hover {
    background-color: #f1f1f1; /* Highlight on hover */
}
</style>