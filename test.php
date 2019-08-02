<?php
$host_name = 'db455676310.db.1and1.com';
$database = 'db455676310';
$user_name = 'dbo455676310';
$password = 'vwiestiemka';
$con_hp = mysqli_connect($host_name, $user_name, $password, $database);

if (mysqli_connect_errno()) {
	die('<p>Verbindung zum MySQL Server fehlgeschlagen: '.mysqli_connect_error().'</p>');
}

$sql = "SELECT * FROM `jom_vwi_semesterprogramm` WHERE event_date_start >= '2019-07-02 00:00:00'";
$result = mysqli_query($con_hp, $sql);

while($row = mysqli_fetch_assoc($result)){
	echo $row["event_name"];
	echo "<br>";
}


  
  
?>