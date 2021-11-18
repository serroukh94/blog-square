<?php declare(strict_types = 1);

namespace App\Controller;

use App\Classes\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Ben\Foundation\View;

class FormController extends AbstractController
{
    #[Route('/form', name: 'form')]
    public function index()
    {
        $mail = new Mail();
        $mail-> send("serroukh94@gmail.com", "mohamed", 'essai'
        , "essi");
        return $this->render('form/index.html.twig');
    }
}
