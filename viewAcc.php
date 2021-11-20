<?php
/**
Allows the viewing of accounts with filter options
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<title>Filter Accounts</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container">
<?php if($_SESSION['AccType'] == "ADMIN")  //Only admins can view all staff accounts
{
    ?>
    <h1>View Accounts</h1>
    <h3>Use the fields to filter specific results</h3>

    <form method="post" action="">
        <div align="center">
            <table border="0" style="table-layout: fixed">
                <caption>Search Filter</caption>
                <tr>
                    <td><label for="uName">Username: </label></td>
                    <td><input type="text" name="uName" id="uName"></td>
                </tr>
                <tr>
                    <td><label for="uIC">User IC: </label></td>
                    <td><input type="text" name="uIC" id="uIC"></td>
                </tr>
                <tr>
                    <td><label for="accType">Account Type: </label></td>
                    <td><select name="accType" id="accType">
                            <option selected="selected"></option>
                            <option value="Admin">Admin</option>
                            <option value="Staff">Staff</option>
                        </select></td>
                </tr>
                <tr>
                    <td><label for="accStat">Account Status: </label></td>
                    <td><select name="accStat" id="accStat">
                            <option selected="selected"></option>
                            <option value="A">Activated</option>
                            <option value="N">Unactivated</option>
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

        $SQL = "SELECT IC, Username, Password, AccType, Status FROM tbllogin";
        $SQL .= " WHERE ";

        //Conditionals to filter records based on any filters used above
        if(trim($_POST['uName'])) $SQL .= "AND Username LIKE '%".trim($_POST['uName'])."%' ";
        if(trim($_POST['uIC'])) $SQL .= "AND IC LIKE '%".trim($_POST['uIC'])."%' ";
        if(trim($_POST['accType'])) $SQL .= "AND AccType LIKE '%".trim($_POST['accType'])."%' ";
        if(trim($_POST['accStat'])) $SQL .= "AND Status LIKE '%".trim($_POST['accStat'])."%' ";

        $SQL .= "AND Status = 'A'"; //Only active accounts will be included
        $SQL .= " ORDER BY Username";

        //Error checking to remove possible contradicting statements
        $SQL = str_replace("WHERE AND", "WHERE", $SQL);
        $SQL = str_replace("WHERE ORDER","ORDER", $SQL);

        //Stores the SQL command to be used in the next page
        $_SESSION['SQL'] = $SQL;
        echo "<script>location='viewAccResult.php?page=1'</script>"; //Takes the user to the next page
    }
    ?>
</div>
</body>
