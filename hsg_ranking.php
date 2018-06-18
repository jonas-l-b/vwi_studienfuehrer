<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="container">
	<h2>Hochschulgruppen-Ranking</h2>

	<div class="row">
		<div class="col-md-6">		
			<h3>Bewertungen dieser Woche (Mi bis Di)</h3>

			<?php
			$sql="
				SELECT ratings.user_ID AS user_ID, username, COUNT(ratings.user_ID) AS count FROM ratings
				JOIN users ON ratings.user_ID = users.user_ID
				WHERE time_stamp >= DATE_ADD(CURDATE(), INTERVAL -(WEEKDAY(CURDATE())-3) DAY)
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
				$i++;
			}
			?>
		</div>
		<div class="col-md-6">	
			<h3>Gesamt-Ranking</h3>
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
				$i++;
			}
			?>
		</div>
	</div>
	<br>
</div>
</body>