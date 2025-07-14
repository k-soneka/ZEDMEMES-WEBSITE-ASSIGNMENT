<?php
// Start the PHP session (in case we need to track the logged-in user later)
session_start();

// Include the database connection
require 'config.php'; //connects to mysql database

// Build the SQL query to fetch memes
// We're selecting: meme ID, file path, caption, upload time, and the uploader's username
$sql = "
  SELECT meme.memeID, meme.file_path, meme.caption, meme.upload_date, user.username,
  (
  SELECT COUNT(*)
  FROM reactions
  WHERE reaction.memeID = meme.memeID AND reaction.reaction_type = 'like'
  ) AS like_count
  FROM meme
  JOIN user ON meme.userID = user.userID
  ORDER BY meme.upload_date DESC
"; 

// Run the query
$result = $conn->query($sql);

// Prepare an array to hold the results
$meme = [];

// If there are rows returned...
if ($result && $result->num_rows > 0) {
    // Fetch each row as an associative array and add it to the $memes list
    while ($row = $result->fetch_assoc()) {
        $meme[] = $row;
    }

    // Return the data as JSON so JavaScript can use it
    echo json_encode([
        "status" => "success",
        "data" => $meme
    ]); //this takes a php array and converts it to a JSON string that JavaScript can read
} else {
    // If no memes found, return an empty list
    echo json_encode([
        "status" => "success",
        "data" => []
    ]);
}
?>
