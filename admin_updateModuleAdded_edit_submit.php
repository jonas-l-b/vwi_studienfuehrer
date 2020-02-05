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

$sql = "
	UPDATE `ADDED_MODULES` SET `name`='$name',`code`='$code',`type`='$type',`ects`='$ects'
	WHERE ID = $id
";

echo $sql;

if(mysqli_query($con, $sql)){
	echo "erfolg";
}
?>