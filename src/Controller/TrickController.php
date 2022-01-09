<?php

namespace App\Controller;

use DateTime;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Service\MediaService;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
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
     * @Route("/{category_slug<^[a-zA-Z][a-z_A-Z-]+$>}/{slug}", name="trick_show")
     */
    public function show($category_slug, $slug, Request $request, EntityManagerInterface $em)
    {
        $trick = $this->repository->findOneBy(["slug" => $slug]);

        if (!$trick) {
            throw $this->createNotFoundException("
            This trick does not exist");
        }
        if ($category_slug !== $trick->getCategory()->getSlug()) {
            throw $this->createNotFoundException("
            This category does not exist");
        }

        $comment = new Comment;
        $currentPage = 1;
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            $comment->setAuthor($this->getUser());
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('trick_show', [
                'category_slug' => $trick->getCategory()->getSlug(),
                'slug'          => $trick->getSlug()
            ]);
        }

        return $this->render("trick/show.html.twig", ['trick' => $trick, "formView" => $form->createView(), 'currentPage' => $currentPage]);
    }


    /**
     * @Route("/admin/trick/create" , name="trick_create", methods={"GET","POST"})
     */
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, MediaService $mediaService)
    {
        $trick = new Trick;

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Ajoute la video au bon format
            $mediaService->addVideos($form, $trick);

            // Ajoute les photos dans la bdd et le dossier local
            $mediaService->addPictures($form, $trick);

            $trick->setUpdatedAt(new DateTime());
            $trick->setSlug(strtolower($slugger->slug($trick->getName())));
            $em->persist($trick);
            $em->flush();
            $this->addFlash("success", "Trick successfully created!");
            return $this->redirectToRoute('home');
        }


        $formView = $form->createView();

        return $this->render("trick/create.html.twig", ['formView' => $formView]);
    }


    /**
     * @Route("/admin/trick/{id<\d+>}/edit" , name="trick_edit", methods={"GET", "PUT"})
     */
    public function edit(Request $request, Trick $trick, EntityManagerInterface $em, SluggerInterface $slugger, MediaService $mediaService)
    {
        $form = $this->createForm(TrickType::class, $trick, [
            'method' => 'PUT'
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // Ajoute les videos au bon format
            $mediaService->addVideos($form, $trick);

            // Ajoute les photos dans la bdd et le dossier local
            $mediaService->addPictures($form, $trick);


            $trick->setUpdatedAt(new DateTime());
            $trick->setSlug(strtolower($slugger->slug($trick->getName())));

            $em->flush();
            $this->addFlash("success", "Trick successfully updated !");
            return $this->redirectToRoute('trick_show', [
                'category_slug' => $trick->getCategory()->getSlug(),
                'slug'          => $trick->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render("trick/edit.html.twig", ['formView' => $formView, 'trick' => $trick]);
    }

    /**
     * @Route("/page/trick/{page<\d+>}" , name="trick_paginated")
     * @return Response
     */
    public function tricksPaginated($page): Response
    {
        $limit = 12;
        $currentPage = $page;
        $total = $this->repository->getTotalTricks();

        $tricks = $this->repository->getPaginatedTricks($currentPage, $limit);
        $isLast = $this->repository->isLast($currentPage, ceil($total / $limit));

        return $this->render("trick/cardList.html.twig", [
            "tricks" => $tricks,
            "total" => $total,
            "currentPage" => $currentPage,
            "limit" => $limit,
            "isLast" => $isLast
        ]);
    }

    /**
     * 
     * @Route("/admin/trick/{id<\d+>}/delete" , name="trick_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Trick $trick, EntityManagerInterface $em)
    {
        if ($this->isCsrfTokenValid("delete" . $trick->getId(), $request->request->get('_token'))) {

            // supprime les photos ddu dossier local
            foreach ($trick->getPictures() as $picture) {
                $this->deletePictures($picture);
            }

            $em->remove($trick);
            $em->flush();
            $this->addFlash("success", "Trick successfully deleted !");
        }

        return $this->redirectToRoute("home");
    }


    /**
     * 
     * @Route("/admin/image/{id<\d+>}/delete" , name="trick_delete_picture", methods="DELETE")
     */
    public function deletePicture(Picture $picture, Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);

        // on verifie Token
        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $data['_token'])) {

            // on supprime du dossier local
            $this->deletePictures($picture);

            // on supprime de la BDD
            $em->remove($picture);
            $em->flush();

            // on répond en Json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    private function deletePictures(Picture $picture)
    {
        // on supprime du dossier local
        unlink($this->getParameter('pictures_directory') . '/' . $picture->getName());
    }
}
