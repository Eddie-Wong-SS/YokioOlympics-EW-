<?php
/** Menubar for the usage of the administrator */
session_start();
?>
<link rel="stylesheet" type = "text/css" href="Menubar.css" />
<!-- Custom table styles -->
<style>
    .navigate{background-color: transparent !important; border-color: transparent !important; table-layout: initial}
</style>
<div align="right" class="navbar">
    <table class="navigate" border="0">
        <tr>
            <td class="paddy"><div class="dropdown"><a href="Main%20Page.php" style="color: #8ffcff;text-decoration: none;display: block; padding: 16px;
    font-size: 16px;"> Main Menu </a> </div></td>
            <td>
                <div class="dropdown">
                    <button class="dropbtn"> Staff &#9660</button>
                    <div class="dropdown-content">
                        <a href="addStaff.php">Staff Registration</a>
                        <a href="viewStaff.php">Staff View</a>
                        <a href="addStaffAcc.php">New Staff Accounts</a>
                        <a href="viewAcc.php">View Staff Accounts</a>
                    </div>
                </div>
            </td>
            <td>
                <div class="dropdown">
                    <button class="dropbtn"> Venues &#9660</button>
                    <div class="dropdown-content">
                        <a href="viewVenue.php">View Venues</a>
                    </div>
                </div>
            </td>
            <td>
                <div class="dropdown">
                    <button class="dropbtn"> Sports &#9660</button>
                    <div class="dropdown-content">
                        <a href="addSport.php">Add Sport</a>
                        <a href="viewSport.php">View Sports</a>
                    </div>
                </div>
            </td>
            <td>
                <div class="dropdown">
                    <button class="dropbtn"> Schedule &#9660</button>
                    <div class="dropdown-content">
                        <a href="viewOpenSports.php">Add To Schedule</a>
                        <a href="viewSchedule.php">View Schedule</a>
                        <a href="viewBookings.php">View Bookings</a>
                    </div>
                </div>
            </td>
            <td>
                <div class="dropdown">
                    <button class="dropbtn"> News &#9660</button>
                    <div class="dropdown-content">
                        <a href="addNews.php">Add News</a>
                        <a href="viewNews.php">View News</a>
                    </div>
                </div>
            </td>
            <td class="paddy"><div class="dropdown"><button class="dropbtn">Report &#9660</button>
                    <div class="dropdown-content">
                        <a href="viewBookTick.php">Bookings and Tickets Report</a>
                    </div> </div> </td>

            <td class="paddy"><div class="dropdown"><button class="dropbtn"> <?php echo $_SESSION['Username']."&#9660"; ?> </button>
                    <div class="dropdown-content">
                        <a href="Main Page.php?log=o">Logout</a>
                    </div> </div> </td>
        </tr>
    </table>
</div>
<br />
