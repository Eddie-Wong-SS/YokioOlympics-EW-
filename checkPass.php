<?php
/**
Checks if the password change is done by the correct owner as only correct owner should know current password
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<title>Confirm Password</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<body>
<div class="container" style="width: 80%">
    <h3>*Mandatory</h3>
    <form method="post" action="">
        <?php
        $SQL = "SELECT IC, Username, Password FROM tbllogin WHERE IC = '".$_GET['Id']."'"; //Gets account details
        $Result = mysqli_query($Link, $SQL);

        if(mysqli_num_rows($Result)) //Record exists
        {
            $RowInfo = mysqli_fetch_array($Result);
                ?>
                <table>
                    <caption>Verify Password</caption>
                    <tr>
                        <td style="width: 50%"><label for="CName">Username: </label></td>
                        <td><input type="text" name="CName" id="CName" size="52"
                                   value="<?php echo $RowInfo['Username']; ?>" style="background-color: lightgray"
                                   readonly></td>
                    </tr>
                    <tr>
                        <td colspan="2"><h2 style="color: darkred">Enter your current password</h2></td>
                    </tr>
                    <tr>
                        <td><label for="CPW">*Password: </label></td>
                        <td><input type="password" name="CPW" ID="CPW" required></td>
                    </tr>
                    <tr>
                        <td colspan="2"><input type="submit" name="btnSubmit" id="btnSubmit" value="Proceed" class="button"></td>
                    </tr>
                </table>
                <br />
                <?php
                $IC = $RowInfo['IC'];
        }
        else //Record does not exist
        {
            echo "An error has occured, please log in and try again";
        }
        ?>
    </form>
</div>
</body>
<?php
if($_REQUEST['btnSubmit']) //Submit button is clicked
{
    $CheckSQL = "Select * FROM tbllogin WHERE Password = '".sha1($_POST['CPW'])."' AND IC = '".$_GET['Id']."'"; //Checks if password matches the account
    $CheckSQLResult = mysqli_query($Link, $CheckSQL);
    if(mysqli_num_rows($CheckSQLResult)) //Password matches the account's
    {
        $URL = "changePass.php?Id=".$_SESSION['IC']."&hashed=".$_SESSION['code'];
        echo ("<script>location.href='$URL'</script>"); //Go to actual password change page
    }
    else //Password does not match the account's
    {
        echo "<script>alert('Your password is wrong, please retry');</script>";
        echo "<script>location='Main Page.php';</script>"; //Return to main page
    }

}
?>
