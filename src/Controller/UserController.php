<?php

namespace App\Controller;

use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{id<\d+>}", name="user_index")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException("
            You must be logged in ");
        }
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/user/{id<\d+>}/edit", name="user_edit")
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException("
            You must be logged in ");
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        // on recupere l'image
        $picture = $form->get('picture')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            // on renomme l'image
            $file = md5(uniqid()) . "." . $picture->guessExtension();

            // on copie l'image dans le dossier uploads
            $picture->move(
                $this->getParameter('pictures_directory') . '/persons/',
                $file
            );

            $user->setPicture($file);
            $em->flush();
            $this->addFlash("success", "
            your profile has been modified");
            return $this->redirectToRoute('user_index', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form'  => $form->createView()
        ]);
    }
}
