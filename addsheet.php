<?php
// Verify Login
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}

$errors = array();
// save form data
$sheet_title = $_POST['sheet_title'] ?? "";
$sheet_description = $_POST['sheet_description'] ?? "";
if (isset($_POST['searchable'])) {
    $sheet_searchable = 1;
} else {
    $sheet_searchable = 0;
}
$slots_remaining = 0;
if (!empty($_POST['sheet_timeslot1'])) {
    $sheet_timeslot1 = $_POST['sheet_timeslot1'] ?? "0000-00-00 00:00:00";
    $slots_remaining++;
}
if (!empty($_POST['sheet_timeslot2'])) {
    $sheet_timeslot2 = $_POST['sheet_timeslot2'] ?? "0000-00-00 00:00:00";
    $slots_remaining++;
}
if (!empty($_POST['sheet_timeslot3'])) {
    $sheet_timeslot3 = $_POST['sheet_timeslot3'] ?? "0000-00-00 00:00:00";
    $slots_remaining++;
}
if (!empty($_POST['sheet_timeslot4'])) {
    $sheet_timeslot4 = $_POST['sheet_timeslot4'] ?? "0000-00-00 00:00:00";
    $slots_remaining++;
}
if (!empty($_POST['sheet_timeslot5'])) {
    $sheet_timeslot5 = $_POST['sheet_timeslot5'] ?? "0000-00-00 00:00:00";
    $slots_remaining++;
}
if (!empty($_POST['sheet_timeslot6'])) {
    $sheet_timeslot6 = $_POST['sheet_timeslot6'] ?? "0000-00-00 00:00:00";
    $slots_remaining++;
}
if (!empty($_POST['sheet_timeslot7'])) {
    $sheet_timeslot7 = $_POST['sheet_timeslot7'] ?? "0000-00-00 00:00:00";
    $slots_remaining++;
}
if (!empty($_POST['sheet_timeslot8'])) {
    $sheet_timeslot8 = $_POST['sheet_timeslot8'] ?? "0000-00-00 00:00:00";
    $slots_remaining++;
}
if (!empty($_POST['sheet_timeslot9'])) {
    $sheet_timeslot9 = $_POST['sheet_timeslot9'] ?? "0000-00-00 00:00:00";
    $slots_remaining++;
}
if (!empty($_POST['sheet_timeslot10'])) {
    $sheet_timeslot10 = $_POST['sheet_timeslot10'] ?? "0000-00-00 00:00:00";
    $slots_remaining++;
}

