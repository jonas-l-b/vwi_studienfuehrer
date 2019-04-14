<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$user_id = $userRow['user_ID'];
$recipient = $_POST['recipient'];
$message = $_POST['message'];

$sql = "
	INSERT INTO `user_messages`(`from_id`, `to_id`, `message`)
	VALUES ($user_id,$recipient,'$message')
";

if(mysqli_query($con, $sql)){
	echo "erfolg";
}

echo $sql;

?>