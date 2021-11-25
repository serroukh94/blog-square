<?php

namespace app\Classes;

  
  use \Mailjet\Resources;

  

  $mj = new \Mailjet\Client('cdc60003c851daefb60f0824ec30f21a','4d5ad9a7ac5d83738a71511231c4c5b9',true,['version' => 'v3.1']);
  $email = htmlspecialchars($_POST['email']);
  if(filter_var($email, FILTER_VALIDATE_EMAIL)){
  $body = [
    'Messages' => [
      [
        'From' => [
          'Email' => "no-reply@square-blog.test",
          'Name' => "Mohamed"
        ],
        'To' => [
          [
            'Email' => "serroukh94@gmail.com",
            'Name' => "Mohamed"
          ]
        ],
        'Subject' => "Demande de renseignement.",
        'TextPart' => "My first Mailjet email",
        'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
        'CustomID' => "AppGettingStartedTest"
      ]
    ]
  ];
  $response = $mj->post(Resources::$Email, ['body' => $body]);
  $response->success() ;
  echo "Email envoyé avec succès !";
        }
        else{
            echo "Email non valide";
        }

    
?>