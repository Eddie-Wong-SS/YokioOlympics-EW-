<?php
/**Adds in a new staff to the database
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/0.0.11/push.min.js"></script> <!-- push.js.master library for notifications -->
<script>

    count = 1;
    count2 = 1;
    Push.Permission.request(); //Browser will request permission to display notifications
    function checkers() //push.js.master notification function
    {
        Push.create('Successfully Added!', { //Create notification
            body: 'Adding of staff <?php echo $_POST['stfName']; ?> account into the database was successful',
            icon: 'icon.png',
            timeout: 8000,                  // Timeout before notification closes automatically.
            onClick: function() {
                // Callback for when the notification is clicked.
                console.log(this);
            }
        });

    }

    function plusEducation()//Adds new qualification row
    {
        var table = document.getElementById("tblEducation"); //Gets qualification table
        //Inserts new row
        var row = table.insertRow(-1);
        //Populate row with cells
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);

        count++;//Increments qualification counter

        //Populate cells
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

    function minusEducation() //Deletes latest qualification row
    {
        var table = document.getElementById("tblEducation"); //Get qualification table
        var counter = document.getElementById("counterEdu");
        var row = table.deleteRow(-1); //deletes latest row

        if ( count !== 1 )
            count--; //Decrements qualification counter

        document.getElementById("counterEdu").value = count; //Updates the hidden HTML qualification row tracker(PHP cannot get tracker in javascript)
    }

    function plusWork() //Adds new work experience row
    {
        var table = document.getElementById("tblWork"); //Gets work experience table
        //Add new row
        var row = table.insertRow(-1);
        //Populate row with cells
        var cell1 = row.insertCell(0);
        var cell2 = row.insertCell(1);
        var cell3 = row.insertCell(2);
        var cell4 = row.insertCell(3);
        var cell5 = row.insertCell(4);
        var cell6 = row.insertCell(5);

        count2++; //Increments work experience counter
        //Populate cells
        cell1.innerHTML = count2 + ".";
        cell2.innerHTML = "<label for='company" + count2 + "'></label><input type='text' name='company" + count2 + "' id='company" + count2 + "' size='52' maxlength='50' placeholder='Company' required/>";
        cell3.innerHTML = "<label for='position" + count2 + "'></label><input type='text' name='position" + count2 + "' id='position" + count2 + "' size='27' maxlength='25' placeholder='Position' required/>";
        cell4.innerHTML = "<label for='workFrom" + count2 + "'></label><input type=\"text\" maxlength=\"4\" oninput=\"this.value=this.value.replace(/[^0-9]/g,'');\"\n" +
            " name='workFrom" + count2 + "' size='8' id='workFrom" + count2 + "' placeholder='From Year' required/>";
        cell5.innerHTML = "-";
        cell6.innerHTML = "<label for='workTo" + count2 + "'></label><input type=\"text\" maxlength=\"4\" oninput=\"this.value=this.value.replace(/[^0-9]/g,'');\"\n" +
            " name='workTo" + count2 + "' size='6' id='workTo" + count2 + "' placeholder='To Year' required/>";

        document.getElementById("counterWork").value = count2; //Updates the hidden HTML work experiencerow tracker(PHP cannot get tracker in javascript)
    }

    function minusWork()//Deletes latest work experience row
    {
        var table = document.getElementById("tblWork"); //Gets work experience row
        var row = table.deleteRow(-1);

        if ( count2 !== 1 )
            count2--;//Decrement work experience counter

        document.getElementById("counterWork").value = count2; //Updates the hidden HTML work experience row tracker(PHP cannot get tracker in javascript)
    }

    function readURL() //Gets and previews images
    {
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("image").files[0]); //Get image data

        oFReader.onload = function (oFREvent)
        {
            document.getElementById("uploadPreview").src = oFREvent.target.result; //Previews images
        };
    }

    function checkName() //Checks if institution name is duplicated
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

    function check() //checks if duplicates are found
    {
        var flag = checkName();

        if(flag === 1) //Duplicate institution not found
        {
            setHid();
            document.forms['fForm'].submit(); //Submits form
        }
        else //Duplicate institutions found
        {
            alert("Duplicate values found in qualifications, please recheck");
        }
    }

    function setHid() //Creates new HTML element to help determine PHP code segment to be run
    {
        var add = document.createElement('input');
        add.type = "hidden";
        add.name = "checked";
        add.id = "checked";
        add.value="2";

        var z = document.getElementById('fForm');
        z.appendChild(add);
    }
</script>
<title>Add Staff</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<?php
if($_REQUEST['btnSub']) //Form submitted and guiding HTML element found
{
    $qualflag = false;
    $qualNum = 0;
    $workflag = false;
    $workNum = 0;
    $target_path = "Images/";
    $target_path = $target_path . "Staff".$_POST['stfIC'].".png"; //Rename image

    $checkStaffSQL = "SELECT * FROM tblstaff WHERE StaffIC='" . strtoupper(trim($_POST['stfIC'])) . "' OR Email='" . strtoupper(trim($_POST['stfEm'])) . "'"; //Check if staff record exists
    $checkStaffSQLRecord = mysqli_query($Link, $checkStaffSQL);

    for ($i = 1; $i <= $_POST['counterEdu']; ++$i)//Check for duplicate qualification records
    {
        $checkStaffQualSQL = "SELECT * FROM tblstaffqual WHERE School='" . strtoupper(trim($_POST['institution' . $i])) . "'";
        $checkStaffQualSQLRecord = mysqli_query($Link, $checkStaffQualSQL);
        if (mysqli_num_rows($checkStaffQualSQLRecord)) //Duplicates found
        {
            $qualflag = true;
            $qualNum = $i; //Mark duplicated row
            break;
        }
    }

    for ($f = 1; $f <= $_POST['counterWork']; ++$f) //check for duplicate work experience records
    {
        $checkWorkSQL = "SELECT * FROM tblworkexp WHERE Company='" . strtoupper(trim($_POST['company' . $f])) . "'";
        $checkWorkSQLRecord = mysqli_query($Link, $checkWorkSQL);
        if (mysqli_num_rows($checkWorkSQLRecord)) //Duplicates found
        {
            $workflag = true;
            $workNum = $f; //Mark duplicated row
            break;
        }
    }

    if (mysqli_num_rows($checkStaffSQLRecord)) //Duplicate staff records found
    {
        echo "<script>alert('The inputted IC or email already exists in the database');</script>";
    } else if ($qualflag == true) //Duplicate qualifications found
    {
        echo '<script>alert("This specific school record has already been recorded into the database. Record: "' . $qualNum . ');</script>';
    } else if ($workflag == true) //Duplicate work experience found
    {
        echo '<script>alert("This specific work record has already been recorded into the database. Record: "' . $workNum . ');</script>';
    } else //No Duplicates found
    {
        //checking if file exsists
        if(file_exists($target_path)) unlink($target_path); //Deletes old images
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path); //Move new image to image folder

//Insert new staff record
        $addStaffSQL = "INSERT INTO tblstaff(StaffIC, StaffName, Gender, Address, DOB, Phone, Email, Position, imageLoc, Remark) VALUES (
                  '" . strtoupper(trim($_POST['stfIC'])) . "',
                  '" . strtoupper(trim($_POST['stfName'])) . "',
                  '" . strtoupper(trim($_POST['stfGen'])) . "',
                  '" . strtoupper(trim($_POST['stfAdd'])) . "',
                  '" . strtoupper(trim($_POST['stfDOB'])) . "',
                  '" . strtoupper(trim($_POST['stfCNO'])) . "',
                  '" . strtoupper(trim($_POST['stfEm'])) . "',
                  '" . strtoupper(trim($_POST['stfPos'])) . "',
                  '$target_path',
                  '" . strtoupper(trim($_POST['stfRem'])) . "'
                  )";
        $addStaffSQLResult = mysqli_query($Link, $addStaffSQL);

        for ($i = 1; $i <= $_POST['counterEdu']; ++$i)//Inserts new qualification records
        {
            $addQualSQL = "INSERT INTO tblstaffqual(StaffIC, Qualification, Specialization, School, gradYear) VALUES(
                    '" . strtoupper(trim($_POST['stfIC'])) . "',
                    '" . strtoupper(trim($_POST['level' . $i])) . "',
                    '" . strtoupper(trim($_POST['specialization' . $i])) . "',
                    '" . strtoupper(trim($_POST['institution' . $i])) . "',
                    '" . strtoupper(trim($_POST['graduateYr' . $i])) . "')";
            $addQualSQLResult = mysqli_query($Link, $addQualSQL);
        }

        for ($f = 1; $f <= $_POST['counterWork']; ++$f) //Inserts new work experience records
        {
            if(strtoupper(trim($_POST['company' . $f])) == "")//error checking for empty rows as work experience is not mandatory
                continue;
            else //Row has values
            {
                $addWorkSQL = "INSERT INTO tblworkexp(StaffIC, Position, Company, fromYear, toYear) VALUES (
                    '" . strtoupper(trim($_POST['stfIC'])) . "',
                    '" . strtoupper(trim($_POST['position' . $f])) . "',
                    '" . strtoupper(trim($_POST['company' . $f])) . "',
                    '" . strtoupper(trim($_POST['workFrom' . $f])) . "',
                    '" . strtoupper(trim($_POST['workTo' . $f])) . "')";
                $addWorkSQLResult = mysqli_query($Link, $addWorkSQL);
            }
        }

        //Notify user of insertion success
        ?>
        <script>checkers();</script>

        <?php
    }

}
?>

<body>
<div class="container" style="width: 80%">
<?php if($_SESSION['AccType'] == "ADMIN") //Only admins can add new staff
{?>
    <h1>Staff Registration</h1>
    <h3>*Mandatory</h3>

    <form method="post" action="" enctype="multipart/form-data" name="fForm" id="fForm">
        <div align="center">
            <table border="0">
                <caption>Staff Details</caption>
                <tr>
                    <td class="move"><label for="stfName">*Staff Name: </label></td>
                    <td><input type="text" name="stfName" id="stfName" maxlength="50" size="52" pattern="[A-Za-z\s]{3,50}" title="3-50 characters required(letters only)" required="required"></td>
                    <td><label for="image">*Upload a picture: </label><input type="file" name="image" id="image" accept="image/*" onchange="readURL();" class="button" required></td>
                </tr>
                <tr>
                    <td class="move"><label for="stfIC">*Staff IC: </label></td>
                    <td><input type="text" name="stfIC" id="stfIC" maxlength="14" pattern="[0-9]{1,14}" title="Numbers only(14 max)" size="16" required></td>
                    <td rowspan="7" align="center"><img src="Images/no%20image%20selected.gif" id="uploadPreview" style="width: 100px; height: 100px;" /></td>
                </tr>
                <tr>
                    <td class="move"><label for="stfGen">Gender: </label></td>
                    <td><select name="stfGen" id="stfGen">
                            <option selected="selected" value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="move"><label for="stfDOB">*Date of Birth: </label></td>
                    <td><input type="date" name="stfDOB" id="stfDOB" max="<?php echo $Mtime ?>" required></td>
                </tr>
                <tr>
                    <td class="move"><label for="stfPos">Position: </label></td>
                    <td><select name="stfPos" id="stfPos">
                            <option selected="selected" value="Employed">Employed</option>
                            <option value="Contracted">Contracted</option>
                        </select>
                    </td>
                </tr>
            </table>
            <br/>
            <table cellpadding="6" border="0">
                <caption>Contact Details</caption>
                <tr>
                    <td class="move"><label for="stfCNO">*Contact Number: </label></td>
                    <td><input type="number" name="stfCNO" id="stfCNO" min="0" step="1" title="Numbers only" required> </td>
                </tr>
                <tr>
                    <td class="move"><label for="stfAdd">*Address: </label></td>
                    <td><textarea name="stfAdd" id="stfAdd" maxlength="250" cols="45" rows="5" required></textarea></td>
                </tr>
                <tr>
                    <td class="move"><label for="stfEm">*Email: </label></td>
                    <td><input type="email" name="stfEm" id="stfEm" maxlength="40" size="42" required> </td>
                </tr>
            </table>
            <br />
            <label id="sError"></label>
            <table align="center" cellpadding="6" border="0">
                <caption>*Qualifications</caption>
                <tr>
                    <td></td>
                    <td><label for="institution1">*Institution</label></td>
                    <td><label for="level1">*Level</label></td>
                    <td><label for="specialization1">Specialization</label></td>
                    <td><label for="graduateYr1">*Graduate Year</label></td>
                </tr>
                <tr>
                    <td>1.</td>
                    <td><input type="text" name="institution1" id="institution1" size="52" maxlength="50" placeholder="Institution" required/></td>
                    <td><select name="level1" id="level1">
                            <option></option>
                            <option value="SPM" selected="selected">SPM</option>
                            <option value="Diploma">Diploma</option>
                            <option value="Degree">Degree</option>
                            <option value="Master">Master</option>
                            <option value="PhD">PhD</option>
                        </select></td>
                    <td><input type="text" maxlength="50" name="specialization1" id="specialization1" size="52" placeholder="Specialization" title="Leave blank if none" /></td>
                    <td><input type="text" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                               name="graduateYr1" size="15" id="graduateYr1" placeholder="Graduate Year" required/></td>
                </tr>
            </table>
            <table align="center" cellpadding="6" border="0" id="tblEducation">
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
            <label id="qError"></label>
            <table align="center" cellpadding="6" border="0">
                <caption>Work Experience(if any)</caption>
                <tr>
                    <td></td>
                    <td>Company</td>
                    <td>Position</td>
                    <td>From Year</td>
                    <td>-</td>
                    <td>To Year</td>
                </tr>
                <tr>
                    <td>1.</td>
                    <td><input type="text" name="company1" id="company1" size="52" maxlength="50" placeholder="Company"/></td>
                    <td><input type="text" name="position1" id="position1" size="27" maxlength="25" placeholder="Position"/></td>
                    <td><input type="text" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                               name="workFrom1" size="8" id="workFrom1" placeholder="From Year"/></td>
                    <td>-</td>
                    <td><input type="text" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                               name="workTo1" size="6" id="workTo1" placeholder="To Year"/></td>
                </tr>
            </table>
            <table align="center" cellpadding="6" border="0" id="tblWork">
            </table>
            <table align="center" cellpadding="6" border="0">
                <tr>
                    <td colspan="6" style="text-align:center">
                        <input type="button" name="plus" id="plus" value="+" onclick="plusWork()"  class="button site"/>
                        <input type="button" name="minus" id="minus" value="-" onclick="minusWork()" class="button site"/>
                    </td>
                </tr>
            </table>
            <br/>
            <table align="center" cellpadding="6" border = "0">
                <caption>Comments(Optional)</caption>
                <tr>
                    <td class="move" style="table-layout: fixed">Remarks: </td>
                    <td><textarea name="stfRem" id="stfRem" rows="5" cols="52" maxlength="500"></textarea> </td>
                </tr>
            </table>

            <br /><br />
            <input type="hidden" name="counterEdu" id="counterEdu" value="1"/><!-- Qualification counter -->
            <input type="hidden" name="counterWork" id="counterWork" value="1"/><!-- Work Experience counter -->
            <input type="submit" name="btnSub" class="button site" style="width: 100px" id="btnSub" value="Register"/>
        </div>
    </form>
<?php } ?>
</div>
</body>