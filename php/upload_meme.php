<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['meme'])) {
    $file = $_FILES['meme'];
    $caption = $_POST['caption'] ?? '';
    $userID = $_SESSION['user'] ?? 0;

    if (!$userID || !$file['tmp_name']) {
        file_put_contents('upload_debug.log', "❌ Missing user session or file\n", FILE_APPEND);
        echo json_encode(['status' => 'error', 'message' => 'Missing user session or file']);
        exit;
    }

    $uploadDir = __DIR__ . '/../uploads/';
    $filename = uniqid() . '_' . basename($file['name']);
    $relativePath = 'uploads/' . $filename;
    $fullPath = $uploadDir . $filename;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $moveSuccess = move_uploaded_file($file['tmp_name'], $fullPath);
    file_put_contents('upload_debug.log', "Move to: $fullPath\nResult: " . var_export($moveSuccess, true) . "\n", FILE_APPEND);

    if ($moveSuccess) {
        $stmt = $conn->prepare("INSERT INTO meme (userID, file_path, caption, like_count, upvote_count) VALUES (?, ?, ?, 0, 0)");
        $stmt->bind_param("iss", $userID, $relativePath, $caption);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status' => 'success', 'file_path' => $relativePath]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File move failed']);
    }
} else {
    file_put_contents('upload_debug.log', "❌ Invalid request type or file not set\n", FILE_APPEND);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
