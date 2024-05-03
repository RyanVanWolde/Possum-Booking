<?php
// Verify Login
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit();
}

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

$current_username = ($_SESSION['username']);
$query = "SELECT * FROM `possum_users` WHERE username=?";
$user_stmt = $pdo->prepare($query);
$user_stmt->execute([$current_username]);
$user = $user_stmt->fetch();
$user_id = $user['user_id'];

if(isset($_POST['submit'])) {
    $query = "select * from possum_users where username=?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$username]);

    // ERROR VALIDATION
    // empty name
    if (!isset($name) || strlen($name) === 0) {
        $errors['name'] = true;
    }
    // empty username
    if (!isset($username) || strlen($name) === 0) {
        $errors['username'] = true;
    }
    //checking for lack of space (indication of not full name)
    if (strpos($name, " ") === false) {
        $errors['fullname'] = true;
    }
    //validate email address
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = true;
    }
    // check if user already has account
    $query = "SELECT * FROM `possum_users` WHERE username=?";
    $email_stmt = $pdo->prepare($query);
    $email_stmt->execute([$username]);
    if ($email_stmt->fetch() && $username != $current_username) {
        $errors['exist'] = true;
    }
    //check if both passwords are the same
    if ($password != $password_confirm) {
        $errors['different_password'] = true;
    }
    //check if password exists
    if (strlen($password) === 0) {
        $errors['no_password'] = true;
    }

    // perfect registration
    if (count($errors) === 0) {
        //query to insert entire voting record
        $query = "UPDATE `possum_users` SET username=?, name=?, email=?, password=? WHERE user_id=?";
        $register_stmt = $pdo->prepare($query);
        $register_stmt->execute([$username, $name, $email, password_hash($password, PASSWORD_BCRYPT), $user_id]);
    
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
            $PAGE_TITLE = "Edit Account - Possum Tracks";
            include "includes/metadata.php";
        ?>
    </head>
    <!-- HEADER -->
    <?php include "includes/header.php" ?>
    <main>
        <h1>Edit Account</h1>
        <div class="information">
            <p>Change details about your account.</p>
        </div>
        <fieldset>
                <form name="edit_account" id="edit_account" method="post">
                    
                    <div class="username">
                        <label for="username">Username: </label>
                        <input type="text" name="username" id="username" maxlength="16" value="<?php echo $user['username']; ?>"/>
                    </div>
                    <div class="name">
                        <label for="name">Name: </label>
                        <input type="text" name="name" id="name" value="<?php echo $user['name']; ?>"/>
                    </div>
                    <div class="email">
                        <label for="email">Email: </label>
                        <input type="email" name="email" id="email" value="<?php echo $user['email']; ?>"/>
                    </div>
                    <div class="password">
                        <label for="password">Password: </label>
                        <input type="password" name="password" id="password" />
                        <label for="password_confirm">Confirm Password: </label>
                        <input type="password" name="password_confirm" id="password_confirm" />
                    </div>
                    <div>
                        <button id="submit" name="submit">Submit</button>
                    </div>
                    <div>
                        <span class="<?= !isset($errors['name']) ? 'hidden' : ""; ?>">*Enter a name</span>
                        <span class="<?= !isset($errors['username']) ? 'hidden' : ""; ?>">*Enter a username</span>
                        <span class="<?= !isset($errors['fullname']) ? 'hidden' : ""; ?>">*Not a full name</span>
                        <span class="<?= !isset($errors['email']) ? 'hidden' : ""; ?>">*Not a valid email</span>
                        <span class="<?= !isset($errors['exist']) ? 'hidden' : ""; ?>">*That user already exists</span>
                        <span class="<?= !isset($errors['different_password']) ? 'hidden' : ""; ?>">*Passwords did not match, enter the same password twice to confirm</span>
                        <span class="<?= !isset($errors['no_password']) ? 'hidden' : ""; ?>">*Enter a password</span>
                    </div>
                </form>
        </fieldset>
    </main>
</html>