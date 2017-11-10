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
	$body = "nosig
		<span>ein Benutzer hat eine Nachricht an die Administratoren geschickt:</span>
		<hr>
		<p>Betreff: <strong>".$type."</strong></p>
		<span>".$comment."</span>
		<hr>
		<span class='foo'><a href=\"studienfuehrer.vwi-karlsruhe.de/admin.php#messages\">Hier</a> kannst du die Nachricht online anschauen. Du erhälst diese Nachricht, weil du als Administrator <a href=\"studienfuehrer.vwi-karlsruhe.de/admin.php#notifications\">hier</a> in die Benachrichtigungs-Liste eingetragen wurdest.</span>
	";

	$sql = "
		SELECT *
		FROM admin_notifications
		JOIN users on users.user_ID = admin_notifications.admin_id
		WHERE type = 'messages'
	";
	$result = mysqli_query($con, $sql);
	while($row = mysqli_fetch_assoc($result)){		
		EmailService::getService()->sendEmail($row['email'], $row['username'], $subject, $body);
	}
	
}

?>