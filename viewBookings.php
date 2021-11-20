<?php
/**
Allows the viewing of all bookings and their associated sports
 */
error_reporting(E_COMPILE_ERROR);
session_start();
include("database.php");
include("Menu.php");
?>
<title>Filter Bookings</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container">
    <?php if($_SESSION['AccType'] == "ADMIN" || $_SESSION['AccType'] == "STAFF") //Checks if user is admin/staff
    {
        ?>
        <h1>View Bookings</h1>
        <h3>Use the fields to filter specific results</h3>

        <form method="post" action="">
            <div align="center">
                <table border="0" style="table-layout: fixed">
                    <caption>Search Filter</caption>
                    <tr>
                        <td><label for="sName">Sport Name: </label></td>
                        <td><input type="text" name="sName" id="sName"></td>
                    </tr>
                    <tr>
                        <td><label for="eDate">Date: </label></td>
                        <td><input type="date" name="eDate" id="eDate" min="2020-05-11" max="2020-05-15" title="YokioOlympics lasts from 11th of May to 15th of May 2020" /></td>
                    </tr>
                    <tr>
                        <td><label for="bRef">Booking Reference: </label></td>
                        <td><input type="text" name="bRef" id="bRef" maxlength="19" size="21"></td>
                    </tr>
                    <tr>
                        <td><label for="bName">Booker Name: </label></td>
                        <td><input type="text" name="bName" id="bName" ></td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center"><input type="submit" name="btnSearch" class="button"></td>
                    </tr>
                </table>
            </div>
        </form>
        <?php
    }

    if($_REQUEST['btnSearch'])
    {

        $SQL = "SELECT * FROM tblbooking, tblschedule, tblsports WHERE tblbooking.schedID = tblschedule.schedID 
        AND tblschedule.sportID = tblsports.sportID ";

        //Conditionals to filter records based on any filters used above
        if(trim($_POST['sName'])) $SQL .= "AND sportName LIKE '%".trim($_POST['sName'])."%' ";
        if(trim($_POST['eDate'])) $SQL .= "AND eventDate LIKE '%".trim($_POST['eDate'])."%' ";
        if(trim($_POST['bRef'])) $SQL .= "AND bookRef = ".trim($_POST['bRef'])." ";
        if(trim($_POST['bName'])) $SQL .= "AND booker LIKE '%".trim($_POST['bName'])."%' ";

        $SQL .= "AND tblbooking.Status = 'A' "; //Only active staff will be included
        $SQL .= "ORDER BY booker";

        //Error checking to remove possible contradicting statements
        $SQL = str_replace("WHERE AND", "WHERE", $SQL);
        $SQL = str_replace("WHERE ORDER","ORDER", $SQL);

        //Stores the SQL command to be used in the next page
        $_SESSION['SQL'] = $SQL;
        echo "<script>location='viewBookingsResult.php?page=1'</script>"; //Takes the user to the next page
    }
    ?>
</div>
</body>
