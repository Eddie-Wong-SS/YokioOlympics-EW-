<?php
/**
Allows the viewing of news with filter options
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<title>Filter News Articles</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container">
    <h1>View News Articles</h1>
    <h3>Use the field to filter specific results</h3>

    <form method="post" action="">
        <div align="center">
            <table border="0" style="table-layout: fixed">
                <caption>Search Filter</caption>
                <tr>
                    <td><label for="aName">Article Title: </label></td>
                    <td><input type="text" name="aName" id="aName"></td>
                </tr>
                <tr>
                    <td><label for="fDate">Date From: </label></td>
                    <td><input type="date" name="fDate" id="fDate" max="<?php echo $Mtime ?>"/></td>
                </tr>
                <tr>
                    <td colspan="2">-</td>
                </tr>
                <tr>
                    <td><label for="tDate">Date To: </label></td>
                    <td><input type="date" name="tDate" id="tDate" max="<?php echo $Mtime ?>"/></td>
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

        $SQL = "SELECT tblnews.*, tbllogin.Username FROM tblnews, tbllogin WHERE tblnews.userId = tbllogin.userId ";

        //Conditionals to filter records based on any filters used above
        if(trim($_POST['aName'])) $SQL .= "AND newsTitle LIKE '%".trim($_POST['aName'])."%' ";
        if(trim($_POST['fDate'])) $SQL .= "AND writtenOn >= '".trim($_POST['fDate'])."' ";
        if(trim($_POST['tDate'])) $SQL .= "AND writtenOn <= '".trim($_POST['tDate'])."' ";

        $SQL .= "AND tblnews.Status != 'I'"; //Only active accounts will be included
        $SQL .= " ORDER BY writtenOn";

        //Error checking to remove possible contradicting statements
        $SQL = str_replace("WHERE AND", "WHERE", $SQL);
        $SQL = str_replace("WHERE ORDER","ORDER", $SQL);

        //Stores the SQL command to be used in the next page
        $_SESSION['SQL'] = $SQL;
        echo "<script>location='viewNewsResult.php?page=1'</script>"; //Takes the user to the next page
    }
    ?>
</div>
</body>
