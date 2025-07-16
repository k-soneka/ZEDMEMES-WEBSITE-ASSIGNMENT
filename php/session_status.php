<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['user'])) {
    echo json_encode([
        "loggedIn" => true,
        "userID" => $_SESSION['user'],
        "username" => $_SESSION['user_name'] ?? 'User'
    ]);
} else {
    echo json_encode([
        "loggedIn" => false
    ]);
}
?>
