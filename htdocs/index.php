<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PAC File Tester</title>
    <!-- Link to external CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div class="container">
    <h3>PAC File Tester</h3>

    <form name="loadFileForm" onsubmit="return loadFile()" method="post">
        <div class="form-group">
            <label for="idSFilePath">Select PAC File</label>
            <select name="sFilePath" id="idSFilePath" onchange="newPACFileURL()">
                <!-- Add an unselectable placeholder option -->
                <option value="" disabled selected>Select PAC file</option>
                <?php
                // Include the config.php file
                include 'config.php';

                // PHP: List PAC files in the parent directory
                $directory = realpath(__DIR__ . '/..');
                foreach (glob($directory . '/*.pac') as $file) {
                    $fileName = basename($file);
                    echo "<option value='{$baseURL}/{$fileName}'>{$fileName}</option>";
                }
                ?>
            </select>
        </div>

        <!-- Hidden PAC file path field -->
        <input type="text" name="nFilePath" id="idFilePath" size="80" placeholder="PAC file path">

        <div class="form-group">
            <input type="submit" value="Load">
        </div>
    </form>

    <form name="testURLForm" onsubmit="return testURL()" method="post">
        <div class="form-group">
            <label for="idURL">Test URL</label>
            <textarea name="nURL" id="idURL" placeholder="Enter URLs to test, one per line..."></textarea>
        </div>
        <div class="form-group">
            <input type="submit" value="Test" id="testURLsButton" disabled>
        </div>
    </form>

    <div id="resultsDiv"></div>
</div>

<!-- Link to external JavaScript -->
<script src="js/scripts.js"></script>

</body>
</html>
