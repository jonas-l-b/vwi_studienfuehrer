<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "nav.php" ?>

<div class="container" style="margin-top:60px">
	
	<?php
	$displayShow = "";
	$displayEdit = "style=\"display:none\"";
	
	//Müssen vorher schon gezogen werden, damit sie beim ÄNDERN geändert werden können; sonst werden Änderungen nicht direkt angezeigt
	$u_degree = $userRow['degree'];
	$u_advance = $userRow['advance'];
	$u_semester = $userRow['semester'];
	
	if (isset($_POST['btn-edit'])){
		$displayShow = "style=\"display:none\"";
		$displayEdit = "";
	}
	
	if (isset($_POST['btn-save'])){
		$displayShow = "";
		$displayEdit = "style=\"display:none\"";
		
		//Daten aus Form ziehen
		$degree = strip_tags($_POST['degree']);
		$advance = strip_tags($_POST['advance']);
		$semester = strip_tags($_POST['semester']);
		
		$q1 = mysqli_query($con,"
			UPDATE users
			SET degree = '".$degree."', advance = '".$advance."', semester = '".$semester."'
			WHERE user_ID = '".$userRow['user_ID']."'
		");
		
		if($q1==true){
			$msg = "
				<div class='alert alert-success'>
					<span class='glyphicon glyphicon-info-sign'></span> &nbsp; Die Änderungen wurden erfolgreich gespeichert.
				</div>
			";
			
			//Hier ändern, damit Änderungen direkt nach Speichern angezeigt werden (nicht erst nach refresh)
			$u_degree = $degree;
			$u_advance = $advance;
			$u_semester = $semester;
		}
	}
	
	if (isset($_POST['btn-cancel'])){
		$displayShow = "";
		$displayEdit = "style=\"display:none\"";
	}
	?>

	<h2><?php echo $userRow['first_name']." ".$userRow['last_name']?></h2>
	<br>
	
	<ul class="nav nav-tabs">
		<li class="active"><a data-toggle="tab" href="#userData">Benutzerdaten</a></li>
		<li><a data-toggle="tab" href="#favourites">Favoriten</a></li>
	<!--<li><a data-toggle="tab" href="#menu2">Menu 2</a></li>
		<li><a data-toggle="tab" href="#menu3">Menu 3</a></li>-->
	</ul>

	<div class="tab-content">
		<div id="userData" class="tab-pane fade in active">
			<br>
			<?php if(isset($msg)) echo $msg?>
			<div <?php echo $displayShow?>>
				<table class="table" style="border-top:solid; border-top-color:white">
					<tbody>
						<tr>
							<th>Benutzername:</th>
							<td><?php echo $userRow['username']?></td>
						</tr>
						<tr>
							<th>E-Mail:</th>
							<td><?php echo $userRow['email']?></td>
						</tr>
						<tr>
							<th>Studiengang:</th>
							<td><?php echo $u_degree?></td>
						</tr>
						<tr>
							<th>Fortschritt:</th>
							<td><?php echo ucfirst($u_advance)?></td>
						</tr>
						<tr>
							<th>Fachsemester:</th>
							<td><?php echo $u_semester?></td>
						</tr>
					</tbody>
				</table>
				<form method="post">
					<button type="submit" class="btn btn-primary" name="btn-edit">Daten bearbeiten</button>
					<button type="button" a href="#myModal" role="button" class="btn btn-primary" data-toggle="modal">Passwort ändern</button>
				</form>
			</div>
			
			<div <?php echo $displayEdit?>>
				<form method="post">
					<table class="table dataChangeTable" style="border-top:solid; border-top-color:white">
						<tbody>
							<tr>
								<th>Studiengang:</th>
								<td>
									<div class="form-group">
										<input value="<?php echo $userRow['degree']?>" name="degree" type="text" class="form-control" placeholder="Studiengang" required />
									</div>
								</td>
							</tr>
							<tr>
								<th>Fortschritt:</th>
								<td>
									<div class="form-group">
										<select name="advance" class="form-control" required>
											<option value="bachelor" <?php if (ucfirst($userRow['advance'])=="Bachelor") echo "selected"?>>Bachelor</option>
											<option value="master" <?php if (ucfirst($userRow['advance'])=="Master") echo "selected"?>>Master</option>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<th>Fachsemester:</th>
								<td>
									<div class="form-group">
										<input value="<?php echo $userRow['semester']?>" type="number" max="18" min="1" step="1" name="semester" type="text" class="form-control" placeholder="Fachsemester" required />
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					
					<button type="submit" class="btn btn-primary" name="btn-save">Änderungen speichern</button>
					<button type="submit" class="btn btn-primary" name="btn-cancel">Abbrechen</button>
				</form>
			</div>
		</div>
		<div id="favourites" class="tab-pane fade">
			<br>
			
			<?php
			//Check favourite status
			$sql="
				SELECT favourites.ID as favID, subject_ID, subject_name, code
				FROM favourites
				JOIN subjects on favourites.subject_id = subjects.ID
				WHERE user_id = '".$userRow['user_ID']."'
			";
			$result = mysqli_query($con, $sql);
			
			while($row = mysqli_fetch_assoc($result)){
			?>
				<p>
				<span id="favIcon<?php echo $row['favID']?>" style="color:rgb(255, 204, 0)" class="glyphicon glyphicon-star favouriteStar"></span>
				<a href="index.php?subject=<?php echo $row['code']?>"><?php echo $row['subject_name']?></a>
				</p>
			<?php
			}
			
			/*
			
			if(mysqli_num_rows($result) >= 1){
				$favClass = "glyphicon glyphicon-star favouriteStar";
				$favColor = "rgb(255, 204, 0)";
			} else{
				$favClass = "glyphicon glyphicon-star-empty favouriteStar";
				$favColor = "grey";
			}*/
			?>
			
			
			
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Firstname</th>
						<th>Lastname</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>John</td>
						<td>Doe</td>
					</tr>
					<tr>
						<td>Mary</td>
						<td>Moe</td>
					</tr>
					<tr>
						<td>July</td>
						<td>Dooley</td>
					</tr>
				</tbody>
			  </table>
		</div>
	<!--<div id="menu2" class="tab-pane fade">
			<h3>Menu 2</h3>
			<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam.</p>
		</div>
		<div id="menu3" class="tab-pane fade">
			<h3>Menu 3</h3>
			<p>Eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
		</div>
	-->
	</div>
	
	
	
	
	


</div>

<!-- End of page. Modal für Passwort vergessen -->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
	<div class="modal-content">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h2 class="modal-title">Passwort ändern</h2> <!-- Dynamisch Name anpassen! -->
	</div>
	<div class="modal-body">
		<p id="pwrecovery"></p>
			<form action="recoverPW.php" method="POST">
				<p>Mit einem Klick auf den Button schicken wir dir eine E-Mail an die Adresse, mit der du dich registriert hast. Mit ihr kannst du dein Passwort ändern.</p>
				<br>
				
				<div class="form-group" style="display:none">
					<input value="<?php echo $u_email?>" type="email" class="form-control" name="email" />
					<input value="<?php echo "change"?>" class="form-control" name="recoverType" />
				</div>
			
				<button type="submit" class="btn btn-primary" >E-Mail zuschicken</button>
			</form>
		
		
		</div><!-- End of Modal body -->
	</div><!-- End of Modal content -->
	</div><!-- End of Modal dialog -->
</div><!-- End of Modal -->

</body>
</html>
