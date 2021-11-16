<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TrickController extends AbstractController
{


    /**
     * @Route("/{category_slug}/{slug}", name="trick_show")
     */
    public function show($category_slug, $slug, TrickRepository $trickRepository)
    {
        $trick = $trickRepository->findOneBy(["slug" => $slug]);

        if (!$trick) {
            throw $this->createNotFoundException("Cette figure n'existe pas");
        }
        if ($category_slug !== $trick->getCategory()->getSlug()) {
            throw $this->createNotFoundException("Cette catégorie n'existe pas");
        }


        return $this->render("trick/show.html.twig", ['trick' => $trick]);
    }


    /**
     * @Route("/admin/trick/create" , name="trick_create")
     */
    public function create(Request $request, EntityManagerInterface $em)
    {
        $trick = new Trick;

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($trick);
            $em->flush();

            return $this->redirectToRoute("home");
        }


        $formView = $form->createView();

        return $this->render("trick/create.html.twig", ['formView' => $formView]);
    }


    /**
     * @param [int] $id
     * @route("/admin/trick/{id}/edit" , name="trick_edit")
     */
    public function edit(int $id, TrickRepository $trickRepository)
    {


        $trick = $trickRepository->find($id);
        return $this->render("trick/edit.html.twig", ['trick' => $trick]);
    }
}
