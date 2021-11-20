<?php
/**
Verifies that the account belongs to the correct user
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
if($_SESSION['Login'] == true) //Sends user back if they have already logged in
{
    $previous = "javascript:history.go(-1)"; //Gets previous page URL
    if(isset($_SERVER['HTTP_REFERER'])) {
        $previous = $_SERVER['HTTP_REFERER'];
    }
    echo "<script>alert('You are already logged in, please log out to access this page again');</script>";
    echo "<script>location = '". $previous ."'; </script>"; //Returns user back to previous page
}?>
<title>Verify Account</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container">
    <h1>Verify Account</h1>
    <h3>Enter the verification code you have received in your email</h3>
    <br/>
    <form method="post">
        <table>
            <caption>Enter Code</caption>
            <tr>
                <td><label for="vCode">Verification Code: </label></td>
                <td><input type="text" name="vCode" id="vCode" maxlength="8" size="10" required> </td>
            </tr>
            <tr>
                <td colspan="100%"><input type="submit" name="btnSub" id="btnSub" value="Submit Code" class="button"> </td>
            </tr>
        </table>
    </form>
    <br/>
    <?php
    if($_REQUEST['btnSub']) //Submit Code button is clicked
    {
        //Checks if verification code matches any unactivated account
        $SQL = "SELECT * FROM tbllogin WHERE Status = 'N' AND veriCode = '".trim($_POST['vCode'])."'";
        $Result = mysqli_query($Link, $SQL);
        $flag = 0;

        if(mysqli_num_rows($Result) > 0)//Account exists
        {
            $RowInfo = mysqli_fetch_array($Result);
            //Activate account and encrypts password
            //Password is originally unencrypted so users can get their password only when account is activated
            $UpSQL = "UPDATE tbllogin SET Status = 'A', Password = '".sha1(trim($RowInfo['Password']))."' WHERE veriCode = '".trim($_POST['vCode'])."'";
            $UpSQLResult = mysqli_query($Link, $UpSQL);
            $flag = 1;
            //Shows user their account username and password
            echo "<h1 style='color: green;'>Your Account Has Been Successfully Verified!</h1>";
            echo "<br/>";
            echo "<h3>Your Username: ".$RowInfo['Username']."</h3>";
            echo "<br/>";
            echo "<h3 style='text-align: center;'>Your Password: <div class='spoiler'>".$RowInfo['Password']."</div></h3>"; //spoiler class used to hide password until hovered over
            echo "<br/><br/>";
            echo "<a href='Login.php'><input type='button' value='Proceed to Login' class='button' style=\" font-size: 36px\"></a>"; //Continue to log in
        }
        else //No unactivated account that matches the verification code is found
        {
            echo "<h1 style='color: red;'>You have either entered the wrong code, or your account has already been verified</h1>";
        }
    }
    ?>
</div>
</body>
