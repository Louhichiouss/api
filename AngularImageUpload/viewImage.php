<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$target_dir = "uploads/";

// Get all files in target directory
$files = scandir($target_dir);

// Filter out non-image files
$image_files = array_filter($files, function($file) use ($target_dir) {
    return pathinfo($target_dir . $file, PATHINFO_EXTENSION) == 'jpg'
        || pathinfo($target_dir . $file, PATHINFO_EXTENSION) == 'jpeg'
        || pathinfo($target_dir . $file, PATHINFO_EXTENSION) == 'png'
        || pathinfo($target_dir . $file, PATHINFO_EXTENSION) == 'gif';
});

// Return list of image names
echo json_encode(array('image_names' => array_values($image_files)));
?>
