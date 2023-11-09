<?php
// delete_file.php

// Get the filename from the query string and sanitize it
$file = basename($_GET['file']);
$filePath = "files/" . $file;

// Check if the file exists and delete it
if (file_exists($filePath)) {
    unlink($filePath);
}
?>