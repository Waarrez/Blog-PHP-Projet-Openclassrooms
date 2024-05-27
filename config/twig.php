<?php

require_once 'vendor/autoload.php';

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader(dirname(__DIR__).'/src/templates');

// Configuration de Twig
$twig = new Environment($loader, [
    'debug' => true,
    'auto_reload' => true,
    'charset' => 'utf-8',
]);

