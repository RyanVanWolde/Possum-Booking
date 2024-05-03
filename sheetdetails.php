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
$query = "SELECT * FROM possum_sheets WHERE sheet_ID=?";
$stmt=$pdo->prepare($query);
$stmt->execute([$id]);
$sheet = $stmt->fetch();

$query = "SELECT * FROM `possum_slots` WHERE sheet_ID=?";
$slot_stmt = $pdo->prepare($query);
$slot_stmt->execute([$id]);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/sheetdetails.css" />
        <?php
            $PAGE_TITLE = "Sheet Details - Possum Tracks";
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
        <div class="timeslot_data">
            <table>

                <tr>
                    <th>Name</th>
                    <th>Timeslot</th>
                    <th>Email</th>
                </tr>
                <?php foreach ($slot_stmt as $row) : ?>
                    <tr>
                        <td><?= $row['name']?></td>
                        <td><?= $row['timeslot']?></td>
                        <td><?= $row['email']?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="sheet_edit">
            <button type="button" name="edit" id="edit" value="edit" onclick="window.location.href='editsheet.php?id=<?=$id?>';">Edit</button>
            <button type="button" name="copy" id="copy" value="copy" onclick="window.location.href='copysheet.php?id=<?=$id?>';">Copy</button>
            <button type="button" name="delete" id="delete" value="delete">Delete</button>
        </div>
    </main>
</html>