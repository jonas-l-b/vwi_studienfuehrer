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
$result = mysqli_query($con, $sql);
$assigned_to = mysqli_fetch_assoc($result);
if(mysqli_num_rows($result) == 0){
	$assigned_to_name = "<i>nicht zugewiesen</i>";
} else{
	$assigned_to_name = $assigned_to['username'];
}

//dropdown
$row1 = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM messages WHERE message_id = '".$message_id."'"));
if($row1['assigned_to_id'] != $userRow['user_ID']){
	$options = "<option value=\"".$userRow['user_ID']."\">-- mir selbst --</option>";
} else{
	$options = "";
}

$result = mysqli_query($con, "SELECT * FROM users WHERE admin = 1");
while($row = mysqli_fetch_assoc($result)){
	if($row['user_ID'] != $userRow['user_ID'] AND $row['user_ID'] != $row1['assigned_to_id']){
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
		$modalMessage = "Wurde der Bug erfolgreich behoben?";
		break;
	case "mistake":
		$type = "Fehler";
		$modalMessage = "Wurde der Fehler erfolgreich behoben?";
		break;
	case "question":
		$type = "Frage";
		$modalMessage = "Kann die Frage zufriedenstellend beantwortet werden?";
		break;
	case "feedback":
		$type = "Feedback";
		$modalMessage = "Wurde das Feedback entsprechend weitergeleitet?";
		break;
}

//Button-Message
if ($message['answer_required'] == 1){
	$additionalComment = "
		<p>Der Nutzer hat um eine Antwort gebeten und wird durch das Markieren dieser Nachricht als bearbeitet automatisch durch eine E-Mail benachrichtigt. Da diese Benachrichtigung nur beinhaltet ist, dass die Nachricht bearbeitet wurde und ob diese Bearbeitung von Erfolg gekrönt war, kannst du hier noch weitere Informationen hinzufügen:</p>
		<div class=\"form-group\">
			<textarea name=\"finishComment\" class=\"form-control\" rows=\"5\"></textarea>
		</div>
	";
	$buttonMessage = "Nachricht jetzt als bearbeitet markieren und Benachrichtigung an Nutzer verschicken";
} else{
	$additionalComment = "";
	$buttonMessage = "Nachricht jetzt als bearbeitet markieren";
}


$messageDetail = "
	<p>Von: <strong>".$sender['username']."</strong><span style=\"float:right\"> ".$message['time_stamp']."</span></p>
	<p>Zuletzt gelesen von: <strong>".$last_read['username']."</strong><span style=\"float:right\"> ".$message['read_last_time_stamp']."</span></p>
	<p>Wird derzeit bearbeitet von: <strong>".$assigned_to_name."</strong><span style=\"float:right\"> ".$message['assigned_to_time_stamp']."</span></p>
	<form id=\"assignForm\" role=\"form\" class=\"form-inline\">
		<div class=\"form-group\">
			<label>Diese Nachricht
				<select name=\"assign_to_id\" class=\"form-control\">
					".$options."
				</select>
			</label>
		</div>
		<button class=\"btn btn-default\" id=\"assignButton\">Zuweisen</button>
	</form>
	<hr>
	<p>Typ: <strong>".$type."</strong></p>
	<p>".$message['comment']."</p>
	<hr>
	<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#finishModal\">Diese Nachricht als bearbeitet markieren</button>

	<!-- Modal -->
	<div class=\"modal fade\" id=\"finishModal\" role=\"dialog\">
	<div class=\"modal-dialog\">

	<!-- Modal content-->
	<div class=\"modal-content\">
	<div class=\"modal-header\">
		<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
		<h4 class=\"modal-title\">Nachricht als bearbeitet markieren</h4>
	</div>
	<div id=\"finishModalBody\" class=\"modal-body\">
		<form id=\"finishForm\" role=\"form\">
			<div class=\"form-group\">
				<label>".$modalMessage."</label>
				<select name=\"processedSuccess\" class=\"form-control\">
					<option value=\"1\">Ja</option>
					<option value=\"2\">Nein</option>
				</select>
			</div>
			".$additionalComment."
			<button class=\"btn btn-primary\" id=\"finishButton\">".$buttonMessage."</button>
		</form>
	</div>
	</div>
	  
	</div>
	</div>
";

echo $messageDetail;

?>