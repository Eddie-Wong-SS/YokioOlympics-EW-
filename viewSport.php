<?php
/**
Allows the viewing of sports with filter options
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<title>Filter Sports</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container">
    <h1>View Sports</h1>
    <h3>Use the fields to filter specific results</h3>

    <form method="post" action="">
        <div align="center">
            <table border="0" style="table-layout: fixed">
                <caption>Search Filter</caption>
                <tr>
                    <td><label for="sName">Sport Name: </label></td>
                    <td><input type="text" name="sName" id="sName"> </td>
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

        $SQL = "SELECT * FROM tblsports";
        $SQL .= " WHERE ";

        //Conditionals to filter records based on any filters used above
        if(trim($_POST['sName'])) $SQL .= "AND sportName LIKE '%".trim($_POST['sName'])."%' ";

        $SQL .= "AND Status = 'A' "; //Only active staff will be included
        $SQL .= "ORDER BY sportName";

        //Error checking to remove possible contradicting statements
        $SQL = str_replace("WHERE AND", "WHERE", $SQL);
        $SQL = str_replace("WHERE ORDER","ORDER", $SQL);

        //Stores the SQL command to be used in the next page
        $_SESSION['SQL'] = $SQL;
        echo "<script>location='viewSportResult.php?page=1'</script>"; //Takes the user to the next page
    }
    ?>
</div>
</body>
