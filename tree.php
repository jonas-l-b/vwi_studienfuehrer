<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div id="div2" class="feeddiv">
	<?php
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
	
	<script>
	$(document).ready(function() {
		// Configure/customize these variables.
		var showChar = 100;  // How many characters are shown by default
		var ellipsestext = "...";
		var moretext = "Mehr";
		var lesstext = "Weniger";
		

		$('.more').each(function() {
			var content = $(this).html();
	 
			if(content.length > showChar) {
	 
				var c = content.substr(0, showChar);
				var h = content.substr(showChar, content.length - showChar);
	 
				var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
	 
				$(this).html(html);
			}
	 
		});
	 
		$(".morelink").click(function(){
			if($(this).hasClass("less")) {
				$(this).removeClass("less");
				$(this).html(moretext);
			} else {
				$(this).addClass("less");
				$(this).html(lesstext);
			}
			$(this).parent().prev().toggle();
			$(this).prev().toggle();
			return false;
		});
	});
	</script>

	<?php
	$feedLimit = 3;
	?>

	<div class="feedbox">
		<div class="feedhead">
			<span class="feedtitle">	
				NEUSTE KOMMENTARE
			</span>
		</div>
		<div class="feedbody">
			<?php
			$sql = "
				SELECT subjects.ID AS ID, subject_name, username, ratings.time_stamp AS time_stamp, comment
				FROM ratings
				JOIN subjects ON ratings.subject_ID = subjects.ID
				JOIN users ON ratings.user_ID = users.user_ID
				ORDER BY ratings.time_stamp DESC
				LIMIT $feedLimit
			"; //Set $feedLimit above
			$result = mysqli_query($con, $sql);
			
			$count = 0;
			while($row = mysqli_fetch_assoc($result)){
				$count++;
				?>
				<p>
					<strong><a href="index.php?subject=<?php echo $row['ID']?>"><?php echo $row['subject_name']?></a></strong>
					<br>
					<span style="color:grey; font-size:10px;"><?php echo $row['username']?> <?php echo time_elapsed_string($row['time_stamp'])?></span>
				</p>
				<p class="more">
					<?php echo $row['comment']?>
				</p>
				<?php
				if($count < $feedLimit) echo "<hr>"; //Set $feedLimit above
			}
			?>
		</div>
	</div>

	<br>

	<div class="feedbox">
		<div class="feedhead">
			<span class="feedtitle">	
				NEUSTE FRAGEN
			</span>
		</div>
		<div class="feedbody">
			<?php
			$sql = "
				SELECT subjects.ID AS ID, subject_name, username, questions.time_stamp AS time_stamp, question
				FROM questions
				JOIN subjects ON questions.subject_ID = subjects.ID
				JOIN users ON questions.user_ID = users.user_ID
				ORDER BY questions.time_stamp DESC
				LIMIT $feedLimit
			"; //Set $feedLimit above
			$result = mysqli_query($con, $sql);
			
			$count = 0;
			while($row = mysqli_fetch_assoc($result)){
				$count++;
				?>
				<p>
					<strong><a href="index.php?subject=<?php echo $row['ID']?>"><?php echo $row['subject_name']?></a></strong>
					<br>
					<span style="color:grey; font-size:10px;"><?php echo $row['username']?> <?php echo time_elapsed_string($row['time_stamp'])?></span>
				</p>
				<p class="more">
					<?php echo $row['question']?>
				</p>
				<?php
				if($count < $feedLimit) echo "<hr>"; //Set $feedLimit above
			}
			?>
		</div>
	</div>
	
	<br>
</div>

<div id="changeButton"></div>

<!--<div id="aaa"></div>-->

<script>
function collision($div1, $div2) {

	var left1 = $div1.offset().left;
	var width1 = $div1.width();
	var right1 = left1 + width1;
	
	var left2 = $div2.offset().left;
	
	//$('#aaa').html("left1: " + left1 + ", width1: " + width1 + ", right1: " + right1 + " // left2: " + left2);
	
	if(right1+33 > left2) return true;
	return false;
};

$(document).ready(function() {
	if(collision($('#treebody'), $('#changeButton')) == true){
		$('#div2').hide();
		$("#changeButton").text("Feed anzeigen");
	}else{
		$('#div2').show();
		$("#changeButton").text("Feed ausblenden");
	}
});

