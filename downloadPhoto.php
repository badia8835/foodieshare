<?php
// Get the image URL from the query string
$url = $_GET['url'];
$mealName = $_GET['mealName'];
if ($url) {
// Set the content type header
header('Content-Type: application/octet-stream');
// Specify the file name for download (you can modify it if needed)
header('Content-Disposition: attachment; filename="' . $mealName . '.jpg"');


// Fetch the image content from the URL
$imageContent = file_get_contents($url);

// Output the image content
echo $imageContent;
}









