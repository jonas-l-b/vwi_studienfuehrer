<?php

include "sessionsStart.php";
include "connect.php";

?>

<?php
$result=mysqli_query($con, "SELECT * FROM vouchers1500");

while($row = mysqli_fetch_assoc($result)){
	
	if(isset($_POST['email'.$row['user_id'].''])){
		$email = 1;
	}else{
		$email = 0;
	}

	if(isset($_POST['voucher1500'.$row['user_id'].''])){
		$voucher1500 = 1;
	}else{
		$voucher1500 = 0;
	}
	
	$sql2="
		UPDATE `vouchers1500`
		SET `email`=$email,`voucher1500`=$voucher1500
		WHERE user_id = ".$row['user_id']."
	";
	if(mysqli_query($con, $sql2)){
		//echo "Erfolgreich geändert!";
	}
}

echo "Erfolgreich geändert!";
?>