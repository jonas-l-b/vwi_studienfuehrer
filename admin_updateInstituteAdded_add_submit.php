<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$id = $_POST['id'];
$name = $_POST['name'];
$abbr = $_POST['abbr'];

$sql1 = "
	INSERT INTO `institutes`(`name`, `abbr`, `user_ID`, `time_stamp`, `active`)
	VALUES ('$name', '$abbr', ".$userRow['user_ID'].", now(), 1)
";
$sql2 = "DELETE FROM `ADDED_INSTITUTES` WHERE `ID` = $id";

if(mysqli_query($con, $sql1)){
	echo "Hinzufügen erfolgreich.";
	if(mysqli_query($con, $sql2)){
		echo " Löschen aus dieser Tabelle erfolgreich.";
	}else{
		echo " Löschen aus dieser Tabelle nicht erfolgreich.";
	}
}else{
	echo "Hinzufügen nicht erfolgreich. Das kann verschiedene Gründe haben - bspw. könnte der Veranstaltungsname bereits existieren.";
}
?>