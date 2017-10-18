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
		
		$lectureHeadings = array("Overall-Score", "Prüfungsrelevanz", "Interessantheit", "Qualität der Arbeitsmaterialien");
		$lecture = array($row['lecture0'], $row['lecture1'], $row['lecture2'], $row['lecture3']);
		
		?>
		<h4><strong>Vorlesung</strong></h4>

		<table class="ratingtable" style="width:100%">
			<?php
			for($i=0;$i<count($lectureHeadings);$i++){
				?>			
				<tr>
					<td>
						<span style="float:left; margin-left:3px;"><?php echo $lectureHeadings[$i] ?></span>
						<span style="float:right; margin-right:3px;"><?php echo $lecture[$i] ?></span>
					</td>
				</tr>
				
				<tr>
					<td valign="center" style="width:70%">
						<div style="font-size:15px; font-weight:bold; line-height:2">
							<div class="progress" style="margin-bottom:6px;">
								<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $lecture[$i]*10 ?>%">

								</div>
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
			$examHeadings = array("Overall-Score", "Aufwand", "Fairness", "Zeitdruck");
			$exam = array($row['exam0'], $row['exam1'], $row['exam2'], $row['exam3'], $row['exam4'], $row['exam5']);
			
			$examRight[0] = 0;
			$examLeft[0] = 0;
			if($exam[4]>0){
				$examRight[0] = $exam[4];
			}elseif($exam[4]<0){
				$examLeft[0] = abs($exam[4]);
			}
			
			$examRight[1] = 0;
			$examLeft[1] = 0;
			if($exam[5]>0){
				$examRight[1] = $exam[5];
			}elseif($exam[5]<0){
				$examLeft[1] = abs($exam[5]);
			}

			?>
			<table class="ratingtable" style="width:100%">
				<?php
				for($i=0;$i<count($examHeadings);$i++){
					?>
					<tr>
						<td>
							<span style="float:left; margin-left:3px;"><?php echo $examHeadings[$i] ?></span>
							<span style="float:right; margin-right:3px;"><?php echo $exam[$i] ?></span>
						</td>
					</tr>
					
					<tr>
						<td valign="center" style="width:70%">
							<div style="font-size:15px; font-weight:bold; line-height:2">
								<div class="progress" style="margin-bottom:6px;">
									<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $exam[$i]*10 ?>%"></div>
								</div>
							</div>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
	
			<br>
			
			<table class="ratingtable" style="width:100%">
				<?php
				$examHeadingTwo = array(array("Reproduktion", "Transfer"), array("Qualitativ", "Quantitativ"));
				for($i=0;$i<2;$i++){
					?>
					<tr>
						<td>
							<span style="float:left; margin-left:3px;"><?php echo $examHeadingTwo[$i][0] ?></span>
						</td>
						<td>
							<span style="float:right; margin-right:3px;"><?php echo $examHeadingTwo[$i][1] ?></span>
						</td>
					</tr>
					
					<tr>
						<td valign="center" style="width:50%">
							<div style="font-size:15px; font-weight:bold; line-height:2">
								<div class="progress" style="margin-bottom:7px; transform: rotate(-180deg); border-top-left-radius:0; border-bottom-left-radius:0; border-left:solid 0.5px grey;">
									<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $examLeft[$i]*10 ?>%"></div>
								</div>
							</div>
						</td>
						
						<td valign="center" style="width:50%">
							<div style="font-size:15px; font-weight:bold; line-height:2">
								<div class="progress" style="margin-bottom:7px; border-top-left-radius:0; border-bottom-left-radius:0; border-left:solid 0.5px grey;">
									<div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $examRight[$i]*10 ?>%"></div>
								</div>
							</div>
						</td>
					</tr>
					<?php
				}?>
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