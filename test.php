<?php
$a = array('a', 'b', 'c');
$b = array('c', 'x');

if (in_array($b, $a)) {
    echo "ja";
}else{
	echo "Nein";
}
?>