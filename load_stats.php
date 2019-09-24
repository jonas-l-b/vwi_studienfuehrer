<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>

<h2>Studienführer-Statistiken</h2>

<br>

<!--Anzahl Bewertungen-->
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

<h4>Anzahl abgegebener Bewertungen insgesamt: <b><?php echo $ratingsCount?></b></h4>

<br>
<div style="border: solid lightgrey 1px; border-radius:3px">
	<div id="chart_div" style="width: 100%; height: 300px;"></div>
</div>
<br>

<!--Anteil Nicht-Kernprogramm-Veranstaltungen-->	
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
<!--Anteil Nicht-Kernprogramm-Veranstaltungen nach Bereichen-->
<h4>Anteil Nicht-Kernprogramm-Veranstaltungen nach Bereichen:</h4>

<button class="btn" id="detailedStatsButton">Diesen Bereich laden</button>
<div id="loadDetails" style="display:none">
	<br><br><div class="loader"><div></div></div><br><br>
	<p style="text-align:center">Der detaillierte Bereich wird geladen</p>
</div>
<div id="details"></div>

<script>
$( document ).ready(function() {
	
	$("#detailedStatsButton").click(function(){
		
		$("#loadDetails").show();
		$("#detailedStatsButton").hide();
		
		$.ajax({
			type: "POST",
			url: "load_detailed_stats.php",
			success: function(data) {
				$("#details").html(data);
			}
		});
		
		$(document).ajaxStop(function () {
			$("#loadDetails").hide();
		});

	});

});
</script>

