<?php
/**
Adds more qualifications and work experiences for a staff record
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<script>
    var count = 0; //Keeps track of qualification rows
    var count2 = 0; //Keeps track of work experience rows

    function plusEducation() //Adds a new qualification row
    {
        var table = document.getElementById("tblEducation"); //Gets qualification table
        var row = table.insertRow(-1); //Inserts a new row below
        //Inserts cells into the row
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);

        count++; //Adds to the qualification rows counter

        //Adds in new row fields
        cell1.innerHTML = count + ".";
        cell2.innerHTML = "<label for='institution" + count + "'></label><input type='text' name='institution" + count + "\' id='institution" + count + "\' size='52' maxlength='50' placeholder='Institution' required/>";
        cell3.innerHTML = "<label for=\"level" + count + "\"></label><select name=\"level" + count + "\" id=\"level" + count + "\">" +
            "<option></option>" +
            "<option value=\"SPM\"  selected=\"selected\">SPM</option>" +
            "<option value=\"Diploma\">Diploma</option>" +
            "<option value=\"Degree\">Degree</option>" +
            "<option value=\"Master\">Master</option>" +
            "<option value=\"PhD\">PhD</option>" +
            "</select>";
        cell4.innerHTML = "<label for='specialization" + count + "' ></label><input type='text' name='specialization" + count + "' id='specialization" + count + "' size='52' maxlength='50' placeholder='Specialization' title=\"Leave blank if none\"/>";
        cell5.innerHTML = "<label for='graduateYr" + count + "'></label><input type=\"text\" maxlength=\"4\" oninput=\"this.value=this.value.replace(/[^0-9]/g,'');\"\n" +
            " name='graduateYr" + count + "' size='15' id='graduateYr" + count + "' placeholder='Graduate Year' required/>";

        document.getElementById("counterEdu").value = count; //Updates the hidden HTML qualification row tracker(PHP cannot get tracker in javascript)
    }

    function minusEducation() //Removes latest qualification row
    {
        var table = document.getElementById("tblEducation");
        var counter = document.getElementById("counterEdu");
        var row = table.deleteRow(-1); //Removes row

        if ( count !== 0 )
            count--; //Decrements the qualifications row tracker

        document.getElementById("counterEdu").value = count; //Updates the hidden HTML qualification row tracker(PHP cannot get tracker in javascript)
    }

    function plusWork() //Adds a new work experience row
    {
        var table = document.getElementById("tblWork"); //Gets the work experience table
        var row = table.insertRow(-1); //Inserts a new row below
        //Populates row with cells
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);
        var cell6 = row.insertCell(5);

        count2++; //Adds to the work experience counter

        cell1.innerHTML = count2 + ".";
        cell2.innerHTML = "<label for='company" + count2 + "'></label><input type='text' name='company" + count2 + "' id='company" + count2 + "' size='52' maxlength='50' placeholder='Company' required/>";
        cell3.innerHTML = "<label for='position" + count2 + "'></label><input type='text' name='position" + count2 + "' id='position" + count2 + "' size='27' maxlength='25' placeholder='Position' required/>";
        cell4.innerHTML = "<label for='workFrom" + count2 + "'></label><input type=\"text\" maxlength=\"4\" oninput=\"this.value=this.value.replace(/[^0-9]/g,'');\"\n" +
            " name='workFrom" + count2 + "' size='8' id='workFrom" + count2 + "' placeholder='From Year' required/>";
        cell5.innerHTML = "-";
        cell6.innerHTML = "<label for='workTo" + count2 + "'></label><input type=\"text\" maxlength=\"4\" oninput=\"this.value=this.value.replace(/[^0-9]/g,'');\"\n" +
            " name='workTo" + count2 + "' size='6' id='workTo" + count2 + "' placeholder='To Year' required/>";

        document.getElementById("counterWork").value = count2; //Updates the hidden HTML work experience row tracker(PHP cannot get tracker in javascript)
    }

    function minusWork() //Deletes latest work experience row
    {
        var table = document.getElementById("tblWork"); //Gets work experience table
        var row = table.deleteRow(-1); //Deletes latest row

        if ( count2 !== 0 )
            count2--; //Decrements work experience counter

        document.getElementById("counterWork").value = count2; //Updates the hidden HTML work experience row tracker(PHP cannot get tracker in javascript)
    }

    function checkName() //Checks that duplicate institutions are not included
    {
        var flag = 0;
        var i = document.getElementById('counterEdu').value; //Gets qualification HTML counter
        if(i == 0) return 1; //Function ends if there is only 1 row
        else //Table has more than 1 row
        {
            for(f = 1; f <= i; ++f) //Checks each row
            {
                if(i === 1) break; //Just extra checks on row numbers
                var checkp = document.getElementById('institution'+(f)).value; //Gets corresponding row's institution value
                for(g = 2; g <= i; ++g) //Checks each row from second row
                {
                    if(f === g) continue; //Ignores same checked rows
                    var check = document.getElementById('institution' + g).value; //Gets corresponding row's institution value
                    if(check === checkp) //Both f and g institution rows' value matches
                    {
                        //Visually informs user
                        document.getElementById('institution'+f).style = "border-color: red";
                        document.getElementById('institution'+g).style = "border-color: red";
                        flag = 1; //tracks the duplication
                        return 0;
                    }
                }
            }
            if(flag === 0) //No duplicate values found
            {
                return 1;
            }
        }
    }

    function check() //Checks if certain conditions have been fulfilled to submit the form
    {
        var flag = checkName(); //Checks for duplicate institution records

        if(flag === 1) //Duplicates not found
        {
            document.forms['fForm'].submit(); //Submit form
        }
        else alert("You have duplicate records, please check again"); //Informs user that duplicates were found
    }

    function back() //Return to editStaff.php
    {
        var b = "<?php echo $_GET['id']; ?>";
        location = "editStaff.php?Id="+b;
    }
</script>
<?php
if($_POST['Editdone'] != "") //Ensures this section of code is ran when form is submitted
{

    for ($i = 1; $i <= $_POST['counterEdu']; ++$i) //Checks existing qualifications for duplicate records
    {
        $checkStaffQualSQL = "SELECT * FROM tblstaffqual WHERE School='" . strtoupper(trim($_POST['institution' . $i])) . "' AND StaffIC = '" . strtoupper(trim($_GET['id'])) . "'";
        $checkStaffQualSQLRecord = mysqli_query($Link, $checkStaffQualSQL);
        if (mysqli_num_rows($checkStaffQualSQLRecord)) //Records found
        {
            $qualflag = true; //Flag duplicate
            $qualNum = $i; //Mark duplicate row
            break;
        }
    }

    for ($f = 1; $f <= $_POST['counterWork']; ++$f) //Checks existing work experiences for duplicate records
    {
        $checkWorkSQL = "SELECT * FROM tblworkexp WHERE Company='" . strtoupper(trim($_POST['company' . $f])) . "' AND StaffIC = '" . strtoupper(trim($_GET['id'])) . "'";
        $checkWorkSQLRecord = mysqli_query($Link, $checkWorkSQL);
        if (mysqli_num_rows($checkWorkSQLRecord)) //Records found
        {
            $workflag = true; //Flag duplicate
            $workNum = $f; //Mark duplicate row
            break;
        }
    }

    if ($qualflag == true) //Duplicate institution record found
    {
        echo '<script>alert("This specific school record has already been recorded into the database. Record: "' . $qualNum . ');</script>';
    }
    else if ($workflag == true) //Duplicate work record found
    {
        echo '<script>alert("This specific work record has already been recorded into the database. Record: "' . $workNum . ');</script>';
    }
    else //No duplicate records found
    {
        if($_POST['counterEdu'] > 0) //New qualification records are to be added
        {
            for ($i = 1; $i <= $_POST['counterEdu']; ++$i) //Adds each row into the database
            {
                $addQualSQL = "INSERT INTO tblstaffqual(StaffIC, Qualification, Specialization, School, gradYear) VALUES(
                    '" . strtoupper(trim($_GET['id'])) . "',
                    '" . strtoupper(trim($_POST['level' . $i])) . "',
                    '" . strtoupper(trim($_POST['specialization' . $i])) . "',
                    '" . strtoupper(trim($_POST['institution' . $i])) . "',
                    '" . strtoupper(trim($_POST['graduateYr' . $i])) . "')";
                $addQualSQLResult = mysqli_query($Link, $addQualSQL);
            }
        }

        if($_POST['counterWork'] > 0) //New work experience records are to be added
        {
            for ($f = 1; $f <= $_POST['counterWork']; ++$f) //Adds each row into the database
            {
                $addWorkSQL = "INSERT INTO tblworkexp(StaffIC, Position, Company, fromYear, toYear) VALUES (
                    '" . strtoupper(trim($_GET['id'])) . "',
                    '" . strtoupper(trim($_POST['position' . $f])) . "',
                    '" . strtoupper(trim($_POST['company' . $f])) . "',
                    '" . strtoupper(trim($_POST['workFrom' . $f])) . "',
                    '" . strtoupper(trim($_POST['workTo' . $f])) . "')";
                $addWorkSQLResult = mysqli_query($Link, $addWorkSQL);
            }
        }

        if($addQualSQLResult || $addWorkSQLResult) //SQL query/queries successful
        {
            $Id = $_GET['id'];
            echo "<script>location='editStaff.php?Id=$Id';</script>"; //Return
        }
    }
}
?>
<title>Add Qualifications and Experiences</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<body>
<div class="container">
<?php if($_SESSION['AccType'] == "ADMIN")// Only admins can add new records
{?>
    <h1>Add Staff Qualificaiton and Experience</h1>
    <h3>*Mandatory</h3>
    <form id="fForm" name="fForm" method="post">
        <table align="center" cellpadding="6" border="0" id="tblEducation">
            <caption>Qualifications</caption>
            <tr>
                <td></td>
                <td><label for="institution1">*Institution</label></td>
                <td><label for="level1">Level</label></td>
                <td><label for="specialization1">Specialization</label></td>
                <td><label for="graduateYr1">*Graduate Year</label></td>
            </tr>
        </table>
        <table align="center" cellpadding="6" border="0">
            <tr>
                <td style="text-align:center">
                    <input type="button" name="plus" id="plus" value="+" onclick="plusEducation()" class="button site"/>
                    <input type="button" name="minus" id="minus" value="-" onclick="minusEducation()" class="button site"/>
                </td>
            </tr>
        </table>
        <br/>
        <table align="center" cellpadding="6" border="0" id="tblWork">
            <caption>Work Experience(if any)</caption>
            <tr>
                <td></td>
                <td>Company</td>
                <td>Position</td>
                <td>From Year</td>
                <td>-</td>
                <td>To Year</td>
            </tr>
        </table>
        <table align="center" cellpadding="6" border="0">
            <tr>
                <td colspan="6" style="text-align:center">
                    <input type="button" name="plus" id="plus" value="+" onclick="plusWork()"  class="button site"/>
                    <input type="button" name="minus" id="minus" value="-" onclick="minusWork()" class="button site"/>
                </td>
            </tr>
        </table>

        <br /><br />
        <input type="hidden" name="counterEdu" id="counterEdu" value="0"/><!-- Tracks qualification rows -->
        <input type="hidden" name="counterWork" id="counterWork" value="0"/><!-- Tracks work experience rows -->
        <input type="hidden" name="Editdone" id="Editdone" value="Editdone"/><!-- Ensures form submission will run correct php segment -->
        <table>
            <tr>
                <td><input type="button" name="btnSub" class="button site" style="width: 100px" id="btnSub" onclick="check()" value="Add"/></td>
                <td><input type="button" name="btnBack" class="button site" style="width: 100px" id="btnBack" onclick="back()" value="Back"/> </td>
            </tr>
        </table>
    </form>
<?php } ?>
</div>
</body>