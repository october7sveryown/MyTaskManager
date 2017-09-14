<?php

// $email and $message are the data that is being
// posted to this page from our html contact form


// When we unzipped PHPMailer, it unzipped to
// public_html/PHPMailer_5.2.0
    require("../libs/PHPMailer-master/PHPMailerAutoload.php");
/**
 * This example shows sending a message using a local sendmail binary.
 */
//Create a new PHPMailer instance
$mail = new PHPMailer;
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 2;
//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';
//Set the hostname of the mail server
$mail->Host = "smtp.gmail.com";
//Set the SMTP port number - likely to be 25, 465 or 587
$mail->Port = 465;
$mail->SMTPSecure='ssl';
//Whether to use SMTP authentication
$mail->SMTPAuth = true;
    $conn=mysql_connect("localhost","root","");
	if(!$conn)
	{
		die('Could not connect'.mysql_error());
	}
$six_digit_random_number = mt_rand(100000, 999999);
$pwd_hash = PwdHash::hash($six_digit_random_number);
$query="UPDATE `diotr`.`users` SET pwd_hash='$pwd_hash',password='$six_digit_random_number' WHERE email='$myemail' ;";
	mysql_query($query,$conn);	
$mail->Username = "teamresourcify@gmail.com";
//Password to use for SMTP authentication
$mail->Password = "cnpevtcfqurbhufz";
//Set who the message is to be sent from
$mail->setFrom('teamresourcify@gmail.com', 'Team Resourcify');
//Set an alternative reply-to address
$result='yashthakerlearns@gmail.com';
    $mail->addAddress($result);
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Temporary Password';
$mail->Body    = 'Hello,<br><b>Temporary password:'.$six_digit_random_number.'</b><br>Only use this password for once.Enjoy!<br><br>Regards,<br><b>Team Resourcify</b>';
    //Set the subject line
//$mail->Subject = 'Resourcify:Temporary Password';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->Body="Team Resourcify says Hello.Temporary password:$six_digit_random_number.Only use this password for once.Enjoy!!!";
//Replace the plain text body with one created manually
$mail->AltBody = 'This is a plain-text message body';

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}