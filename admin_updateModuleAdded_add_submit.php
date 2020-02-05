<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$id = $_POST['id'];
$name = $_POST['name'];
$code = $_POST['code'];
$type = $_POST['type'];
$ects = $_POST['ects'];

$sql1 = "
	INSERT INTO `modules`(`name`, `code`, `type`, `ects`, `user_ID`, `time_stamp`, `active`)
	VALUES ('$name', '$code', '$type', '$ects', ".$userRow['user_ID'].", now(), 1)
";
$sql2 = "DELETE FROM `ADDED_MODULES` WHERE `ID` = $id";

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