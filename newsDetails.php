<?php
/**
Allows for the editing of the news article, or viewing by visitors
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
            body: 'Modification of the news article <?php echo $_POST['aName']; ?> into the database was successful',
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
<title>Article Details</title>
<link rel="stylesheet" type = "text/css" href="Default%20Theme.css" />
<?php
if($_REQUEST['btnSub']) //Submit button is clicked
{
    $imgSQL = "SELECT newsImg FROM tblnews WHERE  newsID = '".$_GET['Id']."'"; //Gets existing image
    $imgSQLResult = mysqli_query($Link, $imgSQL);
    if(mysqli_num_rows($imgSQLResult) > 0) //Existing image found
    {
        $Rows = mysqli_fetch_array($imgSQLResult);
    }

    if($_FILES['image']['size'] != 0) //New image was uploaded
    {
        $target_path = "Images/";
        $target_path = $target_path . "Article".$_POST['dates'].".png"; //Rename image
        //checking if file exsists
        if(file_exists($target_path)) unlink($target_path); //Remove old image
        move_uploaded_file($_FILES['image']['tmp_name'], $target_path); //Set new image path to image folder
    }
    else //No new image uploaded
    {
        $target_path = $Rows['newsImg']; //Original image continues to be used instead
    }

    //Update venue record
    $editNewsSQL = "UPDATE tblnews SET
              newsTitle = '" . strtoupper(trim($_POST['aName'])) . "',
              newsDescp = '" . strtoupper(trim($_POST['aDescp'])) . "',
              newsImg = '$target_path'
              WHERE newsID = '" . strtoupper(trim($_GET['Id'])) . "'
              ";
    $editNewsSQLResult = mysqli_query($Link, $editNewsSQL);
    if($editNewsSQLResult) //Update successful
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
    }
}
?>

<body>
<div class="container" style="width: 90%">
    <?php
    if($_GET['Id'] != "") //Checks if $_GET has value
    {
        $SQL = "SELECT tblnews.*, tbllogin.Username FROM tblnews, tbllogin WHERE tbllogin.userId = tblnews.userId AND newsID = '".$_GET['Id']."'"; //Gets corresponding record
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
            <img src="<?php echo $Row['newsImg']; ?>" name="uploadPreview" id="uploadPreview" style="width: 95%; height: 300px;" />
            <h3>*Mandatory</h3>
            <div align="center">
                <table border="0">
                    <caption>Article Details</caption>
                    <tr>
                        <td class="move"><label for="aName">*Article Title: </label></td>
                        <td><input type="text" name="aName" id="aName" maxlength="100" size="102"
                                   title="Title must not exceed 100 characters" style="text-align: center; font-weight: bold"
                                   value="<?php echo $Row['newsTitle'] ?>" required></td>
                    </tr>
                    <tr>
                        <td colspan="2">By: <?php echo $Row['Username'] ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">Written On: <?php echo $Row['writtenOn'] ?></td>
                    </tr>
                    <tr>
                        <td class="move" style="table-layout: fixed"><label for="aDescp">*Article Content: </label></td>
                        <td><textarea name="aDescp" id="aDescp" rows="5" cols="102" maxlength="1000" required><?php echo $Row['newsDescp'] ?></textarea> </td>
                    </tr>
                </table>
                <input type="hidden" name="dates" id="dates" value="<?php echo $Row['writtenOn'] ?>"/>
                <br /><br />
                <input type="submit" name="btnSub" class="button site" style="width: 100px" id="btnSub" value="Edit"/>
            </div>
        </form>
    <?php }
    else //Default view for visitors
    {?>
        <img src="<?php echo $Row['newsImg']; ?>" name="uploadPreview" id="uploadPreview" style="width: 95%; height: 300px;" />
        <div align="center">
            <table border="0">
                <caption>Article Details</caption>
                <tr>
                    <td><input type="text" name="aName" id="aName" maxlength="100" size="102"
                               value="<?php echo $Row['newsTitle'] ?>" style="text-align: center; font-weight: bold" readonly></td>
                </tr>
                <tr>
                    <td>By: <?php echo $Row['Username'] ?></td>
                </tr>
                <tr>
                    <td>Written On: <?php echo $Row['writtenOn'] ?></td>
                </tr>
                <tr>
                    <td><textarea name="aDescp" id="aDescp" rows="5" cols="102" maxlength="1000" readonly><?php echo $Row['newsDescp'] ?></textarea> </td>
                </tr>
            </table>
            <br /><br />
        </div>
    <?php } ?>
</div>
</body>