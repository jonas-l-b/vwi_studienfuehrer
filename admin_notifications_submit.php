<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$admin_id = strip_tags($_POST['admin_id']);

$sql = "
	INSERT INTO admin_notifications (admin_id, type, time_stamp)
	VALUES ('$admin_id', 'messages', now());
";

if(mysqli_query($con,$sql)){
	echo "<SCRIPT LANGUAGE='JavaScript'>window.location.href='admin.php#notifications';</SCRIPT>";
}else{
	echo "Beim Eintragen ist ein Problem aufgetreten.";
}
?>