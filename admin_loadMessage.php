<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$message_id = substr($_POST['message_id'],11);

mysqli_query($con, "SELECT processed FROM messages WHERE message_id = $message_id");
$processed = mysqli_fetch_assoc(mysqli_query($con, "SELECT processed FROM messages WHERE message_id = $message_id"));

if($processed['processed'] == 0){
	//In Datenbank eintragen, wer Nachricht eben geöffnet hat
	$sql = "
		UPDATE messages
		SET read_last_id = ".$userRow['user_ID'].", read_last_time_stamp = now()
		WHERE message_id = '".$message_id."'
	";
	mysqli_query($con, $sql);

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
			<div class=\"form-group\">
				<label>Nachricht an Nutzer:</label>
				<textarea name=\"finishComment\" class=\"form-control\" placeholder=\"Der Nutzer hat um eine Antwort gebeten und wird durch das Markieren dieser Nachricht als bearbeitet automatisch durch eine E-Mail benachrichtigt. Da diese Benachrichtigung nur beinhaltet ist, dass die Nachricht bearbeitet wurde und ob diese Bearbeitung von Erfolg gekrönt war, kannst du hier noch weitere Informationen hinzufügen.\"rows=\"5\"></textarea>
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
				<label><span class=\"glyphicon glyphicon-arrow-right\"></span> Diese Nachricht
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
				<div class=\"form-group\">
					<label>Kommentar für Admins:</label>
					<textarea name=\"finishCommentAdmin\" class=\"form-control\" placeholder=\"Hier kannst du einen Kommentar für andere Admins hinterlassen (wird nicht an Benutzer verschickt). Wenn du beispielsweise oben Nein ausgewählt hast, kannst du hier erklären, warum.\" rows=\"5\"></textarea>
				</div>
				<button class=\"btn btn-primary\" id=\"finishButton\">".$buttonMessage."</button>
			</form>
		</div>
		</div>
		  
		</div>
		</div>
	";

} else{	
	//Get sender's name
	$sql = "
		SELECT username
		FROM messages
		JOIN users ON messages.sender_id = users.user_ID
		WHERE message_id = '".$message_id."'
	";
	$sender = mysqli_fetch_assoc(mysqli_query($con, $sql));

	//Get processed_by's name
	$sql = "
		SELECT username
		FROM messages
		JOIN users ON messages.processed_by_id = users.user_ID
		WHERE message_id = '".$message_id."'
	";
	$processed_by = mysqli_fetch_assoc(mysqli_query($con, $sql));
	
	/*Get message details*/
	$sql = "
		SELECT *
		FROM messages
		WHERE message_id = '".$message_id."'
	";
	$message = mysqli_fetch_assoc(mysqli_query($con, $sql));
	//answer_required
	if($message['answer_required'] == 1){
		$answer = "Ja";
	} else{
		$answer = "Nein";
	}
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
	//processed_comment
	if($message['processed_comment'] != ""){
		$line = "<hr>";
		$commentator = "<p>Kommentar an Nutzer von <strong>".$processed_by['username']."</strong>:</p>";
		$comment = "<p>".$message['processed_comment']."</p>";
	} else{
		$line = "";
		$commentator = "";
		$comment = "";
	}
	
	//processed_comment_for_admins
	if($message['processed_comment_for_admins'] != ""){
		$commentAdmin = "<p>".$message['processed_comment_for_admins']."</p>";
	} else{
		$commentAdmin = "<i>Kein Kommentar hinterlassen.</i>";
	}
	
	$messageDetail = "
		<p>Von: <strong>".$sender['username']."</strong><span style=\"float:right\"> ".$message['time_stamp']."</span></p>
		<p>Als bearbeitet markiert von: <strong>".$processed_by['username']."</strong><span style=\"float:right\"> ".$message['processed_time_stamp']."</span></p>
		<p>Antwort an Nutzer verschickt: <strong>".$answer."</strong><span style=\"float:right\"> ".$message['processed_time_stamp']."</span></p>
		
		<hr>
		<p>Typ: <strong>".$type."</strong></p>
		<p>".$message['comment']."</p>
		".$line."
		".$commentator."
		".$comment."
		<hr>
		<p>Kommentar von <strong>".$processed_by['username']."</strong> für andere Admins:</p>
		".$commentAdmin."
	";
}

echo $messageDetail;

?>