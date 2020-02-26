<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$infoDate = $_POST['infoDate'];

$sql = "
	UPDATE `help` SET `value`='$infoDate'
	WHERE name = 'infoDate'
";

if(mysqli_query($con, $sql)){
	echo "Änderung erfolgreich.";
}else{
	echo "Änderung nicht erfolgreich.";
}
?>