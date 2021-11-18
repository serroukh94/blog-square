<?php

namespace App\Classes;

use Mailjet\Client;
use Mailjet\Resources;



class Mail {

    private $api_key = 'cdc60003c851daefb60f0824ec30f21a';
    private $api_key_secret='4d5ad9a7ac5d83738a71511231c4c5b9';

    public function send($to_email, $to_name , $subject, $content) {
       
        $mj = new Client( $this->api_key, $this->api_key_secret,true,['version' => 'v3.1.5']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => '$no-reply@square-blog.test',
                        'Name' => "le renard malin"
                    ],
                    'To' => [
                        [
                            'Email' => '$serroukh94@gmail.com',
                            'Name' => "Mohamed"
                        ]
                    ],
                    'TemplateID' => 3351844,
                    'TemplateLanguage' => true,
                    'Subject' => 'Nouveau message',
                    'Variables' => [
                        'content' => $content,
                        
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && dd($response->getData());
    }
}