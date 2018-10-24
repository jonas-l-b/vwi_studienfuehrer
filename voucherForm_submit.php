<?php

include "sessionsStart.php";
include "connect.php";

?>

<?php
$result=mysqli_query($con, "SELECT * FROM vouchers");

while($row = mysqli_fetch_assoc($result)){
	
	if(isset($_POST['email'.$row['user_id'].''])){
		$email = 1;
	}else{
		$email = 0;
	}

	if(isset($_POST['voucher'.$row['user_id'].''])){
		$voucher = 1;
	}else{
		$voucher = 0;
	}
	
	$sql2="
		UPDATE `vouchers`
		SET `email`=$email,`voucher`=$voucher
		WHERE user_id = ".$row['user_id']."
	";
	if(mysqli_query($con, $sql2)){
		//echo "Erfolgreich geändert!";
	}
}

echo "Erfolgreich geändert!";
?>