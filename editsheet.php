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

// Check if user owns this sheet
$id = $_GET['id'];
$query = "SELECT host FROM `possum_sheets` WHERE sheet_ID=?";
$stmt=$pdo->prepare($query);
$stmt->execute([$id]);
if ($_SESSION['username'] != ($stmt->fetch()['host'])) {
  header('Location: login.php');
  exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM possum_sheets WHERE sheet_ID=?";
$stmt=$pdo->prepare($query);
$stmt->execute([$id]);
$sheet = $stmt->fetch();

$query = "SELECT * FROM `possum_slots` WHERE sheet_ID=?";
$slot_stmt = $pdo->prepare($query);
$slot_stmt->execute([$id]);


if(isset($_POST['submit'])) {
  if (isset($_POST['delete'])) {
    foreach($_POST as $key => $value) {
      if ($key == 'delete') {

        // Delete Timeslot
        $query = "DELETE FROM `possum_slots` WHERE sheet_ID=? AND timeslot=?";
        $delete_stmt = $pdo->prepare($query);
        $delete_stmt->execute([$id, $value]);


        // Get Current Slots Remaining
        $query = "SELECT slots_remaining FROM `possum_sheets` WHERE sheet_ID=?";
        $changeslot_stmt = $pdo->prepare($query);
        $changeslot_stmt->execute([$id]);
        $current_slotsremaining = $changeslot_stmt->fetch();
        $current_slotsremaining = $current_slotsremaining['slots_remaining'];
    
        // Update Remaining Time Slots
        $query = "UPDATE `possum_sheets` SET slots_remaining=? WHERE sheet_ID=?";
        $changeslot_stmt = $pdo->prepare($query);
        $changeslot_stmt->execute([$current_slotsremaining - 1, $id]);
      }
    }
  }
  if (!empty($_POST['new_timeslot'])) {
    $new_timeslot = date("Y-m-d H:i:s", strtotime($_POST['new_timeslot']));
    $query = "INSERT INTO `possum_slots` (sheet_ID, name, timeslot, email) VALUES (?, 'OPEN', ?, '')";
    $addslot_stmt = $pdo->prepare($query);
    $addslot_stmt->execute([$id, $new_timeslot]);

    $query = "SELECT slots_remaining FROM `possum_sheets` WHERE sheet_ID=?";
    $changeslot_stmt = $pdo->prepare($query);
    $changeslot_stmt->execute([$id]);
    $current_slotsremaining = $changeslot_stmt->fetch();
    $current_slotsremaining = $current_slotsremaining['slots_remaining'];


    $query = "UPDATE `possum_sheets` SET slots_remaining=? WHERE sheet_ID=?";
    $changeslot_stmt = $pdo->prepare($query);
    $changeslot_stmt->execute([$current_slotsremaining + 1, $id]);
  }

  //send the user to the index page.
  header("Location:sheetdetails.php?id=$id");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/sheetdetails.css" />
        <?php
            $PAGE_TITLE = "Edit Sheet - Possum Tracks";
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
        <h2>Visibility: 
          <?php 
            if ($sheet['public'] > 0) 
            { 
              echo("Public");
            } 
            else 
            { 
              echo("Private");
            }
          ?>
        </h2>
        <h2>Created: <?=$sheet['date_created']?></h2>

        <h2>Slots: <?=$sheet['slots_remaining']?> Remaining</h2>
        <form name="edit_timeslot" id="edit_timeslot" method="post">
                <table>
                  <tr>
                      <th>Name</th>
                      <th>Timeslot</th>
                      <th>Email</th>
                      <th>Delete Slot?</th>
                  </tr>
                  <?php foreach ($slot_stmt as $row) : ?>
                    <tr>
                        <td><?= $row['name']?></td>
                        <td><?= $row['timeslot']?></td>
                        <td><?= $row['email']?></td>
                        <td>
                          <?php
                            if ($row['name'] == 'OPEN') {
                              echo '<div><input type="checkbox" name="delete" id="delete" value=" '.$row['timeslot']. '"></div>';
                            }
                          ?>
                        </td>
                    </tr>
                  <?php endforeach; ?>
                </table>
                <div>
                  <label for="new_timeslot">Add New Slot: </label>
                  <input type="datetime-local" name="new_timeslot" id="new_timeslot">
                </div>
                <div>
                    <button id="submit" name="submit">Save Changes</button>
                </div>
        </form>
    </main>
</html>