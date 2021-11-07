<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController
{

    private $twig;


    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * 
     * @return Response
     */
    public function index(): Response
    {
        return new Response($this->twig->render("pages/home"));
    }

    /**
     * @Route("/inde", name="inde")
     * @return Response
     */
    public function inde(): Response
    {
        return new Response('je suis la inde');
    }
}
