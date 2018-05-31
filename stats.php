<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="container">
	<h2>Studienführer-Statistiken</h2>
	<h4>Was fehlt noch vor dem Launch?</h4>
	
	<br>
	
	<?php
	$sql="SELECT COUNT(*) FROM ratings";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	$ratingsCount = $row['COUNT(*)'];
	$ratingsGoal = 600;
	$ratingsPercent = min(round($ratingsCount*100/$ratingsGoal,0), 100);
	$color1 = "";
	if($ratingsPercent == 100) $color1 = "rgb(37, 160, 3)";
	?>
	
	<h4>Anzahl abgegebene Bewertungen: <?php echo $ratingsCount?> von <?php echo $ratingsGoal?></h4>
	<div class="progress" style="height:40px;">
		<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $ratingsCount?>"	aria-valuemin="0" aria-valuemax="<?php echo $ratingsGoal?>" style="width:<?php echo $ratingsPercent?>%; background-color:<?php echo $color1?>">
			<div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%);">
				<span style="font-size:15px;"><?php echo $ratingsPercent?>%</span>
			</div>
		</div>
	</div>

	<br>
	
	<?php
	$sql="	SELECT COUNT(DISTINCT subject_ID) AS count FROM ratings";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	
	$sql2="	SELECT COUNT(*) AS count2 FROM subjects";
	$result2 = mysqli_query($con, $sql2);
	$row2 = mysqli_fetch_assoc($result2);
	
	$ratingsDistinctCount = $row['count'];
	$subjectsCount = $row2['count2'];
	$ratingsDistinctPercent = round($ratingsDistinctCount*100/$subjectsCount,0);
	$ratingsDistinctGoal = 60;
	$goalReached = min(round($ratingsDistinctPercent*100/$ratingsDistinctGoal,0), 100);
	
	$color2 = "";
	if($goalReached == 100) $color2 = "rgb(37, 160, 3)";
	?>
	
	<h4>Anteil Veranstaltungen, die mindestens eine Bewertung besitzen: <?php echo $ratingsDistinctPercent?> % von <?php echo $ratingsDistinctGoal;?> %</h4>
	<div class="progress" style="height:40px;">
		<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $ratingsDistinctPercent?>"	aria-valuemin="0" aria-valuemax="<?php echo $ratingsDistinctGoal?>" style="width:<?php echo $goalReached?>%; background-color:<?php echo $color2?>">
			<div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%);">
				<span style="font-size:15px;"><?php echo $ratingsDistinctPercent?>%</span>
			</div>
		</div>
	</div>
	
	<br>
	
	<?php
	$sql="SELECT COUNT(*) FROM questions";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	$questionsCount = $row['COUNT(*)'];
	$questionsGoal = 50;
	$questionsPercent = min(round($questionsCount*100/$questionsGoal,0), 100);
	$color3 = "";
	if($questionsPercent == 100) $color3 = "rgb(37, 160, 3)";
	?>
	
	<h4>Anzahl gestellte Fragen: <?php echo $questionsCount?> von <?php echo $questionsGoal?></h4>
	<div class="progress" style="height:40px;">
		<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $questionsCount?>"	aria-valuemin="0" aria-valuemax="<?php echo $questionsGoal?>" style="width:<?php echo $questionsPercent?>%; background-color:<?php echo $color3?>">
			<div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%);">
				<span style="font-size:15px;"><?php echo $questionsPercent?>%</span>
			</div>
		</div>
	</div>
	
</div>
</body>