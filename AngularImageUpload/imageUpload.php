<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type");
header("Content-Type: application/json; charset=UTF-8");

$target_dir = "uploads/";
$image_name = basename($_FILES["fileToUpload"]["name"]);
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
$msg = '';

// Check file size
if ($_FILES["fileToUpload"]["size"] > 1000000) {
   $msg .= 'Sorry, your file is too large. ';
   $uploadOk = 0;
}

// Allow certain file formats
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
    $msg .= 'Sorry, only JPG, PNG, JPEG, GIF files are allowed. ';
    $uploadOk = 0;
}

// Check if a file was uploaded
if (empty($_FILES["fileToUpload"]["name"])) {
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo '{"error": "'.$msg.'"}';
} else {
    // Delete all the other files in the directory
    $files = glob($target_dir . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $image_url = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $target_file;
        $response_array = array(
            "success" => "File has been uploaded.",
            "image_name" => $image_name
        );
        echo json_encode($response_array);
    } else {
        echo '{"error": "Sorry, there was an error uploading your file."}';
    }
}
?>















