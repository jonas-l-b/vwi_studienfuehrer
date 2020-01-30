<?php
$myfile = fopen("uploads/test.txt", "r") or die("Unable to open file!");
echo fread($myfile,filesize("uploads/test.txt"));
fclose($myfile);
?>