<?php
// Verify Login
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}
//include the library file
require 'includes/library.php';
// create the database connection
$pdo = connectDB();

$id = $_GET['id'];
$query = "DELETE FROM `possum_sheets` WHERE sheet_ID=?";
$stmt=$pdo->prepare($query);
$stmt->execute([$id]);

$query = "DELETE FROM `possum_slots` WHERE sheet_ID=?";
$stmt=$pdo->prepare($query);
$stmt->execute([$id]);

header("Location:index.php");
exit;
?>