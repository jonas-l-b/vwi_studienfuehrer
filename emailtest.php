<?php

include('connect.php');

$email = $_GET['email'];

$body = "Testbody";

if(EmailService::getService()->sendEmail($email, "Tester", "Testemail", $body)){
  echo "success send to " . $email;
}

?>
