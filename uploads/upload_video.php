<?php
// Check if the form is submitted and the file is uploaded
if (isset($_FILES['course_video'])) {
    // Get file details
    $fileName = $_FILES['course_video']['name'];
    $fileTmpName = $_FILES['course_video']['tmp_name'];
    $fileSize = $_FILES['course_video']['size'];
    $fileError = $_FILES['course_video']['error'];
    $fileType = $_FILES['course_video']['type'];

    // Define allowed file types (video formats)
    $allowedTypes = ['video/mp4', 'video/avi', 'video/mkv'];

    // Define the upload directory (uploads/)
    $uploadDirectory = 'uploads/';

    // Check for any errors
    if ($fileError === 0) {
        // Validate file type
        if (in_array($fileType, $allowedTypes)) {
            // Create a unique name for the file
            $uniqueFileName = uniqid('', true) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
            
            // Set the destination path for the uploaded file
            $fileDestination = $uploadDirectory . $uniqueFileName;

            // Check if the file already exists in the uploads folder
            if (!file_exists($fileDestination)) {
                // Move the uploaded file to the uploads directory
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    echo "File uploaded successfully!";
                } else {
                    echo "Error uploading the file.";
                }
            } else {
                echo "File already exists.";
            }
        } else {
            echo "Invalid file type. Only MP4, AVI, and MKV are allowed.";
        }
    } else {
        echo "Error uploading file. Code: " . $fileError;
    }
}
?>
