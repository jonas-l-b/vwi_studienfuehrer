<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$id = $_POST['id'];
$identifier = $_POST['identifier'];
$changed_value = $_POST['changed_value'];
$value_new = $_POST['value_new'];

$sql1 = "
	UPDATE `lecturers`
	SET `$changed_value` = '$value_new'
	WHERE `name` = '$identifier'
";

$sql2 = "DELETE FROM `CHANGED_LECTURERS` WHERE `id` = $id";

if(mysqli_query($con, $sql1)){
	echo "Änderungen erfolgreich.";
	if(mysqli_query($con, $sql2)){
		echo " Löschen aus dieser Tabelle erfolgreich.";
	}else{
		echo " Löschen aus dieser Tabelle nicht erfolgreich.";
	}
}else{
	echo "Änderungen nicht erfolgreich. Das kann verschiedene Gründe haben.";
}
?>