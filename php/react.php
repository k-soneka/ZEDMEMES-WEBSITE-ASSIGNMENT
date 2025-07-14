<?php
require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $memeID = intval($data['id'] ?? 0);
    $action = $data['action'] ?? ''; // should be 'like' or 'upvote'
    $userID = intval($data['userID'] ?? 0);

    if (!$memeID || !$userID || !in_array($action, ['like', 'upvote'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit;
    }

    // Check if user already reacted this way
    $check = $conn->prepare("SELECT reactionID FROM reaction WHERE memeID = ? AND userID = ? AND reaction_type = ?");
    $check->bind_param("iis", $memeID, $userID, $action);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Already reacted']);
        exit;
    }
    $check->close();

    // Insert new reaction
    $insert = $conn->prepare("INSERT INTO reaction (userID, memeID, reaction_type) VALUES (?, ?, ?)");
    $insert->bind_param("iis", $userID, $memeID, $action);
    if (!$insert->execute()) {
        echo json_encode(['success' => false, 'error' => 'Failed to add reaction: ' . $insert->error]);
        exit;
    }
    $insert->close();

    // Update counts in meme table
    $field = ($action === 'like') ? 'like_count' : 'upvote_count';
    $update = $conn->prepare("UPDATE meme SET $field = $field + 1 WHERE memeID = ?");
    $update->bind_param("i", $memeID);
    if (!$update->execute()) {
        echo json_encode(['success' => false, 'error' => 'Failed to update meme count: ' . $update->error]);
        exit;
    }
    $update->close();

    // Get updated count from reaction table
    $count = $conn->prepare("SELECT COUNT(*) AS cnt FROM reaction WHERE memeID = ? AND reaction_type = ?");
    $count->bind_param("is", $memeID, $action);
    $count->execute();
    $result = $count->get_result()->fetch_assoc();
    $count->close();

    echo json_encode([
        'success' => true,
        $action . '_count' => intval($result['cnt'])
    ]);
}
?>
