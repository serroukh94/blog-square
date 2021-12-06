<?php declare(strict_types = 1);

namespace app\Classes;

  use \Mailjet\Resources;

  class Mail{

    public function send()
    {
      $mj = new \Mailjet\Client('cdc60003c851daefb60f0824ec30f21a','4d5ad9a7ac5d83738a71511231c4c5b9',true,['version' => 'v3.1']);
   
  if(!empty($_POST['surname']) && !empty($_POST['firstname']) && !empty($_POST['email']) && !empty($_POST['message'])){
    $surname = htmlspecialchars($_POST['surname']);
    $firstname = htmlspecialchars($_POST['firstname']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    if(filter_var($email, FILTER_VALIDATE_EMAIL)){
    $body = [
        'Messages' => [
        [
            'From' => [
            'Email' => "serroukh94@gmail.com",
            'Name' => "Mohamed"
            ],
            'To' => [
            [
                'Email' => "Serroukh94@gmail.com",
                'Name' => "Mohamed"
            ]
            ],
            'Subject' => "Demande de renseignement",
            'TextPart' => '$email, $message', 
            'HTMLPart' => " $surname, $firstname, $email, $message",
            'CustomID' => "AppGettingStartedTest"
        ]
        ]
    ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
        echo "Email envoyé avec succès !";
    }
    else{
        echo "Email non valide";
    }

} else {
    header('Location: index.php');
    die();
}
    }
  }
