<?php
/**
Allows the viewing of staff with no accounts yet with filter options
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<title>Filter Staff Accounts</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container">
<?php if($_SESSION['AccType'] == "ADMIN") //Only admins can add new staff accounts
{ ?>
    <h1>View Staff</h1>
    <h3>Use the fields to filter specific results</h3>

    <form method="post" action="">
        <div align="center">
            <table border="0" style="table-layout: fixed">
                <caption>Search Filter</caption>
                <tr>
                    <td><label for="stfName">Staff Name: </label></td>
                    <td><input type="text" name="stfName" id="stfName"></td>
                </tr>
                <tr>
                    <td><label for="stfGender">Gender: </label></td>
                    <td><select name="stfGender" id="stfGender">
                            <option selected="selected"></option>
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select></td>
                </tr>
                <tr>
                    <td><label for="stfPos">Position: </label></td>
                    <td><select name="stfPos" id="stfPos">
                            <option selected="selected"></option>
                            <option value="Employed">Employed</option>
                            <option value="Contracted">Contracted</option>
                        </select></td>
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
        //Gets only staff records that do not have accounts yet
        $SQL = "SELECT * FROM tblstaff WHERE tblstaff.StaffIC NOT IN (SELECT IC FROM tbllogin WHERE AccType = 'STAFF') ";

        //Conditionals to filter records based on any filters used above
        if(trim($_POST['stfName'])) $SQL .= "AND StaffName LIKE '%".trim($_POST['stfName'])."%' ";
        if(trim($_POST['stfGender'])) $SQL .= "AND Gender LIKE '%".trim($_POST['stfGender'])."%' ";
        if(trim($_POST['stfPos'])) $SQL .= "AND Position LIKE '%".trim($_POST['stfPos'])."%' ";

        $SQL .= "AND Status = 'A'"; //Only active staff will be included
        $SQL .= "ORDER BY StaffName";

        //Error checking to remove possible contradicting statements
        $SQL = str_replace("WHERE AND", "WHERE", $SQL);
        $SQL = str_replace("WHERE ORDER","ORDER", $SQL);

        //Stores the SQL command to be used in the next page
        $_SESSION['SQL'] = $SQL;
        echo "<script>location='addStaffAccResult.php?page=1'</script>"; //Takes the user to the next page
    }
    ?>
</div>
</body>
