<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

//include the library file
require 'includes/library.php';
// create the database connection
$pdo = connectDB();

// Get User Data
$username = $_SESSION['username'];
$stmt = $pdo->prepare("SELECT * FROM `possum_users` WHERE username=?");
$stmt->execute([$username]);

// Delete User Data
$user_stmt = $pdo->prepare("DELETE FROM `possum_users` WHERE username=?");
$user_stmt->execute([$username]);

// Delete User Sheets
$sheet_stmt = $pdo->prepare("DELETE FROM `possum_sheets` WHERE host=?");
$sheet_stmt->execute([$name]);

// Delete User Signups

session_destroy();
header('Location: login.php');
exit();
?>