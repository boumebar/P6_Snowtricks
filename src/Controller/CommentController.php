<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CommentController extends AbstractController
{

    /**
     * @var CommentRepository
     */
    private $repository;


    public function __construct(CommentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/trick/{trickId<\d+>}/comment/{page<\d+>}" , name="comment_paginated")
     * @return Response
     */
    public function commentsPaginated(int $trickId, int $page): Response
    {
        $limit = 10;
        $currentPage = $page;
        $total = $this->repository->getTotalComments($trickId);

        $comments = $this->repository->getPaginatedComments($trickId, $currentPage, $limit);
        $isLast = $this->repository->isLast($currentPage, ceil($total / $limit));

        return $this->render("comment/list.html.twig", [
            "comments" => $comments,
            "total" => $total,
            "currentPage" => $currentPage,
            "limit" => $limit,
            "isLast" => $isLast
        ]);
    }
}
