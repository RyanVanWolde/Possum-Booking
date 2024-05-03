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

// Get id
$id = $_GET['id'];

// Get Sheet Data
$query = "SELECT title, host, description, public, slots_remaining FROM `possum_sheets` WHERE sheet_ID=?";
$getsheet_stmt = $pdo->prepare($query);
$getsheet_stmt->execute([$id]);
$prime_sheet = $getsheet_stmt->fetch();
$sheet_title = $prime_sheet['title'];
$sheet_description = $prime_sheet['description'];
$sheet_host = $prime_sheet['host'];
$sheet_searchable = $prime_sheet['public'];
$slots_remaining = $prime_sheet['slots_remaining'];

// Copy Sheet
$query = "INSERT INTO `possum_sheets` (title, host, description, public, slots_remaining, date_created) VALUES (?, ?, ?, ?, 0, ?)";
$copysheet_stmt = $pdo->prepare($query);
$date_created = date("Y-m-d H:i:s");
$copysheet_stmt->execute([$sheet_title, $sheet_host, $sheet_description, $sheet_searchable, $date_created]);

// Get New Copied Sheet ID
$query = "SELECT sheet_ID FROM `possum_sheets` WHERE title=? AND host=? AND description=? AND public=? AND date_created=?";
$id_stmt = $pdo->prepare($query);
$id_stmt->execute([$sheet_title, $sheet_host, $sheet_description, $sheet_searchable, $date_created]);
$new_id = $id_stmt->fetch()['sheet_ID'];

// Get and Copy Slot Data
$query = "SELECT timeslot FROM `possum_slots` WHERE sheet_ID=?";
$copyslots_stmt = $pdo->prepare($query);
$copyslots_stmt->execute([$id]);
$count = 1;
foreach($copyslots_stmt as $row) {

  // Insert New Slot
  $query = "INSERT INTO `possum_slots` (sheet_ID, name, timeslot, email) VALUES (?, 'OPEN', ?, '')";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$new_id, $row['timeslot']]);

  // Increment Slots Remaining
  $query = "UPDATE `possum_sheets` SET slots_remaining=? WHERE sheet_ID=?";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$count, $new_id]);
  $count++;
}

header("Location: editsheet.php?id=$new_id");
exit();
?>