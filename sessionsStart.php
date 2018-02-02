<?php

session_start();
include 'connect.php';

function endsWith1($haystack, $needle)
{
    $length = strlen($needle);

    return $length === 0 ||
    (substr($haystack, -$length) === $needle);
}

if (!isset($_SESSION['userSession'])) {
  $url =  urlencode((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
	if(endsWith1($url, urlencode('vwi-karlsruhe.de')) || endsWith1($url, urlencode('vwi-karlsruhe.de/')) || endsWith1($url, urlencode('login.php'))){
		echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='login.php';</SCRIPT>");
	}else{
		echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='login.php?url=$url';</SCRIPT>");
	}

}

$query = $con->query("SELECT * FROM users WHERE user_ID=".$_SESSION['userSession']);
$userRow=$query->fetch_array();
$con->close();


?>
