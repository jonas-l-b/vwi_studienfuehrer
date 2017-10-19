<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

include "saveSubjectToVariable.php";

//include "loadSubjectData.php";
include "sumVotes.php";


function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'Jahr',
        'm' => 'Monat',
        'w' => 'Woche',
        'd' => 'Tag',
        'h' => 'Stunde',
        'i' => 'Minute',
        's' => 'Sekunde',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
			if($v == 'Jahr' || $v == 'Monat' || $v == 'Tag'){
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 'en' : '');
			}
			else {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 'n' : '');
			}
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? 'vor ' . implode(', ', $string) : 'gerade eben';
}

?>

<body>

<?php include "inc/nav.php" ?>

<div class="container">
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

	<!--Überschrift und Info und FavIcon-->
	<?php
	//Check favourite status
	$result = mysqli_query($con, "SELECT * FROM favourites WHERE user_ID = '".$userRow['user_ID']."' AND subject_id = '".$subjectData['ID']."'");
	if(mysqli_num_rows($result) >= 1){
		$favClass = "glyphicon glyphicon-star favouriteStar";
		$favColor = "rgb(255, 204, 0)";
	} else{
		$favClass = "glyphicon glyphicon-star-empty favouriteStar";
		$favColor = "grey";
	}
	?>
	
	<div class="row">
		<div class="col-xs-11 col-sm-11 col-md-11 col-lg-11" style="border-bottom: 1px solid #dedede;">
			<h1> <?php echo $subjectData['subject_name'] ?> </h1>
		</div>
		<div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
			<h1><span id="favIcon" style="color:<?php echo $favColor ?>; font-size:35px; cursor: pointer; cursor: hand;" class="<?php echo $favClass ?>"></span> </h1>
		</div>
	</div>
	<p style="font-size:.9em;"><b>Kennung: </b><?php echo $subjectData['identifier'] ?>&nbsp;&nbsp;&nbsp;&nbsp;| <b>LV-Nummer: </b> <?php echo $subjectData['lv_number'] ?>

	<div>
		<p><span style="font-size:1.3em;"><strong><span class="glyphicon glyphicon-calendar"></span></strong></span> &nbsp;&nbsp;<?php echo $subjectData['semester'] ?></p>
		<p><span style="font-size:1.3em;"><strong><span class="glyphicon glyphicon-bullhorn"></span></strong></span> &nbsp;&nbsp;<?php echo $subjectData['language'] ?></p>
	</div>
	
	<div class="infoContainer">
	<?php
	$box = array(
		array("Modultyp", "Teil der Module", "Level", "ECTS", "Dozent"),
		array($module_types, $part_of_modules, $levels, $subjectData['subject_ECTS'], $lecturers)
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
	
	
	<?php
		$sql_modal="
			SELECT *
			FROM ratings
			WHERE subject_ID = '".$subjectData['ID']."' AND user_ID = '".$userRow['user_ID']."';
		";
		
		$result_modal = mysqli_query($con,$sql_modal);
		if(mysqli_num_rows($result_modal)>=1){
			$ratingButtonText = "Bereits bewertet - Danke!";
			$ratingButtonDisabled = "disabled";
		}else{
			$ratingButtonText = "Diese Veranstaltung jetzt bewerten!";
		}
	?>
	<div style="margin-bottom:15px">
		<button <?php echo $displayRatings ?> type="button" href="#" id="jetztBewertenButton2" role="button" class="btn btn-primary" <?php if(isset($ratingButtonDisabled)) echo $ratingButtonDisabled?>><?php echo $ratingButtonText ?></button>
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
				<?php
					echo $twig->render('bewertungen.template.html', 
							array(	'rating' => array(
													'crit1'=> $crit1Prozent,
													'crit2'=> $crit2Prozent,
													'crit3'=> $crit3Prozent,
													'crit4'=> $crit4Prozent,
													'crit5'=> $crit5Prozent,
													)
								));
				?>
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
			<select class="form-control" name="commentorder" id="commentorder" onchange="this.form.submit()">
				<option value="date_newFirst" <?php echo $date_new ?>>Datum (Neuste zuerst)</option>
				<option value="date_newLast" <?php echo $date_old ?>>Datum (Älteste zuerst)</option>
				<option value="rating_bestFirst" <?php echo $rating_best ?>>Bewertung (Beste zuerst)</option>
				<option value="rating_worstFirst" <?php echo $rating_worst ?>>Bewertung (Schlechteste zuerst)</option>
			</select>
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
				
				//Erstellt Variable, um Bearbeiten-Button nur für Ersteller anzuzeigen
				$displayEdit = "display:none;";
				$editClassIdentifier = "";
				$displayReport ="";
				
				//displayEdit auskommentiert, da noch diskutiert werden muss!
				//Falls Funktion nicht behalten werden soll, alles löschen, was damit in Zusammenhang steht!
				
				if($comments['user_ID'] == $userRow['user_ID']){
					$displayEdit = "";
					$editClassIdentifier = "editButtonIdentificationClass";
					$displayReport = "display:none;";
				}
				
				
				
				echo "
					<div class=\"well\" style=\"background-color:white; border-radius:none\">
						<div class=\"media\">
							<div class=\"media-left\">
								<p style=\"white-space: nowrap; padding-right:10px;\"><span style=\"font-weight:bold; cursor: pointer; cursor: hand;\" onclick=\"colorChange(this.id)\" id=\"".$comments['ID']."do\"> &minus; </span><span style=\"padding-right:3px;\" id=\"".$comments['ID']."\">".$comments['comment_rating']."</span><span style=\"font-weight:bold; cursor: pointer; cursor: hand;\" onclick=\"colorChange(this.id)\" id=\"".$comments['ID']."up\">+</span></p>
								<p class=\"nowrap confirmation\" id=\"".$comments['ID']."confirmation\"></p>
							</div>
							<div class=\"media-body\">
								<p> ".$comments['comment']." </p>
								".$recommend."
								<hr style=\"margin:10px\">
								<div style=\"font-size:10px\">
									".$rows['username']." &#124; ". time_elapsed_string($comments['time_stamp'])."
									<span style=\"float:right;\">
										<button type=\"button\" id=\"bewertungAendernButton\" style=\"".$displayEdit."\" role=\"button\" class=\"editTrashButton $editClassIdentifier\"  title=\"Kommentar bearbeiten\"> <span class=\"glyphicon glyphicon-pencil\"></span></button>
										<button type=\"button\" style=\"".$displayEdit."\" href=\"#deleteModal\" role=\"button\" class=\"editTrashButton\" data-toggle=\"modal\"> <span class=\"glyphicon glyphicon-trash\"></span></button>
										<button onclick=\"showStats(this.id)\" id=\"commentstats".$comments['ID']."\" type=\"button\" href=\"#\" role=\"button\" class=\"editTrashButton\"> <span class=\"glyphicon glyphicon-stats\" title=\"Einzelbewertung anzeigen\" ></span></button>
									</span>
									<span style=\"float:right; ".$displayReport."\">
										<button type=\"button\" role=\"button\" data-toggle=\"modal\" data-id=\"".$comments['ID']."\" class=\"editTrashButton reportButton\" title=\"Kommentar melden\"> <span class=\"glyphicon glyphicon-exclamation-sign\"></span></button>
									</span>
								</div>
							</div>
						</div>
					</div>
				";
			}
			?>
			<!-- Zeig Stats zu Kommentar -->
			<script>
				function showStats(id){
						$('#commentsStatsModal').modal('show');
						$('#commentStats').html('<br /><br /><div class="loader"><div></div></div><br /><br />');
						$('#commentStats').load("lade_kommentar_statistik.php?kommentar="+id.replace( /^\D+/g, ''), function( response, status, xhr ) {
						  if ( status == "error" ) {
							$('#commentStats').html('<strong>Daten können nicht geladen werden.</strong>');
						  }
						});
				}	
			</script>
			
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
	<div class="noRatingBox" <?php echo $displayNoRatings ?>>
		<br>
		<h3 class="noRatingText">Über diese Veranstaltung wissen wir bisher leider noch gar nichts -<br>sei der Erste, der sie bewertet!<h3>
		<div style="text-align:center">
			<button style="font-size:20px" id="jetztBewertenButton" type="button" href="#" role="button" class="btn noRatingButton">Diese Veranstaltung jetzt bewerten!</button>
		</div>
	</div>
