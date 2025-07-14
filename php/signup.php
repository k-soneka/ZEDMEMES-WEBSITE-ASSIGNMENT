<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

// Validate required fields
if (!isset($_POST['username'], $_POST['email'], $_POST['password'])) {
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required."
    ]);
    exit;
}

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];

// Check for empty fields
if (empty($username) || empty($email) || empty($password)) {
    echo json_encode([
        "status" => "error",
        "message" => "No field should be empty."
    ]);
    exit;
}

// Optional: Basic email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid email format."
    ]);
    exit;
}

// Hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Prepare SQL insert
$sql = "INSERT INTO user (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $conn->error
    ]);
    exit;
}

$stmt->bind_param("sss", $username, $email, $hashed);

if ($stmt->execute()) {
    $_SESSION['user'] = $stmt->insert_id;
    $_SESSION['username'] = $username;

    echo json_encode([
        "status" => "success"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Signup failed. Email might already be used."
    ]);
}
