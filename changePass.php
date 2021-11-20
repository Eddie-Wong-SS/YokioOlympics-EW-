<?php
/**
Allows the user to reset their password
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<title>Reset Password</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/0.0.11/push.min.js"></script> <!-- push.js.master library for notifications -->
<script>

    Push.Permission.request(); //Browser will request permission to display notifications
    function checkers() //push.js.master notification function
    {
        Push.create('Successfully Changed!', { //Create notification
            body: 'Password change for <?php echo $_POST['CName']; ?> was successful',
            icon: 'icon.png',
            timeout: 8000,                  // Timeout before notification closes automatically.
            onClick: function() {
                // Callback for when the notification is clicked.
                console.log(this);
            }
        });

    }

    function validate() //Checks if passwords match each other
    {
        var a = document.getElementById("newPW").value; //Gets password
        var b = document.getElementById("conPW").value; //Gets confirmation password
        if (a!==b)  //Passwords do not match
        {
            alert("Passwords do not match");
            return false;
        }
        else if(a.length<8) //Password is too short
        {
            textBox.style.borderColor = "red";
            alert("Password is too short");
            return false;
        }
    }

    function callfunction(source)//Checks if password length is long enough
    {

        var textBox = source;
        var textLength = textBox.value.length;

        if(textLength<8) //Password is too short
        {
            textBox.style.borderColor = "red";
        }
        else textBox.style.borderColor = "green"; //Password is long enough

    }
</script>
<body>
<div class="container" onsubmit="validate()" style="width: 80%">
    <h3>*Mandatory</h3>
    <form method="post" action="">
        <?php
        $flag = 0; //checks if $_GET details match accounts or if user is admin
        $SQL = "SELECT IC, Username, Password FROM tbllogin WHERE IC = '".$_GET['Id']."'"; //Gets account details
        $Result = mysqli_query($Link, $SQL);
        if(mysqli_num_rows($Result)) //Record exists
        {
            $RowInfo = mysqli_fetch_array($Result);
            if($RowInfo['Password'] == $_GET['hashed']) //Extra error checking to ensure other staff cant change password
            {
                $flag = 1;
            }
            else if ($_SESSION['AccType'] == "ADMIN")
            {
                $flag = 1;
            }

            if($flag == 1) //$_GET details match an account or user is admin
            {
                ?>
                <table>
                    <caption>Reset Password</caption>
                    <tr>
                        <td style="width: 50%"><label for="CName">Username: </label></td>
                        <td><input type="text" name="CName" id="CName" size="52"
                                   value="<?php echo $RowInfo['Username']; ?>" style="background-color: lightgray"
                                   readonly></td>
                    </tr>
                    <tr>
                        <td colspan="2"><h2 style="color: darkred">Enter the new password</h2></td>
                    </tr>
                    <tr>
                        <td><label for="CPW">*New Password: </label></td>
                        <td><input type="password" name="CPW" ID="CPW" onblur="callfunction(this)" title="Passwords must be at least 8 characters long" required></td>
                    </tr>
                    <tr>
                        <td><label for="RPW">*Reconfirm Password: </label></td>
                        <td><input type="password" name="RPW" id="RPW" onblur="callfunction(this)" title="Passwords must be at least 8 characters long" required></td>
                    </tr>
                </table>
                <br />
                <input type="submit" name="btnSubmit" value="Change Password" class="button" onclick='return confirm("Your password will be changed. Proceed?");'>
                <?php
                $IC = $RowInfo['IC'];
            }
            else//$_GET details do not match any accounts and user is not admin
            {
                echo "You appear to be logged into a different account than the one you logged in with, please log out and try again";
            }

        }
        ?>
    </form>
</div>
</body>
<?php
if($_REQUEST['btnSubmit']) //Submit button is clicked
{
    if($_POST['CPW'] != $_POST['RPW']) echo "<script>alert('Your passwords do not match')</script>"; //Checks if passwords match
    elseif (strlen($_POST['CPW']) < 8) echo "<script>alert('Your password is too short')</script>"; //Checks if password is long enough
    else //All conditions are fulfilled
    {
        $UpSQL = "UPDATE tbllogin SET Password = '".sha1($_POST['CPW'])."' WHERE IC = '".$IC."'"; //Update new password
        $UpSQLResult = mysqli_query($Link, $UpSQL);
        if($UpSQLResult) //Update success
        {
            //Notify user
            ?>
            <script>checkers();</script>
            <?php
        }
    }
}
?>
