<?php

session_start();
include 'connect.php';

if (!isset($_SESSION['userSession'])) {
	$url =  urlencode((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
	echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='login.php?url=$url';</SCRIPT>");
}

$query = $con->query("SELECT * FROM users WHERE user_ID=".$_SESSION['userSession']);
$userRow=$query->fetch_array();
$con->close();


?>