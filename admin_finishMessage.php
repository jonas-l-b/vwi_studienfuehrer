<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$processedSuccess = $_POST['processedSuccess'];
if(!empty($_POST['finishComment'])){
	$finishComment = $_POST['finishComment'];
}
$finishCommentAdmin = $_POST['finishCommentAdmin'];
$message_id = substr($_POST['message_id'],11);

if(isset($finishComment)){
	$sql = "
		UPDATE messages
		SET processed = $processedSuccess, processed_by_id = ".$userRow['user_ID'].", processed_comment = '$finishComment', processed_comment_for_admins = '$finishCommentAdmin', processed_time_stamp = now()
		WHERE message_id = '".$message_id."'
	";
} else{
	$sql = "
		UPDATE messages
		SET processed = $processedSuccess, processed_by_id = ".$userRow['user_ID'].", processed_comment_for_admins = '$finishCommentAdmin', processed_time_stamp = now()
		WHERE message_id = '".$message_id."'
	";
}

if(mysqli_query($con, $sql)){
	echo "Erfolgreich markiert!";
}

?>