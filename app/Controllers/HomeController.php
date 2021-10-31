<?php declare(strict_types = 1);

namespace App\Controllers;   


use Ben\Foundation\AbstractController;
use Ben\Foundation\Authentication as Auth;
use Ben\Foundation\View;


class HomeController extends AbstractController   
{
    public function index(): void
    {
        if (!Auth::check()) {
            $this->redirect('login.form');
        }

        $user = Auth::get();

        View::render('home', [
            'user' => $user,
        ]);
    }
}