<?php
// Get username from GET array
$username = $_GET['username'] ?? null;

// Ensure it's a valid email before bothering to check the database
if (!$username || strlen($username) === 0) {
  echo 'error';
  exit;
}

// Include the library file and connect to the database
require 'includes/library.php';
$pdo = connectDB();

// Query for record matching provided email
$stmt = $pdo->prepare("SELECT * FROM `possum_users` WHERE username = ?");
$stmt->execute([$username]);

// remember that fetch returns false when there were no records
if ($stmt->fetch()) {
  echo 'true'; // username already exists
} else {
  echo 'false'; // username is available
}
