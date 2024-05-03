<?php
$username = $_POST['username'] ?? "";
$email = $_POST['email'] ?? "";

require_once "Mail.php";  //this includes the pear SMTP mail library
$from = "Password System Reset <noreply@loki.trentu.ca>";
$to = $username . "<". $email .">";  //put user's email here
$subject = "Your Password Reset";
$body = "Thank you for making an account for Possum Tracks!\n\n
        Click this link to reset your password:\n
        https://loki.trentu.ca/~ryanvanwolde/3420/assn/assn3/resetpassword.php?email=".$email;
$host = "smtp.trentu.ca";
$headers = array ('From' => $from,
  'To' => $to,
  'Subject' => $subject);
$smtp = Mail::factory('smtp',
  array ('host' => $host));
  
$mail = $smtp->send($to, $headers, $body);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/account.css" />
        <?php
            $PAGE_TITLE = "Forgot Password - Possum Tracks";
            include "includes/metadata.php";
        ?>
    </head>
    <!-- HEADER -->
    <?php include "includes/header.php" ?>
    <main>
        <h1>Forgot Password</h1>
        <p class="information">Enter your information so we can send you a password reset. Thanks</p>
        <fieldset>
            <legend></legend>
                <form name="forgot_password" id="forgot_password" method="post">
                    <p class="information">Please enter your username AND/OR email to receive the reset</p>
                    <div class="username">
                        <label for="username">Username: </label>
                        <input type="text" name="username" id="username" maxlength="16"/>
                    </div>
                    <div class="email">
                        <label for="email">Email: </label>
                        <input type="text" name="email" id="email"/>
                    </div>
                    <button type="submit">Submit</button>
                </form>
        </fieldset>
    </main>
</html>