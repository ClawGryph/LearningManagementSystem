<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['materialTitle'];
    $description = $_POST['materialDescription'];
    $youtubeUrl = !empty($_POST['youtubeUrl']) ? $_POST['youtubeUrl'] : null;

    $fileName = null;
    $filePath = null;
    $fileSize = 0;
    $fileType = null;

    if (isset($_FILES['assignmentFile']) && $_FILES['assignmentFile']['error'] === 0) {
        $file = $_FILES['assignmentFile'];
        $fileName = basename($file['name']);
        $fileTmpPath = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['doc', 'docx', 'pdf', 'ppt', 'pptx'];

        if (!in_array($fileType, $allowedExts)) {
            echo "<script>alert('Invalid file type.'); window.history.back();</script>";//Invalid file type
            exit;
        }

        $uploadDir = '../uploads/materials/';
        $filePath = $uploadDir . $fileName;

        if (!move_uploaded_file($fileTmpPath, $filePath)) {
            echo "<script>alert('File upload failed!'); window.history.back();</script>";
            exit;
        }
    }

    // Insert regardless of whether it's a file or a YouTube link
    $stmt = $conn->prepare("INSERT INTO course_learningmaterials (name, description, file_name, file_path, file_size, uploaded_at, file_type, youtube_url) VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("ssssiss", $title, $description, $fileName, $filePath, $fileSize, $fileType, $youtubeUrl);

    if ($stmt->execute()) {
        echo "<script>alert('Upload Successful!'); window.location.href='../instructor/instructor-landingpage.php';</script>";
    } else {
        echo "Database insert error: " . $stmt->error;
    }
}
?>
