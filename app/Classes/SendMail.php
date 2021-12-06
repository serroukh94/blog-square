<?php declare(strict_types = 1);

use PHPMailer\PHPMailer\PHPMailer;

if (isset($_POST['surname']) && isset($_POST['email'])){

    $surname = $_POST['surname'];
    $firstname = $_POST['firstname'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    require_once "PHPMailer/PHPMailer.php";
    require_once "PHPMailer/SMTP.php";
    require_once "PHPMailer/Exception.php";

    $mail = new PHPMailer();

    //smtp settings
    $mail -> isSMTP();
    $mail ->host="smtp.gmail.com";
    $mail ->SMTPAuth = true;
    $mail ->Username ="serroukh94@gmail.com";
    $mail ->password ='';
    $mail ->Port = 465;
    $mail ->SMTPSecure = "ssl";


    //email settings
    $mail->isHTML(true);
    $mail->setFrom($email, $surname);
    $mail->addAddress("serroukh94@gmail.com");
    $mail->Subject = ("$email ($subject");
    $mail->message = $message;
    
    if($mail->send()){
        $status = "success";
        $response = "Email is sent!";
    }
    else
    {
        $status= "failed";
        $response="Something is wrong: <br>" .$mail->ErrorInfo;

    }

    exit(json_encode(array("status"=> $status, "response" => $response))); 



};


