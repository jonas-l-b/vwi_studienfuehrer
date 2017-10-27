<?php

include "sessionsStart.php";

include "connect.php";

?>

<?php

$superAdminId = strip_tags($_POST['superAdminId']);

$deleteAim = $_POST['deleteAim'];

if($deleteAim == "deleteSuperOnly"){
	$sql = "
		UPDATE users
		SET super_admin = 0
		WHERE user_ID = $superAdminId
	";
}elseif($deleteAim == "deleteBoth"){
	$sql = "
		UPDATE users
		SET super_admin = 0, admin = 0
		WHERE user_ID = $superAdminId
	";	
}

if(mysqli_query($con,$sql)){
	echo "erfolg";
}else{
	echo "error";
}

?>