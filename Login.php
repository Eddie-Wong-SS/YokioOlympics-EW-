<?php
/** Allows users to login or head to registration*/
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<title>Login</title>
<link rel="stylesheet" type = "text/css" href="Menubar.css" />
<?php
if($_SESSION['Login'] == true) //Sends user back if they have already logged in
{
    $previous = "javascript:history.go(-1)"; //Gets previous page URL
    if(isset($_SERVER['HTTP_REFERER'])) {
        $previous = $_SERVER['HTTP_REFERER'];
    }
    echo "<script>alert('You are already logged in, please log out to access this page again');</script>";
    echo "<script>location = '". $previous ."'; </script>"; //Returns user back to previous page
}
?>
<body>
<?php
if($_REQUEST['btnLogin']) //Login button is clicked
{
    if($_POST['txtUsername'] == "" || $_POST['txtPassword'] == "") //Checks if all fields are filled
    {
        echo "<script>alert('You have not entered a username or password!')</script>";
        echo "<script>location='Login.php'</script>";
    }
    else //Fields are all filled
    {
        //Checks if corresponding record exists and has been activated
        $SQL = "SELECT * FROM tbllogin WHERE Username = '" . strtoupper(trim($_POST['txtUsername'])) . "' AND Password = '" . sha1(trim($_POST['txtPassword'])) . "' AND Status = 'A'";
        $Result = mysqli_query($Link, $SQL);
        if (mysqli_num_rows($Result) > 0) //Record found
        {
            $Row = mysqli_fetch_array($Result);

            //Stores various account details for use throughout the system
            $_SESSION['Username'] = $Row['Username'];
            $_SESSION['Id'] = $Row['userId'];
            $_SESSION['IC'] = $Row['IC'];
            $_SESSION['AccType'] = $Row['AccType'];
            $_SESSION['Login'] = true;

            if ($_SESSION['AccType'] == "ADMIN") //Custom admin details to be stored
            {
                $_SESSION['log'] = 'a';
                $_SESSION['code'] = ";";
            }
            else if ($_SESSION['AccType'] == "STAFF") //custom staff details to be stored
            {
                $_SESSION['log'] = 's';
                $_SESSION['code'] = $Row['Password'];
            }

            $GetBookSQL = "SELECT * FROM tblbooking WHERE validTill > $Etime AND Status = 'A'"; //Gets bookings that are past their events
            $GetBookSQLResult = mysqli_query($Link, $GetBookSQL);
            if(mysqli_num_rows($GetBookSQLResult) > 0)
            {
                for($i = 1; $i <= mysqli_num_rows($GetBookSQLResult); ++$i) //Sets status for each booking record
                {
                    $Row = mysqli_fetch_array($GetBookSQLResult);
                    $UpdateBookSQL = "UPDATE tblbooking SET Status = 'I' WHERE bookID = '" . $Row['bookId'] . "'"; //Deactivates expired bookings
                }
            }

            echo "<script>location = 'Main Page.php'; </script>";
        }
        else //No active record found
        {
            //Checks if corresponding account exists but was not activated
            $SQLN = "SELECT * FROM tbllogin WHERE Username = '" . strtoupper(trim($_POST['txtUsername'])) . "' AND Password = '" . trim($_POST['txtPassword']) . "' AND Status = 'N'";
            $ResultN = mysqli_query($Link, $SQLN);
            if (mysqli_num_rows($ResultN) > 0)
            {
                echo "<script>alert('You have not activated your account yet'); </script>";
                echo "<script>location = 'verifyAccount.php';</script>"; //Brings user to account activation
            }
            else //Corresponding account simply does not exist for the inputted username/password
            {
                echo "<script>alert('Invalid Username or Password'); </script>";
                echo "<script>location = 'Login.php'; </script>";
            }
        }
    }
}
?>
<link rel="stylesheet" href="Default%20Theme.css">
<div class="container" style="width: 35%; height: 85%">
    <form id="form1" name="form1" method="post" action="">
        <div align="center"><table width="30%" border="0">
                <caption>Log In</caption>
                <tr>
                </tr>
                <tr style="background-color: inherit">
                    <td align="center"><label for="txtUsername">*Username: </label><input name="txtUsername" id="txtUsername" type="text" value="" required/></td>
                </tr>
                <tr>
                    <td align="center"><label for="txtPassword">*Password: </label><input name="txtPassword" id="txtPassword" type="password" value="" required/></td>
                </tr>
                <tr style="background-color: inherit">
                    <td><br/><div align="center">
                            <input name="btnLogin" class = "button"  type="submit"  id="btnLogin" value="Log in" /></div></td>
                </tr>
                <tr style="background-color: inherit">
                    <td><br/><div align="center">
                            <a href="Verify.php">New account? Verify here</a></div></td>
                </tr>
            </table>
        </div>
    </form>
</div>
</body>
<script type="text/javascript">
    //Function to highlight rows whose links were hovered over
    var allLinks = document.getElementsByTagName('a');
    for(var i=0; i < allLinks.length; ++i) {
        if(allLinks[i].getAttribute('class') === "hoverme") {
            allLinks[i].onmouseover = function () {
                this.parentNode.parentNode.style.background = 'linear-gradient(#ADD8E6,#4169E1)';
                this.style.color = 'red';
            };
            allLinks[i].onmouseout = function () {
                this.parentNode.parentNode.style.background= '';
                this.style.color = 'blue';
            };
        }
    }
</script>