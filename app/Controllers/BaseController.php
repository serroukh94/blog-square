<?php declare(strict_types = 1);

namespace App\Controllers;   

use Faker\Factory;
use Ben\Foundation\AbstractController;
use Ben\Foundation\View;

class BaseController extends AbstractController  //  c'est une classe parent, donc tous les controlleurs devront heriter forcement de la classe parent 
{
    public function index(): void
    {
        $faker = Factory::create();
        View::render('index', [
            'city' => $faker->city,
        ]);
    }
}