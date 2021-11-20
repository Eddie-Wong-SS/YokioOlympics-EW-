<?php
//Headers to enable CORS for http://ip-api.com/json
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Origin: http://ip-api.com/json");
header('Access-Control-Allow-Credentials: true');

/**
Allows bookings for tryout sessions for the selected schedule by locals
 */
error_reporting(1); //Hide error information from end users
session_start();

include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
include ('Email.php'); //Includes emailing functionality

//Generates a random string of 4 chars for use in making a booking reference
function generateRandomString($length = 4)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++)
    {
        $randomString .= $characters[rand(0, $charactersLength - 1)]; //Chooses a random character from the $characters string to add
    }
    return $randomString;
}
?>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/0.0.11/push.min.js"></script> <!-- push.js.master library for notifications -->
<script src="https://code.jquery.com/jquery-3.5.0.js"></script> <!-- jquery library used for ip address lookup -->
<script>
    var flag = false; //Used to check if the visitor is a local

    Push.Permission.request(); //Browser will request permission to display notifications
    function checkers() //push.js.master notification function
    {
        Push.create('Successfully Booked!', { //Create notification
            body: 'Booking of tickets for was successful, please check your email',
            icon: 'icon.png',
            timeout: 8000,                  // Timeout before notification closes automatically.
            onClick: function() {
                // Callback for when the notification is clicked.
                console.log(this);
            }
        });

    }

    //Function to look up ip addresses and obtain user's country from the ip
    function ipLookUp ()
    {
        $.ajax('http://ip-api.com/json') //Connects to API to obtain user location
            .then(
                function success(response) //Connected and response from the API
                {

                    var dec = document.getElementById("Declare");
                    var nm = document.getElementById("Name");
                    var em = document.getElementById("Email");
                    var bNum = document.getElementById("bookNum");
                    var Loc = document.getElementById("Local");
                    var sub = document.getElementById("Submit");

                    if(response.country === "United Kingdom") //For testing purposes locality is set to the UK, not Yokio
                    {
                        flag = true;
                        dec.value = "Eligible";
                        //Sets below fields to required fields and removes readonly attribute as user is a local
                        nm.readOnly = false;
                        nm.required = true;
                        em.readOnly = false;
                        em.required = true;
                        bNum.readOnly = false;
                        bNum.required = true;
                        //Sets button to enabled
                        sub.disabled = false;
                        //Sets local flag in HTML for php to read
                        Loc.value = flag;
                    }
                    else //Not a local
                    {
                        flag = false;
                        dec.value = "Ineligible";
                        Loc.value = flag;
                    }
                    
                },

                function fail(data, status) //Failed to connect to the API
                {
                    console.log('Request failed.  Returned status of',
                        status); //Error log(Change as required)
                }
            );
    }

</script>
<?php
if($_GET['Id'] != "") //Checks if $_GET has values
{
    $GetSchedSQL = "SELECT * FROM tblsports, tblschedule WHERE tblsports.sportID = tblschedule.sportID AND 
        tblschedule.schedID = '" . $_GET['Id'] . "'";//Retrieves corresponding schedule
    $GetSchedSQLResult = mysqli_query($Link, $GetSchedSQL);
    if(mysqli_num_rows($GetSchedSQLResult) > 0)//Record found
    {
        $Row = mysqli_fetch_array($GetSchedSQLResult);//Stores values to populate HTML form
    }
}

