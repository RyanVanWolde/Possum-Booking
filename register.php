<?php

$errors = array(); //declare empty array to add errors too

// GET EVERYTHING FROM $_POST
$username = $_POST['username'] ?? "";
$email = $_POST['email'] ?? "";
$name = $_POST['name'] ?? "";
$password = $_POST['password'] ?? "";
$password_confirm = $_POST['password_confirm'] ?? "";

//include the library file
require 'includes/library.php';
// create the database connection
$pdo = connectDB();

if(isset($_POST['submit'])) {
    $query = "select * from possum_users where username=?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);

    // perfect registration
    if (count($errors) === 0) {
        //query to insert new account
        $query = "INSERT INTO `possum_users` (username, name, email, password) VALUES (?, ?, ?, ?)";
        $register_stmt = $pdo->prepare($query);
        $register_stmt->execute([$username, $name, $email, password_hash($password, PASSWORD_BCRYPT)]);
    
        //send the user to the index page.
        header("Location:index.php");
        exit;
      }
  }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/account.css" />
        <?php
            $PAGE_TITLE = "Create Account - Possum Tracks";
            include "includes/metadata.php";
        ?>
    </head>
    <!-- HEADER -->
    <?php include "includes/header.php" ?>
    <main>
        <h1>Create Account</h1>
        <div class="information">
            <p>With an account, you will be able to use Possum Tracks more efficiently! Being able to sign up for timeslots instantly with the click of a button and the ability to track all of your current slots.</p>
            <p>Accounts are not necessary to use Possum Tracks, but are reccommended for ease-of-access and additional features.</p>
        </div>
        <fieldset>
            <form name="create_account" id="create_account" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="username" id="usernameInput">
                    <label for="username">Username: </label>
                    <input type="text" name="username" id="username" maxlength="16"/>
                </div>
                <div class="name">
                    <label for="name">Name: </label>
                    <input type="text" name="name" id="name"/>
                </div>
                <span class="<?= !isset($errors['name']) ? 'hidden' : ""; ?>">*Name Error: Enter a valid full name</span>
                <div class="email" id="emailInput">
                    <label for="email">Email: </label>
                    <input type="email" name="email" id="email"/>
                </div>
                <script>
                    function showPassword() {
                    var x = document.getElementById("password");
                    if (x.type === "password") {
                        x.type = "text";
                    } else {
                        x.type = "password";
                    }
                    } 
                </script>
                <div class="password">
                    <label for="password">Password: </label>
                    <input type="password" name="password" id="password" />
                    <label for="password_confirm">Confirm Password: </label>
                    <input type="password" name="password_confirm" id="password_confirm" />
                    <label for="meter">Password Security: </label>
                    <progress max="100" value="0" id="password_strength"></progress>
                    <label for="show_password">Show Password: </label>
                    <input type="checkbox" id="show_password" onclick="showPassword()">
                </div>
                <span class="<?= !isset($errors['password']) ? 'hidden' : ""; ?>">*Password Error: Enter the same password twice</span>
                <div>
                    <button id="submit" name="submit">Create Account</button>
                </div>
            </form>
        </fieldset>
        <div class="account_links">
            <a href="login.php">Already have an account?</a>
        </div>
    </main>
</html>