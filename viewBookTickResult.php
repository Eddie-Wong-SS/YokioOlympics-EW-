<?php
/**
Shows the results of bookings and seats for each sport as a report
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<title>News Records</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<script type="text/javascript">
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
<body>
<div class="container" align="center">
    <?php
    $SQL = $_SESSION['SQL']; //Gets the SQL query from viewBookTick.php
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
                    echo "<button><a style='font-size: 21px'  href='viewBookTickResult.php?page=".($page-1)."'>&#60;</a> </button>";
                if($page <= $maxPage && $page != 1) //Checks if 'forward' button should be shown
                    echo "<button><a style='font-size: 21px'  href='viewBookTickResult.php?page=".($page+1)."'>&#62;</a> </button>"?>
                <div id='Printables'>
                    <h1>Total Bookings and Seats Per Schedule Report</h1>
                    <table align="center">
                        <tr>
                            <th style="background-color: blue; color: white" colspan='100%'>Showing <?php echo $minLim ." to ". $maxLim ." of ". $maxRec; ?> Results</th>
                        </tr>
                        <tr style="color: black">
                            <th scope="col">No</th>
                            <th scope="col">Sport Name</th>
                            <th scope="col">Venue</th>
                            <th scope="col">Event Time(inc. Tryouts)</th>
                            <th scope="col">Bookings</th>
                            <th scope="col">Total Seats</th>
                        </tr>
                        <?php //Creates row for each record
                        for($i = $minLim ; $i <= $maxLim; ++$i)
                        {
                            $RowInfo = mysqli_fetch_array($Result);
                            $ReportSQL = "SELECT COUNT(bookRef) AS totalBooks, SUM(bookNum) AS totalSeats,
                              tblschedule.*, tblsports.* FROM tblbooking, tblschedule, tblsports 
                              WHERE tblbooking.schedID = tblschedule.schedID 
                              AND tblschedule.sportID = tblsports.sportID AND tblbooking.Status != 'I'
                              AND sportName = '".$RowInfo['sportName']."'"; //Gets the total number of bookings and seats booked per schedule
                            $ReportSQLResult = mysqli_query($Link, $ReportSQL);
                            if(mysqli_num_rows($ReportSQLResult) > 0)
                            {
                                $Rows = mysqli_fetch_array($ReportSQLResult);
                                if($Rows['sportName'] != "")
                                {
                                    echo "<tr>";
                                    echo "<td>".($i)."</td>";
                                    echo "<td style='text-align: center'>".$Rows['sportName']."</td>";
                                    echo "<td style='text-align: center'>".$Rows['venueName']."</td>";
                                    echo "<td style='text-align: center'>".$Rows['eventDate']." "
                                        .$Rows['startTime']."-".$Rows['tryEnd']."</td>";
                                    echo "<td style='text-align: center'>".$Rows['totalBooks']."</td>";
                                    echo "<td style='text-align: center'>".$Rows['totalSeats']."</td>";
                                    echo "</tr>";
                                }
                            }
                        }
                        ?>
                    </table>
                </div>
                <table>
                    <tr>
                        <td align="center">
                            <input type="button" name="btnPrint" id="btnPrint" value="Print Report"
                                   onclick="print()" class="button">
                        </td>
                    </tr>
                </table>
            </form>
        <?php }
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
