<?php
require_once 'db.php';
header('Content-Type: application/json');

session_start();

// For dev: show errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['meme'])) {
    $file = $_FILES['meme'];
    $caption = $_POST['caption'] ?? null;
    $userID = intval($_POST['userID'] ?? 0);

    if (!$userID || !$file['tmp_name']) {
        echo json_encode(['success' => false, 'error' => 'Missing userID or file']);
        exit;
    }

    // Upload directory
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $filename = uniqid() . '_' . basename($file['name']);
    $relativePath = 'uploads/' . $filename;
    $fullPath = '../' . $relativePath;

    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        $stmt = $conn->prepare("INSERT INTO meme (userID, file_path, caption, like_count, upvote_count)
                                VALUES (?, ?, ?, 0, 0)");
        $stmt->bind_param("iss", $userID, $relativePath, $caption);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true, 'file_path' => $relativePath]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
