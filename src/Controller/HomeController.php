<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    /**
     * @var TrickRepository
     */
    private $repository;


    public function __construct(TrickRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/" , name="home")
     * @return Response
     */
    public function index(Request $request): Response
    {
        $limit = 12;
        $currentPage = (int)$request->get('page', 1);

        $tricks = $this->repository->getPaginatedTricks($currentPage, $limit);

        $total = $this->repository->getTotalTricks();


        return $this->render("home.html.twig", [
            "tricks" => $tricks,
            "total" => $total,
            "currentPage" => $currentPage,
            "limit" => $limit
        ]);
    }
}
