<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="container" style="margin-top:60px">

	<?php
	//Variablen, die dafür sorgen, dass im Falle einer fehlerhaften oder fehlenden ID in der URL eine entsprechende Nachricht angezeigt wird
	$showMain = "";
	$showFailedLoad = "none";
	
	// Modul aus URL speichern
	if (isset($_GET['module_id'])){
		$module_id = strval ($_GET['module_id']);
	}
	else{
		$showMain = "none";
		$showFailedLoad = "";
	}
	
	//Moduldatensatz laden
	$sql = "
		SELECT code, modules.name AS module_name, type, ects, active
		FROM modules
		WHERE modules.module_ID = '".$module_id."'
	";
	$result = mysqli_query($con,$sql);

	// Check, ob Datensatz existiert (ist der Fall, wenn mindestens ein Ergebnis zurückgegeben wird)
	if (mysqli_num_rows($result) >= 1 ) {
		$moduleData = mysqli_fetch_assoc($result);
	} else {
		$showMain = "none";
		$showFailedLoad = "";
	}
	
	/*Lade alle Einträge mit mehreren möglichen Einträgen*/
	//levels
	$sql = "
		SELECT levels.name AS level_name
		FROM modules
		JOIN modules_levels ON modules.module_ID = modules_levels.module_ID
		JOIN levels ON modules_levels.level_ID = levels.level_ID
		WHERE modules.module_ID = '".$module_id."'
		ORDER BY CASE
			when levels.name = 'bachelor_basic' then 1
			when levels.name = 'bachelor' then 2
			when levels.name = 'master' then 3
		END
	";
	$result = mysqli_query($con,$sql);
	$levels = "";
	while($row = mysqli_fetch_assoc($result)){
		switch($row['level_name']){
			case "bachelor_basic":
				$levels .= "Bachelor: Kernprogramm"."<br>";
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
	
	//subjects
	$sql = "
		SELECT subject_name, subjects.ID AS subject_id, subjects.ECTS AS subject_ects
		FROM subjects
		JOIN subjects_modules ON subjects.ID = subjects_modules.subject_ID
		JOIN modules ON subjects_modules.module_ID = modules.module_ID
		WHERE modules.module_ID = '".$module_id."'
		ORDER BY subject_name
	";
	$result = mysqli_query($con,$sql);
	$subjects = "";
	while($row = mysqli_fetch_assoc($result)){
		$subjects .= "<a href=\"index.php?subject=".$row['subject_id']."\">".$row['subject_name']."</a> - <i>".$row['subject_ects']." ECTS</i><br />";
	}
	$subjects = substr($subjects, 0, -4);
	
	/*Durchschnittsbewertung Modul*/
	//Anzahl Bewertungen
	$sql = "
		SELECT COUNT(ratings.ID) AS count FROM ratings
		JOIN subjects_modules ON ratings.subject_ID = subjects_modules.subject_ID
		JOIN modules ON subjects_modules.module_ID = modules.module_ID
		WHERE modules.module_ID = '".$module_id."'
	";
	$result = mysqli_query($con, $sql);
	$row = mysqli_fetch_assoc($result);
	$count = $row['count'];
	
	
	//Summe ECTS in diesem Modul (der Veranstaltungen für die schon Bewertungen vorliegen)
	//(Kein SUM() wegen unterschiedlichen Dezimaltrennzeichen (Punkt und Komma)
	//-> Lösung: interativ aufsummieren)
	$sql = "
		SELECT *, subjects.ECTS as subject_ects FROM ratings
		JOIN subjects_modules ON ratings.subject_ID = subjects_modules.subject_ID
		JOIN modules ON subjects_modules.module_ID = modules.module_ID
		JOIN subjects ON ratings.subject_ID = subjects.ID
		WHERE modules.module_ID = '".$module_id."'
	";
	$result = mysqli_query($con, $sql);
	$sum = 0;
	while($row = mysqli_fetch_assoc($result)){
		$sum += str_replace(",",".",$row['subject_ects']);
	}
	
	$result = mysqli_query($con, $sql);
	$avg = 0;
	while($row = mysqli_fetch_assoc($result)){
		$avg += $row['general0'] * (str_replace(",",".",$row['subject_ects'])/$sum);
	}
	?>
	
	<div style="display:<?php echo $showMain?>">
		<p style="margin-bottom:0px; margin-left:1px; font-weight:bold; color:grey; letter-spacing: 0.5px; font-family:open sans">MODUL</p>
		<h2 style="margin-top:0px"><?php echo $moduleData['module_name']?></h2>
		<hr>
		
		<?php
		if ($moduleData['active'] == 0){
			echo '
				<br>
				<div class="alert alert-danger">
					Dieses Modul existiert im aktuellen Modulhandbuch nicht mehr.
				</div>
			';
		}
		?>
				
		<div class="row">
			<div class="col-md-8">			
			
				<table class="table" style="border-top:solid; border-top-color:white">
					<tbody>
						<tr>
							<th>Kennung:</th>
							<td><?php echo $moduleData['code']?></td>
						</tr>
						<tr>
							<th>Level:</th>
							<td><?php echo $levels?></td>
						</tr>
						<tr>
							<th>Typ:</th>
							<td><?php echo $moduleData['type']?></td>
						</tr>
						<tr>
							<th>ECTS:</th>
							<td><?php echo $moduleData['ects']?></td>
						</tr>
						<tr>
							<th>Veranstaltungen:</th>
							<td>
								<?php echo $subjects?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-md-4">
				<div style="width: 33%; margin: 0 auto;">
					
					<span id="value" style="display:none"><?php echo round($avg,1) ?></span>
					<div class="c100 p0" id="div_loading_progress"><span id="span_progress">0</span>
					  <div class="slice">
						<div class="bar"></div>
						<div class="fill"></div>
					  </div>
					</div>
				</div>
				<br />
				<div style="width: 66%; margin: 0 auto;">
				<h4 style="text-align: center; margin-bottom:0.5px;">Gesamtbewertung</h4>
				<p style="text-align: center;">Basierend auf <strong><?php echo $count ?></strong> <?php if($count == 1) echo "Bewertung"; else echo "Bewertungen" ?></p>
				</div>
			</div>
		</div>
		
		<script>
		var pct = 0,
			span_progress = document.getElementById("span_progress"),
			div_loading_progress = document.getElementById("div_loading_progress");
			
		function display_pct(p) {
			span_progress.innerHTML=""+p/10+" /10";
			div_loading_progress.className="c100 p"+p;
		}

		function update_pct(){
			display_pct(pct++);
				
			if (pct<=$('#value').html()*10) {
				setTimeout(update_pct,10);
			}
		}

		setTimeout(update_pct,100);
		</script>
	</div>
	
	<div style="display:<?php echo $showFailedLoad?>">
		Das Modul konnte nicht geladen werden. Entweder wurde keine Modul-ID übergeben oder die übergebene Modul-ID existiert nicht in unserer Datenbank.
	</div>
	
</div>

</body>
</html>