<?php

include "connect.php";

/*
$sql = "SELECT * FROM CHANGED_SUBJECTS";
$result = mysqli_query($con, $sql);

while($row = mysqli_fetch_assoc($result)){
	echo "
		UPDATE `subjects`
		SET `".$row['changed_value']."` = '".$row['value_new']."'
		WHERE `identifier` = '".$row['identifier']."';
		<br>
	";
}
*/

/*
$sql = "SELECT * FROM ADDED_SUBJECTS";
$result = mysqli_query($con, $sql);

while($row = mysqli_fetch_assoc($result)){
	$language = $row['language'];
	$language = str_replace("nan", "k.A.", $language);
	echo "
		INSERT INTO `subjects`(`subject_name`, `identifier`, `ECTS`, `semester`, `language`, `createdBy_ID`, `time_stamp`, `active`)
		VALUES ('".$row['subject_name']."', '".$row['identifier']."', '".$row['ECTS']."', '".$row['semester']."', '".$language."', 2, now(), 1);
		<br>
	";
}
*/

/*
$sql = "SELECT * FROM CHANGED_MODULES";
$result = mysqli_query($con, $sql);

while($row = mysqli_fetch_assoc($result)){
	echo "
		UPDATE `modules`
		SET `".$row['changed_value']."` = '".$row['value_new']."'
		WHERE `code` = '".$row['identifier']."';
		<br>
	";
}
*/


$sql = "SELECT * FROM ADDED_LECTURERS";
$result = mysqli_query($con, $sql);

while($row = mysqli_fetch_assoc($result)){
	$language = $row['language'];
	$language = str_replace("nan", "k.A.", $language);
	echo "
		INSERT INTO `lecturers`(`name`, `user_ID`, `time_stamp`, `active`)
		VALUES ('".$row['name']."', 2, now(), 1);
		<br>
	";
}

?>