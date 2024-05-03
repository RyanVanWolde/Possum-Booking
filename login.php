<?php
/***********************
 * PUT CODE FOR PROCESSING
 * LOGIN FORM HERE
 ***********************/
$username = $_POST['username'] ?? "";
$password = $_POST['password'] ?? "";
$remember_me = $_POST['remember_me'] ?? NULL;

if(isset($_COOKIE['userCookie'])){
  $usernameCookie=$_COOKIE['userCookie'];
} else {
  $usernameCookie = "";
}
if(isset($_POST['submit'])) {
  require 'includes/library.php';
  $pdo = connectDB();
  $query = "select * from possum_users where username=?";
  $stmt = $pdo->prepare($query);
  $stmt->execute([$username]);
  if(!$results = $stmt->fetch()) {
    $errors['user'] = true;
  } else {
    if(password_verify($password, $results['password'])) {
      session_start();
      $_SESSION['username'] = $username;
      $_SESSION['user_id'] = $results['user_id'];
      if (isset($_POST['remember_me']))
        setcookie("userCookie",$username,time()+60*60*24*30*12);
      header('Location: index.php');
      exit();
    } else {
      $errors['login'] = true;
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/account.css" />
        <?php
            $PAGE_TITLE = "Login - Possum Tracks";
            include "includes/metadata.php";
        ?>
    </head>
    <!-- HEADER -->
    <?php include "includes/header.php" ?>
    <main>
        <h1>Login</h1>
            <form action="<?= htmlentities($_SERVER['PHP_SELF']) ?>" method="POST" autocomplete="off">
                <div>
                    <label for="username">Username:</label>
                    <input
                    type="text"
                    name="username"
                    id="username"
                    placeholder="Username"
                    value="<?= $usernameCookie ?>">
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
                <div>
                    <label for="password">Password:</label>
                    <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Password">
                    <label for="show_password">Show Password: </label>
                    <input type="checkbox" id="show_password" onclick="showPassword()">
                </div>
                <div class="remember_me">
                    <label for="remember_me">Remember Me: </label>
                    <input type="checkbox" name="remember_me" id="remember_me" value="remember_me" />
                </div>
                <span class="<?= !isset($errors['user']) ? 'hidden' : ""; ?>">*That user doesn't exist</span>
                <span class="<?= !isset($errors['login']) ? 'hidden' : ""; ?>">*Incorrect login info</span>
                <div>
                    <button id="submit" name="submit">Login</button>
                </div>
            </form>
        <div class="account_links">
            <a href="register.php">Create an Account</a> |
            <a href="forgot.php">Forgot Password?</a>
        </div>
    </main>
</html>