</div>

<!-- Modal für Bewertungsänderung-->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h2 class="modal-title">Bewertungsänderung für: <?php echo $subjectData['subject_name'] ?></h2>
	</div>
	<div class="modal-body">
		<div id="bewertungAendernForm"></div>
		</div><!-- End of Modal body -->
	</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->

<!-- Modal zum Löschen einer Bewertung-->
<div id="deleteModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h2 class="modal-title">Bewertung löschen für: <?php echo $subjectData['subject_name'] ?></h2>
	</div>
		<div class="modal-body">
			<form action="rating_delete.php?subject=<?php echo $subject?>" method="POST">
				<p style="font-weight: bold; font-size: 20px; color: rgb(0, 51, 153)">Willst du deine Bewertung wirklich löschen?</p>
				<p> Das beinhaltet deine eigentliche Bewertung und den Kommentar, den du abgegeben hast.<br>
				<span style="color:red">Dieser Schritt kann nicht rückgängig gemacht werden.</span></p>
				<button type="submit" class="btn btn-danger">Bewertung unwiderruflich löschen</button>
			</form>
		</div><!-- End of Modal body -->
	</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->

<!-- Scrpit und Modal zum Melden einer Bewertung-->
<script>
$(document).ready(function(){
	$(".reportButton").click(function(){
		$("#commentId").val($(this).data('id'));
		$('#reportCommentModal').modal({show:true});
	});
	
	$("#reportForm").submit(function(e){
		$.ajax({
			type: "POST",
			url: "report_submit.php",
			data: $("#reportForm").serialize(),
			success: function(data) {
				//alert(data);
				if(data.trim().substr(0,6) == "erfolg"){ //substring stellt sicher, dass hier auch reingegangen wenn E-Mail-Fehler auftritt
					$('.modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Dein Anliegen wurde erfolgreich an uns übermittelt!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
				}else{
					$('.modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Bei der Übermittlung Deines Anliegens ist womöglich ein Fehler aufgetreten!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
				}
			}
		});
		e.preventDefault();
	});
});
</script>

<div id="reportCommentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h2 class="modal-title">Kommentar melden</h2>
	</div>
		<div class="modal-body">
			<form action="/" id="reportForm">
				<input type="hidden" name ="commentId" id="commentId" />

				<div class="form-group">
					<label>Warum möchtest du diesen Kommentar melden?</label>
					<textarea name="comment" id="comment" class="form-control" maxlength="5000" placeholder="Hilf uns zu verstehen, warum du diesen Kommentar unangebracht findest." rows="5" required></textarea>
				</div>
				
				<p id ="commentWarning"></p>
				
				<script>
				$('#comment').on("propertychange input textInput", function() {
					if($('#comment').val().length < 4500){
						$('#commentWarning').html("");
					}else if($('#comment').val().length >= 4500 && $('#comment').val().length < 4900){	
						$('#commentWarning').css('color', 'black');
						$('#commentWarning').html("Noch " + (5000 - $('#comment').val().length) + " Zeichen übrig");
					}else{
						$('#commentWarning').css('color', 'red');
						$('#commentWarning').html("Noch " + (5000 - $('#comment').val().length) + " Zeichen übrig");
					}
				});				
				</script>
				
				<div name="answer" class="checkbox">
					<label id="answer"><input name="answer" type="checkbox">Ich möchte gerne eine Antwort erhalten</label>
				</div>
				
				<button id="submitCommentReport" type="submit" class="btn btn-primary">Nachricht abschicken</button>
			</form>
		</div><!-- End of Modal body -->
	</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->

<!-- Modal für Bewertung -->
<!-- Maybe include slider instead of likert: http://foundation.zurb.com/sites/docs/v/5.5.3/components/range_slider.html -->
<div id="jetztBewertenModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title">Bewertung für: <?php echo $subjectData['subject_name'] ?></h2>
			</div>
			<div class="modal-body">
				<div id="bewertungAbgebenForm"></div>
			</div><!-- End of Modal body -->
		</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->


<div class="snackbar" id="snackbarFavAddSuccess">Wir haben die Veranstaltung deinen Favoriten hinzugefügt.</div>
<div class="snackbar" id="snackbarFavRemSuccess">Wir haben die Veranstaltung aus deinen Favoriten entfernt.</div>
<div class="snackbar" id="snackbarFavAddFail">Wir konnten die Veranstaltung leider nicht deinen Favoriten hinzufügen. Bitte überprüfe deine Internetverbindung.</div>
<div class="snackbar" id="snackbarFavRemFail">Wir konnten die Veranstaltung leider nicht aus deinen Favoriten entfernen. Bitte überprüfe deine Internetverbindung.</div>

<div id="commentsStatsModal" tabindex="-1" aria-hidden="true" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Einzelbewertung</h4>
      </div>
      <div class="modal-body">
		<div id="commentStats"></div>
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){
		$("#favIcon").click(function(){
			if($("#favIcon").attr("class") == "glyphicon glyphicon-star-empty favouriteStar"){
				$.post( "favourites_newEntry.php", {
					user_id: "<?php echo $userRow['user_ID'] ?>", 
					subject_id: "<?php echo $subjectData['ID'] ?>"}	)
				  .done(function() {
					$("#favIcon").attr("style", "color:rgb(255, 204, 0); font-size:35px; cursor: pointer; cursor: hand;");
					$("#favIcon").attr("class", "glyphicon glyphicon-star favouriteStar");
					$('#snackbarFavAddSuccess').addClass('show');
					setTimeout(function(){ $('#snackbarFavAddSuccess').removeClass('show'); }, 3000);
				  })
				  .fail(function() {
					$('#snackbarFavAddFail').addClass('show');
					setTimeout(function(){ $('#snackbarFavAddFail').removeClass('show'); }, 3000);
				  });
			} else{
				$.post( "favourites_removeEntry.php", {
					user_id: "<?php echo $userRow['user_ID'] ?>", 
					subject_id: "<?php echo $subjectData['ID'] ?>"} )
				 .done(function() {
					$("#favIcon").attr("style", "color:grey; font-size:35px; cursor: pointer; cursor: hand;");
					$("#favIcon").attr("class", "glyphicon glyphicon-star-empty favouriteStar");
					$('#snackbarFavRemSuccess').addClass('show');
					setTimeout(function(){ $('#snackbarFavRemSuccess').removeClass('show'); }, 3000);
				  })
				 .fail(function() {
					$('#snackbarFavRemFail').addClass('show');
					setTimeout(function(){ $('#snackbarFavRemFail').removeClass('show'); }, 3000);
				});
			}
		});
	
	var bewertenLaden = function(){
			$('#jetztBewertenModal').modal('show');
			$('#bewertungAbgebenForm').html('<br /><br /><div class="loader"><div></div></div><br /><br />');
			$('#bewertungAbgebenForm').load("lade_bewertungsform.php?subject=<?php echo $subject?>", function( response, status, xhr ) {
			  if ( status == "error" ) {
				$('#bewertungAbgebenForm').html('<strong>Daten können nicht geladen werden.</strong>');
			  }
			});
	}
	$('#jetztBewertenButton').click(bewertenLaden);
	$('#jetztBewertenButton2').click(bewertenLaden);
	
	var aendernLaden = function(){
			$('#editModal').modal('show');
			$('#bewertungAendernForm').html('<br /><br /><div class="loader"><div></div></div><br /><br />');
			$('#bewertungAendernForm').load("lade_bewertungsform.php?subject=<?php echo $subject?>&filled=true", function( response, status, xhr ) {
			  if ( status == "error" ) {
				$('#bewertungAendernForm').html('<strong>Daten können nicht geladen werden.</strong>');
			  }
			});
	}
	$('.editButtonIdentificationClass').click(aendernLaden);
});
</script>

</body>
</html>
