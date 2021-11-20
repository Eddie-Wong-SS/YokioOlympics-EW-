<?php
/**
Shows the results of schedule search
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
include ('Email.php'); //Includes emailing functionality
?>
<script language="javascript">
    //Used for checking or unchecking all records
    function toggle(MaxCheck)
    {
        var i = 1;
        if(document.getElementById('chkAll').checked === true)
        {
            for( i = 1; i <= MaxCheck; i++)
            {
                document.getElementById('Rec' + i).checked = true;
            }
        }

        if(document.getElementById('chkAll').checked === false)
        {
            for( i = 1; i <= MaxCheck; i++)
            {
                document.getElementById('Rec' + i).checked = false;
            }
        }
    }

</script>
<title>Schedule Records</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container" align="center">
    <?php
    //Sets schedule records to 'I' for inactive - good practice not to actually delete records, but ensure they cant be viewed nonetheless
    if($_REQUEST['btnDelete'])
    {
        // while(list($key,$val) = each($_POST)) 'each' function is deprecated, kept for posterity
        foreach($_POST as $key => $val)
        {
            if($key != "chkAll" && $key != "btnDelete") //Error checking to ensure stated keys are not included for deletion
            {
                $DelEmpSQL = "UPDATE tblschedule SET Status = 'I' WHERE schedID = '".$key."'"; //Sets staff record to be inactive
                $DelEmpResult = mysqli_query($Link, $DelEmpSQL);

                //Checks if booking for the associated schedule exists
                $CheckBookExists = "SELECT tblbooking.booker, tblbooking.email, tblsports.sportName FROM tblbooking, tblschedule, tblsports
                  WHERE tblbooking.schedID = tblschedule.schedID AND tblschedule.sportID = tblsports.sportID AND tblschedule.sportID = '".$key."'";
                $CheckBookExistsResult = mysqli_query($Link, $CheckBookExists);

                if(mysqli_num_rows($CheckBookExistsResult) > 0)
                {
                    //Sets bookings for the schedule to inactive
                    $DelBookSQL = "UPDATE tblbooking JOIN tblschedule ON tblbooking.schedID = tblschedule.schedID SET Status = 'I' WHERE tblschedule.sportID = '" . $key . "'";
                    $DelBookSQLResult = mysqli_query($Link, $DelBookSQL);

                    $RowBooks = mysqli_fetch_array($CheckBookExistsResult);
                    $Booker = $RowBooks['booker'];
                    $Sport = $RowBooks['sportName'];

                    //Sends an email to bookers to inform them of the booking's cancellation.
                    $Inform = "Dear " . $Booker . ", <br/>  It is with utmost regret that we inform you that the schedule for " . $Sport . " has been taken down from the Yokio Olympics.
                     As such,all bookings for this schedule has been cleared, including yours. 
                     We sincerely apologize for any inconvenience caused";
                    sendEmail("Booking Cancellation", $Inform, $RowBooks['email']);

                }

                if($DelEmpResult)
                {
                    echo "<script>alert('Selected record(s) has been deleted');location='viewScheduleResult.php?page=1';</script>"; //Reloads the webpage after accounts have been deactivated
                }
            }

        }
    }
    else
    {
        $SQL = $_SESSION['SQL']; //Gets the SQL query from viewSchedule.php
        $Result = mysqli_query($Link, $SQL);
        if($Result)
        {
            if(mysqli_num_rows($Result) > 0)
            {
                $page = $_GET['page']; //Gets the current 'page' the webpage is on

                //Determines the number of rows per page, and allows for paging functionality(Currently 25 records per page)
                $maxRec = mysqli_num_rows($Result);
                $maxPage = ($maxRec / 25) + 1;
                settype($maxPage, "integer");//Avoids type conflicts
                $maxLim = $page * 25;
                $minLim = $maxLim - 24;

                if($maxLim > $maxRec) $maxLim = $maxRec; //Ensures proper formatting if page has less than 25 records
                ?>
                <form method="post" action="">
                    <?php if($maxPage > 1 && $page != $maxPage) //Checks if 'back' button should be shown
                        echo "<button><a style='font-size: 21px'  href='viewScheduleResult.php?page=".($page-1)."'>&#60;</a> </button>";
                    if($page <= $maxPage && $page != 1) //Checks if 'forward' button should be shown
                        echo "<button><a style='font-size: 21px'  href='viewScheduleResult.php?page=".($page+1)."'>&#62;</a> </button>"?>
                    <table align="center">
                        <tr>
                            <th style="background-color: blue; color: white" colspan='100%'>Showing <?php echo $minLim ." to ". $maxLim ." of ". $maxRec; ?> Results</th>
                        </tr>
                        <tr style="color: black">
                            <th scope="col">No</th>
                            <?php $count = mysqli_num_rows($Result);
                            if($_SESSION['AccType'] == "ADMIN" || $_SESSION['AccType'] == "STAFF")
                            echo "<th scope='col'><input type='checkbox' name=\"chkAll\" id=\"chkAll\" onClick=\"toggle($count)\"></th>"; //Creates the checkall checkbox ?>
                            <th scope="col">Sport Name</th>
                            <th scope="col">Venue</th>
                            <th scope="col">Event Date</th>
                            <th scope="col">Time</th>
                            <th scope="col">Tryouts Time</th>
							<th colspan="100%">Actions</th>
                        </tr>
                        <?php //Creates row for each record
                        for($i = $minLim ; $i <= $maxLim; ++$i)
                        {
                            $RowInfo = mysqli_fetch_array($Result);
                            echo "<tr>";
                            echo "<td>".($i)."</td>";
                            if($_SESSION['AccType'] == "ADMIN" || $_SESSION['AccType'] == "STAFF")
                            echo "<td style='text-align: center'><input type=\"checkbox\" name=\"".$RowInfo['schedID']."\" id=\"Rec".($i)."\"></td>";
                            echo "<td>".$RowInfo['sportName']."</td>";
                            echo "<td style='text-align: center'>".$RowInfo['venueName']."</td>";
                            echo "<td style='text-align: center'>".$RowInfo['eventDate']."</td>";
                            echo "<td style='text-align: center'>".$RowInfo['startTime']."-".$RowInfo['endTime']."</td>";
                            echo "<td style='text-align: center'>".$RowInfo['tryStart']."-".$RowInfo['tryEnd']."</td>";
                            if($_SESSION['AccType'] == "ADMIN" || $_SESSION['AccType'] == "STAFF")
                            {
                                echo "<td><a class='hoverme' href=\"editSchedule.php?Id=" . $RowInfo['schedID'] . "&sport=" . $RowInfo['sportID'] . "\">Edit</a></td>";
                            }
                            echo "<td><a class='hoverme' title='Book tickets to try out the sport after it is ran(Locals ONLY)' href=\"addBooking.php?Id=" . $RowInfo['schedID'] . "\">Book Tryouts</a></td>";
                            echo "</tr>";
                        }
                        if($_SESSION['AccType'] == "ADMIN" || $_SESSION['AccType'] == "STAFF")
                        {
                            echo "<tr>";
                            echo "<td></td>";
                            echo "<th style='background-color: initial'></td>";
                            echo "<td align=\"center\" colspan=\"100%\"><input type=\"submit\" name=\"btnDelete\" 
                            value=\"Delete checked records\" onclick='return confirm(\"This will delete the selected records. Proceed?\");'
                             id=\"Delete\" class='button'></td>"; //Deletes all checked records
                            echo "</tr>";
                        }
                        ?>
                    </table>
                </form>
            <?php }
        }
    }
    ?>
</div>
</body>
<script type="text/javascript">
    //Function to highlight rows when a link is hovered over for visual clarity
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
