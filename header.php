<!DOCTYPE html>
<html lang="en">
    <script defer src="scripts/main.js"></script>
    <header>
         <div class="header">
            <h1>Possum Tracks</h1>
            <nav>
                <p>
                    <a href="index.php">Home</a> |
                    <a href="search.php">Search</a> |
                    
                    <?php 
                            if (!isset($_SESSION['username'])) 
                            { 
                                echo(" <a href='login.php'>Login</a> |");
                                echo(" <a href='register.php'>Create Account</a> |");
                                echo(" <a href='cancel.php'>Request Timeslot Cancel</a>");
                                
                            } 
                            else 
                            { 
                                echo(" <a href='addsheet.php'>Create Sheet</a> |");
                                echo(" <a href='editaccount.php'>Edit Account</a> |");
                                echo(" <a id='delete_account' href=''>Delete Account</a> |");
                                echo(" <a href='logout.php'>Logout</a>");
                            }
                    ?>
                </p>
            </nav>
        </div>
    </header>
</html>