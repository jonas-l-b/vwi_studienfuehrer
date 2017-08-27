<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$message_id = substr($_POST['message_id'],11);

?>


<?php

//In Datenbank eintragen, wer Nachricht eben geöffnet hat
$sql = "
	UPDATE messages
	SET read_last_id = ".$userRow['user_ID'].", read_last_time_stamp = now()
	WHERE message_id = '".$message_id."'
";
mysqli_query($con, $sql);

?>


<?php

//Get sender's name
$sql = "
	SELECT username
	FROM messages
	JOIN users ON messages.sender_id = users.user_ID
	WHERE message_id = '".$message_id."'
";
$sender = mysqli_fetch_assoc(mysqli_query($con, $sql));

//Get read_last's name
$sql = "
	SELECT username
	FROM messages
	JOIN users ON messages.read_last_id = users.user_ID
	WHERE message_id = '".$message_id."'
";
$last_read = mysqli_fetch_assoc(mysqli_query($con, $sql));

//Get assigned_to's name
$sql = "
	SELECT username
	FROM messages
	JOIN users ON messages.assigned_to_id = users.user_ID
	WHERE message_id = '".$message_id."'
";
$result= mysqli_query($con, $sql);
$assigned_to = mysqli_fetch_assoc($result);
if(mysqli_num_rows($result) == 0){
	$assigned_to_name = "<i>nicht zugewiesen</i>";
} else{
	$assigned_to_name = $assigned_to['username'];
}

//dropdown
$options = "<option value=\"".$userRow['user_ID']."\">-- mir selbst --</option>";
$result = mysqli_query($con, "SELECT * FROM users WHERE admin = 1");
while($row = mysqli_fetch_assoc($result)){
	if($row['user_ID'] != $userRow['user_ID']){
		$options .= "<option value=\"".$row['user_ID']."\">".$row['username']."</option>";
	}
}

/*Get message details*/
$sql = "
	SELECT *
	FROM messages
	WHERE message_id = '".$message_id."'
";
$message = mysqli_fetch_assoc(mysqli_query($con, $sql));
//type
switch($message['message_type']){
	case "bug":
		$type = "Bug";
		break;
	case "mistake":
		$type = "Fehler";
		break;
	case "question":
		$type = "Frage";
		break;
	case "feedback":
		$type = "Feedback";
		break;
}

$messageDetail = "
	<p>Von: <strong>".$sender['username']."</strong><span style=\"float:right\"> ".$message['time_stamp']."</span></p>
	<p>Zuletzt gelesen von: <strong>".$last_read['username']."</strong><span style=\"float:right\"> ".$message['read_last_time_stamp']."</span></p>
	<p>Wird derzeit bearbeitet von: <strong>".$assigned_to_name."</strong></p>
	<form name=\"myForm\" role=\"form\" class=\"form-inline\">
		<div class=\"form-group\">
			<label>Diese Nachricht
				<select id=\"cband\" class=\"form-control\">
					".$options."
				</select>
			</label>
		</div>
		<button class=\"btn btn-default\">Zuweisen</button>
	</form>
	<hr>
	<p>Typ: <strong>".$type."</strong></p>
	<p>".$message['comment']."</p>
	<hr>
	<button type=\"button\" class=\"btn btn-primary\">Diese Nachricht als bearbeitet markieren!</button>
";

echo $messageDetail;

?>