<?php
//** Creates the database of the system and a default administrator account */
//Database used: MySQL on XAMPP v3.2.2
error_reporting();
$Username = "username"; // username used to log into phpMyAdmin
$Pass = "password"; // password used to log into phpMyAdmin
$host = "localhost"; // try "127.0.0.1" should "localhost" not work
$Name = "dbolympic"; //Database name
$TableList = array( //Creates an array storing database records
    "CREATE TABLE tblStaff( 
		StaffIC varchar(14) PRIMARY KEY, 
		StaffName varchar(50),
		Gender CHAR(1),
		Address VARCHAR(250),
		DOB DATE,
		Phone int,
		Email VARCHAR(40),
		Position varchar(20),
		imageLoc varchar(50),
		Remark varchar(500),
		Status char(1) DEFAULT 'A')", //Store general staff details

    "CREATE TABLE tblStaffQual(
        ID int Primary Key AUTO_INCREMENT,
		StaffIC varchar(14),
		Qualification varchar(50),
		Specialization varchar(50),
		School varchar(50),
		gradYear int)", //Stores staff education qualifications

    "CREATE TABLE tblWorkExp(
        ID int Primary Key AUTO_INCREMENT,
		StaffIC varchar(14),
		Position varchar(25),
		Company varchar(50),
		fromYear int(4),
		toYear int(4))", //Stores staff work experiences

    "CREATE TABLE tblVenue(
        venueName varchar(50),
        venueDescp varchar(1000),
        venueImg varchar(100),
        Status char(1) DEFAULT 'A')", //Status is currently unused, placed for possible need to decommission the venue
    //Stores venue details(Venues are set and cannot be changed)

    "CREATE TABLE tblSports(
        sportID int PRIMARY KEY AUTO_INCREMENT,
        sportName varchar(50),
        sportDescp varchar(1000),
        sportImg varchar(100),
        Status char(1) DEFAULT 'A')", //Stores sport records

    "CREATE TABLE tblSchedule(
        schedID int PRIMARY KEY AUTO_INCREMENT,
        sportID int,
        venueName varchar(50),
        eventDate date,
        startTime time,
        endTime time,
        tryStart time,
        tryEnd time,
        Status char(1) DEFAULT 'A')", //Stores schedule for each sport and their details

    "CREATE TABLE tblBooking(
        bookID int PRIMARY KEY AUTO_INCREMENT,
        bookRef char(19),
        schedID int,
        booker varchar(50),
        email varchar(75),
        bookNum int,
        validTill datetime,
        Status char(1) DEFAULT 'A')", //Stores booking records for each schedule

    "CREATE TABLE tblNews(
        newsID int primary key AUTO_INCREMENT,
        newsTitle varchar(100),
        newsDescp varchar(1000),
        newsImg varchar(100),
        userId int,
        writtenOn date,
        Status char(1) DEFAULT 'A')", //Stores news articles

    "CREATE TABLE tbllogin(
		userId int PRIMARY KEY AUTO_INCREMENT,
		IC varchar(14),
		Username VARCHAR(35),
		Password CHAR(40),
		veriCode varchar(8),
		AccType VARCHAR(10),
		Status char(1) DEFAULT 'A'
	)"); //Stores login details
//10 tables
//Password is stored in database as SHA1 hash, which is a set 40 characters long
//Status is used to determine if the account is activated, deactivated or inactive
$Link = mysqli_connect($host, $Username, $Pass) or die("The site is unable to connect to the database, please contact the team's administrator");

if($Link) //Connected to server
{
    if(!mysqli_select_db($Link, $Name)) //Database does not exist
    {
        $SQL = "CREATE DATABASE ". $Name;
        mysqli_query($Link,$SQL);
    }
    mysqli_select_db($Link, $Name); //Connect to corresponding database
    for($i = 0; $i<count($TableList);++$i)
    {
        mysqli_query($Link, $TableList[$i]); //Checks if all tables are in the database and insert any missing tables
    }
    $SQL = "SELECT * FROM tbllogin WHERE Username = 'ADMIN' AND Password = '".sha1("pass")."' AND Status = 'A'"; //Checks if default admin account is in the database
    $Result = mysqli_query($Link, $SQL);
    if(mysqli_num_rows($Result) == 0)
    {
        $SQL = "INSERT INTO tbllogin(IC, Username, Password, AccType, Status) VALUES('12341234', 'ADMIN','".sha1('pass')."','ADMIN','A')";
        $Result = mysqli_query($Link, $SQL); //Inserts a new admin account(for easier system testing)
    }

}
else //Failed to connect
{
    echo "<script language='JavaScript'>alert('Failed to connect');</script>";
}
?>