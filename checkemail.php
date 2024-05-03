<?php
// Get email from GET array
$email = $_GET['email'] ?? null;

// Ensure it's a valid email before bothering to check the database
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo 'error';
  exit;
}

// Include the library file and connect to the database
require 'includes/library.php';
$pdo = connectDB();

// Query for record matching provided email
$stmt = $pdo->prepare("SELECT * FROM `possum_users` WHERE email = ?");
$stmt->execute([$email]);

// remember that fetch returns false when there were no records
if ($stmt->fetch()) {
  echo 'true'; // email was found
} else {
  echo 'false'; // email was not found
}
