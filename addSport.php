<?php
/**
Allows for the addition of a new sport
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
        Push.create('Successfully Added!', { //Create notification
            body: 'Adding of the sport <?php echo $_POST['sName']; ?> into the database was successful',
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
<?php
if($_REQUEST['btnSub']) //Submit button is clicked
{
    $target_path = "Images/";
    $target_path = $target_path . "Sport".$_POST['sName'].".png"; //Set image name

    $checkSportSQL = "SELECT sportName from tblsports WHERE sportName = '" . strtoupper(trim($_POST['sName'])) . "'"; //Checks if sport record exists
    $checkSportSQLResult = mysqli_query($Link, $checkSportSQL);

    if(mysqli_num_rows($checkSportSQLResult) > 0)//Record exists
    {
        echo "<script>alert('This sport already exists in the database')";
    }
    else //Sport is not in database
    {
        //checking if file exsists
        if(file_exists($target_path)) unlink($target_path); //Deletes old image
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path); //Moves new image to image folder

        //Create new sport record
        $addSportSQL = "INSERT INTO tblsports(sportName, sportDescp, sportImg) VALUES (
                  '" . strtoupper(trim($_POST['sName'])) . "',
                  '" . strtoupper(trim($_POST['sDescp'])) . "',     
                  '$target_path'
                  )";
        $addSportSQLResult = mysqli_query($Link, $addSportSQL);
        if($addSportSQLResult) //Creating successful
        {
            //Notify user
            ?>
            <script>checkers();</script>

            <?php
        }
        //Creation failed
        else
        {
            echo "<script>alert('A problem has occured when updating the record, contact an administrator');</script>";
        } 
    }
}
?>
<title>Add Sport</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<body>
<div class="container" style="width: 90%">
    <?php if($_SESSION['AccType'] == "ADMIN" || $_SESSION['AccType'] == "STAFF")
    { ?>
        <form method="post" action="" enctype="multipart/form-data" name="fForm" id="fForm">
            <label for="image">*Upload a picture: </label><input type="file" name="image" id="image" accept="image/*" onchange="readURL();" class="button" required><br/>
            <img src="Images/no%20image%20selected.gif" id="uploadPreview" style="width: 95%; height: 300px;" />
            <h3>*Mandatory</h3>
            <div align="center">
                <table border="0">
                    <caption>Sport Details</caption>
                    <tr>
                        <td class="move" style="width: 25%"><label for="sName">*Sport Name: </label></td>
                        <td style="width: 25%"><input type="text" name="sName" id="sName" title="Maximum 50 characters" maxlength="50" required></td>
                    </tr>
                    <tr>
                        <td class="move" style="table-layout: fixed"><label for="sDescp">*Sport Description: </label></td>
                        <td><textarea name="sDescp" id="sDescp" rows="5" cols="75" maxlength="1000" required></textarea> </td>
                    </tr>
                </table>
                <br /><br />
                <input type="submit" name="btnSub" class="button site" style="width: 100px" id="btnSub" value="Add Sport"/>
            </div>
        </form>
    <?php }
     ?>
</div>
</body>