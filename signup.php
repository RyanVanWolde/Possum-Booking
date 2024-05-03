<?php 
    session_start();
    $errors = array(); //declare empty array to add errors too

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
    $slot_stmt1 = $pdo->prepare($query);
    $slot_stmt1->execute([$id]);

    $query = "SELECT * FROM `possum_slots` WHERE sheet_ID=?";
    $slot_stmt2 = $pdo->prepare($query);
    $slot_stmt2->execute([$id]);

    if(isset($_POST['submit_user'])) {
        // Get User Information
        $query = "SELECT name, email FROM `possum_users` WHERE username=?";
        $grabinfo_stmt = $pdo->prepare($query);
        $grabinfo_stmt->execute([$_SESSION['username']]);
        $results = $grabinfo_stmt->fetch();
        $user_name = $results['name'];
        $user_email = $results['email'];

        // Sign Up User
        $query = "UPDATE `possum_slots` SET sheet_ID=?, name=?, email=? WHERE timeslot=?";
        $signup_stmt = $pdo->prepare($query);
        $signup_stmt->execute([$id, $user_name, $user_email, $_POST['submit_user']]);

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

        require_once "Mail.php";  //this includes the pear SMTP mail library
        $from = "Possum Tracks <noreply@loki.trentu.ca>";
        $to = $user_name . "<". $user_email .">";  //put user's email here
        $subject = "Slot Confirmation";
        $body = "You have sucessfully signed up for the following timeslot!\n\n     Title: ".$sheet['title']. "\n     Description: ".$sheet['description']. "\n     Timeslot: ". $_POST['submit_user'];
        $host = "smtp.trentu.ca";
        $headers = array ('From' => $from,
        'To' => $to,
        'Subject' => $subject);
        $smtp = Mail::factory('smtp',
        array ('host' => $host));
        
        $mail = $smtp->send($to, $headers, $body);
        header("Refresh:0");
    }
    if (isset($_POST['submit_guest'])) {
        // ERROR VALIDATION
        //validate email address
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = true;
        }

        if (count($errors) === 0) {
            // Sign Up User
            $query = "UPDATE `possum_slots` SET sheet_ID=?, name=?, email=? WHERE timeslot=?";
            $signup_stmt = $pdo->prepare($query);
            $signup_stmt->execute([$id, $_POST['name'], $_POST['email'], $_POST['submit_guest']]);

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

            require_once "Mail.php";  //this includes the pear SMTP mail library
            $from = "Possum Tracks <noreply@loki.trentu.ca>";
            $to = $_POST['name'] . "<". $_POST['email'] .">";  //put user's email here
            $subject = "Slot Confirmation";
            $body = "You have sucessfully signed up for the following timeslot!\n\n     Title: ".$sheet['title']. "\n     Description: ".$sheet['description']. "\n     Timeslot: ". $_POST['submit_user'];
            $host = "smtp.trentu.ca";
            $headers = array ('From' => $from,
            'To' => $to,
            'Subject' => $subject);
            $smtp = Mail::factory('smtp',
            array ('host' => $host));
            
            $mail = $smtp->send($to, $headers, $body);
            header("Refresh:0");
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/sheetdetails.css" />
        <?php
            $PAGE_TITLE = "Signup - Possum Tracks";
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
        <div class="logged_user">
            <h2>Account User</h2>
            <form name="timeslot_choice" id="timeslot_choice" method="post">
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Timeslot</th>
                    </tr>
                    <?php foreach ($slot_stmt1 as $row) : ?>
                    <tr>
                        <td><?= $row['name']?></td>>
                        <td>
                          <?php
                            if ($row['name'] == 'OPEN') {
                                echo '<div><input type="submit" name="submit_user" id="submit_user" value="'.$row['timeslot']. '"></div>';
                            } else {
                                echo '<div><input type="submit" name="submit_user" id="submit_user" value="'.$row['timeslot'].'" disabled></div>';
                            }
                          ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </form>
        </div>
        <div class="guest_user">
            <h2>Guest User</h2>
            <form name="timeslot_choice_guest" id="timeslot_choice_guest" method="post">
                <div>
                    <span class="<?= !isset($errors['email']) ? 'hidden' : ""; ?>">*Not a valid email</span>
                </div>
                <div class="name">
                    <label for="name">Name: </label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="email">
                    <label for="email">Email: </label>
                    <input type="email" name="email" id="email" required>
                </div>
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Timeslot</th>
                    </tr>
                    <?php foreach ($slot_stmt2 as $row) : ?>
                    <tr>
                        <td><?= $row['name']?></td>>
                        <td>
                          <?php
                            if ($row['name'] == 'OPEN') {
                                echo '<div><input type="submit" name="submit_guest" id="submit_guest" value="'.$row['timeslot']. '"></div>';
                            } else {
                                echo '<div><input type="submit" name="submit_guest" id="submit_guest" value="'.$row['timeslot'].'" disabled></div>';
                            }
                          ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </table>
            </form>
        </div>
    </main>
</html>