<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

include "saveSubjectToVariable.php";

//include "loadSubjectData.php";

include "sumVotes.php";

?>

<body>

<?php include "nav.php" ?>

<div class="container" style="margin-top:60px">
	<?php
	//Get subject data
	$sqlBody = "
		FROM subjects
		JOIN subjects_lecturers ON subjects.ID = subjects_lecturers.subject_ID
		JOIN lecturers ON subjects_lecturers.lecturer_ID = lecturers.lecturer_ID
		JOIN lecturers_institutes ON lecturers.lecturer_ID = lecturers_institutes.lecturer_ID
		JOIN institutes ON lecturers_institutes.institute_ID = institutes.institute_ID
		JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
		JOIN modules ON subjects_modules.module_ID = modules.module_ID
		JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
		JOIN levels ON modules_levels.level_ID = levels.level_ID
	";
	$sql = "
		SELECT DISTINCT subjects.ID as ID, subject_name, subjects.code AS subject_code, identifier, lv_number, subjects.ECTS AS subject_ECTS, semester, language
		".$sqlBody."
		WHERE subjects.code = '".$subject."'
	";
	$result = mysqli_query($con,$sql);

	// Check, ob Datensatz existiert
	if (mysqli_num_rows($result) >= 1 ) {
		$subjectData = mysqli_fetch_assoc($result);
	} else {
		echo ("<SCRIPT LANGUAGE='JavaScript'>window.location.href='landing.php?m=no_subject_in_db';</SCRIPT>");
	}
	
	//Variablen mit mehreren möglichen Eitnrägen abrufen
	//module_types
	$sql = "
		SELECT DISTINCT type
		".$sqlBody."
		WHERE subjects.ID = ".$subjectData['ID']."
		ORDER BY type
	";
	$result = mysqli_query($con,$sql);
	$module_types = "";
	while($row = mysqli_fetch_assoc($result)){
		$module_types .= $row['type']."<br>";
	}
	$module_types = substr($module_types, 0, -4);
	
	//part_of_modules
	$sql = "
		SELECT DISTINCT modules.name, modules.module_id
		".$sqlBody."
		WHERE subjects.ID = ".$subjectData['ID']."
		ORDER BY modules.name
	";
	$result = mysqli_query($con,$sql);
	$part_of_modules = "";
	while($row = mysqli_fetch_assoc($result)){
		$part_of_modules .= "<a href=\"module.php?module_id=".$row['module_id']."\">".$row['name']."</a><br>";
	}
	$part_of_modules = substr($part_of_modules, 0, -4);
	
	//levels
	$sql = "
		SELECT DISTINCT levels.name
		".$sqlBody."
		WHERE subjects.ID = ".$subjectData['ID']."
			ORDER BY CASE
				when levels.name = 'bachelor_basic' then 1
				when levels.name = 'bachelor' then 2
				when levels.name = 'master' then 3
			END
	";
	$result = mysqli_query($con,$sql);
	$levels = "";
	while($row = mysqli_fetch_assoc($result)){
		switch($row['name']){
			case "bachelor_basic":
				$levels .= "Bachelor: Kernprog."."<br>";
				break;
			case "bachelor":
				$levels .= "Bachelor: Vertiefung"."<br>";
				break;
			case "master":
				$levels .= "Master"."<br>";
				break;
		}
	}
	$levels = substr($levels, 0, -4);
	
	//lecturers
	$sql = "
		SELECT DISTINCT lecturers.lecturer_ID, lecturers.last_name, lecturers.first_name, institutes.institute_ID, abbr
		".$sqlBody."
		WHERE subjects.ID = ".$subjectData['ID']."
		ORDER BY abbr, lecturers.last_name
	";
	$result = mysqli_query($con,$sql);
	$lecturers = "";
	while($row = mysqli_fetch_assoc($result)){
		$lecturers .= "<a href=\"lecturer.php?lecturer_id=".$row['lecturer_ID']."\">".substr($row['first_name'],0,1).". ".$row['last_name']."</a> (<a href=\"institute.php?institute_id=".$row['institute_ID']."\">".$row['abbr']."</a>)<br>";
	}
	$lecturers = substr($lecturers, 0, -4);
	?>
	
	<?php
	//Berechnungen für Bewertungsübersicht
	$result = mysqli_query($con, "SELECT * FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
	
	//Diese Variablen sorgen dafür, dass der Bewertungsteil nur angezeigt wird, wenn auch Bewertungen vorhanden sind; andernfalls wird der Jetzt-bewerten-Teil angezeigt
	$displayRatings = "";
	$displayNoRatings = "style=\"display:none\"";
	
	if (mysqli_num_rows($result) == 0){ //Falls noch keine Bewertungen vorhanden
		$displayRatings = "style=\"display:none\"";
		$displayNoRatings = "";
	}
	else{ //Falls Bewertungen vorhanden
		//Crit1
		$result = mysqli_query($con,"SELECT SUM(crit1) AS value_sum FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
		$row = mysqli_fetch_assoc($result);
		$crit1 = $row['value_sum'];

		$result = mysqli_query($con,"SELECT COUNT(crit1) AS value_count FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
		$row = mysqli_fetch_assoc($result);
		$crit1Total = $row['value_count'];

		$crit1Prozent = round(($crit1 / ($crit1Total*7) ) * 100);

		//Crit2
		$result = mysqli_query($con,"SELECT SUM(crit2) AS value_sum FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
		$row = mysqli_fetch_assoc($result);
		$crit2 = $row['value_sum'];

		$result = mysqli_query($con,"SELECT COUNT(crit2) AS value_count FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
		$row = mysqli_fetch_assoc($result);
		$crit2Total = $row['value_count'];

		$crit2Prozent = round(($crit2 / ($crit2Total*7) ) * 100);

		//Crit3
		$result = mysqli_query($con,"SELECT SUM(crit3) AS value_sum FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
		$row = mysqli_fetch_assoc($result);
		$crit3 = $row['value_sum'];

		$result = mysqli_query($con,"SELECT COUNT(crit3) AS value_count FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
		$row = mysqli_fetch_assoc($result);
		$crit3Total = $row['value_count'];

		$crit3Prozent = round(($crit3 / ($crit3Total*7) ) * 100);

		//Crit4
		$result = mysqli_query($con,"SELECT SUM(crit4) AS value_sum FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
		$row = mysqli_fetch_assoc($result);
		$crit4 = $row['value_sum'];

		$result = mysqli_query($con,"SELECT COUNT(crit4) AS value_count FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
		$row = mysqli_fetch_assoc($result);
		$crit4Total = $row['value_count'];

		$crit4Prozent = round(($crit4 / ($crit4Total*7) ) * 100);

		//Crit5
		$result = mysqli_query($con,"SELECT SUM(crit5) AS value_sum FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
		$row = mysqli_fetch_assoc($result);
		$crit5 = $row['value_sum'];

		$result = mysqli_query($con,"SELECT COUNT(crit5) AS value_count FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
		$row = mysqli_fetch_assoc($result);
		$crit5Total = $row['value_count'];

		$crit5Prozent = round(($crit5 / ($crit5Total*7) ) * 100);

		//Overall
		$overallProzent = round(($crit1Prozent+$crit2Prozent+$crit3Prozent+$crit4Prozent+$crit5Prozent)/5);
		$overallColor = "red";
		if($overallProzent > 50) $overallColor = "orange";
		if($overallProzent > 80) $overallColor = "green";
	}
	?>

	<!--Überschrift und Info-->
	<div style="margin-bottom:20px; padding:20px 20px 0px 0px;">
		<h1> <?php echo $subjectData['subject_name'] ?> </h1>
	</div>
	
	<div class="infoContainer">
	<?php
	$box = array(
		array("Kennung", "LV-Nummer", "Modultyp", "Teil der Module", "Level", "ECTS", "Dozent", "Semester", "Sprache"),
		array($subjectData['identifier'], $subjectData['lv_number'], $module_types, $part_of_modules, $levels, $subjectData['subject_ECTS'], $lecturers, $subjectData['semester'], $subjectData['language'])
	);

	for ($x = 0; $x <= ((count($box, COUNT_RECURSIVE)-2)/2)-1; $x++) {
		echo "
			<div class=\"infoFloater\">
				<p><strong>".$box[0][$x]."</strong></p>
				<span>".$box[1][$x]."</span>
			</div>

		";
	}
	?>
	</div>
	
	<div style="margin-bottom:15px">
		<button <?php echo $displayRatings ?> type="button" a href="#myModal" role="button" class="btn btn-primary" data-toggle="modal">Diese Veranstaltung jetzt bewerten!</button>
	</div>

<!--	
	<table class="toptable">
		<tr>
			<th>Kennung:</th>
			<td><?php echo $subjectData['identifier'] ?></td>
		</tr>
		<tr>
			<th>LV-Nummer:</th>
			<td><?php echo $subjectData['lv_number'] ?></td>
		</tr>
		<tr>
			<th>Modultyp:</th>
			<td><?php echo $module_types ?></td>
		</tr>
		<tr>
			<th>Teil der Module:</th>
			<td><?php echo $part_of_modules ?></td>
		</tr>
		<tr>
			<th>Level:</th>
			<td><?php echo $levels ?></td>
		</tr>
		<tr>
			<th>ECTS:</th>
			<td><?php echo $subjectData['subject_ECTS'] ?></td>
		</tr>
		<tr>
			<th>Dozent:</th>
			<td><?php echo $lecturers ?></td>
		</tr>
		<tr>
			<th>Semester:</th>
			<td><?php echo $subjectData['semester'] ?></td>
		</tr>
		<tr>
			<th>Sprache:</th>
			<td><?php echo $subjectData['language'] ?></td>
		</tr>
	</table>
-->

	<!--Bewertungsübersicht-->
	<div <?php echo $displayRatings ?>>
		<div class="col-md-2 head_left">
			Gesamtbewertung
		</div>

		<div class="col-md-10 well">
			<?php
			$result = mysqli_query($con,"SELECT SUM(recommendation) AS value_sum FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
			$row = mysqli_fetch_assoc($result);
			$yes = $row['value_sum'];
			if ($yes == "") $yes = 0;
			
			$result = mysqli_query($con,"SELECT COUNT(recommendation) AS value_count FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
			$row = mysqli_fetch_assoc($result);
			$total = $row['value_count'];
			?>
			
			<span style="font-size:20px"><strong><?php echo $yes ?></strong> von <strong><?php echo $total ?></strong> <?php if($yes == 1){echo "würde";} else echo "würden" ?> diese Veranstaltung weiterempfehlen.</span>
			
			<hr>

			<div class="col-md-4">
			
				<div class="container">
					<div class="c100 p<?php echo $overallProzent ?>" >
						<span><?php echo $overallProzent ?><?php if ($overallProzent != "-") echo "%" ?></span>
						<div class="slice">
							<div class="bar"></div>
							<div class="fill"></div>
						</div>
					</div>
				</div>

				<p align="center">Gesamt</p>
				
			</div>
			
			<div class="col-md-8">
				<table class="ratingtable" style="width:100%">
				
				<tr>
					<td valign="top" style="width:30%">
						<span>Vorlesung</span>
					</td>
					<td valign="center" style="width:70%">
						<div style="font-size:15px; font-weight:bold; line-height:2">
							<div class="progress">
								<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $crit1Prozent ?>%">
									<?php echo $crit1Prozent ?>%
								</div>
							</div>
						</div>
					</td>
				</tr>
				
				<tr>
					<td valign="top" style="width:30%">
						<span>Aufwand für gute Note</span>
					</td>
					<td valign="center" style="width:70%">
						<div style="font-size:15px; font-weight:bold; line-height:2">
							<div class="progress">
								<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $crit2Prozent ?>%">
									<?php echo $crit2Prozent ?>%
								</div>
							</div>
						</div>
					</td>
				</tr>
				
				<tr>
					<td valign="top" style="width:30%">
						<span>Kriterium3</span>
					</td>
					<td valign="center" style="width:70%">
						<div style="font-size:15px; font-weight:bold; line-height:2">
							<div class="progress">
								<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $crit3Prozent ?>%">
									<?php echo $crit3Prozent ?>%
								</div>
							</div>
						</div>
					</td>
				</tr>
				
				<tr>
					<td valign="top" style="width:30%">
						<span>Kriterium4</span>
					</td>
					<td valign="center" style="width:70%">
						<div style="font-size:15px; font-weight:bold; line-height:2">
							<div class="progress">
								<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $crit4Prozent ?>%">
									<?php echo $crit4Prozent ?>%
								</div>
							</div>
						</div>
					</td>
				</tr>
				
				<tr>
					<td valign="top" style="width:30%">
						<span>Kriterium5</span>
					</td>
					<td valign="center" style="width:70%">
						<div style="font-size:15px; font-weight:bold; line-height:2">
							<div class="progress">
								<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $crit5Prozent ?>%">
									<?php echo $crit5Prozent ?>%
								</div>
							</div>
						</div>
					</td>
				</tr>
				</table>
			</div>
		</div>
		
		<!--Kommentare-->
		<div class="col-md-2 head_left">
			Kommentare
		</div>

		<div class="col-md-10 well" id="commentsection">

			<?php
			//Richtige Dropdown-Auswahl der Sortierung auswählen
			
			$date_new = "";
			$date_old = "";
			$rating_best = "selected";
			$rating_worst = "";
			
			if (isset($_GET['sortBy'])){
				switch($_GET['sortBy']){
					case "dateDESC":
						$date_new = "selected";
						$date_old = "";
						$rating_best = "";
						$rating_worst = "";
						break;
					case "dateASC":
						$date_new = "";
						$date_old = "selected";
						$rating_best = "";
						$rating_worst = "";
						break;
					case "ratingASC":
						$date_new = "";
						$date_old = "";
						$rating_best = "selected";
						$rating_worst = "";
						break;
					case "ratingDESC":
						$date_new = "";
						$date_old = "";
						$rating_best = "";
						$rating_worst = "selected";
						break;
				}
			}
			?>
			
			<form class="form-inline" action="orderComments_submit.php?subject=<?php echo $subject ?>" method="post">
			<label>Sortieren nach: &nbsp </label>
			<select class="form-control" name="commentorder" id="commentorder">
				<option value="date_newFirst" <?php echo $date_new ?>>Datum (Neuste zuerst)</option>
				<option value="date_newLast" <?php echo $date_old ?>>Datum (Älteste zuerst)</option>
				<option value="rating_bestFirst" <?php echo $rating_best ?>>Bewertung (Beste zuerst)</option>
				<option value="rating_worstFirst" <?php echo $rating_worst ?>>Bewertung (Schlechteste zuerst)</option>
			</select>
			<button type="submit" class="btn btn-primary" >Sortieren</button>
			</form>
			
			<br>
		
			<?php
			//Datenabfrage für Kommentare
			
			$orderBy = "comment_rating";
			$orderDirection = "DESC";
			
			if (isset($_GET['sortBy'])){
				switch($_GET['sortBy']){
					case "dateDESC":
						$orderBy = "time_stamp";
						$orderDirection = "DESC";
						break;
					case "dateASC":
						$orderBy = "time_stamp";
						$orderDirection = "ASC";
						break;
					case "ratingASC":
						$orderBy = "comment_rating";
						$orderDirection = "DESC";
						break;
					case "ratingDESC":
						$orderBy = "comment_rating";
						$orderDirection = "ASC";
						break;
				}
			}
			
			$sql = "
				SELECT * FROM ratings
				WHERE subject_ID = '".$subjectData['ID']."'
				ORDER BY ".$orderBy." ".$orderDirection."";
			
			$result = mysqli_query($con,$sql);
			
			if (mysqli_num_rows($result) == 0){
				echo "Noch keine Kommentare vorhanden.";
			}
			
			while($comments = mysqli_fetch_assoc($result)){
				
				$recommend = "<p style=\"font-weight:bold; font-size:12px\"> <img src=\"pictures/greentick.png\" style=\"width:12px;height:12px;\"> Der Kommentator würde diese Veranstaltung empfehlen.</p>";
				if ($comments['recommendation'] == 0) $recommend = "";
				
				$sql2 = "
					SELECT *
					FROM ratings
					JOIN users ON ratings.user_ID = users.user_ID
					WHERE ID = '".$comments['ID']."';
				";
				$join = mysqli_query($con,$sql2);
				$rows = mysqli_fetch_assoc($join);
				
				echo "
					<div class=\"well\" style=\"background-color:white; border-radius:none\">
						<div class=\"media\">
							<div class=\"media-left\">
								<p style=\"white-space: nowrap; padding-right:10px;\"><span style=\"font-weight:bold; cursor: pointer; cursor: hand;\" onclick=\"colorChange(this.id)\" id=\"".$comments['ID']."do\"> &minus; </span><span style=\"padding-right:3px;\" id=\"".$comments['ID']."\">".$comments['comment_rating']."</span><span style=\"font-weight:bold; cursor: pointer; cursor: hand;\" onclick=\"colorChange(this.id)\" id=\"".$comments['ID']."up\">+</span></p>
								<p class=\"nowrap confirmation\" id=\"".$comments['ID']."confirmation\"></p>
							</div>
							<div class=\"media-body\">
								<h4 class=\"media-heading textbold\">".$comments['comment_title']."</h4>
								<p> ".$comments['comment_body']." </p>
								".$recommend."
								<hr style=\"margin:10px\">
								<div style=\"font-size:10px\">".$rows['username']." &#124; ".$comments['time_stamp']."</div>
							</div>
						</div>
					</div>
				";
			}
			?>
			
			<!-- Farbänderung bei Kommentarbewertung -->
			<script>
			function colorChange(id) {
				// Check, ob User Kommentar bereits bewertet hat
				if (window.XMLHttpRequest){ // AJAX nutzen mit IE7+, Chrome, Firefox, Safari, Opera
					xmlhttp=new XMLHttpRequest();
				}else{// AJAX mit IE6, IE5
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						if (this.responseText.trim() == true){
							document.getElementById(id.substring(0, id.length - 2) + 'confirmation').style.color = 'red';
							document.getElementById(id.substring(0, id.length - 2) + 'confirmation').innerHTML = 'Bereits <br> bewertet';
							setTimeout(function() {
								document.getElementById(id.substring(0, id.length - 2) + 'confirmation').innerHTML = '';
							}, 3000);
							document.getElementById(id.substring(0, id.length - 2) + 'up').style.cursor = 'default';
							document.getElementById(id.substring(0, id.length - 2) + 'do').style.cursor = 'default';
							document.getElementById(id.substring(0, id.length - 2) + 'up').style.color = 'lightgrey';
							document.getElementById(id.substring(0, id.length - 2) + 'do').style.color = 'lightgrey';
						} else{
							// Frontend ändern
							if (id.substring(id.length - 2, id.length) == 'do') {
								document.getElementById(id).style.color = 'red';
								document.getElementById(id.substring(0, id.length - 2)).innerHTML = document.getElementById(id.substring(0, id.length - 2)).innerHTML - 1;
								document.getElementById(id).onclick = '';
								document.getElementById(id).style.cursor = 'default';
								document.getElementById(id.substring(0, id.length - 2) + 'up').onclick = '';
								document.getElementById(id.substring(0, id.length - 2) + 'up').style.cursor = 'default';
							} else {
								document.getElementById(id).style.color = 'green';
								document.getElementById(id.substring(0, id.length - 2)).innerHTML = document.getElementById(id.substring(0, id.length - 2)).innerHTML - (-1);
								document.getElementById(id).onclick = '';
								document.getElementById(id).style.cursor = 'default';
								document.getElementById(id.substring(0, id.length - 2) + 'do').onclick = '';
								document.getElementById(id.substring(0, id.length - 2) + 'do').style.cursor = 'default';
							}
							
							document.getElementById(id.substring(0, id.length - 2) + 'confirmation').style.color = 'green';
							document.getElementById(id.substring(0, id.length - 2) + 'confirmation').innerHTML = 'Gewertet';
							
							setTimeout(function() {
								document.getElementById(id.substring(0, id.length - 2) + 'confirmation').remove();
							}, 3000);
							
							//Datenbank aktualisieren
							if (window.XMLHttpRequest){ // AJAX nutzen mit IE7+, Chrome, Firefox, Safari, Opera
								xmlhttp=new XMLHttpRequest();
							}else{// AJAX mit IE6, IE5
								xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
							}
							
							var commentID = id.substring(0, id.length - 2);
							var userID = <?php echo $userRow['user_ID']; ?>;
							var subjectID = <?php echo $subjectData['ID']; ?>;
							var ratingDirection = id.substring(id.length - 2, id.length);
							
							xmlhttp.open("POST","submitCommentRating.php?commentID="+commentID+"&userID="+userID+"&subjectID="+subjectID+"&ratingDirection="+ratingDirection,true);
							xmlhttp.send();
						}
					}
				};
			
				var commentID = id.substring(0, id.length - 2);
				var userID = <?php echo $userRow['user_ID']; ?>;
				
				xmlhttp.open("GET","checkExistence.php?commentID="+commentID+"&userID="+userID,true);
				xmlhttp.send();
			}
			</script>	
		</div>
	</div>
	
	<!--Anzeige falls noch kein Rating vorhanden-->
	<div class="noRating" <?php echo $displayNoRatings ?>>
		<br>
		<h2 class="noRatingText">Über diese Veranstaltung wissen wir bisher leider noch gar nichts -<br>sei der Erste, der sie bewertet!<h2>
		<div style="text-align:center">
			<button style="font-size:20px" type="button" a href="#myModal" role="button" class="btn btn-warning" data-toggle="modal">Diese Veranstaltung jetzt bewerten!</button>
		</div>
	</div>
</div>
	
<!-- End of page. Modal for Rating -->
<!-- Maybe include slider instead of likert: http://foundation.zurb.com/sites/docs/v/5.5.3/components/range_slider.html -->

<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h2 class="modal-title">Bewertung für: <?php echo $subjectData['subject_name'] ?></h2>
	</div>
	<div class="modal-body">
		<?php
		
		$sql_modal="
			SELECT *
			FROM ratings
			WHERE subject_ID = '".$subjectData['ID']."' AND user_ID = '".$userRow['user_ID']."';
		";
		
		$result_modal = mysqli_query($con,$sql_modal);
		
		if(mysqli_num_rows($result_modal)>=1){
			echo "<h3>Du hast diese Veranstaltung bereits bewertet!</h3>
				<br>
				<button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" aria-hidden=\"true\">Schließen</button>
			";
			exit;
		}
		
		?>
		
		<form action="rating_submit.php?subject=<?php echo $subject?>" method="POST">
		
			<p style="font-weight: bold; font-size: 20px; color: rgb(0, 51, 153)">Veranstaltungsbewertung</p>
			
			<br>

			<p style="font-weight:bold">Wie bewertest du die Vorlesung ingesamt?</p>
			
			<div class="col-md-3">
				<p style="font-style:italic">Sehr schlecht</p>
			</div>
			<div class="col-md-6">
				<label class="radio-inline" style="vertical-align:top"><input type="radio" value='1' name="criterion1" required></label>
				<label class="radio-inline" style="vertical-align:top"><input type="radio" value='2' name="criterion1"></label>
				<label class="radio-inline" style="vertical-align:top"><input type="radio" value='3' name="criterion1"></label>
				<label class="radio-inline" style="vertical-align:top"><input type="radio" value='4' name="criterion1"></label>
				<label class="radio-inline" style="vertical-align:top"><input type="radio" value='5' name="criterion1"></label>
				<label class="radio-inline" style="vertical-align:top"><input type="radio" value='6' name="criterion1"></label>
				<label class="radio-inline" style="vertical-align:top"><input type="radio" value='7' name="criterion1"></label>
			</div>
			<div class="col-md-3">
			<p style="font-style:italic">Sehr gut</p>
			</div>

			<br><br><br>

			<p style="font-weight:bold">Wie empfandest du das Verhältnis von Aufwand zu guten Noten?</p>

			<div class="col-md-3">
				<p style="font-style:italic">Sehr unangemessen</p>
			</div>
			<div class="col-md-6">
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='1' name="criterion2" required></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='2' name="criterion2"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='3' name="criterion2"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='4' name="criterion2"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='5' name="criterion2"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='6' name="criterion2"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='7' name="criterion2"></label>
			</div>
			<div class="col-md-3">
			<p style="font-style:italic">Sehr angemessen</p>
			</div>
			
			<br><br><br>

			<p style="font-weight:bold">Was hälst du von Kriterium 3?</p>

			<div class="col-md-3">
				<p style="font-style:italic">Sehr schlecht</p>
			</div>
			<div class="col-md-6">
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='1' name="criterion3" required></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='2' name="criterion3"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='3' name="criterion3"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='4' name="criterion3"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='5' name="criterion3"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='6' name="criterion3"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='7' name="criterion3"></label>
			</div>
			<div class="col-md-3">
			<p style="font-style:italic">Sehr gut</p>
			</div>
			
			<br><br><br>
			
			<p style="font-weight:bold">Was hälst du von Kriterium 4?</p>

			<div class="col-md-3">
				<p style="font-style:italic">Sehr unangemessen</p>
			</div>
			<div class="col-md-6">
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='1' name="criterion4" required></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='2' name="criterion4"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='3' name="criterion4"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='4' name="criterion4"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='5' name="criterion4"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='6' name="criterion4"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='7' name="criterion4"></label>
			</div>
			<div class="col-md-3">
			<p style="font-style:italic">Sehr angemessen</p>
			</div>
			
			<br><br><br>
			
			<p style="font-weight:bold">Was hälst du von Kriterium 5?</p>

			<div class="col-md-3">
				<p style="font-style:italic">Sehr unangemessen</p>
			</div>
			<div class="col-md-6">
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='1' name="criterion5" required></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='2' name="criterion5"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='3' name="criterion5"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='4' name="criterion5"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='5' name="criterion5"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='6' name="criterion5"></label>
				<label class="radio-inline" style="vertical-align:center"><input type="radio" value='7' name="criterion5"></label>
			</div>
			<div class="col-md-3">
			<p style="font-style:italic">Sehr angemessen</p>
			</div>
			
			<br><br><br>
			
			<p style="font-weight:bold">Würdest du diese Veranstaltung weiterempfehlen?</p>
			
			<label class="radio-inline"><input type="radio" name="recommendation" value='1' required>Ja</label>
			<label class="radio-inline"><input type="radio" name="recommendation" value='0'>Nein</label>
						
			<br>
			<hr>
			
			<p style="font-weight: bold; font-size: 20px; color: rgb(0, 51, 153)">Kommentar</p>
			
			<div class="form-group">
				<label for="usr">Titel:</label>
				<input name="title" type="text" class="form-control" required>
			</div>
			<div class="form-group">
				<label for="comment">Inhalt:</label>
				<textarea name="comment" class="form-control" rows="5" required></textarea>
			</div>
			
			<hr>
			
			<button type="submit" class="btn btn-primary">Bewertung abschicken</button>
		</form>
		
		</div><!-- End of Modal body -->
	</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->

</body>
</html>
