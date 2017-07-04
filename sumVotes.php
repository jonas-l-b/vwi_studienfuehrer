<?php

include "connect.php";

?>


<?php

$result = mysqli_query($con,"SELECT * FROM ratings;");

while($rating = mysqli_fetch_assoc($result) ){
	
	//echo "number of rows: " . mysqli_num_rows($result) . "<br>";
	
	$sql="
		SELECT SUM(rating_direction)
		FROM commentratings
		WHERE comment_ID = '".$rating['ID']."';
	";
	//echo "ratingID: " . $rating['ID'] . "<br>";

	$result2 = mysqli_query($con,$sql);
	if (FALSE === $result2) die("Select sum failed: ".mysqli_error());
	$row = mysqli_fetch_row($result2);
	$sum = $row[0];
	if ($row[0] == false) $sum = 0;
	//echo "sum: " . $sum ."<br>";

	$sql="
		UPDATE ratings
		SET comment_rating = '$sum'
		WHERE ID = ".$rating['ID'].";
	";

	if ($con->query($sql) == TRUE) {
		//echo 'success';
	}
}

?>