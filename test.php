<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>
<body>

<?php include "inc/nav.php" ?>

<div class="container">

<!--	
	<table style="width:100%; table-layout: fixed;">
		<?php
		echo "<tr>";
		for ($i = 1; $i <= 61; $i++) {
			if($i==1 || $i==11 || $i==21 || $i==31 || $i==41 || $i==51){
				echo "<td style=\"border-left:solid black 1px; border-bottom:solid black 1px;\">&nbsp;</td>";
			}elseif($i==61){
				echo "<td style=\"border-left:solid black 1px;\">&nbsp;</td>";
			}else{
				echo "<td style=\"border-bottom:solid black 1px;\">&nbsp;</td>";
			}
		}
		echo "</tr>";
		
		echo "<tr>";
		for ($i = 1; $i <= 61; $i++) {
			echo "<td>";
			if($i==1) echo "-3";
			if($i==11) echo "-2";
			if($i==21) echo "-1";
			if($i==31) echo "0";
			if($i==41) echo "1";
			if($i==51) echo "2";
			if($i==61) echo "3";
			echo "</td>";
		}
		echo "</tr>";
		?>
	</table>
	
	<table style="width:100%; table-layout: fixed;">
		<tr>
		<td>1</td>
		<td>1</td>
		<td>1</td>
		<td>1</td>
		<td>1</td>
		<td>1</td>
		<td>1</td>
		</tr>
	</table>
-->
	<div style="width:5%">
		<div style="width:40%; text-align:right"><span class="glyphicon glyphicon-arrow-down" style="color:orange"></span></div>
		<div style="border-right:solid 2px; border-left:solid 2px; height:10px; width:100%"></div>
		<div style="border-top:solid 2px;"></div>
		<div class="contenedor">
			<div class="contenido">3</div>
			<div class="contenido">0</div>
			<div class="contenido">-3</div>
		</div>
	</div>

</div>


</body>