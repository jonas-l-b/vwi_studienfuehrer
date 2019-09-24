<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div id="chart_div" style="width: 100%; height: 500px;"></div>
	

<?php
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

echo $data;
?>

<script>
google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
var data = google.visualization.arrayToDataTable([
	['Day', 'NumEvents'],
	<?php echo $data?>
]);

var options = {
  title: 'Company Performance',
  hAxis: {title: 'Year',  titleTextStyle: {color: '#333'}},
  vAxis: {minValue: 0}
};

var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
chart.draw(data, options);
}
</script>