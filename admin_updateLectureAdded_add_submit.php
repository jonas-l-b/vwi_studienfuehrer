<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$id = $_POST['id'];
$subject_name = $_POST['subject_name'];
$identifier = $_POST['identifier'];
$ECTS = $_POST['ECTS'];
$semester = $_POST['semester'];
$language = $_POST['language'];
$exam_type = $_POST['exam_type'];
$requirements = $_POST['requirements'];
$ilias = $_POST['ilias'];
$modulebook = $_POST['modulebook'];

$sql1 = "
	INSERT INTO `subjects`(`subject_name`, `identifier`, `ECTS`, `semester`, `language`, `exam_type`, `requirements`, `ilias`, `modulebook`, `createdBy_ID`, `time_stamp`, `active`)
	VALUES ('$subject_name', '$identifier', '$ECTS', '$semester', '$language', '$exam_type', '$requirements', '$ilias', '$modulebook', ".$userRow['user_ID'].", now(), 1)
";
$sql2 = "DELETE FROM `ADDED_SUBJECTS` WHERE `ID` = $id";

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