$(window).resize(function(){
	if(collision($('#treebody'), $('#changeButton')) == true){
		$('#div2').hide();
		$("#changeButton").text("Feed anzeigen");
	}else{
		$('#div2').show();
		$("#changeButton").text("Feed ausblenden");
	}	
});


$("#changeButton").click(function () {
	if($('#div2').is(":visible")){
		$('#div2').hide();
		$("#changeButton").text("Feed anzeigen");
	}else{
		$('#div2').show();
		$("#changeButton").text("Feed ausblenden");
	}
});
</script>

<div id="treebody" class="container">
	
	<div id="result"></div>

	<?php
		if(isset($_GET['suchfeld']) && $_GET['suchfeld'] != 'Übersicht Startseite'){
			echo 	'<div class="alert alert-warning alert-dismissable fade in">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					  <strong>Hinweis!</strong> Leider konnten wir für deine Suche keine Ergebnisse finden.
					</div>';
		}
	 ?>

	<!--Karussell-->
	<div class="container">	
	  <div style="margin-left:10%; margin-right:10%" id="myCarousel" class="carousel slide" data-ride="carousel">
		<!-- Indicators -->
		<ol class="carousel-indicators">
		  <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
		  <li data-target="#myCarousel" data-slide-to="1"></li>
		  <li data-target="#myCarousel" data-slide-to="2"></li>
		</ol>

		<!-- Wrapper for slides -->
		<div class="carousel-inner">
		  <div class="item active">
			<img src="pictures/carousel_afterLogin/carouselAfter_one.jpg" style="width:100%;">
		  </div>

		  <div class="item">
			<img src="pictures/carousel_afterLogin/carouselAfter_two.jpg" style="width:100%;">
		  </div>

		  <div class="item">
			<img src="pictures/carousel_afterLogin/carouselAfter_three.jpg" style="width:100%;">
		  </div>
		</div>

		<!-- Left and right controls -->
		<a class="left carousel-control" href="#myCarousel" data-slide="prev">
		  <span class="glyphicon glyphicon-chevron-left"></span>
		  <span class="sr-only">Previous</span>
		</a>
		<a class="right carousel-control" href="#myCarousel" data-slide="next">
		  <span class="glyphicon glyphicon-chevron-right"></span>
		  <span class="sr-only">Next</span>
		</a>
	  </div>
	</div>
 
	<hr>
	
	<?php
	/*Vorbereitung*/
	//Hide all
	$displayTree = "style=\"display:none\"";
	$displaySearch = "style=\"display:none\"";

	//Enable all buttons
	$displayButtonTree = "";
	$displayButtonSearch = "";


	if (isset($_GET['btn-toTree'])){ //Wenn Baum-Button geklickt
		$displayTree = "";
		$displaySearch = "style=\"display:none\"";

		$displayButtonTree = "disabled";
	}

	if (isset($_GET['btn-toSearch'])){ //Wenn Suche-Button geklickt
		$displayTree = "style=\"display:none\"";
		$displaySearch = "";

		$displayButtonSearch = "disabled";
	}
	?>

	<h3 id="auswahl" align="center">Wie möchtest du deine Veranstaltung finden?</h3>
	<div align="center">
			<a id="treebutton" style="width:330px" class="btn btn-primary" >Veranstaltung aus Verzeichnis wählen</a>
			<a id="searchbutton" style="width:330px" class="btn btn-primary" >Veranstaltungen nach Kriterien durchsuchen</a>
	</div>




	<div id="treeSide" <?php echo $displayTree ?>>
	<hr>
		<h2>Veranstaltungsverzeichnis</h2>
		<div class="well" style="width:500px; padding: 8px 0;">
			<div>
				<ul class="nav nav-list">

	<?php

	//Super sexy Caching startet
	$key = "treeside";
	$CachedString = $InstanceCache->getItem($key);


	if (is_null($CachedString->get())) {

		$content = "";
		//Erstellt das Verzeichnis
		$array = array(array("Bachelor - Kernprogramm", "bachelor_basic"), array("Bachelor - Vertiefungsprogramm", "bachelor"), array("Master", "master"));
		for($x = 0; $x <= 2; $x++) {
			$content .= "<li><label class=\"tree-toggler nav-header treetop\" style=\"color:rgb(0, 51, 153)\"><strong>".$array[$x][0]."</strong></label>";

				$content .= "<ul class=\"nav nav-list tree\" style=\"display:none\">";
				$result = mysqli_query($con,"SELECT * FROM moduletypes");
				while($modulTypes = mysqli_fetch_assoc($result)){ //Modultyp
					$content .= "<li><label class=\"tree-toggler nav-header\">".$modulTypes['name']."</label>";

					$content .= "<ul class=\"nav nav-list tree\" style=\"display:none\">";
					$result2 = mysqli_query($con,"
						SELECT modules.name AS module_name, levels.name AS level_name, type
						FROM modules
						JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
						JOIN levels ON modules_levels.level_ID = levels.level_ID
						WHERE levels.name = '".$array[$x][1]."' AND type = '".$modulTypes['name']."'
			ORDER BY TRIM(modules.name);
					");
					while($modules = mysqli_fetch_assoc($result2)){ //Modulname
						$content .= "<li><label class=\"tree-toggler nav-header\">".$modules['module_name']."</label>";

						$content .= "<ul class=\"nav nav-list tree\" style=\"display:none\">";
						$result3 = mysqli_query($con,"
							SELECT subject_name, subjects.ID AS subject_id, modules.name AS module_name
							FROM subjects
							JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
							JOIN modules ON subjects_modules.module_ID = modules.module_ID
							WHERE modules.name = '".$modules['module_name']."'
			  ORDER BY TRIM(subject_name);
						");
						while($subjects = mysqli_fetch_assoc($result3)){ //Veranstaltungsname
							$content .= "<li><a href=\"index.php?subject=".$subjects['subject_id']."\">".$subjects['subject_name']."</a></li>";
						}
						$content .= "</ul>";
						$content .= "</li>";
					}
					$content .= "</ul>";
					$content .= "</li>";
				}
				$content .= "</ul>";
			$content .= "</li>";
		}
		$CachedString->set($content)->expiresAfter(300000);//in seconds, also accepts Datetime
		$InstanceCache->save($CachedString); // Save the cache item just like you do with doctrine and entities

		echo $CachedString->get();

	} else {
		echo $CachedString->get();
	}
?>
				</ul>
			</div>
		</div>

		<script> //Schließt das Verzeichnis beim Laden der Seite
		$(document).ready(function () {
			$('label.tree-toggler').click(function () {
				$(this).parent().children('ul.tree').toggle(300);
			});
		});
		</script>
	</div>


	<div id="searchSide" <?php echo $displaySearch ?>>
		<hr>
		<h2>Veranstaltungssuche</h2>
		<p><i>Vorsicht beim Filtern: Wird beim Modul-Typ "BWL" angegeben, beim Modul aber "Informatik", kann es natürlich keine Ergebnisse geben. Ebenso können sich beispielsweise Dozent und Institut schnell gegenseitig ausschließen.</i></p>

		<form id="filtersort" class="form-horizontal" method="post">
			<div class="row">
				<div class="col-md-4">

					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4">Modul-Typ:</label>
							<div class="col-md-8">
								<div class="row">

									<!--
									VORSICHT!
									Folgender Code erstellt Checkboxen dynamisch. Funktioniert allerdings (kontrolliert) nur
									bis zu 12 Checkboxen wegen der Spaltenaufteilung (col-md-x), siehe switch unten.
									Sollte aber eigentlich genügen.
									-->

									<?php

									$q = mysqli_query($con,"SELECT * FROM moduletypes");
									$array_types = array();
									while($row = mysqli_fetch_assoc($q)){
										$array_types[] .= $row['name'];
									}

									$columnSize = "";
									switch(count($array_types)){
										case 1:
										case 2:
										case 3:
										$columnSize = 12;
										break;
										case 4:
										case 5:
										case 6:
										$columnSize = 6;
										break;
										case 7:
										case 8:
										case 9:
										$columnSize = 4;
										break;
										case 10:
										case 11:
										case 12:
										$columnSize = 3;
										break;
									}
									?>

									<div class="col-md-<?php echo($columnSize)?>">
										<?php
										for ($j = 1; $j <= count($array_types); $j++) {
											$i = $j-1; //Nicht einfach "$j = 0; $j < count($array_types)" damit Modulus-Operation unten funktioniert

											echo("
												<div class=\"checkbox\"><label><input type=\"checkbox\" name=\"modulType[]\" value=\"".$array_types[$i]."\" checked >".$array_types[$i]."</label></div>
											");

											if(($j%3)==0){ //Fängt eine neue Spalte nach drei Einträgen an
												echo("</div><div class=\"col-md-".$columnSize."\">");
											}
										}
										?>
									</div>
								</div>
							</div>
						</div>
					</div>

				<?php
					$key = "table_mod_selection";
					$CachedString = $InstanceCache->getItem($key);
					if (is_null($CachedString->get())) {
						$mod_selection = "<option value=\"none\">(Keine Einschränkung)</option>";
						$result = mysqli_query($con,"SELECT * FROM modules ORDER BY name");
						while($mod_row = mysqli_fetch_assoc($result)){
			  $mod_selection .= "<option value=".$mod_row['module_ID'].">".$mod_row['name']." [".$mod_row['code']."]</option>";
			}
						$CachedString->set($mod_selection)->expiresAfter(3000000);//in seconds, also accepts Datetime
						$InstanceCache->save($CachedString); // Save the cache item just like you do with doctrine and entities
					} else {
						$mod_selection = $CachedString->get();
					}
				?>
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Modul:</label>
							<div class="col-md-8">
								<select class="form-control" name="module">
									<?php echo $mod_selection ?>
								</select>
							</div>
						</div>
					</div>

				</div>

				<div class="col-md-4">

				<?php
					$key = "table_lec_selection";
					$CachedString = $InstanceCache->getItem($key);
					if (is_null($CachedString->get())) {
						$lec_selection = "<option value=\"none\">(Keine Einschränkung)</option>";
						$sql = "
							SELECT *
							FROM lecturers
							JOIN lecturers_institutes ON lecturers.lecturer_ID=lecturers_institutes.lecturer_ID
							JOIN institutes ON lecturers_institutes.institute_ID=institutes.institute_ID
							ORDER BY name, last_name
						";
						$result = mysqli_query($con,$sql);
						while($row = mysqli_fetch_assoc($result)){
							$lec_selection .= "<option value=".$row['lecturer_ID'].">".$row['last_name'].", ".$row['first_name']." (".$row['abbr'].")</option>";
						}
						$CachedString->set($lec_selection)->expiresAfter(3000000);//in seconds, also accepts Datetime
						$InstanceCache->save($CachedString); // Save the cache item just like you do with doctrine and entities
					} else {
						$lec_selection = $CachedString->get();
					}
				?>
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Dozent:</label>
							<div class="col-md-8">
								<select class="form-control" name="lecturer">
									<?php echo $lec_selection ?>
								</select>
							</div>
						</div>
					</div>

				<?php
					$key = "table_insti_selection";
					$CachedString = $InstanceCache->getItem($key);
					if (is_null($CachedString->get())) {
						$insti_selection = "<option value=\"none\">(Keine Einschränkung)</option>";
						$result = mysqli_query($con, "SELECT * FROM institutes ORDER BY name");
						while($row = mysqli_fetch_assoc($result)){
			  $insti_selection .= "<option value=".$row['institute_ID'].">".$row['name']." (".$row['abbr'].")</option>";
			}
						$CachedString->set($insti_selection)->expiresAfter(3000000);//in seconds, also accepts Datetime
						$InstanceCache->save($CachedString); // Save the cache item just like you do with doctrine and entities
					} else {
						$insti_selection = $CachedString->get();
					}
				?>
					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Institut:</label>
							<div class="col-md-8">
								<select class="form-control" name="institute">
									<?php echo $insti_selection ?>
								</select>
							</div>
						</div>
					</div>

				</div>

				<div class="col-md-4">

					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Level:</label>
							<div class="col-md-8">
								<select name="level" class="form-control" required>
									<option value="none">(Keine Einschränkung)</option>
									<option value="bachelor_basic">Bachelor: Kernprogramm</option>
									<option value="bachelor">Bachelor: Vertiefungsprogramm</option>
									<option value="master">Master</option>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4">Semester:</label>
							<div class="col-md-8">
								<select class="form-control" name="semester">
									<option value="none">(Keine Einschränkung)</option>
									<option value="Winter">Winter</option>
									<option value="Sommer">Sommer</option>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="row">
							<label class="control-label col-md-4" for="pwd">Sprache:</label>
							<div class="col-md-8">
								<select class="form-control" name="language">
									<option value="none">(Keine Einschränkung)</option>
									<option value="Deutsch">Deutsch</option>
									<option value="Englisch">Englisch</option>
								</select>
							</div>
						</div>
					</div>

				</div>
			</div>

			<br><br>

			<div class="row"><div class="control-group"> <div class="controls form-inline">
				<span><strong>Sortieren nach</strong></span>

				<select class="form-control" id="sortArea" name="sortArea">
					<option value="overall">Bewertung insgesamt</option>
					<option value="lecture">Vorlesung</option>
					<option value="exam">Prüfung</option>
				</select>

				<select class="form-control treeSort" id="sortOverall" name="sortOverall">
					<option value="overallRating">Gesamtbewertung</option>
					<option value="recoms">Veranstaltungsempfehlungen</option>
				</select>

				<select class="form-control treeSort" id="sortLecture" style="display:none" name="sortLecture">
					<option value="overallLecture">Overall Vorlesung</option>
					<option value="relevance">Prüfungsrelevanz</option>
					<option value="interest">Interessantheit</option>
					<option value="quality">Qualität der Arbeitsmaterialien</option>
				</select>

				<select class="form-control treeSort" id="sortExamType" style="display:none" name="sortExamType">
					<option value="written_oral">Schriftlich/Mündlich</option>
					<!--<option value="oral">Mündlich</option>-->
					<option value="other">Sonstige</option>
				</select>

				<select class="form-control treeSort" id="sortExamItem" style="display:none" name="sortExamItem">
					<option value="overallExam">Overall Prüfung</option>
					<option value="effort">Aufwand</option>
					<option value="fairness">Fairness</option>
					<option value="timePressure">Zeitdruck</option>
					<option value="reproductionTransfer">Reproduktion/Transfer</option>
					<option value="qualitativeQuantitative">Qualitativ/Quantiativ</option>
				</select>

				<select class="form-control treeSort" id="sortExamOther" style="display:none" name="sortExamOther">
					<option value="amountRatings">#Anmerkungen Prüfung</option>
				</select>

				<select class="form-control" name="orderDirection">
					<option id="ab" value="ASC">Absteigend</option>
					<option id="auf" value="DESC">Aufsteigend</option>
				</select>

				<button type="submit" class="btn btn-primary" id="btn-filterSort">Filtern & Sortieren</button>
			</div></div></div>
		</form>

		<div style="align:center;display:none;" id="tabelleLaden"></div>

		<script>
		//Script für die Sortierungs-Dropdowns
		$('#sortArea').on('change', function() {
			switch(this.value){
				case "overall":
					$('.treeSort').hide();
					$('#sortOverall').show();
					$('#auf').html("Aufsteigend");
					$('#ab').html("Absteigend");
					break;
				case "lecture":
					$('.treeSort').hide();
					$('#sortLecture').show();
					$('#auf').html("Aufsteigend");
					$('#ab').html("Absteigend");
					break;
				case "exam":
					$('.treeSort').hide();
					$('#sortExamType').show();
					$('#sortExamItem').show();
					if($('#sortExamItem').val() != "reproductionTransfer" && $('#sortExamItem').val() != "qualitativeQuantitative"){
						$('#auf').html("Aufsteigend");
						$('#ab').html("Absteigend");
					}else{
						switch($('#sortExamItem').val()){
							case "reproductionTransfer":
								$('#auf').html("Reproduktion zuerst");
								$('#ab').html("Transfer zuerst");
								break;
							case "qualitativeQuantitative":
								$('#auf').html("Qualitativ zuerst");
								$('#ab').html("Quantitativ zuerst");
								break;
						}
					}
					break;
			}
		})

		$('#sortExamType').on('change', function() {
			switch(this.value){
				case "other":
					$('#sortExamItem').hide();
					$('#sortExamOther').show();
					$('#auf').html("Aufsteigend");
					$('#ab').html("Absteigend");
					break;
				default:
					$('#sortExamOther').hide();
					$('#sortExamItem').show();
					if($('#sortExamItem').val() != "reproductionTransfer" && $('#sortExamItem').val() != "qualitativeQuantitative"){
						$('#auf').html("Aufsteigend");
						$('#ab').html("Absteigend");
					}else{
						switch($('#sortExamItem').val()){
							case "reproductionTransfer":
								$('#auf').html("Reproduktion zuerst");
								$('#ab').html("Transfer zuerst");
								break;
							case "qualitativeQuantitative":
								$('#auf').html("Qualitativ zuerst");
								$('#ab').html("Quantitativ zuerst");
								break;
						}
					}
					break;
			}
		})

		$('#sortExamItem').on('change', function() {
			switch(this.value){
				case "reproductionTransfer":
					$('#auf').html("Reproduktion zuerst");
					$('#ab').html("Transfer zuerst");
					break;
				case "qualitativeQuantitative":
					$('#auf').html("Qualitativ zuerst");
					$('#ab').html("Quantitativ zuerst");
					break;
				default:
					$('#auf').html("Aufsteigend");
					$('#ab').html("Absteigend");
					break;
			}
		})
		</script>

		<!--Ergebnistabelle-->
		<script>

		$('#btn-filterSort').on('click', function(e) {
			e.preventDefault();
			$('#limitationText').hide();
			if($('input[type="checkbox"]:checked').length) {
				$('#resultTable').hide();
				$('#tabelleLaden').show();
				$.ajax({
					url: "tree_createTable.php",
					type: "get",
					data: $("#filtersort").serialize(),
					success: function (data) {
						var help = $('#resultTable').html();
						$('#resultTable').show();
						$('#resultTable').html(data);
						if(help == ""){ //Nur beim ersten Mal (wenn noch keine Tabelle vorhanden)
							$('html, body').animate({ //Scroll down to results
								scrollTop: $("#btn-filterSort").offset().top -100
							}, 1500);
						}
			$('#tabelleLaden').hide();
						history.replaceState("Studienführer Such- und Filterseite", "Such- und Filterergebnis", "tree.php?filterandsearch=filterandsearch&val="+encodeURI($("#filtersort").serialize()));
					},
					error: function() {
						$('#tabelleLaden').hide();
						alert("Error!");
					}
				});
			} else {
				alert("Wähle mindestens einen Modultyp aus - andernfalls kann es keine Ergebnisse geben.");
			}

		});
		</script>

		<div id="resultTable"></div>
		<p id="limitationText" style="text-align:center; display:none"><i>Das Suchergebnis ist aus Performancegründen auf 50 Ergebnisse limitiert.</i></p>
	</div>
</div>
<script>
	//Startet Pagination
	$(document).ready(function() {
		insertLoader('#tabelleLaden');
		if((decodeURIComponent((new RegExp('[?|&]' + 'filterandsearch' + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null)=="filterandsearch"){
			$('#searchbutton').addClass('disabled');
			$('#treebutton').removeClass('disabled');
			$('#treeSide').hide();
			$('#searchSide').show();
			$('#tabelleLaden').show();
			$.ajax({
					url: "tree_createTable.php",
					type: "get",
					data: decodeURI(window.location.href.split("&val=")[1]),
					success: function (data) {
              $('#tabelleLaden').hide();
						var help = $('#resultTable').html();
						$('#resultTable').html(data);
						if(help == ""){ //Nur beim ersten Mal (wenn noch keine Tabelle vorhanden)
							$('html, body').animate({ //Scroll down to results
								scrollTop: $("#btn-filterSort").offset().top -100
							}, 500);
						}
						history.replaceState("Studienführer Such- und Filterseite", "Such- und Filterergebnis", "tree.php?filterandsearch=filterandsearch&val="+$("#filtersort").serialize());
					},
					error: function() {
						$('#tabelleLaden').hide();
						alert("Error!");
					},
          finally: function(){
            $('#tabelleLaden').hide();
          }
				});
		}
		$('#treebutton').click(function(event){
			$('#treebutton').addClass('disabled');
			$('#searchbutton').removeClass('disabled');
			$('#searchSide').hide();
			$('#treeSide').show();
		});
		$('#searchbutton').click(function(event){
			$('#searchbutton').addClass('disabled');
			$('#treebutton').removeClass('disabled');
			$('#treeSide').hide();
			$('#searchSide').show();
		});
	});
</script>
<script src="res/lib/jquery.simplePagination.js"></script>
<script src="res/lib/jquery.nicescroll-master/jquery.nicescroll.js"></script>
<br />
<br />
<br />
<br />
<br />
</body>
</html>
