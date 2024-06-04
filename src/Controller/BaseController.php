<?php

namespace Root\P5\Controller;

use Twig\Environment;
use Root\P5\Classes\DatabaseConnect;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BaseController
{
    protected Environment $twig;
    protected DatabaseConnect $db;

    public function __construct(Environment $twig, DatabaseConnect $db)
    {
        $this->twig = $twig;
        $this->db = $db;
    }

    protected function render(string $template, array $context = []): void
    {
        try {
            echo $this->twig->render($template, $context);
        } catch (LoaderError | RuntimeError | SyntaxError $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}