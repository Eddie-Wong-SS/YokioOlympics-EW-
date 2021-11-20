<?php
/**
Allows for the editing of staff
 */
error_reporting(1); //Hide error information from end users
session_start();
include("database.php"); //Checks database integrity and recreate database/tables if not
include("Menu.php"); //Includes menubar
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/push.js/0.0.11/push.min.js"></script> <!-- push.js.master library for notifications -->
<script>

    Push.Permission.request(); //Browser will request permission to display notifications
    function checkers() //push.js.master notification function
    {
        Push.create('Successfully Edited!', { //Create notification
            body: 'Modification of the staff member <?php echo $_POST['stfName']; ?> account into the database was successful',
            icon: 'icon.png',
            timeout: 8000,                  // Timeout before notification closes automatically.
            onClick: function() {
                // Callback for when the notification is clicked.
                console.log(this);
            }
        });

    }


    function readURL() //Gets and shows image preview
    {
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("image").files[0]); //Gets image data from input type file

        oFReader.onload = function (oFREvent) {
            document.getElementById("uploadPreview").src = oFREvent.target.result; //display image preview
        };
    }

    //Enables "Check All" button functionality for qualifications
    function toggleq(MaxCheck) {
        var i = 0;
        if(document.getElementById('chkAllq').checked === true) //Checks all checkboxes
        {
            for( i = 0; i <= MaxCheck; i++)
            {
                document.getElementById('Recq' + i).checked = true;
            }
        }

        if(document.getElementById('chkAllq').checked === false) //Unchecks all checkboxes
        {
            for( i = 0; i <= MaxCheck; i++)
            {
                document.getElementById('Recq' + i).checked = false;
            }
        }
    }

    //Enables "Check All" button functionality for work experience
    function togglew(MaxCheck) {
        var i = 0;
        if(document.getElementById('chkAllw').checked === true) //Checks all checkboxes
        {
            for( i = 0; i <= MaxCheck; i++)
            {
                document.getElementById('Recw' + i).checked = true;
            }
        }

        if(document.getElementById('chkAllw').checked === false) //Unchecks all checkboxes
        {
            for( i = 0; i <= MaxCheck; i++)
            {
                document.getElementById('Recw' + i).checked = false;
            }
        }
    }
