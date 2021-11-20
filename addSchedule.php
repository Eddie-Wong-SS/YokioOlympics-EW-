<?php
/**
Allows for adding of a schedule to an existing sport
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
        Push.create('Successfully Added!', { //Create notification
            body: 'Adding of schedule for sport <?php echo $_POST['sName']; ?> into the database was successful',
            icon: 'icon.png',
            timeout: 8000,                  // Timeout before notification closes automatically.
            onClick: function() {
                // Callback for when the notification is clicked.
                console.log(this);
            }
        });

    }

    //Function to enable start and end times after the event date has been filled, and makes them required completion
    function enableTime()
    {
        document.getElementById("sTime").readOnly = false;
        document.getElementById("sTime").required = true;
        document.getElementById("eTime").readOnly = false;
        document.getElementById("eTime").required = true;
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
if($_REQUEST['btnSub']) //Submit button has been clicked
{
    $checkSportSQL = "SELECT sportID from tblschedule WHERE sportID = '" . $_GET['Id'] . "'"; //Checks if the sport already has a schedule
    $checkSportSQLResult = mysqli_query($Link, $checkSportSQL);

    if(mysqli_num_rows($checkSportSQLResult) > 0)//Conflicting schedule found
    {
        echo "<script>alert('This sport already has a schedule!');</script>";
        $reloadURL = "addSchedule.php?Id=" . $_GET['Id'];
        ?>
        <script>location = "<?php echo $reloadURL ?>";</script>

        <?php
    }
    else//No conflicts found
    {
        $checkConflictSQL = "SELECT * FROM tblschedule WHERE venueName = '" . $_POST['vName'] . "' AND eventDate = '" . $_POST['eDate'] . "' 
        AND ('" . $_POST['eTime'] . "' > startTime AND '" . $_POST['sTime'] . "' < tryEnd)"; //Checks if another schedule with the same location and conflicting times exists

        $checkConflictSQLResult = mysqli_query($Link, $checkConflictSQL);
        if(mysqli_num_rows($checkConflictSQLResult) > 0) //Conflicting schedule found
        {
            echo "<script>alert('There is already a conflicting schedule at your chosen time/date/location, please choose again');</script>";
            $reloadURL = "addSchedule.php?Id=" . $_GET['Id'];
            ?>
            <script>location = "<?php echo $reloadURL ?>";</script>

            <?php
        }
        else //No conflicts location found
        {
            if($_POST['eTime'] <= $_POST['sTime']) // End time is earlier than start time
            {
                echo "<script>alert('End time cannot be before start time');</script>";
                $reloadURL = "addSchedule.php?Id=" . $_GET['Id'];
                ?>
                <script>location = "<?php echo $reloadURL ?>";</script>

                <?php
            }
            else //No conflicts in time or location found
            {
                //Add schedule
                $addScheduleSQL = "INSERT INTO tblschedule(sportID, venueName, eventDate, startTime, endTime, tryStart, tryEnd) VALUES (
                  '" .$_GET['Id'] . "',
                  '" . strtoupper(trim($_POST['vName'])) . "',     
                  '" . strtoupper(trim($_POST['eDate'])) . "',  
                  '" . strtoupper(trim($_POST['sTime'])) . "',  
                  '" . strtoupper(trim($_POST['eTime'])) . "',  
                  '" . strtoupper(trim($_POST['tsTime'])) . "',
                  '" . strtoupper(trim($_POST['teTime'])) . "'  
                  )";
                $addScheduleSQLResult = mysqli_query($Link, $addScheduleSQL);
                if($addScheduleSQLResult) //Adding successful
                {
                    //Notify user
                    ?>
                    <script>checkers();</script>

                    <?php
                }
                else echo "<script>alert('A problem has occured when adding the record, contact an administrator');</script>"; //Failed to add schedule
            }
        }
    }
}
else if($_GET['Id'] != "") //Checks if $_GET has values
{
    $GetSportSQL = "SELECT * FROM tblsports WHERE sportID = '" . $_GET['Id'] . "'"; //Gets corresponding sport
    $GetSportSQLResult = mysqli_query($Link, $GetSportSQL);
    if(mysqli_num_rows($GetSportSQLResult) > 0)//Record found
    {
        $Row = mysqli_fetch_array($GetSportSQLResult); //Stores values to populate HTMl forms
    }
}
?>
<title>Add Sport Schedule</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<body>
<div class="container" style="width: 90%">
<?php if($_SESSION['AccType'] == "ADMIN" || $_SESSION['AccType'] == "STAFF")
{
?>
    <form method="post" action="" enctype="multipart/form-data" name="fForm" id="fForm" >
        <h1>Add Sports Schedule</h1>
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
                            <option value="Aquatics Palace" selected="selected">Aquatics Palace</option>
                            <option value="FunOlympic Village">FunOlympic Village</option>
                            <option value="Stadium of Delight">Stadium of Delight</option>
                        </select></td>
                </tr>
                <tr>
                    <td style="text-align: right"><label for="eDate">*Event Date: </label> </td>
                    <td><input type="date" name="eDate" id="eDate" min="2020-05-11" max="2020-05-15" title="List dates from 11th of May 2020 to 15th of may 2020 ONLY" onchange="enableTime()" required></td>
                </tr>
                <tr>
                    <td style="text-align: right"><label for="sTime">*Start Time: </label> </td>
                    <td><input type="time" name="sTime" id="sTime" min="10:00" max="20:00" title="List only time between 10 a.m. to 8 p.m." readonly> </td>
                </tr>
                <tr>
                    <td style="text-align: right"><label for="eTime">*End Time: </label> </td>
                    <td><input type="time" name="eTime" id="eTime" min="11:00" max="21:00" title="List only time between 11 a.m. to 9 p.m." onblur="tryTime()" readonly></td>
                </tr>
                <tr>
                    <td style="text-align: right"><label for="tDur">Tryout Duration(Local visitors only): </label> </td>
                    <td><input type="text" name="tDur" id="tDur" value="1 hour" readonly> </td>
                </tr>
                <tr>
                    <td style="text-align: right"><label for="tsTime">Tryout Start Time: </label> </td>
                    <td><input type="text" name="tsTime" id="tsTime" value="" readonly> </td>
                </tr>
                <tr>
                    <td style="text-align: right"><label for="teTime">Tryout End Time: </label> </td>
                    <td><input type="text" name="teTime" id="teTime" value="" readonly></td>
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
