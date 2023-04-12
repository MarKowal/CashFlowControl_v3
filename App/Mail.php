<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use \App\Config;

require '../vendor/autoload.php';

class Mail {

    public static function send($to, $subject, $text, $html){

        try {

            $mail = new PHPMailer(true);

            //Server settings
            $mail->isSMTP();  
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->SMTPDebug = 0;                      // !!! wartość 0 nie pokazuje bebechów CLIENT->SERVER
            $mail->Host = "smtp.gmail.com";                                           //Send using SMTP
            //$mail->Host = gethostbyname('smtp.gmail.com');
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = Config::GMAIL_USERNAME;                     //SMTP username
            $mail->Password   = Config::GMAIL_PASSWORD;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            
            $mail->CharSet = 'UTF-8'; // !!! POLSKIE OGONKI

            //Recipients
            $mail->setFrom('marcin.kowalski.programista@gmail.com','Marcin');
            $mail->addAddress($to);     //Add a recipient
            //$mail->addAddress('ellen@example.com');               //Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $text;;

            $mail->send();
            //echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    }

}


?>