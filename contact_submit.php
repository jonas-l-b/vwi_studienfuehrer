<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php


$user_id = $userRow['user_ID'];
$reason = strip_tags($_POST['reason']);

if(!empty($_POST['area'])){
	$area = strip_tags($_POST['area']); //IS THIS SAFE??? if(!empty(strip_tags($_POST['area']))) geht nicht
} else{
	$area = "";
}

if(!empty($_POST['subject_id'])){
	$object_id = strip_tags($_POST['subject_id']);
}elseif(!empty($_POST['module_id'])){
	$object_id = strip_tags($_POST['module_id']);
}elseif(!empty($_POST['lecturer_id'])){
	$object_id = strip_tags($_POST['lecturer_id']);
}elseif(!empty($_POST['institute_id'])){
	$object_id = strip_tags($_POST['institute_id']);
}else{
	$object_id = 0;
}

if(!empty($_POST['answer']) OR $reason == "question"){
	$answer = 1;
}else{
	$answer = 0;
}
	
$comment = strip_tags($_POST['comment']);

$sql = "
	INSERT INTO messages (sender_id, receiver_id, message_type, area, object_id, answer_required, comment, time_stamp)
	VALUES ('$user_id', -1, '$reason', '$area', '$object_id', '$answer', '$comment', now());
";

if(mysqli_query($con,$sql)){
	echo "erfolg";
	
	//E-Mail-Benachrichtigungen verschicken
	$sql = "
		SELECT *
		FROM admin_notifications
		JOIN users on users.user_ID = admin_notifications.admin_id
		WHERE type = 'messages'
	";
	
	$result = mysqli_query($con, $sql);
	while($row = mysqli_fetch_assoc($result)){
		$subject = "[Studienführer: Benachrichtigung] Neue Nachricht für Admins eingegangen";
		
		switch($reason){
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
		
		$body = "
			<p>Ein Benutzer hat eine Nachricht an die Administratoren geschickt:</p>
			<hr>
			<p>Datum: ".now()."</p>
			<p>Typ: ".$type."</p>
			<p><u>Nachricht</u>:<br> ".$comment."</p>
			<hr>
			<a href=\"admin.php#messages\">Hier</a> kannst du die Nachricht online anschauen.
		";
		
		EmailService::getService()->sendEmail($row['email'], $row['username'], $subject, $body);
	}
	
}

?>