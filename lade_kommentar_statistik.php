<?php

/*
*		Dieses Skript liefert die Statistiken zu einem Kommentar
*/


include "sessionsStart.php";
include "connect.php";



if (isset($_GET['kommentar'])){

	$kommentarID = $_GET['kommentar'];
	$statement1 = $con->prepare("SELECT * FROM ratings WHERE ID = ?");
	$statement1->bind_param('s', $kommentarID);
	$statement1->execute();
	$users = $statement1->get_result();
	if($row = mysqli_fetch_assoc($users)){
		
		$lectureHeadings = array(array("Nicht Prüfungsrelevant", "Sehr prüfungsrelevant"), array("Uninteressant", "Sehr interessant"), array("Materialien schlecht", "Materialien gut"));
								
		for($i=0;$i<count($lectureHeadings);$i++){
			if($row['lecture'.$i.''] < 0 ){
				$lecture[$i][0] = abs($row['lecture'.$i.'']);
				$lecture[$i][1] = 0;
			}else{
				$lecture[$i][0] = 0;
				$lecture[$i][1] = $row['lecture'.$i.''];
			}
		}
		
		?>
		<h4><strong>Vorlesung</strong></h4>

		<table class="ratingtable" style="width:100%">
			<?php
			for($i=0;$i<count($lectureHeadings);$i++){
				?>
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

		<h4><strong>Prüfung</strong></h4>
		
		<?php
		switch($row['examType']){
			case "written":
				$type = "Schriftlich";
				break;
			case "oral":
				$type = "Mündlich";
				break;
			case "other":
				$type = "Sonstige";
				break;
		}
		?>
		
		<p>Prüfungsart: <strong><?php echo $type ?></strong></p>

		<?php
		if($row['examType'] == "written" OR $row['examType'] == "oral"){
			$examHeadings = array(array("Reproduktion", "Transfer"), array("Nicht rechenlastig", "Sehr rechenlastig"), array("Aufwand < ECTS", "Aufwand > ECTS"), array("Prüfungsvorbereitung schlecht", "Prüfungsvorbereitung gut"));
			
			for($i=0;$i<count($examHeadings);$i++){
				if($row['exam'.$i.''] < 0 ){
					$exam[$i][0] = abs($row['exam'.$i.'']);
					$exam[$i][1] = 0;
				}else{
					$exam[$i][0] = 0;
					$exam[$i][1] = $row['exam'.$i.''];
				}
			}

			?>
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
								<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $exam[$i][0]*(100/3) ?>%"></div>
							</div>
						</div>
					</td>

					<td valign="center" style="width:50%">
						<div style="font-size:15px; font-weight:bold; line-height:2">
							<div class="progress" style="border-top-left-radius:0; border-bottom-left-radius:0; border-left:solid 0.5px grey;">
								<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $exam[$i][1]*(100/3) ?>%"></div>
							</div>
						</div>
					</td>
				</tr>
				<?php
			}
			?>
			</table>
		<?php
		}elseif($row['examType'] == "other"){
			?>
			<p>Kommentar zur Prüfung: </p>
			<p class="well"><?php echo $row['examText'] ?></p>
			<?php
		}
	}else{
		echo "<script> alert('Ein Fehler ist aufgetreten.');</script>";
	}
	$statement1->close();
}else{
	exit;
}

?>