<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$user_id = strip_tags($_POST['user_id']);

$sql = "
	UPDATE users
	SET super_admin = 1
	WHERE user_ID = $user_id
";

if(mysqli_query($con,$sql)){
	echo "<SCRIPT LANGUAGE='JavaScript'>window.location.href='admin.php#adminList';</SCRIPT>";
}else{
	echo "Beim Eintragen ist ein Problem aufgetreten.";
}

?>