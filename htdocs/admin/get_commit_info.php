<?php
// get_commit_info.php

// Include the configuration file
include 'config.php';

// Set the content type to HTML
header('Content-Type: text/html; charset=utf-8');

// Get the file parameter from the URL
$filePath = isset($_GET['file']) ? basename($_GET['file']) : null;

if ($filePath) {
    // Construct the full path
    $fullPath = BASE_PATH . '/' . $filePath; // Changed '/templates.d/' to '/'

    // Function to get last commit info of a PAC file
    function getLastCommitInfo($filePath) {
        global $baseURL;
        $gitPath = '/usr/bin/git'; // Use the correct path to Git
        $command = "$gitPath log -1 --pretty=format:'%h - %an, %ar : %s' -- " . escapeshellarg($filePath) . " 2>&1";

        $output = [];
        $returnVar = 0;

        // Change to the directory containing the PAC file
        chdir(BASE_PATH); // Change directory to the base path

        // Execute the command
        exec($command, $output, $returnVar);

        // Check for errors
        if ($returnVar !== 0) {
            return "<tr><td colspan='3'>Error retrieving commit info.</td></tr>";
        }

        // Parse the output
        $commitInfo = implode("\n", $output);
        list($info, $comment) = explode(' : ', $commitInfo, 2);

        // Prepare the HTML table output with URL

        $fileName = basename($filePath); // Get only the filename without the path
        $url = $baseURL . '/' . htmlspecialchars("{$fileName}"); // Create URL from the file path
        $htmlOutput = '<table>';
        $htmlOutput .= '<tr><th class="commit-info-header">GIT info</th><td>' . htmlspecialchars($info) . '</td></tr>';
        $htmlOutput .= '<tr><th class="commit-info-header">Comment</th><td>' . htmlspecialchars($comment) . '</td></tr>';
        $htmlOutput .= '<tr><th class="commit-info-header">URL</th><td>' . $url . '</td></tr>';
        $htmlOutput .= '</table>';

        return $htmlOutput;
    }

    // Output the last commit info
    echo getLastCommitInfo($fullPath);
} else {
    echo "No file specified.";
}
