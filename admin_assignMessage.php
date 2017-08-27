<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$assign_to_id = $_POST['assign_to_id'];
$message_id = substr($_POST['message_id'],11);

$sql = "
	UPDATE messages
	SET assigned_to_id = $assign_to_id, assigned_to_time_stamp = now()
	WHERE message_id = '".$message_id."'
";

//Get assigned name
$sql2 = "
	SELECT username
	FROM users
	WHERE user_ID = '".$assign_to_id."'
";
$result = mysqli_query($con, $sql2);
$assigned_to = mysqli_fetch_assoc($result);

if(mysqli_query($con, $sql)){
	echo $assigned_to['username'];
}

?>