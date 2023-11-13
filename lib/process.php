<?php

//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'mail/Exception.php';
require 'mail/PHPMailer.php';
require 'mail/SMTP.php';

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

$mailbody = file_get_contents('php://input');

//Create a new PHPMailer instance
$mail = new PHPMailer();
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
//SMTP::DEBUG_OFF = off (for production use)
//SMTP::DEBUG_CLIENT = client messages
//SMTP::DEBUG_SERVER = client and server messages
$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->CharSet = 'UTF-8';
//Set the hostname of the mail server
$mail->Host = 'smtp.yandex.ru';
//Set the SMTP port number - likely to be 25, 465 or 587
$mail->Port = 25;
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
//Username to use for SMTP authentication
$mail->Username = 'venderu@yandex.ru';
//Password to use for SMTP authentication
$mail->Password = 'JXD65284hc7';
//Set who the message is to be sent from
$mail->setFrom('venderu@yandex.ru', 'Заказ с сайта');
//Set an alternative reply-to address
$mail->addReplyTo('venderu@yandex.ru', 'Виталий');
//Set who the message is to be sent to
// $mail->addAddress('personafurs@yandex.ru', 'Persona Furs');
$mail->addAddress('venderu@gmail.com', 'Persona Furs');
//Set the subject line
$mail->Subject = 'Заказ с сайта';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
$mail->msgHTML($mailbody, __DIR__);
//Replace the plain text body with one created manually
// $mail->AltBody = 'This is a plain-text message body';
//Attach an image file
// $mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message sent!';
}