</script>
<title>Edit Staff</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<?php
if($_REQUEST['btnDelq']) //Deletes records for staff qualifications
{
    // while(list($key,$val) = each($_POST)) 'each' function is deprecated
    foreach($_POST['Qual'] as $key) //Gets the checkbox value
    {
        if($key != "chkAllq" && $key != "btnDelq") //Ensures "Check All" and deletion button are not deleted
        {
            $DelEmpSQL = "DELETE FROM tblstaffqual WHERE ID = '".$key."'";
            $DelEmpResult = mysqli_query($Link, $DelEmpSQL);
        }
    }
    if($DelEmpResult) //Deletion successful
    {
        $id = $_GET['Id'];
        echo "<script>alert('Selected record(s) has been deleted');location='editStaff.php?Id=$id';</script>"; //Reloads page
    }
}
else if($_REQUEST['btnDelw']) //Deletes records for staff work experience
{
    // while(list($key,$val) = each($_POST)) each function is deprecated
    foreach($_POST['Work'] as $key) //Gets the checkbox value
    {
        if($key != "chkAllw" && $key != "btnDelw") //Ensures "Check All" and deletion button are not deleted
        {
            $DelEmpSQL = "DELETE FROM tblworkexp WHERE ID = '".$key."'";
            $DelEmpResult = mysqli_query($Link, $DelEmpSQL);
        }
    }
    if($DelEmpResult) //Deletion successful
    {
        $id = $_GET['Id'];
        echo "<script>alert('Selected record(s) has been deleted');location='editStaff.php?Id=$id';</script>"; //reloads page
    }
}
else if($_REQUEST['btnSub']) //Submits updated record for editing
{
    //Gets original staff profile pic
    $imgSQL = "SELECT * FROM tblstaff WHERE  StaffIC = '".$_GET['Id']."'";
    $imgSQLResult = mysqli_query($Link, $imgSQL);
    if(mysqli_num_rows($imgSQLResult) > 0)
    {
        $Rows = mysqli_fetch_array($imgSQLResult);
    }

    if($_FILES['image']['size'] != 0) //New image was uploaded
    {
        $target_path = "Images/";
        $target_path = $target_path . "staff".$_POST['stfIC'].".png"; //Rename image
        //checking if file exsists
        if(file_exists($target_path)) unlink($target_path); //Remove old image
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path); //Move new image to image folder
    }
    else //No new image was uploaded, continue using original image
    {
        $target_path = $Rows['imageLoc']; //Existing image set
    }

    if($_POST['stfRem'] == "") $remark = ""; //Checks if new remarks are added(If originally no remarks)
    else $remark = strtoupper(trim($_POST['stfRem']));


    $accType = "STAFF";

    //Update staff records
    $editStaffSQL = "UPDATE tblstaff SET 
              StaffName = '" . strtoupper(trim($_POST['stfName'])) . "',
              Gender = '" . strtoupper(trim($_POST['stfGen'])) . "',
              Address = '" . strtoupper(trim($_POST['stfAdd'])) . "',
              DOB = '" . strtoupper(trim($_POST['stfDOB'])) . "',
              Phone = '" . strtoupper(trim($_POST['stfCNO'])) . "',
              Email = '" . strtoupper(trim($_POST['stfEm'])) . "',
              Position = '" . strtoupper(trim($_POST['stfPos'])) . "',
              Remark = '$remark',
              imageLoc = '$target_path'
              WHERE StaffIC = '" . strtoupper(trim($_GET['Id'])) . "'
              ";
    $editStaffSQLResult = mysqli_query($Link, $editStaffSQL);

    for ($i = 0; $i < $_POST['cQual']; ++$i) //Update each qualification row
    {
        $editQualSQL = "UPDATE tblStaffQual SET
                StaffIC = '" . strtoupper(trim($_POST['stfIC'])) . "',
                Qualification = '" . strtoupper(trim($_POST['level' . $i])) . "',
                Specialization = '" . strtoupper(trim($_POST['specialization' . $i])) . "',
                School = '" . strtoupper(trim($_POST['institution' . $i])) . "',
                gradYear = '" . strtoupper(trim($_POST['graduateYr' . $i])) . "'
                WHERE ID = '" . strtoupper(trim($_POST['qID'. $i])) . "'
                ";
        $editQualSQLResult = mysqli_query($Link, $editQualSQL);
    }

    for ($f = 0; $f < $_POST['cWork']; ++$f) //Update each work experience row
    {
        $editWorkSQL = "UPDATE tblworkexp SET
                StaffIC = '" . strtoupper(trim($_POST['stfIC'])) . "',
                Position = '" . strtoupper(trim($_POST['position' . $f])) . "',
                Company = '" . strtoupper(trim($_POST['company' . $f])) . "',
                fromYear = '" . strtoupper(trim($_POST['workFrom' . $f])) . "',
                toYear = '" . strtoupper(trim($_POST['workTo' . $f])) . "'
                WHERE ID = '" . strtoupper(trim($_POST['wID' . $f])) . "'
                ";
        $editWorkSQLResult = mysqli_query($Link, $editWorkSQL);
    }

    //Call notification to inform user of success
    ?>
    <script>checkers();</script>

    <?php
}
?>

