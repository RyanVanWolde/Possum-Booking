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

    // get user's name
    $query = "SELECT possum_users.`name` FROM `possum_users` WHERE possum_users.username=?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_SESSION['username'] ?? ""]);
    $name = $stmt->fetch();
    if (is_bool($name)) {
        // do nothing
    } else {
        $name = $name['name'];
    }

    // query for all sheets that user owns
    $query = "SELECT sheet_ID, title, host, description, public, slots_remaining, date_created FROM `possum_sheets` WHERE host=?";
    $host_stmt = $pdo->prepare($query);
    $host_stmt->execute([$_SESSION['username'] ?? ""]);
    

    // query for all sheets user is attending
    $query = "SELECT * FROM `possum_sheets` INNER JOIN `possum_slots` ON possum_slots.sheet_ID=possum_sheets.sheet_ID WHERE possum_slots.name=?";
    $slot_stmt = $pdo->prepare($query);
    $slot_stmt->execute([$name]);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/main.css" />
        <?php
            $PAGE_TITLE = "Index - Possum Tracks";
            include "includes/metadata.php";
        ?>
    </head>
    <!-- HEADER -->
    <?php include "includes/header.php" ?>
    <main>
        <p class="welcome">Welcome to Possum Tracks, a website which allows you to create and register for sign up sheets!</p>
        <h2>Hosting</h2>
        <div class="my_sheets">
            <?php foreach ($host_stmt as $row) : ?>
                <div class="sheet" id="sheet">
                    <h3><?=$row['title']?></h3>
                    <h4>Host: <?=$name?></h4>
                    <div class="description">
                        <p>Description:</p>
                        <p><?=$row['description']?></p>
                    </div>
                    <h4><?=$row['slots_remaining']?> Slots Remaining</h4>
                    <a href="sheetdetails.php?id=<?=$row['sheet_ID']?>">Details</a>
                </div>
                <div id="modalDetails_<?=$row['sheet_ID']?>" class="modal">
                    <!-- Modal content -->
                    <div class="modal-content">
                        <?php 
                            $query = "SELECT * FROM `possum_slots` WHERE sheet_ID=?";
                            $sheetslots_stmt = $pdo->prepare($query);
                            $sheetslots_stmt->execute([$row['sheet_ID']]);
                        ?>
                        <span class="close_<?=$row['sheet_ID']?>" id="close">&times;</span>
                        <h1><?=$row['title']?></h1> 
                        <h2>Host: <?=$name?></h2>
                        <div class="description">
                            <p>Description:</p>
                            <p>
                                <?=$row['description']?>
                            </p>
                        </div>
                        <h2>Visibility: <?php 
                            if ($row['public'] > 0) 
                            { 
                                echo("Public");
                            } else { 
                                echo("Private");
                            }
                        ?></h2>
                        <h2>Created: <?=$row['date_created']?></h2>
                        <h2>Slots: <?=$row['slots_remaining']?> Remaining</h2>
                        <div class="timeslot_data">
                            <table>
                                <tr>
                                    <th>Name</th>
                                    <th>Timeslot</th>
                                    <th>Email</th>
                                </tr>
                                <script>
                                    $query = "SELECT * FROM `possum_slots` WHERE sheet_ID=?";
                                    $sheetslots_stmt = $pdo->prepare($query);
                                    $sheetslots_stmt->execute([$id]);
                                </script>
                                <?php foreach ($sheetslots_stmt as $timeslot) : ?>
                                    <tr>
                                        <td><?= $timeslot['name']?></td>
                                        <td><?= $timeslot['timeslot']?></td>
                                        <td><?= $timeslot['email']?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <!-- Buttons -->
                        <div class="sheet_edit">
                            <button type="button" name="edit" id="edit" value="edit" onclick="window.location.href='editsheet.php?id=<?=$row['sheet_ID']?>';">Edit</button>
                            <button type="button" name="copy" id="copy" value="copy" onclick="window.location.href='copysheet.php?id=<?=$row['sheet_ID']?>';">Copy</button>
                            <button type="button" name="delete" id="delete" value="delete">Delete</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <script>
            document.querySelectorAll('#sheet').forEach(sheet => {
                // Open Modal on Click
                sheet.addEventListener('click', event => {
                    var modal = document.getElementById("modalDetails_<?=$row['sheet_ID']?>");

                    // Get the <span> element that closes the modal
                    var span = document.getElementsByClassName("close_<?=$row['sheet_ID']?>")[0];
                    // When the user clicks on <span> (x), close the modal
                    span.onclick = function() {
                    modal.style.display = "none";
                    }
                    // When the user clicks anywhere outside of the modal, close it
                    window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                    }
                    
                    modal.style.display = "block";
                    })
            })
        </script>
        <h2>Attending</h2>
        <div class="attending_sheets">
            <?php foreach ($slot_stmt as $row) : ?>
                <?php
                    $query = "SELECT possum_users.`name` FROM `possum_users` WHERE possum_users.username=?";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([$row['host']]);
                    $hostname = implode($stmt->fetch());
                ?>
                <div class="sheet" id="sheet">
                    <h3><?=$row['title']?></h3>
                    <h4>Host: <?=$hostname?></h4>
                    <div class="description">
                        <p>Description:</p>
                        <p><?=$row['description']?></p>
                    </div>
                    <h4><?=$row['slots_remaining']?> Slots Remaining</h4>
                    <div class="timeslot">
                        <h4>Timeslot: <?=$row['timeslot']?></h4>
                    </div>
                    <a href="slotdetails.php?id=<?=$row['slot_ID']?>">Timeslot Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</html>