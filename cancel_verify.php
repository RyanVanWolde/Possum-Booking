<?php
//include the library file
require 'includes/library.php';
// create the database connection
$pdo = connectDB();
session_start();
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


if (isset($_POST['submit'])) {
        // Cancel Timeslot
        $id = $_GET['id'];
        $query = "UPDATE `possum_slots` SET name='OPEN', email='' WHERE slot_ID=?";
        $stmt=$pdo->prepare($query);
        $stmt->execute([$id]);
        
        // Get Sheet_ID
        $query = "SELECT * FROM `possum_slots` WHERE slot_ID=?";
        $stmt=$pdo->prepare($query);
        $stmt->execute([$id]);
        $slot = $stmt->fetch();
        $sheet_ID = $slot['sheet_ID'];

        // Get Current Slots Remaining
        $query = "SELECT slots_remaining FROM `possum_sheets` WHERE sheet_ID=?";
        $changeslot_stmt = $pdo->prepare($query);
        $changeslot_stmt->execute([$sheet_ID]);
        $current_slotsremaining = $changeslot_stmt->fetch();
        $current_slotsremaining = $current_slotsremaining['slots_remaining'];
    
        // Update Remaining Time Slots
        $query = "UPDATE `possum_sheets` SET slots_remaining=? WHERE sheet_ID=?";
        $changeslot_stmt = $pdo->prepare($query);
        $changeslot_stmt->execute([$current_slotsremaining + 1, $sheet_ID]);

        // Redirect
        header('Location: index.php');
        exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/sheetdetails.css" />
        <?php
            $PAGE_TITLE = "Cancel Verify - Possum Tracks";
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
        <h1>Verify Cancellation</h1>
        <div class="welcome">
            <p>Are you sure you want to cancel your timeslot?</p>
            <p>Click the "Cancel Timeslot" button below to be removed from the sheet.</p>
        </div>
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
        <h2>Timeslot: <?= $slot['timeslot']?></h2>
        <fieldset class="cancel_email">
            <form name="verification" id="verification" method="post">
                <div>
                    <button id="submit" name="submit">Cancel Timeslot</button>
                </div>
            </form>
        </fieldset>
    </main>
</html>