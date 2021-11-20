<?php
/**
Allows for the adding of a new news article
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
            body: 'Adding of news article <?php echo $_POST['aName']; ?> into the database was successful',
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
<title>Add News</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<?php
if($_REQUEST['btnSub']) //Submit button is clicked
{
    $CheckNewsExists = "SELECT * FROM tblnews WHERE writtenOn = '".$Mtime."'"; //Checks if a news article for the day has already been written
    $CheckNewsExistsResult = mysqli_query($Link,$CheckNewsExists);
    if(mysqli_num_rows($CheckNewsExistsResult) > 0) //One exists
    {
        echo "<script>alert('A news article for today has already been written, please delete that first');</script>";
    }
    else //No news article for the day has been written
    {
        $target_path = "Images/";
        $target_path = $target_path . "Article".$Mtime.".png"; //Rename image

        //checking if file exsists
        if(file_exists($target_path)) unlink($target_path); //Deletes old images
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path); //Move new image to image folder

        $AddNewsSQL = "INSERT INTO tblnews(newsTitle, newsDescp, newsImg, userId, writtenOn) VALUES (
                  '" . strtoupper(trim($_POST['aName'])) . "',
                  '" . strtoupper(trim($_POST['aDescp'])) . "',
                  '$target_path',
                  '" .  $_SESSION['Id'] . "',
                  '" . $Mtime . "'
                  )";
        $AddNewsSQLResult = mysqli_query($Link, $AddNewsSQL);

        if($AddNewsSQL) //Insertion successful
        {
            //Notify user
            ?>
            <script>checkers();</script>

            <?php
        }
        //Insertion unsuccessful
        else echo "<script>alert('A problem has occured while adding the record, contact an administrator');</script>";
    }

}
?>

<body>
<div class="container" style="width: 90%">
<?php
if($_SESSION['AccType'] == "ADMIN" || $_SESSION['AccType'] == "STAFF") //Only admins and staff may add news
{ ?>
    <form method="post" action="" enctype="multipart/form-data" name="fForm" id="fForm">
        <label for="image">*Upload a picture: </label><input type="file" name="image" id="image" accept="image/*" onchange="readURL();" class="button"><br/>
        <img src="Images/no%20image%20selected.gif" name="uploadPreview" id="uploadPreview" style="width: 95%; height: 300px;" />
        <h3>*Mandatory</h3>
        <h3>Please be advised only one article a day may be written</h3>
        <div align="center">
            <table border="0">
                <caption>Article Details</caption>
                <tr>
                    <td class="move"><label for="aName">*Article Title: </label></td>
                    <td><input type="text" name="aName" id="aName" maxlength="100" size="102" title="Title must not exceed 100 characters" required></td>
                </tr>
                <tr>
                    <td class="move" style="table-layout: fixed"><label for="aDescp">*Article Content: </label></td>
                    <td><textarea name="aDescp" id="aDescp" rows="5" cols="102" maxlength="1000" required></textarea> </td>
                </tr>
            </table>
            <br /><br />
            <input type="submit" name="btnSub" class="button site" style="width: 100px" id="btnSub" value="Add"/>
        </div>
    </form>
<?php } ?>
</div>
</body>