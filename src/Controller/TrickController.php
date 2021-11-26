<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickController extends AbstractController
{


    /**
     * @Route("/{category_slug}/{slug}", name="trick_show")
     */
    public function show($category_slug, $slug, TrickRepository $trickRepository, CommentRepository $commentRepository)
    {
        $trick = $trickRepository->findOneBy(["slug" => $slug]);
        $comments = $commentRepository->findBy(["trick" => $trick->getId()]);

        if (!$trick) {
            throw $this->createNotFoundException("Cette figure n'existe pas");
        }
        if ($category_slug !== $trick->getCategory()->getSlug()) {
            throw $this->createNotFoundException("Cette catégorie n'existe pas");
        }


        return $this->render("trick/show.html.twig", ['trick' => $trick, "comments" => $comments]);
    }


    /**
     * @Route("/admin/trick/create" , name="trick_create")
     */
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $trick = new Trick;

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setSlug(strtolower($slugger->slug($trick->getName())));
            $em->persist($trick);
            $em->flush();

            return $this->redirectToRoute('trick_show', [
                'category_slug' => $trick->getCategory()->getSlug(),
                'slug'          => $trick->getSlug()
            ]);
        }


        $formView = $form->createView();

        return $this->render("trick/create.html.twig", ['formView' => $formView]);
    }


    /**
     * @param [int] $id
     * @route("/admin/trick/{id}/edit" , name="trick_edit")
     */
    public function edit(int $id, Request $request, TrickRepository $trickRepository, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $trick = $trickRepository->find($id);
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if (!$trick) {
            throw $this->createNotFoundException("Cette figure n'existe pas !!");
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setSlug(strtolower($slugger->slug($trick->getName())));
            $em->flush();
            return $this->redirectToRoute('trick_show', [
                'category_slug' => $trick->getCategory()->getSlug(),
                'slug'          => $trick->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render("trick/edit.html.twig", ['formView' => $formView]);
    }
}
