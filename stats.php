<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="container">
	<h2>Studienführer-Statistiken</h2>
	
	<br>

<!--STAT 1-->
	<?php
	$sql="SELECT COUNT(*) FROM ratings";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	$ratingsCount = $row['COUNT(*)'];
	$ratingsGoal = 500;
	$ratingsPercent = min(round($ratingsCount*100/$ratingsGoal,0), 100);
	$color1 = "";
	if($ratingsPercent == 100) $color1 = "rgb(37, 160, 3)";
	?>
	
	<h4>Anzahl abgegebener Bewertungen insgesamt: <b><?php echo $ratingsCount?><b></h4>

	<br>

<!--STAT 2-->	
	<!--
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
	$ratingsDistinctGoal = 50;
	$goalReached = min(round($ratingsDistinctPercent*100/$ratingsDistinctGoal,0), 100);
	
	$color2 = "";
	if($goalReached == 100) $color2 = "rgb(37, 160, 3)";
	?>
	
	<h4>Anteil Veranstaltungen, die mindestens eine Bewertung besitzen: <?php echo $ratingsDistinctPercent?> % von <?php echo $ratingsDistinctGoal;?> % 
	<a href="#" data-trigger="focus" data-toggle="popoverPercent" data-content="Der Studienführer umfasst <?php echo $subjectsCount?> Veranstaltungen. Davon sollen <?php echo $ratingsDistinctGoal?> %, also <?php echo round($subjectsCount*($ratingsDistinctGoal/100),0)?> Veranstaltungen, mindestens 1x bewertet worden sein. Bisher wurden <?php echo $ratingsDistinctPercent?> %, also <?php echo $ratingsDistinctCount?> Veranstaltungen,  mindestens 1x bewertet.">
		<span class="glyphicon glyphicon-question-sign"></span>
	</a>
	<script>$('[data-toggle="popoverPercent"]').popover();</script>
	</h4>
	
	<div class="progress" style="height:40px;">
		<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $ratingsDistinctPercent?>"	aria-valuemin="0" aria-valuemax="<?php echo $ratingsDistinctGoal?>" style="width:<?php echo $goalReached?>%; background-color:<?php echo $color2?>">
			<div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%);">
				<span style="font-size:15px;"><?php echo min(round($ratingsDistinctPercent*100/$ratingsDistinctGoal,0),100)?>%</span>
			</div>
		</div>
	</div>
	
	<br>
	-->

<!--STAT 3-->	
	<?php
	$sql="
		SELECT COUNT(DISTINCT ratings.subject_ID) AS count FROM ratings
		JOIN subjects ON ratings.subject_ID = subjects.ID
		JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
		JOIN modules ON subjects_modules.module_ID = modules.module_ID
		JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
		WHERE modules_levels.level_ID != 1
	";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	
	$sql2="
		SELECT COUNT(DISTINCT subject_name) AS count2 FROM subjects
		JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
		JOIN modules ON subjects_modules.module_ID = modules.module_ID
		JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
		WHERE modules_levels.level_ID != 1
		";
	$result2 = mysqli_query($con, $sql2);
	$row2 = mysqli_fetch_assoc($result2);
	
	$ratingsDistinctCount = $row['count'];
	$subjectsCount = $row2['count2'];
	$ratingsDistinctPercent = round($ratingsDistinctCount*100/$subjectsCount,0);
	$ratingsDistinctGoal = 100;
	$goalReached = min(round($ratingsDistinctPercent*100/$ratingsDistinctGoal,0), 100);
	
	$color3 = "";
	if($goalReached == 100) $color3 = "rgb(37, 160, 3)";
	?>
	
	<h4>Anteil Nicht-Kernprogramm-Veranstaltungen, die mindestens eine Bewertung besitzen: <?php echo $ratingsDistinctPercent?> %
	<a href="#" data-trigger="focus" data-toggle="popoverPercent2" data-content="Der Studienführer umfasst <?php echo $subjectsCount?> Nicht-Kernprogramm-Veranstaltungen von denen bisher <?php echo $ratingsDistinctPercent?> %, also <?php echo $ratingsDistinctCount?> Veranstaltungen,  mindestens 1x bewertet wurden.">
		<span class="glyphicon glyphicon-question-sign"></span>
	</a>
	<script>$('[data-toggle="popoverPercent2"]').popover();</script>
	</h4>
	
	<div class="progress" style="height:40px;">
		<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $ratingsDistinctPercent?>"	aria-valuemin="0" aria-valuemax="<?php echo $ratingsDistinctGoal?>" style="width:<?php echo $goalReached?>%; background-color:<?php echo $color3?>">
			<div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%);">
				<span style="font-size:15px;"><?php echo min(round($ratingsDistinctPercent*100/$ratingsDistinctGoal,0),100)?>%</span>
			</div>
		</div>
	</div>
	
	<br>
