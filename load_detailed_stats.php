<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>

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