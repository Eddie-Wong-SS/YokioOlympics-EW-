<?php
/**
Allows the viewing of schedules with filter options
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<title>Filter Schedules</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container">
        <h1>View Schedules</h1>
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
                        <td ><label for="vName">Venue Name: </label></td>
                        <td><select name="vName" id="vName">
                                <option selected="selected"></option>
                                <option value="Aquatics Palace">Aquatics Palace</option>
                                <option value="FunOlympic Village">FunOlympic Village</option>
                                <option value="Stadium of Delight">Stadium of Delight</option>
                            </select></td>
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
    if($_REQUEST['btnSearch'])
    {

        $SQL = "SELECT sportName, tblschedule.* FROM tblsports, tblschedule WHERE tblschedule.sportID = tblsports.sportID ";

        //Conditionals to filter records based on any filters used above
        if(trim($_POST['sName'])) $SQL .= "AND sportName LIKE '%".trim($_POST['sName'])."%' ";
        if(trim($_POST['vName'])) $SQL .= "AND venueName LIKE '%".trim($_POST['vName'])."%' ";
        if(trim($_POST['eDate'])) $SQL .= "AND eventDate LIKE '%".trim($_POST['eDate'])."%' ";

        $SQL .= "AND tblschedule.Status = 'A' "; //Only active staff will be included
        $SQL .= "ORDER BY eventDate";

        //Error checking to remove possible contradicting statements
        $SQL = str_replace("WHERE AND", "WHERE", $SQL);
        $SQL = str_replace("WHERE ORDER","ORDER", $SQL);

        //Stores the SQL command to be used in the next page
        $_SESSION['SQL'] = $SQL;
        echo "<script>location='viewScheduleResult.php?page=1'</script>"; //Takes the user to the next page
    }
    ?>
</div>
</body>
