<?php
require_once 'db.php';
header('Content-Type: application/json');

// Show errors for dev
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['meme'])) {
    $file = $_FILES['meme'];
    $caption = $_POST['caption'] ?? null;
    $userID = intval($_POST['userID'] ?? 0);

    // Basic validation
    if (!$userID || !$file['tmp_name']) {
        echo json_encode(['success' => false, 'error' => 'Missing userID or file']);
        exit;
    }

    $uploadDir = '../uploads/';
    $relativePath = 'uploads/' . uniqid() . '_' . basename($file['name']);
    $fullPath = '../' . $relativePath;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        $stmt = $conn->prepare("INSERT INTO meme (userID, file_path, caption, like_count, upvote_count)
                                VALUES (?, ?, ?, 0, 0)");
        $stmt->bind_param("iss", $userID, $relativePath, $caption);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true, 'file_path' => $relativePath]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Upload failed']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>

