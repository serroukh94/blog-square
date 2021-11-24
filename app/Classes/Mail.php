<?php

namespace app\Classes;

require 'vendor/autoload.php';
  use \Mailjet\Resources;

  $mj = new \Mailjet\Client('cdc60003c851daefb60f0824ec30f21a','4d5ad9a7ac5d83738a71511231c4c5b9',true,['version' => 'v3.1']);
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
        'Subject' => "Greetings from Mailjet.",
        'TextPart' => "My first Mailjet email",
        'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
        'CustomID' => "AppGettingStartedTest"
      ]
    ]
  ];
  $response = $mj->post(Resources::$Email, ['body' => $body]);
  $response->success() && var_dump($response->getData());
?>