<?php


$user = 'root';
$pass = '';
$db = 'studienfuehrer';
$con = mysqli_connect('localhost', $user, $pass, $db) or die("Unable to connect to database!");
$con->set_charset("utf8");

/*
$user = 'YOUR USER';
$pass = 'YOUR PASSWORD';
$db = 'YOUR DATABSE';
$con = mysqli_connect('YOUR LINK', $user, $pass, $db) or die("Unable to connect to database!");
$con->set_charset("utf8");
*/

//if($con) echo ("<br> Sucessfully connected to database $db."); //CHECK

?>