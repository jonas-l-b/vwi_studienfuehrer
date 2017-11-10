<?php

include('connect.php');

$email = $_GET['email'];

$body = "Testbody Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. Lorem Ipsum Dolor Sit Amet. ";

if(EmailService::getService()->sendEmail($email, "Tester", "Testemail", $body)){
  echo "success send to " . $email;
}

?>
