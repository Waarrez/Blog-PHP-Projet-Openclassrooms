<?php

namespace Root\P5\Controller;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Controller for handling home-related actions.
 */
class HomeController
{
    protected Environment $twig;

    /**
     * Constructor.
     *
     * @param Environment $twig The Twig environment.
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     *
     *
     * @return string
     *
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(): string
    {
        return $this->twig->render('home/home.twig');
    }
}
