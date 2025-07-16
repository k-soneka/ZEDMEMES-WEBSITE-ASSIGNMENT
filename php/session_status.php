<?php
session_start();

// This script returns JSON about the user's session status

header('Content-Type: application/json');

if (isset($_SESSION['user'])) {
    echo json_encode([
        "loggedIn" => true,
        "userID" => $_SESSION['user'],
        "username" => $_SESSION['user_name'] ?? 'User' // fallback if username isn't set
    ]);
} else {
    echo json_encode([
        "loggedIn" => false
    ]);
}
?>
