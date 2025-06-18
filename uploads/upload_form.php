<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Course Video</title>
</head>
<body>

    <h2>Upload Course Video</h2>
    <form action="upload_video.php" method="POST" enctype="multipart/form-data">
        <label for="course_video">Choose Video to Upload:</label>
        <input type="file" name="course_video" required>
        <br><br>
        <button type="submit">Upload Video</button>
    </form>

</body>
</html>
