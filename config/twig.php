<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/../src/templates');
$twig = new Environment($loader);

return $twig;
