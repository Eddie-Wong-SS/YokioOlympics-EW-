<?php
/**
Gets the booker's reference number to retrieve the correct booking record
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
    echo "<script>alert('This page is for visitors only, staff and admins do not need to use this page');</script>";
    echo "<script>location = '". $previous ."'; </script>"; //Returns user back to previous page
}?>
<title>Get Booking Reference</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container">
    <h1>Booking Reference</h1>
    <h3>Enter your booking reference code as written in your email(Reference Code)</h3>
    <h3>If you have lost your reference code please contact an administrator</h3>
    <br/>
    <form method="post">
        <table>
            <caption>Enter Reference</caption>
            <tr>
                <td><label for="bCode">Booking Reference Code: </label></td>
                <td><input type="text" name="bCode" id="bCode" maxlength="19" size="21" required> </td>
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
        //Checks if booking reference code matches any booking records'
        $SQL = "SELECT * FROM tblbooking WHERE Status = 'A' AND bookRef = '".trim($_POST['bCode'])."'";
        $Result = mysqli_query($Link, $SQL);

        if(mysqli_num_rows($Result) > 0)//Match found
        {
            $RowInfo = mysqli_fetch_array($Result);
            //Shows user their account username and password
            echo "<h1 style='color: green;'>Your Booking Record has been found!</h1>";
            echo "<a href='bookDetails.php?Id=".$RowInfo['bookRef']. "'><input type='button' value='Proceed to Booking Details' class='button' style=\" font-size: 36px\"></a>"; //Continue to log in
        }
        else //No unactivated account that matches the verification code is found
        {
            echo "<h1 style='color: red;'>You have either entered the wrong code, or your booking has been cancelled previously</h1>";
        }
    }
    ?>
</div>
</body>
