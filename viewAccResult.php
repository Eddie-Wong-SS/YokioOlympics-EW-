<?php
/**
Shows the results of staff search
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<script language="javascript">
    //Used for checking or unchecking all records
    function toggle(MaxCheck)
    {
        var i = 1;
        if(document.getElementById('chkAll').checked === true) //All checkboxes checked
        {
            for( i = 1; i <= MaxCheck; i++)
            {
                document.getElementById('Rec' + i).checked = true;
            }
        }

        if(document.getElementById('chkAll').checked === false) //All checkboxes unchecked
        {
            for( i = 1; i <= MaxCheck; i++)
            {
                document.getElementById('Rec' + i).checked = false;
            }
        }
    }

</script>
<title>Accounts</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container" align="center">
    <?php
    //Sets accounts to 'I' for inactive - good practice not to actually delete records, but ensure they cant be viewed nonetheless
    if($_REQUEST['btnDelete'])
    {
        // while(list($key,$val) = each($_POST)) 'each' function is deprecated, kept for posterity
        foreach($_POST as $key => $val)
        {
            if($key != "chkAll" && $key != "btnDelete") //Error checking to ensure stated keys are not included for deletion
            {
                $DelEmpSQL = "UPDATE tbllogin SET Status = 'I' WHERE IC = '".$key."'"; //Sets staff record to be inactive
                $DelEmpResult = mysqli_query($Link, $DelEmpSQL);

            }
        }
        if($DelEmpResult)
        {
            echo "<script>alert('Selected record(s) has been deleted');location='viewAccResult.php?page=1';</script>"; //Reloads the webpage after accounts have been deactivated
        }
    }
    else if($_SESSION['AccType'] == "ADMIN")//Currently only Admins can view accounts
    {
        $SQL = $_SESSION['SQL']; //Gets the SQL query from viewStaff.php
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
                        echo "<button><a style='font-size: 21px'  href='viewAccResult.php?page=".($page-1)."'>&#60;</a> </button>";
                    if($page <= $maxPage && $page != 1) //Checks if 'forward' button should be shown
                        echo "<button><a style='font-size: 21px'  href='viewAccResult.php?page=".($page+1)."'>&#62;</a> </button>"?>
                    <table align="center">
                        <tr>
                            <th style="background-color: blue; color: white" colspan='100%'>Showing <?php echo $minLim ." to ". $maxLim ." of ". $maxRec; ?> Results</th>
                        </tr>
                        <tr style="color: black">
                            <th scope="col" style="width: 3%">No</th>
                            <?php $count = mysqli_num_rows($Result);
                            echo "<th scope='col'><input type='checkbox' name=\"chkAll\" id=\"chkAll\" onClick=\"toggle($count)\"></th>"; //Creates the checkall checkbox ?>
                            <th scope="col">Username</th>
                            <th scope="col">IC</th>
                            <th scope="col">Account type</th>
                            <th scope="col">Status</th>
                            <th colspan="100%">Actions</th>
                        </tr>
                        <?php //Creates row for each record
                        for($i = $minLim ; $i <= $maxLim; ++$i)
                        {
                            $RowInfo = mysqli_fetch_array($Result);
                            echo "<tr>";
                            echo "<td>".($i)."</td>";
                            echo "<td style='text-align: center'><input type=\"checkbox\" name=\"".$RowInfo['IC']."\" id=\"Rec".($i)."\"></td>";
                            echo "<td>".$RowInfo['Username']."</td>";
                            echo "<td style='text-align: center'>".$RowInfo['IC']."</td>";
                            echo "<td>".$RowInfo['AccType']."</td>";
                            if($RowInfo['Status'] == "A")
                                $State = "Activated";
                            else $State = "Deactivated";
                            echo "<td style='text-align: center'>".$State."</td>";
                            echo "<td><a class='hoverme' href=\"changePass.php?Id=".$RowInfo['IC']."&hashed=".sha1($RowInfo['Password'])."\">Change Password</a></td>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<th style='background-color: initial'></td>";
                        echo "<td align=\"center\" colspan=\"100%\"><input type=\"submit\" name=\"btnDelete\" 
                            value=\"Delete checked records\" onclick='return confirm(\"This will delete the selected records. Proceed?\");'
                             id=\"Delete\" class='button'></td>"; //Deletes all checked records
                        echo "</tr>";
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
