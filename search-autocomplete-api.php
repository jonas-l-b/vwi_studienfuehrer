<?php

/*
*		Dieses Skript dient als API für das autocomplete-Feature unserer Suchfunktion. 
*
*
*
*/
header('Content-type: application/json');
include "connect.php";

if(isset($_GET['query'])){
  if($_GET['query'] == "a"){
	echo '{
    "query": "Unit",
    "suggestions": [
        { "value": "United Arab Emirates", "data": "AE" },
        { "value": "United Kingdom",       "data": "UK" },
        { "value": "United States",        "data": "US" }
    ]
	}';  
  }else{
	 echo '{
    "query": "Unit",
    "suggestions": [
        { "value": "United Arab Emirates", "data": "AE" }
    ]
	}';  
  }
  
}



?>
