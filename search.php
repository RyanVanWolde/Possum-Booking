<?php 
    session_start();
    //include the library file
    require 'includes/library.php';
    // create the database connection
    $pdo = connectDB();

    $filter_title = $_POST['sheet_name'] ?? "";
    $filter_host = $_POST['sheet_host'] ?? "";

    if ($filter_host == "" AND $filter_title == "") {
        $query = "SELECT sheet_ID, title, host, description, public, slots_remaining FROM `possum_sheets` WHERE public=1";
        $results_stmt = $pdo->query($query);
    } else if ($filter_host != "" AND $filter_title == "") {
        $query = "SELECT possum_users.`username` FROM `possum_users` WHERE possum_users.name=?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$filter_host]);
        $name = implode($stmt->fetch());

        $query = "SELECT sheet_ID, title, host, description, public, slots_remaining FROM `possum_sheets` WHERE public=1 AND host=?";
        $results_stmt = $pdo->prepare($query);
        $results_stmt->execute([$name]);
    } else if ($filter_host == "" AND $filter_title != "") {
        $query = "SELECT sheet_ID, title, host, description, public, slots_remaining FROM `possum_sheets` WHERE public=1 AND title=?";
        $results_stmt = $pdo->prepare($query);
        $results_stmt->execute([$filter_title]);
    } else if ($filter_host != "" AND $filter_title != "") {
        $query = "SELECT possum_users.`username` FROM `possum_users` WHERE possum_users.name=?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$filter_host]);
        $name = implode($stmt->fetch());

        $query = "SELECT sheet_ID, title, host, description, public, slots_remaining FROM `possum_sheets` WHERE public=1 AND host=? AND title=?";
        $results_stmt = $pdo->prepare($query);
        $results_stmt->execute([$name, $filter_title]);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/main.css" />
        <?php
            $PAGE_TITLE = "Search - Possum Tracks";
            include "includes/metadata.php";
        ?>
    </head>
    <!-- HEADER -->
    <?php include "includes/header.php" ?>
    <main>
        <fieldset class="search">
            <legend>Search</legend>
            <form name="search" id="search" method="post">
                <div class="sheet_name">
                    <label for="sheet_name">Sheet Name: </label>
                    <input type="text" name="sheet_name" id="sheet_name">
                </div>
                <div class="sheet_host">
                    <label for="sheet_host">Sheet Host: </label>
                    <input type="text" name="sheet_host" id="sheet_host">
                </div>
                <button type="submit">Submit</button>
            </form>
        </fieldset>

        <div class="Results">
            <?php foreach ($results_stmt as $row) : ?>
                <?php
                    $query = "SELECT possum_users.`name` FROM `possum_users` INNER JOIN `possum_sheets` ON possum_users.username=possum_sheets.host WHERE possum_sheets.host=?";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute([$row['host']]);
                    $name = implode($stmt->fetch());
                ?>
                <div class="sheet">
                    <h3><?=$row['title']?></h3>
                    <h4>Host: <?=$name?></h4>
                    <div class="description">
                        <p>Description:</p>
                        <p><?=$row['description']?></p>
                    </div>
                    <h4><?=$row['slots_remaining']?> Slots Remaining</h4>
                    <a href="signup.php?id=<?=$row['sheet_ID']?>">Sign Up</a>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</html>