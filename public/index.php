<?php declare(strict_types = 1);

define('ROOT', str_replace('/public', '', __DIR__));  // on a defini cette constante qui va nous permettre d'avoir de disponible partout dans notre projet une constante qui s'apelle route et qui va du coup nous permettre d'avoir le repertoire de base dans lequel est notre projet (dossier mvc)

require_once ROOT.'/vendor/autoload.php'; // toute nos entites et nos dependance seront disponible partout dans notre projet grace a require

$app = new Ben\Foundation\App(); // on a creer la variable app  qui va etre une instance de app
$app->render(); 