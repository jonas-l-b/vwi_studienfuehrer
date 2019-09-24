<?php

include "sessionsStart.php";

include "header.php";

include "connect.php";

?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<body>

<?php include "inc/nav.php" ?>

<div id="load">
	<br><br><div class="loader"><div></div></div><br><br>
	<p style="text-align:center">Die Bewertungsstatistiken werden geladen</p>
</div>

<div id="main" class="container">
</div>

<?php
// Load data for chart
$sql = "
	SELECT 
		 DATE_FORMAT(ratings.time_stamp, '%d.%m.%y') as day, 
		 count(*) AS numEvents 
	FROM ratings
	GROUP BY YEAR(ratings.time_stamp), MONTH(ratings.time_stamp), DAYOFMONTH(ratings.time_stamp)
";
$result = mysqli_query($con, $sql);
$data = "";
while($row = mysqli_fetch_assoc($result)){
	$data = $data . "['".$row["day"]."', ".$row["numEvents"]."],";
}
?>

<script>
$( document ).ready(function() {

	$.ajax({
		type: "POST",
		url: "load_stats.php",
		success: function(data) {
			$("#main").html(data);
			
			//Load chart
			google.charts.load('current', {'packages':['corechart']});
			google.charts.setOnLoadCallback(drawChart);

			function drawChart() {
				var data = google.visualization.arrayToDataTable([
					['Datum', 'Anzahl'],
					<?php echo $data?>
				]);

				var options = {
					title: 'Anzahl abgegebener Bewertungen im Zeitverlauf',
					hAxis: {title: '',  titleTextStyle: {color: '#333'}},
					vAxis: {minValue: 0},
					//chartArea:{left:10,top:20,width:"100%",height:"100%"}
					legend: {position: 'none'}
				};

				var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
				chart.draw(data, options);
			}	
		}
	});
	
	$(document).ajaxStop(function () {
		$("#load").hide();
	});
	
});
</script>

</body>