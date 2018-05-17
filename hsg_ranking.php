<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="container">
	<h2>Hochschulgruppen-Ranking</h2>
	<h4>Wer hat am fleißigsten bewertet?</h4>
	<br>
	<?php
	$sql="
		SELECT ratings.user_ID AS user_ID, username, COUNT(ratings.user_ID) AS count FROM ratings
		JOIN users ON ratings.user_ID = users.user_ID
		GROUP BY ratings.user_ID
		ORDER BY COUNT(ratings.user_ID) DESC
	";
	$result = mysqli_query($con, $sql);

	
	$i=1;
	while($row = mysqli_fetch_assoc($result)){
		if($row['count']>1){
			$r="Bewertungen";
		}else{
			$r="Bewertung";
		}
		echo "<h4>Platz ".$i.": ".$row['username']." (".$row['count']." ".$r.")</h4>";
		if($i==3){
			echo "<hr style=\"border-color:grey; margin-bottom:0\">";
			echo "<p>(Wer am 05. Mai am Ende der Sitzung über dieser Linie steht, bekommt ein Bier im Ox!)";
		}
		$i++;
	}
	?>
	<br>
</div>
</body>