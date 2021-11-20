<?php
/**Determines the menubar to be used in the project
 */
date_default_timezone_set("Europe/London"); //Sets timezone to UK
$Mtime = date('Y-m-d'); //Get current date
$Etime = date('Y-m-d H:i:s'); //Get current datetime
if($_SESSION['log'] == 'a') //Admin logged in
{
    $_SESSION['Login'] = true;
    include("Admin Menubar.php"); //Admin menubar is used
}
else if($_SESSION['log'] == 's') //Staff logged in
{
    $_SESSION['Login'] = true; //Staff menubar is used
    include("Staff Menubar.php");
}
else //Default menubars and global details for unlogged users
{
    $_SESSION['Username'] = "";
    $_SESSION['UserId'] = "";
    $_SESSION['AccType'] = "";
    $_SESSION['Login'] = false;
    include("Visitor Menubar.php"); //Default menubar
}