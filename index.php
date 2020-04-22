<html lang="de">

<?php
include "sessionsStart.php";
include "header.php";
include "connect.php";
include "sumVotes.php";
?>

<body>

<?php include "inc/nav.php" ?>


<div class="container">
	<?php
	//Variablen, die dafür sorgen, dass im Falle einer fehlerhaften oder fehlenden ID in der URL eine entsprechende Nachricht angezeigt wird
	$showMain = "";
	$showFailedLoad = "none";
	
	// Subject aus URL speichern
	if (isset($_GET['subject'])){
		$subject = strval ($_GET['subject']);
	}
	else{
		//$showMain = "none";
		//$showFailedLoad = "";
		echo "<script>window.location.href = 'tree.php';</script>";
	}
	
	/*Infos aus DB ziehen*/
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
		SELECT DISTINCT subjects.ID as ID, subject_name, subjects.ID AS subject_id, identifier, exam_type, subjects.requirements AS subject_requirements, subjects.ECTS AS subject_ECTS, semester, language, subjects.active AS active
		".$sqlBody."
		WHERE subjects.ID = '".$subject."'
	";
	$result = mysqli_query($con,$sql);

	// Check, ob Datensatz existiert
	if (mysqli_num_rows($result) >= 1 ) {
		$subjectData = mysqli_fetch_assoc($result);
	} else {
		$showMain = "none";
		$showFailedLoad = "";
	}

	//part_of_modules
	$sql = "
		SELECT DISTINCT modules.name, modules.module_id, type
		".$sqlBody."
		WHERE subjects.ID = ".$subjectData['ID']."
		ORDER BY modules.name
	";
	$result = mysqli_query($con,$sql);
	$part_of_modules = "";
	while($row = mysqli_fetch_assoc($result)){
		$part_of_modules .= "<a href=\"module.php?module_id=".$row['module_id']."\">".$row['name']."</a> (".$row['type'].")<br>";
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
		SELECT DISTINCT lecturers.lecturer_ID, lecturers.name
		".$sqlBody."
		WHERE subjects.ID = ".$subjectData['ID']."
		ORDER BY abbr, lecturers.last_name
	";
	$result = mysqli_query($con,$sql);
	$lecturers = "";
	while($row = mysqli_fetch_assoc($result)){
		$sql_abbr = mysqli_query($con,"
						SELECT *
						FROM institutes
						JOIN lecturers_institutes ON institutes.institute_ID=lecturers_institutes.institute_ID
						WHERE lecturer_ID = '".$row['lecturer_ID']."'
			");
		$abbr = "";
		while($abbr_row = mysqli_fetch_assoc($sql_abbr)){
			$abbr .= "<a href=\"institute.php?institute_id=".$abbr_row['institute_ID']."\">".$abbr_row['abbr']."</a>, ";
		}
		$abbr = substr($abbr, 0, -2);



		$lecturers .= "<a href=\"lecturer.php?lecturer_id=".$row['lecturer_ID']."\">".$row['name']."</a> (".$abbr.")<br>";
	}
	$lecturers = substr($lecturers, 0, -4);
	?>

	<?php
	//Vorbereiten: Variablen, die dafür sorgen, dass der Bewertungsteil nur angezeigt wird, wenn auch Bewertungen vorhanden sind;
	//andernfalls wird der Jetzt-bewerten-Teil angezeigt
	$result = mysqli_query($con, "SELECT * FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");

	$displayRatings = "";
	$displayNoRatings = "style=\"display:none\"";

	if (mysqli_num_rows($result) == 0){ //Falls noch keine Bewertungen vorhanden
		$displayRatings = "style=\"display:none\"";
		$displayNoRatings = "";
	}
	?>

	<?php
	//Favourites: Check favourite status
	$result = mysqli_query($con, "SELECT * FROM favourites WHERE user_ID = '".$userRow['user_ID']."' AND subject_id = '".$subjectData['ID']."'");
	if(mysqli_num_rows($result) >= 1){
		$favClass = "glyphicon glyphicon-star favouriteStar";
		$favColor = "rgb(255, 204, 0)";
	} else{
		$favClass = "glyphicon glyphicon-star-empty favouriteStar";
		$favColor = "grey";
	}
	?>

	<div style="display:<?php echo $showMain?>">

		<div style="border-bottom: 1px solid #dedede;">
			<p>
				<h1>
					<div class="enter">
						<?php echo $subjectData['subject_name'] ?>
						<span id="favIcon" style="color:<?php echo $favColor ?>;cursor: pointer; cursor: hand;" class="<?php echo $favClass ?>"></span>
					</div>
				</h1>
			</p>
		</div>

		<div class="general-flex-container">
			<div class="general-flex-item" style="font-size:.9em;"><b>Kennung: </b><?php echo $subjectData['identifier'] ?></div>
			<div class="general-flex-item" style="font-size:.9em;"><a id="contact2" style="cursor: pointer; cursor: hand;">Fehler dieser Seite melden</a></div>
		</div>
		
		<?php
		if ($subjectData['active'] == 0){
			echo '
				<br>
				<div class="alert alert-danger">
					Diese Veranstaltung existiert im aktuellen Modulhandbuch nicht mehr.
				</div>
			';
		}
		?>

		<div id="contactModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h2 class="modal-title">Kontakt</h2>
			</div>
				<div class="modal-body">
					<div id="contactModalBody"></div>
				</div><!-- End of Modal body -->
			</div><!-- End of Modal content -->
			</div><!-- End of Modal dialog -->
		</div><!-- End of Modal -->

		<script>
		$(document).ready(function(){
			$("#contact2").click(function(){
				$('#contactModal').modal('show');
				$('#contactModalBody').html('<br /><br /><div class="loader"><div></div></div><br /><br />');
				$('#contactModalBody').load("contactModalvorgefullt.php", function( response, status, xhr ) {
					if ( status == "error" ) {
						$('#contactModalBody').html('<strong>Daten können nicht geladen werden.</strong>');
					}else{
						$('#area').attr("style", "");
						$('#subject').attr("style", "");

						$('#reason').attr("disabled", "disabled");
						$('#select_area').attr("disabled", "disabled");
						$('#select_subject').attr("disabled", "disabled");

						$("option[value='mistake']").attr('selected','selected');
						$('#select_area').val("subject");
						$('#select_subject').val("<?php echo $subject?>");

						$('.checkbox').show();
						$('#answer').html("<input name=\"answer\" type=\"checkbox\" checked>Ich möchte informiert werden, wenn der Fehler behoben wurde");
						$("#area :input").prop('required');
						$("#subject :input").prop('required');

						$('#comment').attr("placeholder", "Was genau ist inhaltlich falsch bei der Veranstaltung \""+$('#subject option:selected').text()+"\"?");
					}
				});
			});
		});
		</script>

		<?php
		//Bereits bewertet für Bewerten-Button
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
		<!--
		<button <?php if(isset($ratingButtonDisabled)) echo "style=\"display:none\"";?> data-toggle="tooltip" title="Jetzt Bewerten!" href="#" id="jetztBewertenButton2" role="button" type="button" class="btn btn-primary btn-circle btn-xl"><i class="glyphicon glyphicon-plus"></i></button>
		-->
		<button <?php if(isset($ratingButtonDisabled)) echo "style=\"display:none\"";?> data-toggle="tooltip" title="Jetzt Bewerten!" href="#" id="jetztBewertenButton2" role="button" type="button" class="btn addRatingButton">Jetzt bewerten!</button>
		
		<script>
		$(document).ready(function(){
			$('[data-toggle="tooltip"]').tooltip();
		});
		</script>

		<?php
		$result = mysqli_query($con, "SELECT * FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");

		//Diese Variablen sorgen dafür, dass der Bewertungsteil nur angezeigt wird, wenn auch Bewertungen vorhanden sind; andernfalls wird der Jetzt-bewerten-Teil angezeigt
		$displayRatings = "";
		$displayNoRatings = "style=\"display:none\"";
		$noRatingsYet = FALSE;

		if (mysqli_num_rows($result) == 0){ //Falls noch keine Bewertungen vorhanden
			$displayRatings = "style=\"display:none\"";
			$displayNoRatings = "";
			$noRatingsYet = TRUE;
		}
		?>
		<!--ENDE Überschirft, Veranstaltungsinfos und Favourite Icon-->

		<!-- Infobox-->
		<div id="" class="well">
		
			<div class="info-flex-container">

				<div class="info-flex-item">
					<div style="text-align:center">
						<span style="font-size:1.3em;"><strong><span data-toggle="tooltip" title="Veranstaltungsturnus" class="glyphicon glyphicon-calendar"></span></strong></span><br /><?php echo $subjectData['semester'] ?>
					</div>
				</div>
				
				<div class="info-flex-item">
					<div style="text-align:center">
						<span style="font-size:1.3em;"><strong><span data-toggle="tooltip" title="Leistungsumfang" class="glyphicon glyphicon-briefcase"></span></strong></span><br /><?php echo $subjectData['subject_ECTS'] ?> ECTS
					</div>
				</div>

				<div class="info-flex-item">
					<div style="text-align:center">
						<span style="font-size:1.3em;"><strong><span data-toggle="tooltip" title="Veranstaltungssprache" class="glyphicon glyphicon-bullhorn"></span></strong></span><br /><?php echo $subjectData['language'] ?>
					</div>
				</div>		

				<div class="info-flex-item">
					<div style="text-align:center">
						<b>Level</b><br />
						<?php echo $levels ?>
					</div>
				</div>
				
				<div class="info-flex-item">
					<div style="text-align:center">
						<b>Teile der Module</b><br />
						<?php echo $part_of_modules ?>
					</div>
				</div>

				<div class="info-flex-item">
					<div style="text-align:center">
						<b>Dozent(en)</b><br />
						<?php echo $lecturers; ?>
					</div>
				</div>
				
				<div class="info-flex-item">
					<div style="text-align:center">
						<b>Prüfungsart</b><br />
						<?php echo $subjectData['exam_type']; ?>
					</div>
				</div>

			</div>
			
			<?php
			if($subjectData['subject_requirements'] != ""){
			?>
				<hr>
				<div class="alert alert-warning" style="text-align:center">
					<strong>Aufgepasst!</strong>
					<?php
					$req = $subjectData['subject_requirements'];
					$req = str_replace(":", ":<br>", $req);
					$req = str_replace(".", ".<br>", $req);
					echo $req	
					?>
				</div>
			<?php
			}
			?>
			
			<!--
			<div align="center">
				<a data-toggle="collapse" href="#additionalInfo" id="moreInfo" style="background-color:white; padding:4px; border-radius:5px; text-decoration:none">Weitere Infos anzeigen</a>
			</div>
			-->
			
			<script>
			$(document).ready(function(){
				$("#moreInfo").click(function(){
					if($("#moreInfo").html() == "Weitere Infos anzeigen"){
						$("#moreInfo").html("Weniger Infos anzeigen");
					}else{
						$("#moreInfo").html("Weitere Infos anzeigen");
					}
				});
			});
			</script>
			
			<div id="additionalInfo" class="collapse">
				<div class="info-flex-container">
					
					<div class="info-flex-item">
						<div style="text-align:center">
							<b>Voraussetungen</b><br />
							<?php echo $lecturers; ?>
						</div>
					</div>
					
					<div class="info-flex-item">
						<div style="text-align:center">
							<b>Prüfungsform</b><br />
							<?php echo $lecturers; ?>
						</div>
					</div>

					<div class="info-flex-item">
						<div style="text-align:center">
							<b>Notenskala</b><br />
							<?php echo $lecturers; ?>
						</div>
					</div>
					
				</div>
			</div>
			<hr>
			<div align="right" style="font-size:90%;">
				<?php
				$result=mysqli_query($con, "SELECT value FROM help WHERE name='infoDate'");
				$row=mysqli_fetch_assoc($result);
				echo "Stand der Informationen: " . $row['value'];
				?>	
			
				<a href="#" data-trigger="focus" data-toggle="dateOfInfo_popover" data-content="
						Die Informationen aus diesem Kasten kommen direkt aus dem Modulhandbuch. Wir beziehen diese direkt von der KIT-Seite und verändern sie grundsätzlich nicht.
					">
					<span class="glyphicon glyphicon-question-sign"></span>
				</a>
				<script>$('[data-toggle="dateOfInfo_popover"]').popover();</script>
			</div>
		
		</div>
		<!--ENDE Infobox-->	

		<!--START Bewertungsübersicht-->
		<div class="well" style="position:relative">
		
			<div <?php echo $displayNoRatings ?>>
				<h3 class="noRatingText">Über diese Veranstaltung wissen wir bisher leider noch gar nichts -<br>sei der Erste, der sie bewertet!<h3>
				<div style="text-align:center">
					<button style="font-size:20px" id="jetztBewertenButton" type="button" href="#" role="button" class="btn noRatingButton">Diese Veranstaltung jetzt bewerten!</button>
				</div>
			
			</div>

			<div <?php echo $displayRatings ?>>

				<?php
				$result = mysqli_query($con,"SELECT SUM(recommendation) AS value_sum FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
				$row = mysqli_fetch_assoc($result);
				$yes = $row['value_sum'];
				if ($yes == "") $yes = 0;

				$result = mysqli_query($con,"SELECT COUNT(recommendation) AS value_count FROM ratings WHERE subject_ID = '".$subjectData['ID']."'");
				$row = mysqli_fetch_assoc($result);
				$total = $row['value_count'];
				?>
				
				<?php
				$result = mysqli_query($con, "SELECT AVG(general0) FROM ratings WHERE subject_ID = ".$subjectData['ID']);
				$row = mysqli_fetch_assoc($result);
				?>
				
				<div class="general-flex-container">
					<div class="general-flex-item" style="font-size:20px;"><strong><?php echo $yes ?></strong> von <strong><?php echo $total ?></strong> <?php if($yes == 1){echo "würde";} else echo "würden" ?> diese Veranstaltung weiterempfehlen.</div>
					<div class="general-flex-item" style="font-size:20px;<?php if(isset($ratingButtonDisabled)) echo "padding-right: 25px;"?>">Gesamtbewertung: <b><?php echo round($row['AVG(general0)'], 1) ?></b> / 10</div>
				</div>
				
				<hr>
				<div <?php if(!isset($ratingButtonDisabled)) echo "style=\"display:none;\""?> class="ribbon"><span>Bewertet!</span></div>

				<div class="row">
					<div class="col-md-6">
						<?php
						//Lecture
						$items = array("lecture0", "lecture1", "lecture2");
						foreach($items as $key => $item){
							$result = mysqli_query($con, "SELECT AVG(".$item.") FROM ratings WHERE subject_ID = ".$subjectData['ID']);
							$row = mysqli_fetch_assoc($result);
							if(round($row['AVG('.$item.')'],1) < 0 ){
								$lecture[$key][0] = abs(round($row['AVG('.$item.')'],1));
								$lecture[$key][1] = 0;
							}else{
								$lecture[$key][0] = 0;
								$lecture[$key][1] = round($row['AVG('.$item.')'],1);
							}
						}

						//$lectureHeadings = array(array("Nicht Prüfungsrelevant", "Sehr prüfungsrelevant"), array("Uninteressant", "Sehr interessant"), array("Materialien unstrukturiert/unvollständig", "Materialien strukturiert, selbsterklärend, vollständig"));
						$lectureCaptions = array(array("Relevanz des Vorlesungsbesuches", "Hinblicklich: Folien selbsterklärend, Vorlesung behandelt zusätzlichen Stoff"), array("Gestaltung der Vorlesung", ""), array("Qualität der Vorlesungsmaterialien", "Hinblicklich: Vollständigkeit, Struktur"));
						$lectureHeadings = array(array("Nicht Prüfungsrelevant", "Sehr prüfungsrelevant"), array("Uninteressant", "Sehr interessant"), array("Materialien schlecht", "Materialien gut"));
						
						//Exam
						$items = array("exam0", "exam1", "exam2", "exam3");
						$examType = array("written", "oral");
						for($i=0;$i<count($examType);$i++){
							foreach($items as $key => $item){
								$result = mysqli_query($con, "SELECT AVG(".$item.") FROM ratings WHERE subject_ID = ".$subjectData['ID']." AND examType = '".$examType[$i]."'");
								$row = mysqli_fetch_assoc($result);
								if(round($row['AVG('.$item.')'],1) < 0 ){
									$exam[$i][$key][0] = abs(round($row['AVG('.$item.')'],1));
									$exam[$i][$key][1] = 0;
								}else{
									$exam[$i][$key][0] = 0;
									$exam[$i][$key][1] = round($row['AVG('.$item.')'],1);
								}
							}
						}
						$examHeadings = array(array("Reproduktion", "Transfer"), array("Nicht rechenlastig", "Sehr rechenlastig"), array("Aufwand < ECTS", "Aufwand > ECTS"), array("Prüfungsvorbereitung schlecht", "Prüfungsvorbereitung gut"));

						$row = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(examType) FROM ratings WHERE subject_ID = ".$subjectData['ID']." AND examType = 'written'"));
						$writtenBadge = $row['COUNT(examType)'];
						$row = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(examType) FROM ratings WHERE subject_ID = ".$subjectData['ID']." AND examType = 'oral'"));
						$oralBadge = $row['COUNT(examType)'];
						$row = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(examType) FROM ratings WHERE subject_ID = ".$subjectData['ID']." AND examType = 'other'"));
						$otherBadge = $row['COUNT(examType)'];

						?>
						<h4><strong>Vorlesung</strong></h4>
						<br>
						<!--<div style="height: 42px;"></div>-->
						<table class="ratingtable" style="width:100%">
							<?php
							for($i=0;$i<count($lectureHeadings);$i++){
								?>
								
								<tr>
									<td colspan="2">
										<span><strong><?php echo $lectureCaptions[$i][0]?></strong></span>
										<br>
										<span style="font-size:12px;"><i><?php echo $lectureCaptions[$i][1]?></i></span>
										<div style="height: 7px;"></div>
									</td>
								</tr>
								
								<tr>
									<td>
										<span style="float:left; margin-left:3px;"><?php echo $lectureHeadings[$i][0] ?></span>
									</td>
									<td>
										<span style="float:right; text-align: right; margin-right:3px;"><?php echo $lectureHeadings[$i][1] ?></span>
									</td>
								</tr>

								<tr>
									<td valign="center" style="width:50%">
										<div style="font-size:15px; font-weight:bold; line-height:2">
											<div class="progress" style="transform: rotate(-180deg); border-top-left-radius:0; border-bottom-left-radius:0; border-left:solid 0.5px grey;">
												<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $lecture[$i][0]*(100/3) ?>%"></div>
											</div>
										</div>
									</td>

									<td valign="center" style="width:50%">
										<div style="font-size:15px; font-weight:bold; line-height:2">
											<div class="progress" style="border-top-left-radius:0; border-bottom-left-radius:0; border-left:solid 0.5px grey;">
												<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $lecture[$i][1]*(100/3) ?>%"></div>
											</div>
										</div>
									</td>
								</tr>
							<?php
							}
							?>
						</table>
					</div>
					<div class="col-md-6">
						<h4><strong>Prüfung</strong></h4>

						<ul class="nav nav-pills">
							<li class="written" ><a data-toggle="pill" href="#written">Schriftlich <span id="writtenBadge" data-number-of-reviews="<?php echo $writtenBadge ?>" class="badge"><?php echo $writtenBadge ?></span></a></li>
							<li class="oral" ><a data-toggle="pill" href="#oral">Mündlich <span id="oralBadge" data-number-of-reviews="<?php echo $oralBadge ?>" class="badge"><?php echo $oralBadge ?></span></a></li>
							<li class="other" ><a data-toggle="pill" href="#other">Sonstige <span id="otherBadge" data-number-of-reviews="<?php echo $otherBadge ?>" class="badge"><?php echo $otherBadge ?></span></a></li>
						</ul>

						<div class="tab-content">
							<br>
							<?php
							for($j=0;$j<count($examType);$j++){
								?>
								<div id="<?php echo $examType[$j] ?>" class="tab-pane fade">

									<table class="ratingtable" style="width:100%">
										<?php
										for($i=0;$i<count($examHeadings);$i++){
											?>
											<tr>
												<td>
													<span style="float:left; margin-left:3px;"><?php echo $examHeadings[$i][0] ?></span>
												</td>
												<td>
													<span style="float:right; text-align: right; margin-right:3px;"><?php echo $examHeadings[$i][1] ?></span>
												</td>
											</tr>

											<tr>
												<td valign="center" style="width:50%">
													<div style="font-size:15px; font-weight:bold; line-height:2">
														<div class="progress" style="transform: rotate(-180deg); border-top-left-radius:0; border-bottom-left-radius:0; border-left:solid 0.5px grey;">
															<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $exam[$j][$i][0]*(100/3) ?>%"></div>
														</div>
													</div>
												</td>

												<td valign="center" style="width:50%">
													<div style="font-size:15px; font-weight:bold; line-height:2">
														<div class="progress" style="border-top-left-radius:0; border-bottom-left-radius:0; border-left:solid 0.5px grey;">
															<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $exam[$j][$i][1]*(100/3) ?>%"></div>
														</div>
													</div>
												</td>
											</tr>
											<?php
										}
										?>
									</table>
								</div>
								<?php
							}?>

							<div id="other" class="tab-pane fade otherTab">
								<?php
								$result = mysqli_query($con, "SELECT ID, examText FROM ratings WHERE subject_ID = ".$subjectData['ID']." AND examType = 'other'");
								while($row = mysqli_fetch_assoc($result)){
									?>
									<p class="otherComment"><?php echo $row['examText'] ?> <a href="#bewertungMitID<?php echo $row['ID'] ?>" data-comment-id="<?php echo $row['ID'] ?>"  class="sonstigesZuCommentLink"><span class="pull-right"><span class="glyphicon glyphicon-comment"></span></span></a></p>
									<?php
								}
								?>
							</div>
						</div>
					</div>

					<script>
					written = $('#writtenBadge').text();
					oral = $('#oralBadge').text();
					other = $('#otherBadge').text();
					var max = Math.max(written, oral, other);

					switch (true){
						case written == max:
							$('li.written').addClass("active");
							$('#written').addClass("in active");
							break;
						case oral == max:
							$('li.oral').addClass("active");
							$('#oral').addClass("in active");
							break;
						case other == max:
							$('li.other').addClass("active");
							$('#other').addClass("in active");
							break;
					}
					</script>

				</div>
			</div>
		</div>
		<!--ENDE Bewertungsübersicht-->

		<!--START Sempro-Werbung-->
		<?php
		/*Check for passed events and delete from database*/
		//Get upcoming event_ids
		$sql = "
			SELECT * FROM `jom_vwi_semesterprogramm`
			WHERE application_date >= now()
		";			
		$result = mysqli_query($con_hp, $sql);

		$ids = array(0);
		while($row = mysqli_fetch_assoc($result)) {
			$ids[] = $row['event_id'];
		}
		$upcoming_events_ids = implode(',', $ids);

		//Delete passed events
		mysqli_query($con, "DELETE FROM `sempro_ads` WHERE event_id NOT IN ($upcoming_events_ids)");
		?>
		
		<?php
		$sql = "
			SELECT event_id FROM sempro_ads
			WHERE subject_id = ".$subjectData['ID']."

		";
		$result = mysqli_query($con, $sql);

		$ids = array();
		while($row = mysqli_fetch_assoc($result)) {
			$ids[] = $row['event_id'];
		}
		$query_ids = implode(',', $ids);
		
		$sql = "
			SELECT * FROM `jom_vwi_semesterprogramm`
			WHERE event_id IN ($query_ids)
			ORDER BY application_date
			LIMIT 1
		";
		$result = mysqli_query($con_hp, $sql);
		$row = mysqli_fetch_assoc($result);
		
		if(mysqli_num_rows($result) != 0){
		?>
			
			<div style="border: 3px solid; border-color: #F8F8F8; padding: 10px">
				<div class="row align-items-sm-center">
					<div class="col-sm-3">
						<img style="width:100%; padding: 10px;" src="https://www.vwi-karlsruhe.de/images/semesterprogramm/<?php echo $row["event_picture"]?>">
					</div>
					<div class="col-sm-9">
						<p>
							<strong><?php echo $row["event_name"]?></strong><br>
							<?php
							echo date('d.', strtotime($row['event_date_start'])).date('m.', strtotime($row['event_date_start'])).date('Y', strtotime($row['event_date_start']));
							if($row['multiday']=='on') {
								echo ' - ' . date('d.', strtotime($row['event_date_end'])).date('m.', strtotime($row['event_date_end'])).date('Y', strtotime($row['event_date_end']));
							}
							echo '</p>';
							?>
						</p>
						<p>
							<?php echo nl2br($row["event_text"])?>
						</p>
						<p style="margin-bottom:0">
							Hier geht es zur Veranstaltungsseite von VWI-ESTIEM: <a href="https://www.vwi-karlsruhe.de/veranstaltungen" target="_blank">vwi-karlsuhe.de/veranstaltungen</a>
						</p>
					</div>
				</div>
			
				<hr>
				
				<p style="font-size:10px">
					Dies ist Veranstaltungwerbung der Hochschulgruppe VWI-ESTIEM. Unsere Veranstaltungen werden kostenlos von Studierenden für Studierende organisiert. Mehr Infos zur Hochschulgruppe: <a href="https://www.vwi-karlsruhe.de/" target="_blank">vwi-karlsuhe.de</a>.
				</p>
			</div>
		
			<br>
		<?php
		}?>
		<!--ENDE Sempro-Werbung-->

		<!--START Fragen-->
		<div class="well">

			<span style="font-size: 1.5em;font-weight:bold;">Fragen
				<span style="float:right;">
					<button id="newQuestionButton" type="button" class="btn btn-primary">Neue Frage stellen</button>
				</span>
			</span>

			<!-- Modal neue Frage stellen-->
			<div id="questionModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Frage stellen für:<br><strong><?php echo $subjectData['subject_name'] ?></strong></h4>
				</div>
				<div class="modal-body question-modal-body">
					<form id="questionForm">
						<div class="form-group">
							<textarea name="formQuestion" class="form-control" rows="6" maxlength="3000" placeholder="Gib hier deine Frage ein." required></textarea>
						</div>
						<button id="submitQuestionButton" type="button" class="btn btn-primary">Abschicken</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
					</form>
				</div><!-- End of Modal body -->
				</div><!-- End of Modal content -->
				</div><!-- End of Modal dialog -->
			</div><!-- End of Modal -->

			<br><br>

			<div id="questionBody" style="max-height:800px; overflow:auto">

				<?php
				$sql = "
					SELECT questions.ID AS ID, questions.subject_ID AS subject_ID, questions.user_ID AS user_ID, questions.question AS question, questions.time_stamp AS time_stamp, users.username AS username
					FROM questions
					JOIN users ON questions.user_ID = users.user_ID
					WHERE subject_ID = ".$subjectData['ID']."
					ORDER BY time_stamp DESC;
				";
				$result = mysqli_query($con, $sql);

				if(mysqli_num_rows($result)==0){
					echo "<i>Noch keine Fragen vorhanden.</i>";
				}

				while($row = mysqli_fetch_assoc($result)){
					?>
					<div class="well" style="background-color:white; border-radius:none">
						<span class="actualQuestion" id="question<?php echo $row['ID']?>"><?php echo nl2br($row['question'])?></span>
						<hr style="margin:10px">
						
						<?php
						//Erstellt Variable, um Bearbeiten-Button nur für Ersteller anzuzeigen
						$displayEdit = "display:none;";
						$editClassIdentifier = "";
						$displayReport ="";

						if($row['user_ID'] == $userRow['user_ID']){
							$displayEdit = "";
							$editClassIdentifier = "editButtonIdentificationClassQuestions";
							$displayReport = "display:none;";
						}
						
						//Löschen-Button Anzeige
						$displayAdminDelete = "none";
						if($userRow['super_admin'] == 1){
							$displayAdminDelete = "";
						}
						
						?>
						
						<div class="general-flex-container" style="font-size:10px; margin-bottom:7px;">
							<div style="margin-top:3px; margin-bottom:3px">
								<a href="sendMessage.php?recipient_id=<?php echo $row['user_ID']?>"><?php echo $row['username']?></a> &#124; <?php echo time_elapsed_string($row['time_stamp']);?>
							</div>
							<div style="margin-top:3px; margin-bottom:3px">
								<button data-toggle="modal" data-target="#editQuestionModal<?php echo $row['ID']?>" type="button" style="<?php echo $displayEdit ?>" role="button" class="editTrashButton <?php echo $editClassIdentifier ?>"  title="Frage bearbeiten"> <span class="glyphicon glyphicon-pencil"></span></button>
								
								<button data-id="<?php echo $row['ID']?>" type="button" style="<?php echo $displayEdit ?>" href="#deleteQuestionModal" role="button" class="editTrashButton deleteQuestionButton" data-toggle="modal" title="Frage löschen"> <span class="glyphicon glyphicon-trash"></span></button>
								<!--
								<button style="<?php echo $displayReport ?>" type="button" role="button" data-toggle="modal" data-id="<?php echo $row['ID'] ?>" class="editTrashButton reportButton" title="Frage melden"> <span class="glyphicon glyphicon-exclamation-sign"></span></button>
								<button style="display:<?php echo $displayAdminDelete ?>" onclick="deleteRatingByAdmin(this.id)" id="deleteratingbyadmin<?php echo $row['ID']?>" type="button" href="#" role="button" class="editTrashButton"> <span style="color:red" class="glyphicon glyphicon-trash" title="Frage als Admin löschen" ></span></button>
								-->
							</div>
						</div>
						
						<!-- Modal für Fragenänderung-->
						<div id="editQuestionModal<?php echo $row['ID']?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
							<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title">Frage ändern</strong></h4>
							</div>
							<div class="modal-body change-question-body">
								<p>Falls du deine Frage ausführlicher oder erweitert stellen willst, hilfst du anderen, indem du den Zusatz mit &ldquo;Nachtrag&rdquo; oder &ldquo;Edit&rdquo; kennzeichnest.</p>
								<form id="ChangeQuestionForm<?php echo $row['ID']?>">
									<div class="form-group">
										<textarea name="formQuestion" class="form-control" rows="6" maxlength="3000" placeholder="Gib hier deine Frage ein." required><?php echo nl2br($row['question'])?></textarea>
									</div>
									<button id="changeQuestionButton<?php echo $row['ID']?>" type="button" class="btn btn-primary">Abschicken</button>
									<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
								</form>
							</div><!-- End of Modal body -->
							</div><!-- End of Modal content -->
							</div><!-- End of Modal dialog -->
						</div><!-- End of Modal -->
						
						<script>
						$( document ).ready(function() {

							$("#changeQuestionButton<?php echo $row['ID']?>").click(function(){
								$.ajax({
									type: "POST",
									url: "changeQuestion_submit.php",
									data: $("#ChangeQuestionForm<?php echo $row['ID']?>").serialize() + "&subject_id=" + "<?php echo $subjectData['ID']?>" + "&question_id=<?php echo $row['ID']?>",
									success: function(data) {
										//alert(data);
										if(data.includes("erfolg")){
											$('.change-question-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Deine Frage wurde erfolgreich geändert!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen</button>");
										}else{
											$('.change-question-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Beim Ändern Deiner Frage ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt); falls es immernoch nicht funktioniert, schreib uns: studienfuehrer@vwi-karlsruhe.de.</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
										}
									}
								});
							});
						
						});
						</script>
						
						<?php
						$num = mysqli_num_rows(mysqli_query($con, "SELECT * FROM answers WHERE question_ID = ".$row['ID']));
						?>
					
						<div class="general-flex-container">

							<div><a class="answerThisQuestion">Frage beantworten</a></div>	

							<div>
								<span class="showAnswers">
									<?php
									switch($num){
										case 0:
											echo "Keine Antworten zum Anzeigen vorhanden";
											break;
										default:
											echo "<a>Antworten verbergen</a>";
											break;
									}
									?>
								</span>
							</div>

						</div>

						<div class="answerSection" style=""> <!--Antworten-->

							<?php
							$sql2 = "
								SELECT answers.ID AS ID, answers.question_ID AS question_ID, answers.user_ID AS user_ID, answers.answer AS answer, answers.time_stamp AS time_stamp, users.username AS username
								FROM answers
								JOIN users ON answers.user_ID = users.user_ID
								WHERE question_ID = ".$row['ID']."
								ORDER BY time_stamp DESC;
							";
							$result2 = mysqli_query($con, $sql2);

							if(mysqli_num_rows($result2) > 0){
								echo "<hr class=\"style\">";
							}

							while($row2 = mysqli_fetch_assoc($result2)){
								?>
								<div class="well" style="background-color:white; border-radius:none; margin-bottom:5px; margin-left:3%">
									<?php echo nl2br($row2['answer'])?>
									<hr style="margin:10px">
									<p style="font-size:10px; margin-bottom:0px"><a href="sendMessage.php?recipient_id=<?php echo $row2['user_ID']?>"><?php echo $row2['username']?></a> &#124; <?php echo time_elapsed_string($row2['time_stamp']);?></p>
								</div>
								<?php
							}
							?>
						</div>
					</div>
					<?php
				}
				?>

				<!-- Modal für Fragenlöschung-->
				<div id="deleteQuestionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-dialog">
					<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">Frage löschen</strong></h4>
					</div>
					<div class="modal-body" id="deleteQuestionModalBody">
						<div style="display:none" id="deleteId"></div>
						<p>Bist du dir sicher, dass du deine Frage löschen möchtest? Dieser Schritt kann nicht widerrufen werden.</p>
						<br>
						<button type="button" id="submitDeleteQuestionButton" class="btn btn-danger">Unwiderruflich löschen</button>
						<button type="button" class="btn btn-primary" data-dismiss="modal">Doch nicht löschen :)</button>
					</div><!-- End of Modal body -->
					</div><!-- End of Modal content -->
					</div><!-- End of Modal dialog -->
				</div><!-- End of Modal -->

				<script>
				$( document ).ready(function() {

					$('.deleteQuestionButton').click(function(){
						var k_id = $(this).attr('data-id');
						$('#deleteId').html(k_id);
					});

					$('#submitDeleteQuestionButton').click(function(){
						var q_id = $('#deleteId').html();
						$.ajax({
							type: "POST",
							url: "delete_question.php",
							data: "&q_id=" + q_id,
							success: function(data) {
								//alert(data);
								if(data.includes("erfolg")){
									$('#deleteQuestionModalBody').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Deine Frage wurde erfolgreich gelöscht!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen</button>");
								}else{
									$('#deleteQuestionModalBody').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Beim Löschen deiner Frage ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt); falls es immernoch nicht funktioniert, schreib uns: studienführer@vwi-karlsruhe.de.</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
								}
							}
						});
					});

				});
				</script>

			</div>

			<br>
			<p style="text-align:center; margin-bottom:0"><a id="showAllQuestions" style="cursor: pointer; cursor: hand;">Alle Fragen aufklappen (Scrollbar entfernen)</a></p>
		</div>
		
		<!-- Modal Frage beantworten-->
		<div id="answerModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Antwort schreiben</h4>
			</div>
			<div class="modal-body answer-modal-body">
				<p><strong>Frage:</strong></p>
				<p id="questionForAnswerModal"></p>
				<p style="display:none" id="questionID"></p>
				<p><strong>Deine Antwort:</strong></p>
				<form id="answerForm">
					<div class="form-group">
						<textarea name="formAnswer" class="form-control" rows="6" maxlength="3000" placeholder="Gib hier deine Antwort ein." required></textarea>
					</div>
					<button id="submitAnswerButton" type="button" class="btn btn-primary">Abschicken</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
				</form>
			</div><!-- End of Modal body -->
			</div><!-- End of Modal content -->
			</div><!-- End of Modal dialog -->
		</div><!-- End of Modal -->
		
		<script>
		$( document ).ready(function() {
			(function($) { //Scrollbar vorhanden?
				$.fn.hasScrollBarIH = function() {
					return this.get(0).scrollHeight > this.innerHeight();
				}
			})(jQuery);

			if(!$('#questionBody').hasScrollBarIH()){
				$('#showAllQuestions').hide();
			}
		});


		//Fragen auf- und zuklappen
		$('#showAllQuestions').click(function() {
			if(!($('#questionBody').css("max-height")=="none")){
				$('#questionBody').css("max-height", "");
				$('#showAllQuestions').html("Fragen wieder einklappen");
			}else{
				$('#questionBody').css("max-height", "800px");
				$('#questionBody').css("overflow", "auto");
				$('#showAllQuestions').html("Alle Fragen aufklappen (Scrollbar entfernen)");
			}
		});

		//Antworten anzeigen
		$('.showAnswers').click(function() {
			if(!($(this).text().trim() == "Keine Antworten zum Anzeigen vorhanden")){
				if(($(this).text().trim() == "Antworten verbergen")){
					$(this).parent().parent().next(".answerSection").hide(); //Bin nicht ganz sicher, wie stabil das ist
					$(this).html("<a>Antworten anzeigen</a>");
					if($('#questionBody').hasScrollBarIH()){
						$('#showAllQuestions').show();
					}else if($('#showAllQuestions').text()!="Fragen wieder einklappen"){
						$('#showAllQuestions').hide();
					}
				}else{
					$(this).parent().parent().next(".answerSection").show(); //Bin nicht ganz sicher, wie stabil das ist
					$(this).html("<a>Antworten verbergen	</a>");
					if($('#questionBody').hasScrollBarIH()){
						$('#showAllQuestions').show();
					}else if($('#showAllQuestions').text()!="Fragen wieder einklappen"){
						$('#showAllQuestions').hide();
					}
				}
			}
		});

		//neue Frage stellen
		$('#newQuestionButton').click(function(){
			$('#questionModal').modal('show');
		});

		$("#submitQuestionButton").click(function(){
			$.ajax({
				type: "POST",
				url: "question_submit.php",
				data: $("#questionForm").serialize() + "&subject_id=" + "<?php echo $subjectData['ID']?>",
				success: function(data) {
					//alert(data);
					if(data.trim().substr(0,6) == "erfolg"){ //substring stellt sicher, dass hier auch reingegangen wird wenn E-Mail-Fehler auftritt
						$('.question-modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Deine Frage wurde erfolgreich an uns übermittelt!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen</button>");
					}else{
						$('.question-modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Bei der Übermittlung Deiner Frage ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt); falls es immernoch nicht funktioniert, schreib uns: studienführer@vwi-karlsruhe.de.</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
					}
				}
			});
		});

		//Frage beantworten
		$('.answerThisQuestion').click(function(){
			$('#answerModal').modal('show');
			$('#questionForAnswerModal').html($(this).parent().parent().prevAll(".actualQuestion:first").text());
			$('#questionID').html($(this).parent().parent().prevAll(".actualQuestion:first").attr('id').slice(8));
		});

		$("#submitAnswerButton").click(function(){
			$.ajax({
				type: "POST",
				url: "answer_submit.php",
				data: $("#answerForm").serialize() + "&question_id=" + $('#questionID').html(),
				success: function(data) {
					//alert(data);
					if(data.includes("erfolg")){ //substring stellt sicher, dass hier auch reingegangen wenn E-Mail-Fehler auftritt
						$('.answer-modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Deine Antwort wurde erfolgreich an uns übermittelt!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen</button>");
					}else{
						$('.answer-modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Bei der Übermittlung Deiner Antwort ist womöglich ein Fehler aufgetreten! Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt); falls es immernoch nicht funktioniert, schreib uns: studienführer@vwi-karlsruhe.de.</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\">Schließen</button>");
					}
					if(data.includes("achievement")){
						alert("Du hast eine neue Errungenschaft freigeschaltet! Schau gleich nach unter Profil > Errungenschaften.");
					}
				}
			});
		});
		</script>
		<!--ENDE Fragen-->
		
		<!--START Hilfreiche Links-->
		<div class="well" id="links">
			<p style="font-size: 1.5em;font-weight:bold;">Hilfreiche Links</p>
			<p class="contenedor">
			
				<?php
				$result=mysqli_query($con, "SELECT ilias, ilias_pw, facebook, studydrive, modulebook FROM subjects WHERE ID = ".$subject."");
				$row = mysqli_fetch_assoc($result);
				//facebook
				if($row['facebook'] != ""){
					$facebook_link = "onClick=\"window.open('".$row['facebook']."')\"";
					$facebook_disabled = "";
				}else{
					$facebook_link = "";
					$facebook_disabled = "disabled";
				}
				//studydrive
				if($row['studydrive'] != ""){
					$studydrive_link = "onClick=\"window.open('".$row['studydrive']."')\"";
					$studydrive_disabled = "";
				}else{
					$studydrive_link = "";
					$studydrive_disabled = "disabled";
				}
				//modulebook
				if($row['modulebook'] != ""){
					$modulebook_link = "onClick=\"window.open('".$row['modulebook']."')\"";
					$modulebook_disabled = "";
				}else{
					$modulebook_link = "";
					$modulebook_disabled = "disabled";
				}
				//ilias
				if($row['ilias'] != ""){
					$ilias_link = "onClick=\"window.open('".$row['ilias']."')\"";
					$ilias_disabled = "";
				}else{
					$ilias_link = "";
					$ilias_disabled = "disabled";
				}					
				?>
			
				<button <?php echo $ilias_link ?> class="btn btn-primary contenido" style="border-radius:0;" <?php echo $ilias_disabled ?>>ILIAS <span class="glyphicon glyphicon-new-window"></span></button>
				<button <?php echo $modulebook_link ?> class="btn btn-primary contenido" style="border-radius:0;" <?php echo $modulebook_disabled ?>>Modulhandbuch <span class="glyphicon glyphicon-new-window"></span></button>
				<button id="but" <?php echo $facebook_link ?> class="btn btn-primary contenido" style="border-radius:0;" <?php echo $facebook_disabled ?>>Facebook-Gruppe <span class="glyphicon glyphicon-new-window"></span></button>
				<button <?php echo $studydrive_link ?> class="btn btn-primary contenido" style="border-radius:0;" <?php echo $studydrive_disabled ?>>Studydrive <span class="glyphicon glyphicon-new-window"></span></button>
				
			</p>
			
			<label>ILIAS-Passwort: </label>
			<input id="iliasPW" class="form-control" style="width: auto; display:inline; background-color: white" type="text" value="<?php echo ($row['ilias_pw'] != "") ? $row['ilias_pw'] : "(Nichts eingetragen)";?>">
			<button type="button" class="btn btn-default" onclick="copyLink()" style="margin-bottom:2px">Kopieren</button>
			<div class="snackbar" id="snackbarIliasPwCopied">Das ILIAS-Passwort wurde kopiert.</div>

			<script>
			function copyLink() {
				var copyText = document.getElementById("iliasPW");
				copyText.select();
				document.execCommand("copy");
				
				$('#snackbarIliasPwCopied').addClass('show');
				setTimeout(function(){ $('#snackbarIliasPwCopied').removeClass('show'); }, 3000);
			}
			</script>
			
			<br><br>
			<p><a href=\"#\" data-toggle="modal" data-target="#changeLinksModal">Links und Passwort bearbeiten</a></p>
			
			<!-- Links Modal -->
			<div id="changeLinksModal" class="modal fade" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" onClick="window.location.reload()">&times;</button>
							<h4 class="modal-title">Links hinzufügen oder bearbeiten</h4>
						</div>
						<div class="modal-body">
							<p>Hier kannst du Links hinzufügen oder bearbeiten. Bitte füge keinen Quatsch hinzu - wir können nachvollziehen, wer was hinzufügt oder ändert :)</p>

							<form id="linkForm">

								<div class="form-group">
									<label>Link zum ILIAS-Kurs:</label>
									<input type="text" class="form-control" value="Wird automatisch eingefügt" name="ilias_link" disabled>
								</div>
								
								<div class="form-group">
									<label>ILIAS-Passwort:</label>
									<input type="text" class="form-control" value="<?php echo $row['ilias_pw']?>" name="ilias_pw">
								</div>
								
								<div class="form-group">
									<label>Link zum Modulhandbuch:</label>
									<input type="text" class="form-control" value="Wird automatisch eingefügt" name="modulhandbuch_link" disabled>
								</div>
								
								<div class="form-group">
									<label>Link zur Facebook-Gruppe:</label>
									<input type="text" class="form-control" value="<?php echo $row['facebook']?>" name="facebook_link">
								</div>

								<div class="form-group">
									<label>Link zum Studydrive:</label>
									<input type="text" class="form-control" value="<?php echo $row['studydrive']?>" name="studydrive_link">
								</div>
								
								<br>
								<button class="btn btn-primary">Änderungen speichern</button>
							</form>
							
							<div id="linkFormSuccess" style="display:none">
								<br>
								<div class="alert alert-success">
									Deine Änderung wurde erfolgreich übernommen. Vielen Dank!
								</div>
							</div>
							
							<script>
							$(document).ready(function(){
						
								$("#linkForm").submit(function(e) {


									var form = $(this);
									var url = 'linkForm_submit.php';

									$.ajax({
										type: "POST",
										url: url,
										data: form.serialize() + "&user_id=" + <?php echo $userRow['user_ID']?> + "&subject_id=" + <?php echo $subject?>, // serializes the form's elements.
										success: function(data){
											//alert(data);
											if(data.includes("achievement")){
												alert("Du hast eine neue Errungenschaft freigeschaltet! Schau gleich nach unter Profil > Errungenschaften.");
											}
											if(data.includes("erfolg")){
												$('#linkFormSuccess').show();
											}
										}
									});
									e.preventDefault(); // avoid to execute the actual submit of the form.
								});
								
							});	
							</script>
							
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal" onClick="window.location.reload()">Schließen</button>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<!--ENDE Hilfreiche Links-->
		
		<!--START Kommentare-->
		<div <?php echo $displayRatings ?> class="well" id="commentsection">

			<div>
				<p style="font-size: 1.5em;font-weight:bold;">Kommentare und Einzelbewertungen</p>
				<p>
					<form class="form-inline" action="orderComments_submit.php?subject=<?php echo $subject ?>" method="post">
					<label>
						<span id="filterIcon" style="font-size: 1.5em;vertical-align:bottom;" class="glyphicon glyphicon-filter"></span>&nbsp;
						<div class="loader" id="load" style="display:none; padding-right: 5em;"><div></div></div>
					</label>
					<select class="form-control" style="width: auto; display:inline;" name="commentorder" id="commentorder">
						<option value="date_newFirst">Datum (Neuste zuerst)</option>
						<option value="date_newLast">Datum (Älteste zuerst)</option>
						<option value="rating_bestFirst" selected>Bewertung (Beste zuerst)</option>
						<option value="rating_worstFirst">Bewertung (Schlechteste zuerst)</option>
					</select>
					</form>
				</p>
				<div style="margin-top: 1em;"></div>
			</div>

			<br>

			<!--Für Übergabe an JS-->
			<span id="hiddenSubjectId" style="display:none"><?php echo $subjectData['ID']?></span>
			<span id="hiddenUserId" style="display:none"><?php echo $userRow['user_ID']?></span>

			<div id="commentDiv">
				<?php
				include "loadComments.php";
				?>
			</div>

			<script>
			$('#commentorder').change(function () {
				$('#filterIcon').hide();
				$('#load').show();

				$.ajax({
					type: "POST",
					url: "loadComments.php",
					data: {order: $('#commentorder').val(), subject_id: $('#hiddenSubjectId').html(), user_id: $('#hiddenUserId').html()},
					success: function(data) {
						$('#load').fadeOut(function(){
							$('#filterIcon').fadeIn();
						});
						$('#commentDiv').fadeOut(function() {
							$('#commentDiv').html(data);
							$('#commentDiv').fadeIn();
						});
					}
				});
			});
			</script>
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
								xmlhttp2=new XMLHttpRequest();
							}else{// AJAX mit IE6, IE5
								xmlhttp2=new ActiveXObject("Microsoft.XMLHTTP");
							}

							var commentID = id.substring(0, id.length - 2);
							var userID = <?php echo $userRow['user_ID']; ?>;
							var subjectID = <?php echo $subjectData['ID']; ?>;
							var ratingDirection = id.substring(id.length - 2, id.length);

							xmlhttp2.onreadystatechange = function() {
								if (xmlhttp2.readyState == XMLHttpRequest.DONE) {
									//alert(xmlhttp2.responseText.trim());
									if(xmlhttp2.responseText.trim().includes("achievement")){
										alert("Du hast eine neue Errungenschaft freigeschaltet! Schau gleich nach unter Profil > Errungenschaften.");
									}
								}
							}
							
							xmlhttp2.open("POST","submitCommentRating.php?commentID="+commentID+"&userID="+userID+"&subjectID="+subjectID+"&ratingDirection="+ratingDirection,true);
							xmlhttp2.send();
							


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
		<!--ENDE Kommentare-->
	</div>
	
	<div style="display:<?php echo $showFailedLoad?>">
		Die Veranstaltung konnte nicht geladen werden, da die übergebene Veranstaltungs-ID nicht in unserer Datenbank existiert.
	</div>
</div>



<!-- Modal für Bewertungsänderung-->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h4 class="modal-title">Bewertungsänderung für:<br><strong><?php echo $subjectData['subject_name'] ?></strong></h4>
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
		<h4 class="modal-title">Bewertung löschen für:<br><strong><?php echo $subjectData['subject_name'] ?></strong></h4>
	</div>
		<div class="modal-body">
			<form action="rating_delete.php?subject=<?php echo $subject?>" method="POST">
				<p style="font-weight: bold; font-size: 20px; color: rgb(0, 51, 153)">Willst du deine Bewertung wirklich löschen?</p>
				<p> Das beinhaltet deine eigentliche Bewertung und den Kommentar, den du abgegeben hast.<br>
				<span style="color:red">Dieser Schritt kann nicht rückgängig gemacht werden.</span></p>
				<br>
				<button type="submit" class="btn btn-danger">Bewertung unwiderruflich löschen</button>
				<button class="btn btn-primary" data-dismiss="modal">Bewertung doch nicht löschen :)</button>
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
				if(data.includes("achievement")){
					alert("Du hast eine neue Errungenschaft freigeschaltet! Schau gleich nach unter Profil > Errungenschaften.");
				}
				if(data.trim().substr(0,6) == "erfolg"){ //substring stellt sicher, dass hier auch reingegangen wenn E-Mail-Fehler auftritt
					$('.modal-body').html("<div class=\'alert alert-success\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Dein Anliegen wurde erfolgreich an uns übermittelt!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\" onClick=\"window.location.reload()\">Schließen</button>");
				}else{
					$('.modal-body').html("<div class=\'alert alert-danger\'><span class=\'glyphicon glyphicon-info-sign\'></span> &nbsp; Bei der Übermittlung Deines Anliegens ist womöglich ein Fehler aufgetreten!</div><button type=\"button\" class=\"btn btn-primary\" data-dismiss=\"modal\ onClick=\"window.location.reload()\">Schließen</button>");
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
		<button type="button" class="close" data-dismiss="modal" onClick="window.location.reload()" aria-hidden="true">&times;</button>
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
        <h4 class="modal-title"><strong>Einzelbewertung</strong></h4>
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

<div id="deleteCommentByAdminModal" tabindex="-1" aria-hidden="true" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><strong>Bewertung löschen</strong></h4>
      </div>
      <div class="modal-body">
		<p>Möchtest du diesen Kommentar und die dazugehörige Bewertung wirklich und unwiederruflich löschen? Der Nutzer wird <strong>nicht</strong> über diesen Vorgang benachrichtigt.</p>
		<p style="color:red">Bitte lösche Kommentare/Bewertungen als Admin nur, wenn es unbedingt sein muss.</p>
		<p id="deleteCommentByAdminCommentId" style="display:none"></p>
	  </div>
      <div class="modal-footer">
        <button id="deleteCommentByAdminDeleteButton" class="btn btn-danger">Löschen</button>
		<button type="button" class="btn btn-primary" data-dismiss="modal">Schließen</button>
      </div>
    </div>
  </div>
</div>

<!-- Lösche Kommentar als Admin -->
<script>
function deleteRatingByAdmin(id){
	$('#deleteCommentByAdminModal').modal('show');
	$('#deleteCommentByAdminCommentId').html(id.substring(19));
}

$("#deleteCommentByAdminDeleteButton").click(function(){
	$.ajax({
		type: "POST",
		url: "admin_DeleteRatingByAdmin.php",
		data: {user_id: $('#deleteCommentByAdminCommentId').html()},
		success: function(data) {
			alert(data);
			location.reload();
		},
		error: function(){
			alert("Beim Löschen ist ein Fehler aufgetreten. Bitte probiere es erneut (oftmals liegt es am Server, sodass es beim zweiten Mal klappt); falls es immernoch nicht funktioniert, schreib uns: studienführer@vwi-karlsruhe.de.");
		}
	});
});
</script>


<script>
$(document).ready(function(){
		if($('#writtenBadge').attr('data-number-of-reviews')==0){
			if($('#oralBadge').attr('data-number-of-reviews')==0){
				$("[href$='#otherBadge']").tab('show');
			}else{
				$("[href$='#oralBadge']").tab('show');
			}
		}

		$("#favIcon").click(function(){
			if($("#favIcon").attr("class") == "glyphicon glyphicon-star-empty favouriteStar"){
				$.post( "favourites_newEntry.php", {
					user_id: "<?php echo $userRow['user_ID'] ?>",
					subject_id: "<?php echo $subjectData['ID'] ?>"}	)
				  .done(function() {
					$("#favIcon").attr("style", "color:rgb(255, 204, 0);cursor: pointer; cursor: hand;");
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
					$("#favIcon").attr("style", "color:grey; cursor: pointer; cursor: hand;");
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
			
			//$('#bewertungAbgebenForm').html('Leider können gerade keine Bewertungen abgegeben werden - wir sind dabei, den Fehler zu beheben!');
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
	
	


	// Noch verbuggt. Funktioniert nur 1 mal
	/*$('.sonstigesZuCommentLink').click(function(){
		setTimeout(function(){
			$('.ausrufezeichen').fadeOut();
		}, 3000);
	});*/
});
</script>

</body>
</html>
