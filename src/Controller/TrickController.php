<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
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
    public function show($category_slug, $slug, TrickRepository $trickRepository, Request $request, EntityManagerInterface $em)
    {
        $trick = $trickRepository->findOneBy(["slug" => $slug]);

        if (!$trick) {
            throw $this->createNotFoundException("Cette figure n'existe pas");
        }
        if ($category_slug !== $trick->getCategory()->getSlug()) {
            throw $this->createNotFoundException("Cette catégorie n'existe pas");
        }

        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            $em->persist($comment);
            $em->flush();
            return $this->redirectToRoute('trick_show', [
                'category_slug' => $trick->getCategory()->getSlug(),
                'slug'          => $trick->getSlug()
            ]);
        }

        return $this->render("trick/show.html.twig", ['trick' => $trick, "formView" => $form->createView()]);
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
