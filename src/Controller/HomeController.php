<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController extends AbstractController
{

    /**
     * 
     * @return Response
     */
    public function index(): Response
    {
        return new Response($this->render("pages/home.html.twig"));
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
