<?php
    //include the library file
    require 'includes/library.php';
    // create the database connection
    $pdo = connectDB();
    session_start();
    if (isset($_SESSION['username'])) {
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
    if (isset($_POST['submit'])) {
        $email = $_POST['email'];
        $query = "SELECT * FROM `possum_slots` INNER JOIN `possum_sheets` ON possum_slots.sheet_ID=possum_sheets.sheet_ID WHERE email=?";
        $slot_stmt = $pdo->prepare($query);
        $slot_stmt->execute([$email]);
        $message = "";
        foreach ($slot_stmt as $row) {
            $message .= "Sheet: ". $row['title'] ." | Timeslot: ". $row['timeslot']. "| Cancel: https://loki.trentu.ca/~ryanvanwolde/3420/assn/assn3/cancel_verify.php?id=". $row['slot_ID']."\n";
        }

        require_once "Mail.php";  //this includes the pear SMTP mail library
        $from = "Possum Tracks <noreply@loki.trentu.ca>";
        $to = "Guest User <". $email .">";  //put user's email here
        $subject = "Timeslot Cancellation";
        $body = "Here are all of your timeslots. Click the link and verify you want to cancel and you will be taken off the list!\n\n". $message; 
        $host = "smtp.trentu.ca";
        $headers = array ('From' => $from,
        'To' => $to,
        'Subject' => $subject);
        $smtp = Mail::factory('smtp',
        array ('host' => $host));
        
        $mail = $smtp->send($to, $headers, $body);
        if (PEAR::isError($mail)) {
        echo("<p>" . $mail->getMessage() . "</p>");
        } else {
        echo("<p>Message successfully sent!</p>");
        }

        //send the user to the index page.
        header("Location:index.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/main.css" />
        <?php
            $PAGE_TITLE = "Cancel - Possum Tracks";
            include "includes/metadata.php";
        ?>
    </head>
    <!-- HEADER -->
    <?php include "includes/header.php" ?>
    <main>
        <h1 class="title">Request Slot Cancellation</h1>
        <div class="welcome">
            <p>As a guest user, we cannot see your timeslots. So, enter your email in the form below if you want to request a timeslot cancellation!</p>
            <p>You will receive an email which contains links to all of your timeslots, click the link and verify you want to cancel and you will be good to go!</p>
        </div>
        <fieldset class="cancel_email">
            <form name="cancel_email" id="cancel_email" method="post">
                <div class="email">
                    <label for="email">Email: </label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div>
                    <button id="submit" name="submit">Request Cancel</button>
                </div>
            </form>
        </fieldset>
    </main>
</html>