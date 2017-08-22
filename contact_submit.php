<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$user_id = $userRow['user_ID'];
$reason = strip_tags($_POST['reason']);
$area = strip_tags($_POST['area']);

if(!is_null(strip_tags($_POST['subject_id']))){
	$object_id = strip_tags($_POST['subject_id']);
}elseif(!is_null(strip_tags($_POST['module_id']))){
	$object_id = strip_tags($_POST['module_id']);
}elseif(!is_null(strip_tags($_POST['lecturer_id']))){
	$object_id = strip_tags($_POST['lecturer_id']);
}elseif(!is_null(strip_tags($_POST['institute_id']))){
	$object_id = strip_tags($_POST['institute_id']);
}

$answer = strip_tags($_POST['answer']);
$comment = strip_tags($_POST['comment']);

$sql = "
	INSERT INTO messages (sender_id, receiver_id, message_type, area, object_id, answer, comment, time_stamp, mread, warning, processed)
	VALUES ('$user_id', -1, '$reason', '$object_id', '$answer', '$comment', now(), 0, 0, 0);
";

if(mysqli_query($con,$sql)){
	echo "erfolg";
}







?>