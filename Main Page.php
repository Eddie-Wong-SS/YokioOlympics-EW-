<?php
/** Default page of the system*/
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
//echo $_POST['supName'];
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/0.0.11/push.min.js"></script> <!-- push.js.master library for notifications -->
<script>
    Push.Permission.request(); //Browser will request permission to display notifications
    function checkers() //push.js.master notification function
    {
        Push.create('Daily News Article!', { //Create notification
            body: 'Please be advised that the news article for today has not yet been written, and should be resolved ASAP',
            icon: 'icon.png',
            timeout: 8000,                  // Timeout before notification closes automatically.
            onClick: function() {
                // Callback for when the notification is clicked.
                console.log(this);
            }
        });

    }
</script>
<head>
    <title>Main Page</title>
</head>
<?php include("Menu.php"); //Includes menubar
if ($_GET['log'] == 'o') //Logout of account is requested
{
    session_unset(); //Unsets all session variables
    session_destroy(); //Destroys session to ensure all information has been cleared
    echo "<script>location = 'Main Page.php'; </script>"; //Reloads page to ensure default view
}
if($_SESSION['Login'] == true)
{
    $CheckNewsExists = "SELECT * FROM tblnews WHERE writtenOn = '".$Mtime."'"; //Checks if a news article for the day has already been written
    $CheckNewsExistsResult = mysqli_query($Link,$CheckNewsExists);
    if(mysqli_num_rows($CheckNewsExistsResult) < 1) //No news article for the day has been written
    {
        ?>
        <script>checkers();</script>
        <?php
    }
}
?>
<link rel="stylesheet" href="Default%20Theme.css">
<div align="center" class="container" style="height: 100%; width: 80%">
    <h1 style="font: 72px bold;">Welcome To The Yokio Olympics Webpage</h1>
    <br/><br>
</div>
