<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$id = $_POST['id'];
$subject_name = $_POST['subject_name'];
$identifier = $_POST['identifier'];
$ECTS = intval($_POST['ECTS']);
$semester = $_POST['semester'];
$language = $_POST['language'];

$sql = "
	UPDATE `ADDED_LECTURES` SET `subject_name`='$subject_name',`identifier`='$identifier', `ECTS`='$ECTS',`semester`='$semester',`language`='$language'
	WHERE ID = $id
";

if(mysqli_query($con, $sql)){
	echo "erfolg";
}
?>