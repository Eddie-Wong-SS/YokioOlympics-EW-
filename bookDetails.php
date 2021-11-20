<?php
/**
Allows viewing of the complete details of a booking, as well as additional tasks such as printing or deactivating the booking
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/0.0.11/push.min.js"></script> <!-- push.js.master library for notifications -->
<script>

    Push.Permission.request(); //Browser will request permission to display notifications
    function checkers() //push.js.master notification function
    {
        Push.create('Successfully Deleted!', { //Create notification
            body: 'Deletion of the booking record <?php echo $_POST['bRef']; ?> was successful',
            icon: 'icon.png',
            timeout: 8000,                  // Timeout before notification closes automatically.
            onClick: function() {
                // Callback for when the notification is clicked.
                console.log(this);
            }
        });

    }

    function print() //Prints out the booking details as defined within the specified div
    {
        var prtContent = document.getElementById("Printables"); //Gets div that defines the HTML elements to be printed
        var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0'); //Open in new window
        WinPrint.document.write(prtContent.innerHTML); //Gets HTML content of div
        WinPrint.document.close();
        WinPrint.focus();
        WinPrint.print(); //Enables printing functionality
        WinPrint.close();
    }
</script>
<title>Booking Details</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<?php
if($_REQUEST['btnDel']) //Submit button is clicked
{
    //Update venue record
    //Good practice not to actually delete the record
    $editVenueSQL = "UPDATE tblbooking SET
              Status = 'I'
              WHERE bookRef = '" . strtoupper(trim($_GET['Id'])) . "'
              ";
    $editVenueSQLResult = mysqli_query($Link, $editVenueSQL);
    if($editVenueSQLResult) //Update successful
    {
        //Notify user
        ?>
        <script>checkers();</script>

        <?php
    }
    //Update unsuccessful
    else echo "<script>alert('A problem has occured when deleting this booking, contact an administrator');</script>";
}
if($_GET['Id'] != "") //Checks if $_GET has value
{
    $SQL = "SELECT * FROM tblbooking, tblschedule, tblsports WHERE tblbooking.schedID = tblschedule.schedID 
      AND tblschedule.sportID = tblsports.sportID AND bookRef = '".$_GET['Id']."'"; //Gets corresponding record
    $SQLResult = mysqli_query($Link, $SQL);
    if(mysqli_num_rows($SQLResult) > 0)//Record exists
    {
        $Row = mysqli_fetch_array($SQLResult);//Stores values to populate HTML form
    }
}
else//$_GET has no values
{
    echo"<script>alert(\"An error has occured, please contact an administrator\")";
}
?>

<body>
<div class="container" style="width: 90%">
    <form method="post" action="" enctype="multipart/form-data" name="fForm" id="fForm">
        <div align="center" style="text-align: center" id="Printables">
            <table border="0">
                <caption>Booking Details</caption>
                <tr>
                    <td style="text-align: right"><label for="bRef">Booking Reference: </label></td>
                    <td style="width: 25%"><input type="text" name="bRef" id="bRef" value="<?php echo $Row['bookRef']; ?>" readonly></td>
                </tr>
                <tr>
                    <td style="text-align: right"><label for="Name">Name: </label></td>
                    <td><input type="text" name="Name" id="Name" title="Enter your name" size="52" value="<?php echo $Row['booker']; ?>"readonly> </td>
                </tr>
                <tr>
                    <td style="text-align: right"><label for="Email">Email: </label></td>
                    <td><input type="email" name="Email" id="Email" size="52" value="<?php echo $Row['email']; ?>" readonly></td>
                </tr>
                <tr>
                    <td style="text-align: right"><label for="bookNum">Number of people(1-8): </label></td>
                    <td><input type="text" name="bookNum" id="bookNum" size="1" value="<?php echo $Row['bookNum']; ?>" readonly> </td>
                </tr>
                <tr>
                    <td style="text-align: right"><label for="sName">Sport Name: </label></td>
                    <td style="width: 25%"><input type="text" name="sName" id="sName" value="<?php echo $Row['sportName']; ?>" readonly></td>
                </tr>
                <tr>
                    <td style="text-align: right"><label for="sDescp">Sport Description: </label></td>
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
            <br /><br />
        </div>
        <input type="button" name="Print" id="Print" class="button" style="width: 250px" value="Print Booking" onclick="print()">
        <input type="submit" name="btnDel" class="button site" style="width: 100px" id="btnDel" value="Delete"
               onclick='return confirm("This booking will be deleted. You will not be able to " +
                "participate in the tryouts for this schedule after this. Proceed?");'/>
    </form>
</div>
</body>