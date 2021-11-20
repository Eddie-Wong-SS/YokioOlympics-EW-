<?php
/** Default menubar visitors see */
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
                        <a href="viewSport.php">View Sports</a>
                    </div>
                </div>
            </td>
            <td>
                <div class="dropdown">
                    <button class="dropbtn"> Schedule & Tickets &#9660</button>
                    <div class="dropdown-content">
                        <a href="viewSchedule.php">View Schedule</a>
                        <a href="getBookRef.php">View Booking</a>
                    </div>
                </div>
            </td>
            <td>
                <div class="dropdown">
                    <button class="dropbtn"> News &#9660</button>
                    <div class="dropdown-content">
                        <a href="viewNews.php">View News</a>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>
<br />
