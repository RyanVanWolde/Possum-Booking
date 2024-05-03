<?php
$errors = array();

$newpassword = $_POST["new_password"] ?? "";
$confirmpassword = $_POST["confirm_password"] ?? "";

//include the library file
require 'includes/library.php';
// create the database connection
$pdo = connectDB();

if (isset($_POST['submit'])) {
    if ([$newpassword] != [$confirmpassword]) {
        $errors['different_password'] = true;
    }
    if (count($errors) === 0) {
        $email = $_GET['email'];
        $query = "UPDATE `possum_users` SET password=? WHERE email=?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([password_hash($newpassword, PASSWORD_BCRYPT), $email]);
        //send the user to the index page.
        header("Location:login.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/account.css" />
        <?php
            $PAGE_TITLE = "Reset Password - Possum Tracks";
            include "includes/metadata.php";
        ?>
    </head>
    <!-- HEADER -->
    <?php include "includes/header.php" ?>
    <main>
        <h1>Reset Password</h1>
        <p class="information">Enter your new password. Afterwards you will be sent to the login page.</p>
        <fieldset>
                <form name="reset_password" id="reset_password" method="post">
                    <div class="new_password">
                        <label for="newpassword">New Password: </label>
                        <input type="text" name="new_password" id="new_password"/>
                    </div>
                    <div class="confirm_password">
                        <label for="confirm_password">Confirm Password: </label>
                        <input type="text" name="confirm_password" id="confirm_password"/>
                    </div>
                    <div>
                        <button id="submit" name="submit">Reset Password</button>
                    </div>
                    <span class="<?= !isset($errors['different_password']) ? 'hidden' : ""; ?>">*Passwords did not match, enter the same password twice to confirm</span>
                </form>
        </fieldset>
    </main>
</html>