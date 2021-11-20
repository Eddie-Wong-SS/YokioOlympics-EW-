<?php
/**
Allows the viewing of bookings and seats for each schedule as a report
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<title>Filter Bookings and Tickets</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container">
    <?php if($_SESSION['AccType'] == "ADMIN")//Only admins may view the report
    {
        ?>
        <h1>View Bookings and Tickets Number Report Per Sport</h1>
        <h3>Use the field to filter specific results</h3>

        <form method="post" action="">
            <div align="center">
                <table border="0" style="table-layout: fixed">
                    <caption>Search Filter</caption>
                    <tr>
                        <td><label for="sName">Sport Name: </label></td>
                        <td><input type="text" name="sName" id="sName"></td>
                    </tr>
                    <tr>
                        <td><label for="eDate">Event Date: </label></td>
                        <td><input type="date" name="eDate" id="eDate" min="2020-05-11" max="2020-05-15" title="YokioOlympics lasts from 11th of May to 15th of May 2020" /></td>
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
        $SQL = "SELECT DISTINCT sportName FROM tblschedule, tblsports 
          WHERE tblschedule.sportID = tblsports.sportID ";

        //Conditionals to filter records based on any filters used above
        if(trim($_POST['sName'])) $SQL .= "AND sportName LIKE '%".trim($_POST['sName'])."%' ";
        if(trim($_POST['eDate'])) $SQL .= "AND eventDate LIKE '%".trim($_POST['eDate'])."%' ";

        $SQL .= "AND tblsports.Status != 'I' AND tblschedule.Status != 'I'"; //Only active accounts will be included
        $SQL .= " ORDER BY sportName";

        //Error checking to remove possible contradicting statements
        $SQL = str_replace("WHERE AND", "WHERE", $SQL);
        $SQL = str_replace("WHERE ORDER","ORDER", $SQL);

        //Stores the SQL command to be used in the next page
        $_SESSION['SQL'] = $SQL;
        echo "<script>location='viewBookTickResult.php?page=1'</script>"; //Takes the user to the next page
    }
    ?>
</div>
</body>
