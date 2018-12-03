<?php

include "sessionsStart.php";
include "header.php";
include "connect.php";

if($userRow['super_admin']==0){
	echo('Access denied.');
	exit();
}
?>
<head>
<style>
th, td {
    padding: 5px;
	border: solid 1px lightgrey;
}
</style>
</head>

<body>

<?php include "inc/nav.php" ?>

<div class="container">
	
	<h2>Gutscheinmanagement</h2>
	
	<h3>Über 2500 Punkte</h3>
	
	<?php
	//Neue Einträge in voucher
	$sql="
		SELECT *, sum(badges.points) AS sum_of_points FROM badges
		JOIN users_badges ON badges.id = users_badges.badge_id
		GROUP BY users_badges.user_id
		HAVING sum_of_points >= 2500
	";
	$result=mysqli_query($con, $sql);
	
	while($row = mysqli_fetch_assoc($result)){
		$result2 = mysqli_query($con, "SELECT * FROM vouchers2500 WHERE user_id = ".$row['user_id']);
		if(mysqli_num_rows($result2) == 0){ //Wenn Eintrag noch nicht vorhanden
			$sql2="INSERT INTO `vouchers2500`(`user_id`, `email`, `voucher2500`) VALUES (".$row['user_id'].",0,0)";
			if ($con->query($sql2) == TRUE) {
				//echo 'erfolg';
			}
		}
	}
	?>

	<form id="voucherForm2500">
	
		<table style="width:100%">
			<tr>
				<th>Username</th>
				<th>Vorname</th> 
				<th>Nachname</th>
				<th>E-Mail verschickt?</th>
				<th>Gutschein 2500 Punkte erhalten?</th> 
			</tr>
			<?php
			$result=mysqli_query($con, "SELECT vouchers2500.user_id, username, first_name, last_name, vouchers2500.email, voucher2500 FROM vouchers2500 JOIN users ON vouchers2500.user_id = users.user_ID");
			while($row = mysqli_fetch_assoc($result)){
				?>
				<tr>
					<td><?php echo $row['username']?></td>
					<td><?php echo $row['first_name']?></td> 
					<td><?php echo $row['last_name']?></td>
					<td><input type="checkbox" name="email<?php echo $row['user_id']?>" value="1" <?php if($row['email'] == 1) echo "checked"?>></td> 
					<td><input type="checkbox" name="voucher2500<?php echo $row['user_id']?>" value="1" <?php if($row['voucher2500'] == 1) echo "checked"?>></td>
				</tr>
				<?php
			}
			?>
		</table>
		
		<br>
		<button class="btn btn-primary">Änderungen speichern</button>
	</form>
	
	<br>

	<h3>Über 1500 Punkte</h3>
	
	<?php
	//Neue Einträge in voucher
	$sql="
		SELECT *, sum(badges.points) AS sum_of_points FROM badges
		JOIN users_badges ON badges.id = users_badges.badge_id
		GROUP BY users_badges.user_id
		HAVING sum_of_points >= 1500
	";
	$result=mysqli_query($con, $sql);
	
	while($row = mysqli_fetch_assoc($result)){
		$result2 = mysqli_query($con, "SELECT * FROM vouchers1500 WHERE user_id = ".$row['user_id']);
		if(mysqli_num_rows($result2) == 0){ //Wenn Eintrag noch nicht vorhanden
			$sql2="INSERT INTO `vouchers1500`(`user_id`, `email`, `voucher1500`) VALUES (".$row['user_id'].",0,0)";
			if ($con->query($sql2) == TRUE) {
				//echo 'erfolg';
			}
		}
	}
	?>

	<form id="voucherForm1500">
	
		<table style="width:100%">
			<tr>
				<th>Username</th>
				<th>Vorname</th> 
				<th>Nachname</th>
				<th>E-Mail verschickt?</th>
				<th>Gutschein 1500 Punkte erhalten?</th> 
			</tr>
			<?php
			$result=mysqli_query($con, "SELECT vouchers1500.user_id, username, first_name, last_name, vouchers1500.email, voucher1500 FROM vouchers1500 JOIN users ON vouchers1500.user_id = users.user_ID");
			while($row = mysqli_fetch_assoc($result)){
				?>
				<tr>
					<td><?php echo $row['username']?></td>
					<td><?php echo $row['first_name']?></td> 
					<td><?php echo $row['last_name']?></td>
					<td><input type="checkbox" name="email<?php echo $row['user_id']?>" value="1" <?php if($row['email'] == 1) echo "checked"?>></td> 
					<td><input type="checkbox" name="voucher1500<?php echo $row['user_id']?>" value="1" <?php if($row['voucher1500'] == 1) echo "checked"?>></td>
				</tr>
				<?php
			}
			?>
		</table>
		
		<br>
		<button class="btn btn-primary">Änderungen speichern</button>
	</form>
	

	<script>
	$(document).ready(function(){

		$("#voucherForm2500").submit(function(e) {
			var form = $(this);
			var url = 'voucherForm2500_submit.php';

			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize(), // serializes the form's elements.
				success: function(data){
					alert(data);
				}
			});
			e.preventDefault(); // avoid to execute the actual submit of the form.
		});
		
		$("#voucherForm1500").submit(function(e) {
			var form = $(this);
			var url = 'voucherForm1500_submit.php';

			$.ajax({
				type: "POST",
				url: url,
				data: form.serialize(), // serializes the form's elements.
				success: function(data){
					alert(data);
				}
			});
			e.preventDefault(); // avoid to execute the actual submit of the form.
		});
		
	});	
	</script>

</div>

</body>