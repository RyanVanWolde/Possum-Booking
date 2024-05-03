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
$query = "SELECT * FROM possum_slots WHERE slot_ID=?";
$stmt=$pdo->prepare($query);
$stmt->execute([$id]);
$slot = $stmt->fetch();
$sheet_ID = $slot['sheet_ID'];

$query = "SELECT * FROM possum_sheets WHERE sheet_ID=?";
$stmt=$pdo->prepare($query);
$stmt->execute([$sheet_ID]);
$sheet = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/sheetdetails.css" />
        <?php
            $PAGE_TITLE = "Viewing Slot Details - Possum Tracks";
            include "includes/metadata.php";
        ?>
    </head>
    <!-- HEADER -->
    <?php include "includes/header.php" ?>
    <main>
        <?php
            $query = "SELECT possum_users.`name` FROM `possum_users` INNER JOIN `possum_sheets` ON possum_users.username=possum_sheets.host WHERE possum_sheets.host=?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$sheet['host']]);
            $name = implode($stmt->fetch());
        ?>
        <h1><?=$sheet['title']?></h1> 
        <h2>Host: <?=$name?></h2>
        <div class="description">
            <p>Description:</p>
            <p>
                <?=$sheet['description']?>
            </p>
        </div>
        <h2>Visibility: <?php 
                            if ($sheet['public'] > 0) 
                            { 
                                echo("Public");
                            } 
                            else 
                            { 
                                echo("Private");
                            }
                        ?></h2>
        <h2>Created: <?=$sheet['date_created']?></h2>

        <h2>Slots: <?=$sheet['slots_remaining']?> Remaining</h2>
        <div class="timeslot">
            <h2>Timeslot:</h2>
            <p>Name: <?=$slot['name']?></p>
            <p>Timeslot: <?=$slot['timeslot']?></p>
        </div>
        <button type="button" name="cancel" id="cancel" value="cancel">Cancel Timeslot</button>
    </main>
</html>