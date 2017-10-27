<?php

include('connect.php');

$email = $_GET['email'];

$body = "Testbody";

EmailService::getService()->sendEmail($email, "Tester", "Testemail", $body);

?>