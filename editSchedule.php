<?php
/**
Allows for adding of a schedule to an existing sport
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
include ('Email.php'); //Includes emailing functionality
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/0.0.11/push.min.js"></script> <!-- push.js.master library for notifications -->
<script>

    Push.Permission.request(); //Browser will request permission to display notifications
    function checkers() //push.js.master notification function
    {
        Push.create('Successfully Edited!', { //Create notification
            body: 'Modification of the schedule <?php echo $_POST['sName']; ?> into the database was successful',
            icon: 'icon.png',
            timeout: 8000,                  // Timeout before notification closes automatically.
            onClick: function() {
                // Callback for when the notification is clicked.
                console.log(this);
            }
        });

    }

    //Function to add the start and end times for the tryout period
    function tryTime()
    {
        var startTime = document.getElementById("eTime").value; //Gets the event ending time
        var timePart = startTime.split(':'); //Splits startTime to convert into date later
        var curDate = document.getElementById("eDate").value; //Gets the event date
        var datePart = curDate.split('-'); //Splits the date to convert alongside timePart later
        var dateTime = new Date(datePart[0], datePart[1] - 1, datePart[2], timePart[0], timePart[1]); //Combines timepart and datePart into a new date
        dateTime.setHours(dateTime.getHours() + 1); //Adds 1 hour to dateTime to get the end of tryout time(since tryout time lasts 1 hour)

        var getTryHours = dateTime.getHours();
        var getTryMins = (dateTime.getMinutes()<10?'0':'') + dateTime.getMinutes(); //<10?'0':'' is an if else statement, else getMinutes() returns single digits for 0-9 minutes
        var endTime = getTryHours + ":" + getTryMins; //Combines hours and minutes to become a string

        document.getElementById("tsTime").value = startTime.toString();
        document.getElementById("teTime").value = endTime.toString();

    }

    //This function opens existing schedules in a new window/tab to allow the user to check for potential scheduling conflicts
    function openSchedule()
    {
        url = "viewSchedule.php";
        window.open(url); //Opens the listed url in a new window(may be new tab instead depending on browser)
    }
</script>
<?php
if($_REQUEST['btnSub']) //Submit button is clicked
{
    $checkConflictSQL = "SELECT * FROM tblschedule WHERE venueName = '" . $_POST['vName'] . "' AND eventDate = '" . $_POST['eDate'] . "' 
    AND schedID != '" . $_GET['Id'] . "' AND ('" . $_POST['eTime'] . "' > startTime AND '" . $_POST['sTime'] . "' < tryEnd)"; //Checks for conflicting time and location of other schedules

    $checkConflictSQLResult = mysqli_query($Link, $checkConflictSQL);
    if(mysqli_num_rows($checkConflictSQLResult) > 0)//Conflict found
    {
        echo "<script>alert('There is already a conflicting schedule at your chosen time/date/location, please choose again');</script>";
        $reloadURL = "editSchedule.php?Id=" . $_GET['Id']."&sport=". $_GET['sport'];
        ?>
        <script>location = "<?php echo $reloadURL ?>";</script>

        <?php
    }
    else// No conflicts with other schedules found
    {
        if($_POST['eTime'] <= $_POST['sTime']) //End time is set earlier than start time
        {
            echo "<script>alert('Start time cannot be later than end time, please recheck');</script>";
            $reloadURL = "editSchedule.php?Id=" . $_GET['Id']."&sport=". $_GET['sport'];
            ?>
            <script>location = "<?php echo $reloadURL ?>";</script>

            <?php
        }
        //Update schedule
        $editScheduleSQL = "UPDATE tblschedule SET
              venueName = '" . strtoupper(trim($_POST['vName'])) . "',     
              eventDate = '" . strtoupper(trim($_POST['eDate'])) . "',  
              startTime = '" . strtoupper(trim($_POST['sTime'])) . "',  
              endTime = '" . strtoupper(trim($_POST['eTime'])) . "',  
              tryStart = '" . strtoupper(trim($_POST['tsTime'])) . "',
              tryEnd = '" . strtoupper(trim($_POST['teTime'])) . "' 
              WHERE schedID =  '" .$_GET['Id'] . "' AND sportID = '" .$_GET['sport'] . "'
              ";
        $editScheduleSQLResult = mysqli_query($Link, $editScheduleSQL);
        if($editScheduleSQLResult) //Update successful
        {
            //Notify user
            ?>
            <script>checkers();</script>

            <?php
            //Checks if booking for the associated schedule exists
            $CheckBookExists = "SELECT tblbooking.booker, tblbooking.email, tblsports.sportName FROM tblbooking, tblschedule, tblsports
                  WHERE tblbooking.schedID = tblschedule.schedID AND tblschedule.sportID = tblsports.sportID AND tblschedule.schedID = '".$_GET['Id']."'";
            $CheckBookExistsResult = mysqli_query($Link, $CheckBookExists);

            if(mysqli_num_rows($CheckBookExistsResult) > 0)
            {
                for($i = 1; $i <= mysqli_num_rows($CheckBookExistsResult); ++$i)
                {
                    $RowBooks = mysqli_fetch_array($CheckBookExistsResult);
                    $Booker = $RowBooks['booker'];
                    $Sport = $RowBooks['sportName'];

                    //Sends an email to bookers to inform them that the schedule has changed
                    $Inform = "Dear " . $Booker . ", <br/>  Please be advised that the schedule for " . $Sport . " has been changed.
                        Please take a look on our website to view these changes. 
                        We sincerely apologize for any inconvenience caused";
                    sendEmail("Schedule Change", $Inform, $RowBooks['email']);
                }
            }
        }
        else echo "<script>alert('A problem has occured when updating the record, contact an administrator');</script>";
    }

}
else if($_GET['Id'] != "") //Checks if $_GET has value
{
    $GetSchedSQL = "SELECT * FROM tblsports, tblschedule WHERE tblsports.sportID = tblschedule.sportID AND 
        tblschedule.schedID = '" . $_GET['Id'] . "' AND tblsports.sportID = '" . $_GET['sport'] . "'";//Retrieves corresponding schedule
    $GetSchedSQLResult = mysqli_query($Link, $GetSchedSQL);
    if(mysqli_num_rows($GetSchedSQLResult) > 0)//Record found
    {
        $Row = mysqli_fetch_array($GetSchedSQLResult);//Stores values to populate HTML form
    }
}
?>
<title>Edit Sport Schedule</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<body>
<div class="container" style="width: 90%">
    <?php if($_SESSION['AccType'] == "ADMIN" || $_SESSION['AccType'] == "STAFF")
    {
        ?>
        <form method="post" action="" enctype="multipart/form-data" name="fForm" id="fForm" >
            <h1>Edit Sports Schedule</h1>
            <h3>*Mandatory</h3>
            <div align="center">
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
                        <td><select name="vName" id="vName">
                                <option value="<?php echo $Row['venueName']; ?>" selected="selected"><?php echo $Row['venueName']; ?></option>
                                <option value="Aquatics Palace">Aquatics Palace</option>
                                <option value="FunOlympic Village">FunOlympic Village</option>
                                <option value="Stadium of Delight">Stadium of Delight</option>
                            </select></td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><label for="eDate">*Event Date: </label> </td>
                        <td><input type="date" name="eDate" id="eDate" value="<?php echo $Row['eventDate']; ?>" min="2020-05-11" max="2020-05-15" title="List dates from 11th of May 2020 to 15th of may 2020 ONLY" required></td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><label for="sTime">*Start Time: </label> </td>
                        <td><input type="time" name="sTime" id="sTime" value="<?php echo $Row['startTime']; ?>" min="10:00" max="20:00" title="List only time between 10 a.m. to 8 p.m." required> </td>
                    </tr>
                    <tr>
                        <td style="text-align: right"><label for="eTime">*End Time: </label> </td>
                        <td><input type="time" name="eTime" id="eTime" value="<?php echo $Row['endTime']; ?>" min="11:00" max="21:00" title="List only time between 11 a.m. to 9 p.m." onblur="tryTime()" required></td>
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
                <!-- Opens a new window/tab that shows existing schedules to help determine potential conflicts-->
                <input type="button" name="btnOpen" class="button site" style="width: 250px" id="btnOpen" value="Check Existing Schedules" onclick="openSchedule()"/>
                <br /><br />
                <input type="submit" name="btnSub" class="button site" style="width: 200px" id="btnSub" value="Add Schedule"/>
            </div>
        </form>
    <?php }
    ?>
</div>
</body>