if($_REQUEST['Submit']) //User submitted the booking
{
    if(($_POST['Local'] == true && $_POST['Declare'] == "Eligible") || $_SESSION['Login'] == true) //Checks if user is a local, or admin/staff
    {
        $CheckExistSQL = "SELECT bookID from tblbooking WHERE schedID = '" .$_GET['Id'] . "' AND email = '" . strtoupper(trim($_POST['Email'])); //Checks if the email has done bookings before
        $CheckExistSQLResult = mysqli_query($Link, $CheckExistSQL);
        if(mysqli_num_rows($CheckExistSQLResult) > 0) //The email has been used for booking before
        {
            echo "<script>alert('Sorry, but this email has already been used for booking');</script>";
        }
        else //The email has not been used to book before
        {
            //Generates a unique booking reference
            $Rand1 = generateRandomString();
            $Rand2 = generateRandomString();
            $Rand3 = generateRandomString();
            $BookRef = "YOLY-".$Rand1."-".$Rand2."-".$Rand3;
            //Combines event date and tryout end time to get booking valid duration
            $date = $_POST['eDate'];
            $time = $_POST['teTime'];
            $Valid = date('Y-m-d H:i:s', strtotime("$date $time"));
            //Insert new booking
            $NewBookSQL = "INSERT INTO tblbooking(bookRef,schedID, booker, email, bookNum, validTill) VALUES ( 
                  '".$BookRef."',
                  '" .$_GET['Id'] . "',
                  '" . strtoupper(trim($_POST['Name'])) . "',     
                  '" . strtoupper(trim($_POST['Email'])) . "',  
                  '" . strtoupper(trim($_POST['bookNum'])) . "',  
                  '" . $Valid . "'
                  )";
            $NewBookSQLResult = mysqli_query($Link, $NewBookSQL);
            if($NewBookSQL) //Booking successful
            {
                $Name = $_POST['Name'];
                $Num = $_POST['bookNum'];
                $Sport = $_POST['sName'];
                $Venue = $_POST['vName'];
                $Date = $_POST['eDate'];
                $Start = $_POST['tsTime'];
                $End = $_POST['teTime'];
                //Sends the user an email with booking details
                $Inform = "Dear ".$Name.", <br/> We are pleased to inform you that your booking for ".$Sport." for ".$Num." people has been confirmed.
                Enclosed below are the details of your booking. This email is sufficient proof of booking for verification on the scheduled day. 
                Thank you for your interest. <br/> <h3 style='align-content: center'><strong>Booking Details</strong></h3> <br />
                Reference Code: ".$BookRef."<br/>
                Name:             ".$Name."<br/>
                Sport:            ".$Sport." <br/>
                Venue:            ".$Venue."<br/>
                Event Date:       ".$Date."<br/>
                Time:             ".$Start." to ".$End."<br/>
                Number Booked:    ".$Num;
                sendEmail("Booking", $Inform, $_POST['Email']);
                //Notify user of booking success
                ?>
                <script>checkers();</script>

                <?php
            }
        }
    }
    else //User is not a local or admin/staff
    {
        echo "<script>alert('Sorry, only locals may book to try out the sports');</script>";
    }
}
?>
<title>Add Sport Schedule</title>
<?php if($_SESSION['Login'] == true)  {?><body>
<?php } else { ?> <body onload="ipLookUp()"> <?php }  //ipLookup is called only for visitors and not admin/staff?>
<div class="container" style="width: 80%">
    <form method="post" action="" enctype="multipart/form-data" name="fForm" id="fForm" >
        <h1>Book To Try Out The Sport!</h1>
        <h3>Warning: Booking is allowed to locals ONLY, international visitors cannot book tryouts</h3>
        <h3>*Mandatory</h3>
        <table border="0">
            <caption>Sport Schedule</caption>
            <tr>
                <td class="move" style="width: 25%"><label for="sName">Sport Name: </label></td>
                <td style="width: 25%"><input type="text" name="sName" id="sName" value="<?php echo $Row['sportName']; ?>" readonly></td>
            </tr>
            <tr>
                <td class="move" style="table-layout: fixed"><label for="sDescp">Sport Description: </label></td>
                <td><textarea name="sDescp" id="sDescp" rows="5" cols="75" maxlength="1000" readonly><?php echo $Row['sportDescp']; ?></textarea> </td>
            </tr>
            <tr>
                <td style="text-align: right"><label for="vName">Venue Name: </label></td>
                <td><input type="text" name="vName" id="vName" value="<?php echo $Row['venueName']; ?>" readonly></td>
            </tr>
            <tr>
                <td style="text-align: right"><label for="eDate">Event Date: </label> </td>
                <td><input type="text" name="eDate" id="eDate" value="<?php echo $Row['eventDate']; ?>" readonly></td>
            </tr>
            <tr>
                <td style="text-align: right"><label for="sTime">Start Time: </label> </td>
                <td><input type="text" name="sTime" id="sTime" value="<?php echo $Row['startTime']; ?>" readonly> </td>
            </tr>
            <tr>
                <td style="text-align: right"><label for="eTime">End Time: </label> </td>
                <td><input type="text" name="eTime" id="eTime" value="<?php echo $Row['endTime']; ?>" readonly></td>
            </tr>
            <tr>
                <td style="text-align: right"><label for="tDur">Tryout Duration(Local visitors only): </label> </td>
                <td><input type="text" name="tDur" id="tDur" value="1 hour" readonly> </td>
            </tr>
            <tr>
                <td style="text-align: right"><label for="tsTime">Tryout Start Time: </label> </td>
                <td><input type="text" name="tsTime" id="tsTime" value="<?php echo $Row['tryStart']; ?>" readonly> </td>
            </tr>
            <tr>
                <td style="text-align: right"><label for="teTime">Tryout End Time: </label> </td>
                <td><input type="text" name="teTime" id="teTime" value="<?php echo $Row['tryEnd']; ?>" readonly></td>
            </tr>
        </table>
        <br />
        <input type="hidden" name="Local" id="Local" value="">
        <table border="0">
            <caption>Book Tryouts</caption>
        <?php if($_SESSION['Login'] == true) //Admin/Staff are using
            { ?>
                <tr>
                    <td class="move"><label for="Name">Name: </label></td>
                    <td><input type="text" name="Name" id="Name" title="Enter your name" size="52" maxlength="50" required> </td>
                </tr>
                <tr>
                    <td class="move"><label for="Email">Email: </label></td>
                    <td><input type="email" name="Email" id="Email" title="Enter your email to register your booking" required></td>
                </tr>
                <tr>
                    <td class="move"><label for="bookNum">Number of people(1-8): </label></td>
                    <td><input type="number" name="bookNum" id="bookNum" size="1" title="Only a maximum of 8 are allowed" min="1" max="8" required> </td>
                </tr>
           <?php
            }
            else //Visitor using
            {?>
                <tr>
                    <td class="move"><label for="Declare">Eligible Booker?</label></td>
                    <td><input type="text" name="Declare" id="Declare" style="width: fit-content"
                        title="If this field is blank, please disable adblock for this page and enable Javascript, else booking will NOT succeed" readonly/> </td>
                </tr>
                <tr>
                    <td class="move"><label for="Name">Name: </label></td>
                    <td><input type="text" name="Name" id="Name" title="Enter your name" size="52" maxlength="50" readonly> </td>
                </tr>
                <tr>
                    <td class="move"><label for="Email">Email: </label></td>
                    <td><input type="email" name="Email" id="Email" title="Enter your email to register your booking" readonly></td>
                </tr>
                <tr>
                    <td class="move"><label for="bookNum">Number of people(1-8): </label></td>
                    <td><input type="number" name="bookNum" id="bookNum" size="1" title="Only a maximum of 8 are allowed" min="1" max="8" readonly> </td>
                </tr>
            <?php } ?>
        </table>
        <br/>
        <?php if($_SESSION['Login'] == true) //Admin/Staff are using
        { ?>
            <input type="Submit" class="button" name="Submit" id="Submit" value="Book">
            <?php
        }
        else //Visitor using
        {?>
            <input type="Submit" class="button" name="Submit" id="Submit" value="Book" disabled>
        <?php } ?>
    </form>
</div>
</body>
