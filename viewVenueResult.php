<?php
/**
Shows the results of venue search
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
        if(document.getElementById('chkAll').checked === true) //All checkboxes are checked
        {
            for( i = 1; i <= MaxCheck; i++)
            {
                document.getElementById('Rec' + i).checked = true;
            }
        }

        if(document.getElementById('chkAll').checked === false) //All checkboxes are unchecked
        {
            for( i = 1; i <= MaxCheck; i++)
            {
                document.getElementById('Rec' + i).checked = false;
            }
        }
    }

</script>
<title>Venue Records</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container" align="center">
    <?php
    $SQL = $_SESSION['SQL']; //Gets the SQL query from viewVenue.php
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
                    echo "<button><a style='font-size: 21px'  href='viewVenueResult.php?page=".($page-1)."'>&#60;</a> </button>";
                if($page <= $maxPage && $page != 1) //Checks if 'forward' button should be shown
                    echo "<button><a style='font-size: 21px'  href='viewVenueResult.php?page=".($page+1)."'>&#62;</a> </button>"?>
                <table align="center">
                    <tr>
                        <th style="background-color: blue; color: white" colspan='100%'>Showing <?php echo $minLim ." to ". $maxLim ." of ". $maxRec; ?> Results</th>
                    </tr>
                    <tr style="color: black">
                        <th scope="col">No</th>
                        <th scope="col">Venue Name</th>
                        <th scope="col" width="70%">Venue Description</th>
                        <th colspan="100%">Actions</th>
                    </tr>
                    <?php //Creates row for each record
                    for($i = $minLim ; $i <= $maxLim; ++$i)
                    {
                        $RowInfo = mysqli_fetch_array($Result);
                        echo "<tr>";
                        echo "<td>".($i)."</td>";
                        echo "<td style='text-align: center'>".$RowInfo['venueName']."</td>";
                        echo "<td style='text-align: left'>".$RowInfo['venueDescp']."</td>";
                        echo "<td style='text-align: center'><a class='hoverme' href=\"venueDetails.php?Id=".$RowInfo['venueName']."\">View Full Details</a></td>"; //Sends user to the venue details page
                        echo "</tr>";
                    }
                    ?>
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
