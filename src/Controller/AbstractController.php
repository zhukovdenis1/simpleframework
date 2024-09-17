<?php

namespace App\Controller;
use Doctrine\DBAL\Exception\ServerException;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class AbstractController
{
    protected Environment $twig;

    public function render(string $name, array $context): Response
    {
        return new Response($this->twig->render($name.'.html.twig', $context));
    }
}