<?php

include('connect.php');

$sql = "SELECT COUNT(*) AS count FROM subjects;";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	$verCount = $row['count'];
	
$sql = "SELECT COUNT(*) AS count FROM modules;";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	$modCount = $row['count'];
	
$sql = "SELECT COUNT(*) AS count FROM institutes;";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	$insCount = $row['count'];
	
$sql = "SELECT COUNT(*) AS count FROM lecturers;";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	$dozCount = $row['count'];
	

echo json_encode(array('insVal' => $insCount , 'modVal' => $modCount, 'dozVal' => $dozCount, 'verVal' => $verCount));

?>