<!--STAT 4-->
	<h4>Anteil Nicht-Kernprogramm-Veranstaltungen nach Bereichen:</h4>
	<div class="row">
		<div class="col-md-6">
			<?php
			$areas = array("BWL", "VWL", "INFO", "OR", "ING", "Sonstige");
			
			foreach($areas as $i=>$area){
				$sql="
					SELECT COUNT(DISTINCT ratings.subject_ID) AS count FROM ratings
					JOIN subjects ON ratings.subject_ID = subjects.ID
					JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
					JOIN modules ON subjects_modules.module_ID = modules.module_ID
					JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
					WHERE modules_levels.level_ID != 1 AND modules.type = '$area'
				";
				$result = mysqli_query($con, $sql);
				$row = mysqli_fetch_assoc($result);
				
				$sql2="
					SELECT COUNT(DISTINCT subject_name) AS count2 FROM subjects
					JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
					JOIN modules ON subjects_modules.module_ID = modules.module_ID
					JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
					WHERE modules_levels.level_ID != 1 AND modules.type = '$area'
					";
				$result2 = mysqli_query($con, $sql2);
				$row2 = mysqli_fetch_assoc($result2);
				
				$ratingsDistinctCount = $row['count'];
				$subjectsCount = $row2['count2'];
				$ratingsDistinctPercent = round($ratingsDistinctCount*100/$subjectsCount,0);
				$ratingsDistinctGoal = 100;
				$goalReached = min(round($ratingsDistinctPercent*100/$ratingsDistinctGoal,0), 100);
				
				$color3 = "";
				if($goalReached == 100) $color3 = "rgb(37, 160, 3)";
				?>
				
				<h4><?php echo $area ?>: <?php echo $ratingsDistinctPercent?> %
				<a href="#" data-trigger="focus" data-toggle="popoverPercent3" data-content="Der Studienführer umfasst <?php echo $subjectsCount?> <?php echo $area?>-Veranstaltungen außerhalb des Kernprogramms von denen bisher <?php echo $ratingsDistinctPercent?> %, also <?php echo $ratingsDistinctCount?> Veranstaltungen,  mindestens 1x bewertet wurden.">
					<span class="glyphicon glyphicon-question-sign"></span>
				</a>
				<script>$('[data-toggle="popoverPercent3"]').popover();</script>
				</h4>
				
				<div class="progress" style="height:40px;">
					<div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $ratingsDistinctPercent?>"	aria-valuemin="0" aria-valuemax="<?php echo $ratingsDistinctGoal?>" style="width:<?php echo $goalReached?>%; background-color:<?php echo $color3?>">
						<div style="position: relative; top: 50%; left: 50%; transform: translate(-50%, -50%);">
							<span style="font-size:15px;"><?php echo min(round($ratingsDistinctPercent*100/$ratingsDistinctGoal,0),100)?>%</span>
						</div>
					</div>
				</div>
				<?php
				if($i==2) echo "</div><div class=\"col-md-6\">";
				?>
			<?php
			}
			?>
		</div>
	</div>
	
</div>
</body>