// send sheet to the database
if(isset($_POST['submit'])) {
    require 'includes/library.php';
    $pdo = connectDB();
    
    // // empty title
    // if (!isset($sheet_title) || strlen($sheet_title) === 0) {
    //     echo '<script>console.log("Error: no_title"); </script>';
    //     $errors['no_title'] = true;
    // }
    // empty description
    if (!isset($sheet_description) || strlen($sheet_description) === 0) {
        echo '<script>console.log("Error: no_description"); </script>';
        $errors['no_description'] = true;
    }
    // send sheet to database
    if (count($errors) === 0) {
        echo '<script>console.log("No Errors"); </script>';
        //query to insert new sheet
        $query = "INSERT INTO `possum_sheets` (title, host, description, public, slots_remaining, date_created) VALUES (?, ?, ?, ?, ?, ?)";
        $addsheet_stmt = $pdo->prepare($query);
        $date_created = date("Y-m-d");
        $addsheet_stmt->execute([$sheet_title, $_SESSION['username'], $sheet_description, $sheet_searchable, $slots_remaining, $date_created]);

        $query = "SELECT sheet_ID FROM `possum_sheets` WHERE title=? AND host=? AND description=? AND public=? AND slots_remaining=? AND date_created=?";
        $findsheet_stmt = $pdo->prepare($query);
        $findsheet_stmt->execute([$sheet_title, $_SESSION['username'], $sheet_description, $sheet_searchable, $slots_remaining, $date_created]);
        $sheet_ID = $findsheet_stmt->fetch();
        $sheet_ID = $sheet_ID['sheet_ID'];

        $query = "INSERT INTO `possum_slots` (sheet_ID, name, timeslot, email) VALUES (?, 'OPEN', ?, '')";
        $addslot_stmt = $pdo->prepare($query);
        if (!empty($_POST['sheet_timeslot1'])) {
            $addslot_stmt = $pdo->prepare($query);
            $addslot_stmt->execute([$sheet_ID, date("Y-m-d H:i:s", strtotime($sheet_timeslot1))]);
        }
        if (!empty($_POST['sheet_timeslot2'])) {
            $addslot_stmt = $pdo->prepare($query);
            $addslot_stmt->execute([$sheet_ID, date("Y-m-d H:i:s", strtotime($sheet_timeslot2))]);
        }
        if (!empty($_POST['sheet_timeslot3'])) {
            $addslot_stmt->execute([$sheet_ID, date("Y-m-d H:i:s", strtotime($sheet_timeslot3))]);
        }
        if (!empty($_POST['sheet_timeslot4'])) {
            $addslot_stmt->execute([$sheet_ID, date("Y-m-d H:i:s", strtotime($sheet_timeslot4))]);
        }
        if (!empty($_POST['sheet_timeslot5'])) {
            $addslot_stmt->execute([$sheet_ID, date("Y-m-d H:i:s", strtotime($sheet_timeslot5))]);
        }
        if (!empty($_POST['sheet_timeslot6'])) {
            $addslot_stmt->execute([$sheet_ID, date("Y-m-d H:i:s", strtotime($sheet_timeslot6))]);
        }
        if (!empty($_POST['sheet_timeslot7'])) {
            $addslot_stmt->execute([$sheet_ID, date("Y-m-d H:i:s", strtotime($sheet_timeslot7))]);
        }
        if (!empty($_POST['sheet_timeslot8'])) {
            $addslot_stmt->execute([$sheet_ID, date("Y-m-d H:i:s", strtotime($sheet_timeslot8))]);
        }
        if (!empty($_POST['sheet_timeslot9'])) {
            $addslot_stmt->execute([$sheet_ID, date("Y-m-d H:i:s", strtotime($sheet_timeslot9))]);
        }
        if (!empty($_POST['sheet_timeslot10'])) {
            $addslot_stmt->execute([$sheet_ID, date("Y-m-d H:i:s", strtotime($sheet_timeslot10))]);
        }
        
        //send the user to the index page.
        header("Location:index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/main.css" />
        <?php
            $PAGE_TITLE = "Create Sheet - Possum Tracks";
            include "includes/metadata.php";
        ?>
    </head>
    <!-- HEADER -->
    <?php include "includes/header.php" ?>
    <main>
        <h1 class="title">Create Sheet</h1>
        <fieldset class="create_sheet">
            <form name="create_sheet" id="create_sheet" method="post">
                <div class="sheet_title">
                    <label for="sheet_title">Title: </label>
                    <input type="text" name="sheet_title" id="sheet_title" maxlength="35">
                </div>
                <span class="<?= !isset($errors['no_title']) ? 'hidden' : ""; ?>">*Enter a title</span>
                <div class="sheet_description">
                    <label for="sheet_description">Description: </label>
                    <input type="text" name="sheet_description" id="sheet_description" placeholder="A short description..." >
                </div>
                <span class="<?= !isset($errors['no_description']) ? 'hidden' : ""; ?>">*Enter a description</span>
                <div class="searchable">
                    <label for="searchable">Public? </label>
                    <input type="checkbox" name="searchable" id="searchable" value="searchable">
                </div>
                <div class="sheet_timeslot">
                    <table>
                        <tr><th>Timeslots:</th></tr>
                        <tr><td><input type="datetime-local" name="sheet_timeslot1" id="sheet_timeslot1"></td></tr>
                        <tr><td><input type="datetime-local" name="sheet_timeslot2" id="sheet_timeslot2"></td></tr>
                        <tr><td><input type="datetime-local" name="sheet_timeslot3" id="sheet_timeslot3"></td></tr>
                        <tr><td><input type="datetime-local" name="sheet_timeslot4" id="sheet_timeslot4"></td></tr>
                        <tr><td><input type="datetime-local" name="sheet_timeslot5" id="sheet_timeslot5"></td></tr>
                        <tr><td><input type="datetime-local" name="sheet_timeslot6" id="sheet_timeslot6"></td></tr>
                        <tr><td><input type="datetime-local" name="sheet_timeslot7" id="sheet_timeslot7"></td></tr>
                        <tr><td><input type="datetime-local" name="sheet_timeslot8" id="sheet_timeslot8"></td></tr>
                        <tr><td><input type="datetime-local" name="sheet_timeslot9" id="sheet_timeslot9"></td></tr>
                        <tr><td><input type="datetime-local" name="sheet_timeslot10" id="sheet_timeslot10"></td></tr>
                    </table>
                </div>
                <div>
                    <button id="submit" name="submit">Create Sheet</button>
                </div>
            </form>
        </fieldset>
    </main>
</html>