<?php

include "sessionsStart.php";
include "connect.php";

if(isset($_GET["code"])){
	if($userRow['super_admin']==0){
		echo('Access denied.');
		exit();
	}
	$neuercode = substr(md5(rand()),0,7);
	$con->query("INSERT INTO codes (code, used) VALUES ('".$neuercode."', 0)");
	echo $neuercode;
	exit();
}

include "header.php";


?>
<body>

<?php include "inc/nav.php" ?>


<?php
	if($userRow['super_admin']==0):
 ?>
<div class="container">
	<p>Nur Super-Admins haben Zugang zu dieser Seite.</p>
</div>
<?php
	exit();
	endif;
 ?>
<div class="container">
	<p>
		Hier kannst du neue Einladungscodes generieren (Refresh, damit sie auch in der Tabelle angezeigt werden).
	</p>
	<br >
	<button type="button" id="generierButton" class="btn btn-primary">Code generieren.</button>
	<br />
	<br />
	<div class="result">

	</div>
	<script>
		$('#generierButton').click(function(){
			$.get( "codes.php?code=true", function( data ) {
			  $( ".result" ).html( "Neuer Code: "+data );
			});
		});
	</script>
	<br >
	<br>

	<table class="table table-striped">
		<thead>
			<tr>
				<th>Code</th>
				<th>Bereits verbraucht? (1=verbraucht, 0=unverbraucht)</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$result = mysqli_query($con,"SELECT * FROM codes");

			while($row = mysqli_fetch_assoc($result)){
				echo "<tr>";
				echo "<td>";
				echo $row['code'];
				echo "</td>";
				echo "<td>";
				echo $row['used'];
				echo "</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
</div>

<?php
//Mehrere Codes generieren
/*
for ($i = 1; $i <= 39; $i++) {
    $neuercode = substr(md5(rand()),0,7);
	$con->query("INSERT INTO codes (code, used) VALUES ('".$neuercode."', 0)");
}
*/
?>

</body>
