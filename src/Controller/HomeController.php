<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use App\Service\PaginationService;
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


    public function __construct(TrickRepository $repository, PaginationService $pagination)
    {
        $this->repository = $repository;
        $this->pagination = $pagination;
    }

    /**
     * @Route("/" , name="home")
     * @return Response
     */
    public function index(Request $request): Response
    {
        $limit = 12;
        $currentPage = (int)$request->get('page', 1);
        $total = $this->repository->getTotalTricks();

        $tricks = $this->repository->getPaginatedTricks($currentPage);
        $isLast = $this->repository->isLast($currentPage, ceil($total / $limit));



        return $this->render("home.html.twig", [
            "tricks" => $tricks,
            "currentPage" => $currentPage,
            "isLast" => $isLast
        ]);
    }
}
