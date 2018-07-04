<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>

<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

<?php include "inc/nav.php" ?>

<div class="container">
	<h2>Das große Bewertungsranking</h2>
	
	<?php
	$sql="
		SELECT ratings.user_ID AS user_ID, username, COUNT(ratings.user_ID) AS count, time_stamp FROM ratings
		JOIN users ON ratings.user_ID = users.user_ID
		WHERE time_stamp >= DATE_ADD(CURDATE(), INTERVAL -(WEEKDAY(CURDATE())+5-(IF(WEEKDAY(CURDATE())>=2,7,0))) DAY)
		GROUP BY ratings.user_ID
		ORDER BY COUNT(ratings.user_ID) DESC
	";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	
	if(date('w')==2){
		$dis = "";
	}else{
		$dis = "";
	}
	?>

	<div style="background: #F7D358; padding:20px; text-align:center; border: solid 1px lightgrey; border-radius:3px; display:<?php echo $dis?>">
		<h3 style="padding:0;margin:0">
			<?php
			if(mysqli_num_rows($result)!=0){?>
				Rising Star dieser Woche: <strong><?php echo $row['username']?></strong>
			<?php
			}else{
			?>
				Noch kein Rising Star diese Woche - Gib jetzt eine Bewertung ab!
			<?php
			}
			?>
		</h3>
	</div>
		
	<div class="row">
		<div class="col-md-6">		
			<h3>Bewertungen dieser Woche (Mi bis Di)</h3>

			<?php
			$sql="
				SELECT ratings.user_ID AS user_ID, username, COUNT(ratings.user_ID) AS count, time_stamp FROM ratings
				JOIN users ON ratings.user_ID = users.user_ID
				WHERE time_stamp >= DATE_ADD(CURDATE(), INTERVAL -(WEEKDAY(CURDATE())+5-(IF(WEEKDAY(CURDATE())>=2,7,0))) DAY)
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
				echo "<h4>Platz ".$i.": ".$row['username']." (".$row['count']." ".$r.") ".(($i==1)?'<i class="fa fa-trophy" style="color:#FACC2E"></i>':'')."</h4>";
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
				echo "<h4>Platz ".$i.": ".$row['username']." (".$row['count']." ".$r.") ".(($i==1)?'<i class="fa fa-trophy" style="color:#FACC2E"></i>':'')."</h4>";
				$i++;
			}
			?>
		</div>
	</div>
	<br>
</div>
</body>