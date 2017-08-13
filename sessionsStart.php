<?php
//Speichert User-Infos in $userRow

session_start();

include 'connect.php';

if (!isset($_SESSION['userSession'])) {
 header("Location: login.php");
}

$query = $con->query("SELECT * FROM users WHERE user_ID=".$_SESSION['userSession']);
$userRow=$query->fetch_array();
$con->close();


?>