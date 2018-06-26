<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<?php
if($userRow['super_admin']==0){
	$display = "";
	$displaySuper = "style=\"display:none\"";
}else{
	$display = "style=\"display:none\"";
	$displaySuper = "";
}

?>

<div <?php echo $display?> class="container">	
	<p>Nur Super-Admins haben Zugang zu dieser Seite.</p>
</div>

<div <?php echo $displaySuper?> class="container">	
	<p>Hier können Einladungscodes eingesehen werden.</p>
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

</body>