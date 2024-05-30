<?php

namespace Root\P5\Controller;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController {

    protected $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index() {
        echo $this->twig->render('home/home.twig');
    }
}