<?php
/**
Allows for the editing of the sport, or viewing by visitors
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
            body: 'Modification of the sport <?php echo $_POST['sName']; ?> into the database was successful',
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
        oFReader.readAsDataURL(document.getElementById("image").files[0]); //Gets image data

        oFReader.onload = function (oFREvent) {
            document.getElementById("uploadPreview").src = oFREvent.target.result; //Shows image preview
        };
    }
</script>
<title>Sport Details</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<?php
if($_REQUEST['btnSub']) //Submit button is clicked
{
    $imgSQL = "SELECT sportImg FROM tblsports WHERE  sportID = '".$_GET['Id']."'"; //Gets existing image
    $imgSQLResult = mysqli_query($Link, $imgSQL);
    if(mysqli_num_rows($imgSQLResult) > 0) //Image found
    {
        $Rows = mysqli_fetch_array($imgSQLResult);
    }

    if($_FILES['image']['size'] != 0) //New image is uploaded
    {
        $target_path = "Images/";
        $target_path = $target_path . "Sport".$_POST['vName'].".png"; //Rename new image
        //checking if file exsists
        if(file_exists($target_path)) unlink($target_path); //Delete old image
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path); //Set new image to image folder
    }
    else //No new image is uploaded
    {
        $target_path = $Rows['sportImg']; //Use existing image
    }

    //Update sport record
    $editVenueSQL = "UPDATE tblsports SET
              sportName = '" . strtoupper(trim($_POST['sName'])) . "',
              sportDescp = '" . strtoupper(trim($_POST['sDescp'])) . "',
              sportImg = '$target_path'
              WHERE sportID = '" . strtoupper(trim($_GET['Id'])) . "'
              ";
    $editVenueSQLResult = mysqli_query($Link, $editVenueSQL);
    if($editVenueSQLResult) //Updated successful
    {
        //Notify user
        ?>
        <script>checkers();</script>

        <?php
    }
    //Updated failed
    else echo "<script>alert('A problem has occured when updating the record, contact an administrator');</script>";
}
?>

<body>
<div class="container" style="width: 90%">
    <?php
    if($_GET['Id'] != "") //Checks if $_GET values exist
    {
        $SQL = "SELECT * FROM tblsports WHERE  sportID = '".$_GET['Id']."'"; //Get corresponding record
        $SQLResult = mysqli_query($Link, $SQL);
        if(mysqli_num_rows($SQLResult) > 0)//record exists
        {
            $Row = mysqli_fetch_array($SQLResult);//Stores values to populate HTML form
        }
    }
    else //No $_GET value found
    {
        echo"<script>alert(\"An error has occured, please contact an administrator\")";
    }

    if($_SESSION['AccType'] == "ADMIN" || $_SESSION['AccType'] == "STAFF")
    { ?>
        <form method="post" action="" enctype="multipart/form-data" name="fForm" id="fForm">
            <label for="image">*Upload a picture: </label><input type="file" name="image" id="image" accept="image/*" onchange="readURL();" class="button"><br/>
            <img src="<?php echo $Row['sportImg']; ?>" name="uploadPreview" id="uploadPreview" style="width: 95%; height: 300px;" />
            <h3>*Mandatory</h3>
            <div align="center">
                <table border="0">
                    <caption>Venue Details</caption>
                    <tr>
                        <td class="move"><label for="sName">Sport Name: </label></td>
                        <td><input type="text" name="sName" id="sName" value="<?php echo $Row['sportName']; ?>" readonly></td>
                    </tr>
                    <tr>
                        <td class="move" style="table-layout: fixed"><label for="sDescp">*Sport Description: </label></td>
                        <td><textarea name="sDescp" id="sDescp" rows="5" cols="75" maxlength="1000" required><?php echo $Row['sportDescp']; ?></textarea> </td>
                    </tr>
                </table>
                <br /><br />
                <input type="submit" name="btnSub" class="button site" style="width: 100px" id="btnSub" value="Edit"/>
            </div>
        </form>
    <?php }
    else //Default view for visitors
    {?>
        <img src="<?php echo $Row['sportImg']; ?>" name="uploadPreview" id="uploadPreview" style="width: 95%; height: 300px;" />
        <div align="center">
            <table border="0">
                <caption>Venue Details</caption>
                <tr>
                    <td class="move"><label for="sName">Sport Name: </label></td>
                    <td><input type="text" name="sName" id="sName" value="<?php echo $Row['sportName']; ?>" readonly></td>
                </tr>
                <tr>
                    <td class="move" style="table-layout: fixed"><label for="sDescp">*Sport Description: </label></td>
                    <td><textarea name="sDescp" id="sDescp" rows="5" cols="75" maxlength="1000" readonly><?php echo $Row['sportDescp']; ?></textarea> </td>
                </tr>
            </table>
            <br /><br />
        </div>
    <?php } ?>
</div>
</body>