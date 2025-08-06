<!DOCTYPE html>
<html>
<head>
    <title>Image Upload & Bibliography</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Image Upload & Bibliography Generator</h1>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="image">Select Image to Upload:</label>
                <input type="file" name="image" id="image" accept="image/*" required>
            </div>
            <input type="submit" value="Upload Image & Generate Bibliography" name="submit">
        </form>

<?php
// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if file was uploaded without errors
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $file_name = $_FILES["image"]["name"];
        $file_tmp = $_FILES["image"]["tmp_name"];
        $file_size = $_FILES["image"]["size"];
        $file_type = $_FILES["image"]["type"];
        
        // Validate file is an image
        $check = getimagesize($file_tmp);
        if ($check !== false) {
            // Create uploads directory if it doesn't exist
            $upload_dir = "uploads/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename with timestamp
            $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_filename = "uploaded_image_" . date('Y-m-d_H-i-s') . "." . $file_extension;
            $target_path = $upload_dir . $new_filename;
            
            // Move uploaded file to uploads directory
            if (move_uploaded_file($file_tmp, $target_path)) {
                // Create and write to bibliography.txt
                $file = fopen("bibliography.txt", "w");
                if ($file === false) {
                    header("Location: upload.html?error=bibliography_creation_failed");
                    exit();
                } else {
                    // Write simple biography-style entry
                    fwrite($file, "Alvarez, Jilliana. Computer Science Student and Aspiring Software Developer. ");
                    fwrite($file, "Currently pursuing studies in Computer Science with interests in software development, ");
                    fwrite($file, "emerging technologies, and motorsports. Career aspirations include becoming a software ");
                    fwrite($file, "engineer or UI/UX developer. Active in technology communities through reading tech news, ");
                    fwrite($file, "observing technological innovations, and contributing to open source projects. ");
                    fwrite($file, "Current focus areas include database optimization and security best practices. ");
                    fwrite($file, "Personal interests extend to reading technical blogs and staying current with ");
                    fwrite($file, "industry developments. Current Academic Year: " . date('Y') . ".\n");
                    
                    fclose($file);
                    
                    // Append a new paragraph
                    $file = fopen("bibliography.txt", "a");
                    if ($file === false) {
                        header("Location: upload.html?error=bibliography_append_failed");
                        exit();
                    } else {
                        fwrite($file, "Additional research focus on artificial intelligence and machine learning applications in modern software development.\n");
                        fclose($file);
                        
                        // Redirect back to upload.html with success message
                        header("Location: upload.html?success=1&image=" . urlencode($new_filename));
                        exit();
                    }
                }
            } else {
                header("Location: upload.html?error=save_failed");
                exit();
            }
        } else {
            header("Location: upload.html?error=not_image");
            exit();
        }
    } else {
        header("Location: upload.html?error=no_file");
        exit();
    }
} else {
    header("Location: upload.html");
    exit();
}
?>