<body>
<div class="container" style="width: 90%">
    <h1>Staff Record</h1>

    <?php
    if($_GET['Id'] != "") //Ensures $_GET is in the URL
    {
        $SQL = "SELECT * FROM tblstaff WHERE  StaffIC = '".$_GET['Id']."'"; //Calls corresponding staff record as $_GET value
        $SQLResult = mysqli_query($Link, $SQL);
        if(mysqli_num_rows($SQLResult) > 0)
        {
            $Row = mysqli_fetch_array($SQLResult);
            //Sets default values in gender selection fields
            if($Row['Gender'] == 'M') //Staff gender is male
            {
                $select = "Male";
                $notselect = "Female";
                $val = 'M';
                $notval = 'F';
            }
            else //Staff gender is female
            {
                $select = "Female";
                $notselect = "Male";
                $val = 'F';
                $notval = 'M';
            }

            //Sets default values in staff position selection fields
            if($Row['Position'] == "EMPLOYED") //Staff is employed position
            {
                $PSelect = "Employed";
                $NotPSelect = "Contracted";
            }
            else //Staff is contracted position
            {
                $PSelect = "Contracted";
                $NotPSelect = "Employed";
            }
        }
    }
    else //Failed to obtain a $_GET variable
    {
        echo"<script>alert(\"An error has occured, please contact an administrator\")";
    }

    if($_SESSION['AccType'] == 'STAFF') //Shows an uneditable staff record if staff is viewing
    {
        ?>
        <table border="0">
            <caption>Staff Details</caption>
            <tr>
                <td class="move"><label for="stfName">*Staff Name: </label></td>
                <td><?php echo $Row['StaffName']; ?></td>
            </tr>
            <tr>
                <td class="move"><label for="stfIC">*Staff IC: </label></td>
                <td><?php echo $Row['StaffIC']; ?></td>
                <td rowspan="7" align="center"><img name="uploadPreview" src="<?php echo $Row['imageLoc']; ?>" id="uploadPreview" style="width: 100px; height: 100px;" /></td>
            </tr>
            <tr>
                <td class="move"><label for="stfGen">Gender: </label></td>
                <td><?php echo $select; ?></td>
            </tr>
            <tr>
                <td class="move"><label for="stfDOB">*Date of Birth: </label></td>
                <td><?php echo $Row['DOB']; ?></td>
            </tr>
            <tr>
                <td class="move"><label for="stfPos">Position: </label></td>
                <td><?php echo $PSelect; ?></td>
            </tr>
        </table>
        <br/>
        <table cellpadding="6" border="0">
            <caption>Contact Details</caption>
            <tr>
                <td class="move"><label for="stfCNO">*Contact Number: </label></td>
                <td><?php echo $Row['Phone']; ?></td>
            </tr>
            <tr>
                <td class="move"><label for="stfAdd">*Address: </label></td>
                <td><?php echo $Row['Address']; ?></td>
            </tr>
            <tr>
                <td class="move"><label for="stfEm">*Email: </label></td>
                <td><?php echo $Row['Email']; ?></td>
            </tr>
        </table>
        <br />
        <?php $SQL = "SELECT * FROM tblstaffqual WHERE StaffIC = '".$_GET['Id']."' ORDER BY gradYear"; //Gets corresponding staff qualification records
        $Result = mysqli_query($Link, $SQL); ?>
        <table align="center" cellpadding="6" border="0">
            <caption>Qualifications</caption>
            <tr>
                <td></td>
                <td><label for="institution1">Institution</label></td>
                <td><label for="level1">Level</label></td>
                <td><label for="specialization1">Specialization</label></td>
                <td><label for="graduateYr1">Graduate Year</label></td>
            </tr>
            <?php if(mysqli_num_rows($Result) > 0) //SQL query returns records
            {
                for($i = 0; $i < mysqli_num_rows($Result); ++$i ) //Displays corresponding staff qualification records
                {
                    $RowInfo = mysqli_fetch_array($Result);
                    $cQual = $i + 1; ?>
                    <tr>
                        <td><?php echo ($i+1); ?></td>
                        <td><?php echo $RowInfo['School']; ?></td>
                        <td><?php echo $RowInfo['Qualification']; ?></td>
                        <td><?php echo $RowInfo['Specialization']; ?></td>
                        <td>"<?php echo $RowInfo['gradYear']; ?></td>
                    </tr>
                <?php   }
            }
            ?>
        </table>
        <br/>
        <?php $SQL = "SELECT * FROM tblworkexp WHERE StaffIC = '".$_GET['Id']."' ORDER BY fromYear"; //Gets corresponding staff work experience records
        $Result = mysqli_query($Link, $SQL);?>
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
            <?php if(mysqli_num_rows($Result) > 0) //SQL query returns records
            {
                for($i = 0; $i < mysqli_num_rows($Result); ++$i ) //Displays corresponding staff work experience records
                {
                    $RowInfo = mysqli_fetch_array($Result);
                    $cWork = $i + 1;?>
                    <tr>
                        <td><?php echo ($i+1); ?></td>
                        <td><?php echo $RowInfo['Company']; ?></td>
                        <td><?php echo $RowInfo['Position']; ?></td>
                        <td><?php echo $RowInfo['fromYear']; ?></td>
                        <td>-</td>
                        <td><?php echo $RowInfo['toYear']; ?></td>
                    </tr>
                <?php   }
            }
            ?>
        </table>
        <br/>
        <table align="center" cellpadding="6" border = "0">
            <caption>Comments(Optional)</caption>
            <tr>
                <td class="move" style="table-layout: fixed">Remarks: </td>
                <td style="text-align: center"><?php echo $Row['Remark']; ?></td>
            </tr>
        </table>
    <?php }
    else if($_SESSION['AccType'] == "ADMIN") //Admins can edit staff records, all fields except IC can be edited
    { ?>
        <h3>*Mandatory</h3>
        <form method="post" action="" enctype="multipart/form-data" name="fForm" id="fForm">
            <div align="center">
                <table border="0">
                    <caption>Staff Details</caption>
                    <tr>
                        <td class="move"><label for="stfName">*Staff Name: </label></td>
                        <td><input type="text" name="stfName" id="stfName" value="<?php echo $Row['StaffName']; ?>" maxlength="50" size="52" pattern="[A-Za-z\s]{3,50}" title="3-50 characters required" required></td>
                        <td><label for="image">*Upload a picture: </label><input type="file" name="image" id="image" accept="image/*" onchange="readURL();" class="button"></td> <!-- Allows new image upload -->
                    </tr>
                    <tr>
                        <td class="move"><label for="stfIC">*Staff IC: </label></td>
                        <td><input type="text" name="stfIC" id="stfIC" maxlength="14" size="16" value="<?php echo $Row['StaffIC']; ?>" pattern="[0-9]+" style = "background-color: lightgray" readonly></td>
                        <td rowspan="7" align="center"><img src="<?php echo $Row['imageLoc']; ?>" name="uploadPreview" id="uploadPreview" style="width: 100px; height: 100px;" /></td> <!-- Shows image preview -->
                    </tr>
                    <tr>
                        <td class="move"><label for="stfGen">Gender: </label></td>
                        <td><select name="stfGen" id="stfGen">
                                <option selected="selected" value="<?php echo $val; ?>"><?php echo $select; ?></option>
                                <option value="<?php echo $notval; ?>"><?php echo $notselect ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="move"><label for="stfDOB">*Date of Birth: </label></td>
                        <td><input type="date" name="stfDOB" id="stfDOB" value="<?php echo $Row['DOB']; ?>" max="<?php echo $Mtime; ?>" required></td>
                    </tr>
                    <tr>
                        <td class="move"><label for="stfPos">Position: </label></td>
                        <td><select name="stfPos" id="stfPos">
                                <option selected="selected" value="<?php echo $PSelect; ?>"><?php echo $PSelect; ?></option>
                                <option value="<?php echo $NotPSelect; ?>"><?php echo $NotPSelect; ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                <br/>
                <table cellpadding="6" border="0">
                    <caption>Contact Details</caption>
                    <tr>
                        <td class="move"><label for="stfCNO">*Contact Number: </label></td>
                        <td><input type="text" name="stfCNO" id="stfCNO" min="0" step="1" oninput="this.value=this.value.replace(/[^0-9]/g,'');" title="Numbers only" value="<?php echo $Row['Phone']; ?>" required> </td>
                    </tr>
                    <tr>
                        <td class="move"><label for="stfAdd">*Address: </label></td>
                        <td><textarea name="stfAdd" id="stfAdd" maxlength="250" cols="45" rows="5" required><?php echo $Row['Address']; ?></textarea></td>
                    </tr>
                    <tr>
                        <td class="move"><label for="stfEm">*Email: </label></td>
                        <td><input type="email" name="stfEm" id="stfEm" maxlength="40" size="42" value="<?php echo $Row['Email']; ?>" required> </td>
                    </tr>
                </table>
                <br />
                <label id="sError"></label>
                <table align="center" cellpadding="6" border="0">
                    <caption>Qualifications</caption>
                    <tr>
                        <td></td>
                        <?php $count = mysqli_num_rows($Result); //Creates check all button
                        echo "<th scope='col'><input type='checkbox' name=\"chkAllq\" id=\"chkAllq\" onClick=\"toggleq($count)\"></th>"; ?>
                        <td><label for="institution1">Institution</label></td>
                        <td><label for="level1">Level</label></td>
                        <td><label for="specialization1">Specialization</label></td>
                        <td><label for="graduateYr1">Graduate Year</label></td>
                    </tr>
                    <?php $SQL = "SELECT * FROM tblstaffqual WHERE StaffIC = '".$_GET['Id']."' ORDER BY gradYear"; //Gets corresponding staff qualification records
                    $Result = mysqli_query($Link, $SQL);
                    if(mysqli_num_rows($Result) > 0) //SQL query returns records
                    {
                        for($i = 0; $i < mysqli_num_rows($Result); ++$i ) //Displays corresponding staff qualification records
                        {
                            $RowInfo = mysqli_fetch_array($Result);
                            $cQual = $i + 1; ?>
                            <tr>
                                <td><?php echo ($i+1); ?></td>
                                <?php echo "<td><input type=\"checkbox\" name=\"Qual[]\" id=\"Recq".($i)."\" value=\"".$RowInfo['ID']."\"></td>"; ?>
                                <td><input type="text" name="<?php echo 'institution'.$i; ?>" id="<?php echo 'institution'.$i; ?>" size="52" maxlength="50" value = "<?php echo $RowInfo['School']; ?>" readonly/></td>
                                <td><select name="<?php echo 'level'.$i; ?>" id="<?php echo 'level'.$i; ?>">
                                        <option></option>
                                        <option value="<?php echo $RowInfo['Qualification']; ?>" selected="selected"><?php echo $RowInfo['Qualification']; ?></option>
                                        <option value="SPM">SPM</option>
                                        <option value="Diploma">Diploma</option>
                                        <option value="Degree">Degree</option>
                                        <option value="Master">Master</option>
                                        <option value="PhD">PhD</option>
                                    </select></td>
                                <td><input type="text" maxlength="50" name="<?php echo 'specialization'.$i; ?>" id="<?php echo 'specialization'.$i; ?>" size="52" value = "<?php echo $RowInfo['Specialization']; ?>" /></td>
                                <td><input type="text" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                                           name="<?php echo 'graduateYr'.$i; ?>" size="15" id="<?php echo 'graduateYr'.$i; ?>" value = "<?php echo $RowInfo['gradYear']; ?>" required/></td>
                            </tr>
                            <input type="hidden" name="<?php echo 'qID'.$i; ?>" value="<?php echo $RowInfo['ID'] ?>">
                        <?php   }
                        echo "<tr>";
                        echo "<td></td><td></td>";
                        echo "<td ><input type='submit' name='btnDelq' id='btnDelq' value='Delete Checked Records' class='button'></td>";
                        echo "</tr>";
                    }
                    ?>
                    <input type="hidden" name="cQual" value="<?php echo $cQual; ?>">
                </table>
                <br/>
                <?php $SQL = "SELECT * FROM tblworkexp WHERE StaffIC = '".$_GET['Id']."' ORDER BY fromYear"; //Gets corresponding staff work experience records
                $Result = mysqli_query($Link, $SQL);?>
                <label id="qError"></label>
                <table align="center" cellpadding="6" border="0">
                    <caption>Work Experience(if any)</caption>
                    <tr>
                        <td></td>
                        <?php $count = mysqli_num_rows($Result); //Creates check all button
                        echo "<th scope='col'><input type='checkbox' name=\"chkAllw\" id=\"chkAllw\" onClick=\"togglew($count)\"></th>"; ?>
                        <td>Company</td>
                        <td>Position</td>
                        <td>From Year</td>
                        <td>-</td>
                        <td>To Year</td>
                    </tr>
                    <?php if(mysqli_num_rows($Result) > 0) //SQL query returns records
                    {
                        for($i = 0; $i < mysqli_num_rows($Result); ++$i ) //Displays corresponding staff work experiences
                        {
                            $RowInfo = mysqli_fetch_array($Result);
                            $cWork = $i + 1;?>
                            <tr>
                                <td><?php echo ($i+1); ?></td>
                                <?php echo "<td><input type=\"checkbox\" name=\"Work[]\" id=\"Recw".($i)."\" value=\"".$RowInfo['ID']."\"></td>"; ?>
                                <td><input type="text" name="<?php echo 'company'.$i; ?>" id="<?php echo 'company'.$i; ?>" size="52" maxlength="50" value = "<?php echo $RowInfo['Company']; ?>" readonly/></td>
                                <td><input type="text" name="<?php echo 'position'.$i; ?>" id="<?php echo 'position'.$i; ?>" size="27" maxlength="25" value = "<?php echo $RowInfo['Position']; ?>"/></td>
                                <td><input type="text" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                                           name="<?php echo 'workFrom'.$i; ?>" size="8" id="<?php echo 'workFrom'.$i; ?>" value = "<?php echo $RowInfo['fromYear']; ?>"/></td>
                                <td>-</td>
                                <td><input type="text" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');"
                                           name="<?php echo 'workTo'.$i; ?>" size="6" id="<?php echo 'workTo'.$i; ?>" value = "<?php echo $RowInfo['toYear']; ?>"/></td>
                            </tr>
                            <input type="hidden" name="<?php echo 'wID'.$i; ?>" value="<?php echo $RowInfo['ID'] ?>">
                        <?php   }
                        echo "<tr>";
                        echo "<td></td><td></td>";
                        echo "<td><input type='submit' name='btnDelw' id='btnDelw' value='Delete Checked Records' class='button'></td>";
                        echo "</tr>";
                    }
                    ?>
                    <input type="hidden" name="cWork" value="<?php echo $cWork; ?>">
                </table>
                <br/>
                <table>
                    <tr> <!-- Sends admins to a page to add more staff qualifications and records -->
                        <td><a href="addQualExp.php?id=<?php echo $Row['StaffIC']; ?>">Add More Qualifications and Experience</a></td>
                    </tr>
                </table>
                <br/>
                <table align="center" cellpadding="6" border = "0">
                    <caption>Comments(Optional)</caption>
                    <tr>
                        <td class="move" style="table-layout: fixed">Remarks: </td>
                        <td><textarea name="stfRem" id="stfRem" rows="5" cols="52" maxlength="500"><?php echo $Row['Remark']; ?></textarea> </td>
                    </tr>
                </table>
                <br /><br />
                <input type="submit" name="btnSub" class="button site" style="width: 100px" id="btnSub" value="Edit"/>
            </div>
        </form>
    <?php } ?>
</div>
</body>