<?php
/**
Allows for the editing of the venue, or viewing by visitors(Only 3 venues are used, so no adding or deleting of venues)
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
            body: 'Modification of the venue <?php echo $_POST['vName']; ?> into the database was successful',
            icon: 'icon.png',
            timeout: 8000,                  // Timeout before notification closes automatically.
            onClick: function() {
                // Callback for when the notification is clicked.
                console.log(this);
            }
        });

    }

    function readURL() //Gets image and shows on page as preview
    {
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("image").files[0]); //read image data

        oFReader.onload = function (oFREvent) {
            document.getElementById("uploadPreview").src = oFREvent.target.result; //Shows image as preview
        };
    }

</script>
<title>Venue Details</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<?php
if($_REQUEST['btnSub']) //Submit button is clicked
{
    $imgSQL = "SELECT venueImg FROM tblvenue WHERE  venueName = '".$_GET['Id']."'"; //Gets existing image
    $imgSQLResult = mysqli_query($Link, $imgSQL);
    if(mysqli_num_rows($imgSQLResult) > 0) //Existing image found
    {
        $Rows = mysqli_fetch_array($imgSQLResult);
    }

    if($_FILES['image']['size'] != 0) //New image was uploaded
    {
        $target_path = "Images/";
        $target_path = $target_path . "venue".$_POST['vName'].".png"; //Rename image
        //checking if file exsists
        if(file_exists($target_path)) unlink($target_path); //Remove old image
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path); //Set new image path to image folder
    }
    else //No new image uploaded
    {
        $target_path = $Rows['venueImg']; //Original image continues to be used instead
    }

    //Update venue record
    $editVenueSQL = "UPDATE tblvenue SET
              venueName = '" . strtoupper(trim($_POST['vName'])) . "',
              venueDescp = '" . strtoupper(trim($_POST['vDescp'])) . "',
              venueImg = '$target_path'
              WHERE venueName = '" . strtoupper(trim($_GET['Id'])) . "'
              ";
    $editVenueSQLResult = mysqli_query($Link, $editVenueSQL);
    if($editVenueSQLResult) //Update successful
    {
        //Notify user
        ?>
        <script>checkers();</script>

        <?php
    }
    //Update unsuccessful
    else 
    {
        echo "<script>alert('A problem has occured when updating the record, contact an administrator');</script>";
        echo $editVenueSQL;
    }
}
?>

<body>
<div class="container" style="width: 90%">
    <?php
    if($_GET['Id'] != "") //Checks if $_GET has value
    {
        $SQL = "SELECT * FROM tblvenue WHERE  venueName = '".$_GET['Id']."'"; //Gets corresponding record
        $SQLResult = mysqli_query($Link, $SQL);
        if(mysqli_num_rows($SQLResult) > 0)//Record exists
        {
            $Row = mysqli_fetch_array($SQLResult);//Stores values to populate HTML form
        }
    }
    else//$_GET has no values
    {
        echo"<script>alert(\"An error has occured, please contact an administrator\")";
    }

    if($_SESSION['AccType'] == "ADMIN" || $_SESSION['AccType'] == "STAFF")
    { ?>
        <form method="post" action="" enctype="multipart/form-data" name="fForm" id="fForm">
            <label for="image">*Upload a picture: </label><input type="file" name="image" id="image" accept="image/*" onchange="readURL();" class="button"><br/>
            <img src="<?php echo $Row['venueImg']; ?>" name="uploadPreview" id="uploadPreview" style="width: 95%; height: 300px;" />
            <h3>*Mandatory</h3>
            <div align="center">
                <table border="0">
                    <caption>Venue Details</caption>
                    <tr>
                        <td class="move"><label for="vName">Venue Name: </label></td>
                        <td><input type="text" name="vName" id="vName" value="<?php echo $Row['venueName']; ?>" readonly></td>
                    </tr>
                    <tr>
                        <td class="move" style="table-layout: fixed"><label for="vDescp">*Venue Description: </label></td>
                        <td><textarea name="vDescp" id="vDescp" rows="5" cols="52" maxlength="1000" required><?php echo $Row['venueDescp']; ?></textarea> </td>
                    </tr>
                </table>
                <br /><br />
                <input type="submit" name="btnSub" class="button site" style="width: 100px" id="btnSub" value="Edit"/>
            </div>
        </form>
    <?php }
    else //Default view for visitors
    {?>
        <img src="<?php echo $Row['venueImg']; ?>" name="uploadPreview" id="uploadPreview" style="width: 95%; height: 300px;" />
        <div align="center">
            <table border="0">
                <caption>Venue Details</caption>
                <tr>
                    <td class="move"><label for="vName">Venue Name: </label></td>
                    <td><input type="text" name="vName" id="vName" value="<?php echo $Row['venueName']; ?>" readonly></td>
                </tr>
                <tr>
                    <td class="move" style="table-layout: fixed"><label for="vDescp">*Venue Description: </label></td>
                    <td><textarea name="vDescp" id="vDescp" rows="5" cols="52" maxlength="1000" readonly><?php echo $Row['venueDescp']; ?></textarea> </td>
                </tr>
            </table>
            <br /><br />
        </div>
    <?php } ?>
</div>
</body>