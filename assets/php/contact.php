<?php
header('Content-type: application/json');

if($_POST)
{
    $to_email       = "rutger.klamer@gmail.com"; //Recipient email, Replace with own email here

    //check if its an ajax request, exit if not
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

        $output = json_encode(array( //create JSON data
            'type'=>'error',
            'text' => 'Sorry Request must be Ajax POST'
        ));
        die($output); //exit script outputting json data
    }

    //Sanitize input data using PHP filter_var().
    $user_name      = filter_var($_POST["name"],    FILTER_SANITIZE_STRING);
    $user_email     = filter_var($_POST["email"],   FILTER_SANITIZE_EMAIL);
    $phone_number   = filter_var($_POST["phone"],   FILTER_SANITIZE_NUMBER_INT);
    $message        = filter_var($_POST["message"], FILTER_SANITIZE_STRING);

    //additional php validation
    if(strlen($user_name)<4){ // If length is less than 4 it will output JSON error.
        $output = json_encode(array('type'=>'error', 'text' => 'Naam is te kort!'));
        die($output);
    }

    if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)){ //email validation
        $output = json_encode(array('type'=>'error', 'text' => 'Voer een echt e-mail adress in!'));
        die($output);
    }


    if(!filter_var($phone_number, FILTER_SANITIZE_NUMBER_FLOAT)){ //check for valid numbers in phone number field
        $output = json_encode(array('type'=>'error', 'text' => 'Alleen nummers bij je telefoonnummer!'));
        die($output);
    }

    if(strlen($message)<3){ //check emtpy message
        $output = json_encode(array('type'=>'error', 'text' => 'Bericht te kort!));
        die($output);
    }

    //email subject
    $subject ='New mail via contact form';

    //email body
    $message_body = $message."\r\n\r\n-".$user_name."\r\n\r\nEmail : ".$user_email."\r\nTelefoon nummer : ". $phone_number ;

    //proceed with PHP email.
    $headers = 'From: '.$user_name.'<'.$user_email.'>'."\r\n" .
    'Reply-To: '.$user_name.'<'.$user_email.'>' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

    $send_mail = mail($to_email, $subject, $message_body, $headers);

    if(!$send_mail)
    {
        //If mail couldn't be sent output error. Check your PHP email configuration (if it ever happens)
        $output = json_encode(array('type'=>'error', 'text' => 'Email niet verzonden'));
        die($output);
    }else{
        $output = json_encode(array('type'=>'success', 'text' => 'He '.$user_name .'! Bedankt voor je berichtje'));
        die($output);
    }
}


?>
