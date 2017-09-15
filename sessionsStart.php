<?php

session_start();
include 'connect.php';

if (!isset($_SESSION['userSession'])) {
	header("Location: login.php"); //to do: Landing mit Message, dann Login
}
/*
$result = mysqli_query($con, "SELECT * FROM users WHERE user_ID=".$_SESSION['userSession']);
$userRow = mysqli_fetch_assoc($result);
$con->close();
*/

$query = $con->query("SELECT * FROM users WHERE user_ID=".$_SESSION['userSession']);
$userRow=$query->fetch_array();
$con->close();


?>