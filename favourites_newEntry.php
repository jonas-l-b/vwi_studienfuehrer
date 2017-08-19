<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php
$user_id = filter_var($_POST['user_id'], FILTER_SANITIZE_STRING);
$subject_id = filter_var($_POST['subject_id'], FILTER_SANITIZE_STRING);


$stmt = $con->prepare("
	INSERT INTO favourites (user_ID, subject_ID)
	VALUES (?,?);
");
$stmt->bind_param("ss", $user_id, $subject_id);
$stmt->execute();

$stmt->close();
$con->close();



?>