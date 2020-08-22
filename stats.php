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
// Load data for register chart
$sql = "
	SELECT 
		 DATE_FORMAT(users.time_stamp_reg, '%u/%y') AS yearweek_formatted,
		 YEARWEEK(users.time_stamp_reg, 4) AS yearweek,
		 count(*) AS numEvents 
	FROM users
    WHERE users.time_stamp_reg != ''
	GROUP BY yearweek
    ORDER BY yearweek
";
$result = mysqli_query($con, $sql);
$data_reg = "";
while($row = mysqli_fetch_assoc($result)){
	$data_reg = $data_reg . "['".$row['yearweek_formatted']."', ".$row["numEvents"]."],";
}

// Load data for ratings chart
$sql = "
	SELECT 
		 DATE_FORMAT(ratings.time_stamp, '%u/%y') AS yearweek_formatted,
		 YEARWEEK(ratings.time_stamp, 4) AS yearweek,
		 count(*) AS numEvents 
	FROM ratings
	GROUP BY yearweek
    ORDER BY yearweek
";
$result = mysqli_query($con, $sql);
$data = "";
while($row = mysqli_fetch_assoc($result)){
	$data = $data . "['".$row["yearweek_formatted"]."', ".$row["numEvents"]."],";
}
?>

<script>
$( document ).ready(function() {

	$.ajax({
		type: "POST",
		url: "load_stats.php",
		success: function(data) {
			$("#main").html(data);
			
			//Load register chart
			google.charts.load('current', {'packages':['corechart']});
			google.charts.setOnLoadCallback(drawChart_reg);

			function drawChart_reg() {
				var data = google.visualization.arrayToDataTable([
					['Datum', 'Anzahl'],
					<?php echo $data_reg?>
				]);

				var options = {
					title: 'Anzahl Neuregistrierungen im Zeitverlauf (aggregiert auf Wochen)',
					hAxis: {title: '',  titleTextStyle: {color: '#333'}},
					vAxis: {minValue: 0},
					//chartArea:{left:10,top:20,width:"100%",height:"100%"}
					legend: {position: 'none'}
				};

				var chart = new google.visualization.AreaChart(document.getElementById('chart_reg_div'));
				chart.draw(data, options);
			}	
			
			//Load ratings chart
			google.charts.load('current', {'packages':['corechart']});
			google.charts.setOnLoadCallback(drawChart);

			function drawChart() {
				var data = google.visualization.arrayToDataTable([
					['Datum', 'Anzahl'],
					<?php echo $data?>
				]);

				var options = {
					title: 'Anzahl abgegebener Bewertungen im Zeitverlauf (aggregiert auf Wochen)',
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