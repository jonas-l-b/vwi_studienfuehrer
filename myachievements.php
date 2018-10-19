<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>

<?php include "inc/nav.php" ?>

<div class="container">
	<h1 style="text-align:center"><i class="fa fa-trophy" style="color:#FACC2E"></i> <u>Meine Errungenschaften</u> <i class="fa fa-trophy" style="color:#FACC2E"></i></h1>
	<br>

	<div style="text-align:center;">
	
		<?php
		$sql="
			SELECT * FROM badges
			LEFT JOIN users_badges ON badges.id = users_badges.badge_id
			WHERE users_badges.user_id = 2 OR users_badges.user_id IS NULL"
		;
		$result=mysqli_query($con, $sql);
		
		while($row = mysqli_fetch_assoc($result)){
			?>
			<div style="border: solid lightgrey 3px; width: 330px; padding: 5px; background-color:#f2f2f2; display: inline-block; margin:5px;">
				<table style="width:100%">
					<tr>
						<td style="width:1%; padding:5px;"><img src="pictures/badges/<?php echo $row['image']?>" class="media-object custom-media" style="width:80px; background:rgb(20,90,157); border: 4px solid white; padding:5px;"></td>
						<td>
							<table style="width:100%">
								<tr>
									<td style="text-align:left; font-size:20px;"><b><?php echo $row['name']?></b></td> 
								</tr>
								<tr>
									<td style="text-align:left;"><?php echo $row['description']?> | <?php echo $row['points']?> Punkte</td> 
								</tr>
							</table>
						</td> 
					</tr>
				</table>
			</div>
			<?php
		}
		?>
	
	</div>
</body>