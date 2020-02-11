<?php

$check = array();

$check[0] = TRUE;
$check[1] = TRUE;
$check[2] = FALSE;

print_r($check);

if(in_array(false, $check, true) === false){
	echo "All true";
}else{
	echo "At least one false";
}


?>