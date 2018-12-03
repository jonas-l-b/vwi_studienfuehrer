<?php

include "sessionsStart.php";
include "connect.php";

?>

<?php
$result=mysqli_query($con, "SELECT * FROM vouchers2500");

while($row = mysqli_fetch_assoc($result)){
	
	if(isset($_POST['email'.$row['user_id'].''])){
		$email = 1;
	}else{
		$email = 0;
	}

	if(isset($_POST['voucher2500'.$row['user_id'].''])){
		$voucher2500 = 1;
	}else{
		$voucher2500 = 0;
	}
	
	$sql2="
		UPDATE `vouchers2500`
		SET `email`=$email,`voucher2500`=$voucher2500
		WHERE user_id = ".$row['user_id']."
	";
	if(mysqli_query($con, $sql2)){
		//echo "Erfolgreich geändert!";
	}
}

echo "Erfolgreich geändert!";
?>