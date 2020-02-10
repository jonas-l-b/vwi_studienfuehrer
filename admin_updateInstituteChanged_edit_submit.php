<?php
include "sessionsStart.php";
include "connect.php";
?>

<?php
$id = $_POST['id'];
$value_new = $_POST['value_new'];

$sql = "
	UPDATE `CHANGED_INSTITUTES` SET `value_new`='$value_new'
	WHERE ID = $id
";

if(mysqli_query($con, $sql)){
	echo "erfolg";
}
?>