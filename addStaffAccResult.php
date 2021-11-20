<?php
/**
Shows the results of staff records with no accounts search
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
include ('Email.php'); //Includes emailing functionality
?>
<script language="javascript">
    //Used for checking or unchecking all records
    function toggle(MaxCheck)
    {
        var i = 1;
        if(document.getElementById('chkAll').checked === true)//All checkboxes checked
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
<title>Staff Accounts</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />

<body>
<div class="container" align="center">
    <?php
    //Creates new accounts for selected records
    if($_REQUEST['btnCreate'])
    {
        // while(list($key,$val) = each($_POST)) each function is deprecated, kept for posterity
        foreach($_POST as $key => $val)
        {
            if($key != "chkAll" && $key != "btnCreate") //Error checking to ensure stated keys are not included for deletion
            {
                $SQL = "SELECT StaffName, Email from tblstaff WHERE StaffIC = '".$key."'";
                $SQLResult = mysqli_query($Link, $SQL);
                $RowInfo = mysqli_fetch_array($SQLResult);
                if($RowInfo['StaffName'] != "")
                {
                    $name = generate_unique_username($RowInfo['StaffName'], 9999);
                    $PW = randomPassword(10, 1, "lower_case,numbers,upper_case,special_symbols");
                    $random_hash = substr(uniqid(rand(), true), 8, 8); //Generates a random code to verify the account
                    $SQLAdd = "INSERT INTO tbllogin(IC, Username, Password, veriCode, AccType, Status) VALUES (
                        '".strtoupper(trim($key))."',
                        '".strtoupper(trim($name))."',
                        '".strtoupper(trim($PW))."',
                         '".$random_hash."',
                        'STAFF',
                        'N'
                    )";
                    $SQLAddResult = mysqli_query($Link, $SQLAdd);
                    $Verify = "An account has been created for you on the Yokio Olympics Website! Please enter the code below to verify your account and receive your new username and password!<br/>
                                    ".$random_hash."<br/> Please ignore if you did not wish or plan on having an account with us";
                    sendEmail("Account Verification", $Verify, $RowInfo['Email']);
                    if($SQLAddResult)
                    {
                        echo "<script>alert('Accounts have been created');location='addStaffAccResult.php?page=1';</script>"; //Reloads the webpage after accounts have been deactivated
                    }
                }
            }
        }
    }
    else if($_SESSION['AccType'] == "ADMIN")//Currently only Admins can create new accounts
    {
        $SQL = $_SESSION['SQL']; //Gets the SQL query from addStaffAcc.php
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
                        echo "<button><a style='font-size: 21px'  href='addStaffAccResult.php?page=".($page-1)."'>&#60;</a> </button>";
                    if($page <= $maxPage && $page != 1) //Checks if 'forward' button should be shown
                        echo "<button><a style='font-size: 21px'  href='addStaffAccResult.php?page=".($page+1)."'>&#62;</a> </button>"?>
                    <table align="center">
                        <tr>
                            <th style="background-color: blue; color: white" colspan='100%'>Showing <?php echo $minLim ." to ". $maxLim ." of ". $maxRec; ?> Results</th>
                        </tr>
                        <tr style="color: black">
                            <th scope="col">No</th>
                            <?php $count = mysqli_num_rows($Result);
                            echo "<th scope='col'><input type='checkbox' name=\"chkAll\" id=\"chkAll\" onClick=\"toggle($count)\"></th>"; //Creates the checkall checkbox ?>
                            <th scope="col">Staff Name</th>
                            <th scope="col">Staff IC</th>
                            <th scope="col">Position</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Address</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Email</th>
                        </tr>
                        <?php //Creates row for each record
                        for($i = $minLim ; $i <= $maxLim; ++$i)
                        {
                            $RowInfo = mysqli_fetch_array($Result);
                            echo "<tr>";
                            echo "<td>".($i)."</td>";
                            echo "<td><input type=\"checkbox\" name=\"".$RowInfo['StaffIC']."\" id=\"Rec".($i)."\"></td>";
                            echo "<td>".$RowInfo['StaffName']."</td>";
                            echo "<td style='text-align: center'>".$RowInfo['StaffIC']."</td>";
                            echo "<td>".$RowInfo['Position']."</td>";
                            echo "<td style='text-align: center'>".$RowInfo['Phone']."</td>";
                            echo "<td>".$RowInfo['Address']."</td>";
                            echo "<td>".$RowInfo['Gender']."</td>";
                            echo "<td>".$RowInfo['Email']."</td>";
                            echo "</tr>";
                        }
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<th style='background-color: initial'></td>";
                        echo "<td align=\"center\" colspan=\"100%\"><input type=\"submit\" name=\"btnCreate\" 
                            value=\"Create accounts for checked records\" onclick='return confirm(\"This will create new accounts for the selected records. Proceed?\");'
                             id=\"Create\" class='button'></td>"; //Creates accounts for checked records
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
<?php
//Generate a unique username using staff name
function generate_unique_username($string_name, $rand_no)
{
    while(true)
    {
        $username_parts = array_filter(explode(" ", strtolower($string_name))); //explode and lowercase name
        $username_parts = array_slice($username_parts, 0, 2); //return only first two arry part

        $part1 = (!empty($username_parts[0]))?substr($username_parts[0], 0,8):""; //cut first name to 8 letters
        $part2 = (!empty($username_parts[1]))?substr($username_parts[1], 0,5):""; //cut second name to 5 letters
        $part3 = ($rand_no)?rand(0, $rand_no):"";

        $username = $part1. str_shuffle($part2). $part3; //str_shuffle to randomly shuffle all characters

        $username_exist_in_db = username_exist_in_database($username); //check username in database
        if(!$username_exist_in_db)
        {
            return $username;
        }
        else
        {
            echo generate_unique_username($string_name, 9999);
        }
    }
}

//Checks if the created username is already taken in the database
function username_exist_in_database($username)
{
    $mysqli = new mysqli('localhost','username','password','dbadmin'); //connect to database

    if ($mysqli->connect_error) {
        die("An error has occured connecting to the database, please try again later");
    }

    $statement = $mysqli->prepare("SELECT userId FROM tbllogin WHERE username = ?"); //Gets records that has the same username(if any)
    $statement->bind_param('s', $username);
    if($statement->execute()) //Statement returns a match or more
    {
        $statement->store_result();
        return $statement->num_rows;
    }
}

function randomPassword($length,$count, $characters)
{

// $length - the length of the generated password
// $count - number of passwords to be generated
// $characters - types of characters to be used in the password

// define variables used within the function
    $symbols = array();
    $passwords = array();
    $used_symbols = '';
    $pass = '';

// an array of different character types
    $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
    $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $symbols["numbers"] = '1234567890';
    $symbols["special_symbols"] = '!?~@#-_+<>[]{}';

    $characters = split(",",$characters); // get characters types to be used for the passsword
    foreach ($characters as $key=>$value) {
        $used_symbols .= $symbols[$value]; // build a string with all characters
    }
    $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1

    for ($p = 0; $p < $count; $p++) {
        $pass = '';
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $symbols_length); // get a random character from the string with all characters
            $pass .= $used_symbols[$n]; // add the character to the password string
        }
        $passwords[] = $pass;
        return $passwords[$p]; // return the generated password
    }
}

?>