<?php

namespace Root\P5\Controller;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PostsController
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
     * Render the home page.
     *
     * @return void
     *
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(): void
    {
        try {
            echo $this->twig->render('posts/index.twig');
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
