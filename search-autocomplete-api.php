<?php

/*
*		Dieses Skript dient als API für das autocomplete-Feature unserer Suchfunktion. 
*
*
*
*/
header('Content-type: application/json');
include "connect.php";

if (isset($_GET['query'])){
	$result = mysqli_query($con,"SELECT subject_name FROM subjects WHERE subject_name LIKE '%".$_GET['query']."%'");
	echo '{
    "query": "Unit",
    "suggestions": [';
	while($row = mysqli_fetch_assoc($result)){
		echo '{ "value" :"'.$row['subject_name'].'", "data": {"category" : "Veranstaltung"} },';
	}
	echo '{ "value": "Wouters", "data": {"category" : "Dozenten"}}]}'; 
}else{
	echo '{
    "query": "Unit",
    "suggestions": []
	}'; 
}
/*
if(isset($_GET['query'])){
  if($_GET['query'] == "a"){
	echo '{
    "query": "Unit",
    "suggestions": [
        { "value": "United Arab Emirates", "data": {"category" : "Veranstaltung"} },
        { "value": "Amazing Emiarte Airline", "data": {"category" : "Veranstaltung"} },
        { "value": "Fuzz Tube People", "data": {"category" : "Veranstaltung"} },
        { "value": "Etechnik", "data": {"category" : "Module"} },
        { "value": "A I F B.....", "data": {"category" : "Institute"} },
        { "value": "IPEK", "data": {"category" : "Institute"} },
        { "value": "KIT", "data": {"category" : "Institute"} },
        { "value": "Reiß", "data": {"category" : "Dozenten"} },
        { "value": "Wouters", "data": {"category" : "Dozenten"} }
    ]
	}';  
  }else{
	  
  }
  
}
*/


?>
