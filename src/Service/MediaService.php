<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\Video;
use App\Entity\Picture;
use Symfony\Component\DependencyInjection\ContainerInterface;


class MediaService
{

    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addVideos($form, Trick $trick)
    {

        $videos = $form->get('videos')->getData();
        foreach ($videos as $video) {
            $trick->removeVideo($video);
            $newUrl = $this->encode($video->getUrl());
            $vid = new Video;
            $vid->setUrl($newUrl);
            $trick->addVideo($vid);
        }
    }

    public function addPictures($form, Trick $trick)
    {
        // on recupere les images 
        $pictures = $form->get('pictures')->getData();

        // on boucle sur les images 
        foreach ($pictures as $picture) {
            // on renomme les images 
            $file = md5(uniqid()) . "." . $picture->guessExtension();

            // on copie les images dans le dossier uploads
            $picture->move(
                $this->container->getParameter('pictures_directory'),
                $file
            );

            // on stock le fichier dans la bdd(le nom)
            $img = new Picture;
            $img->setName($file);
            $trick->addPicture($img);
        }
    }

    public function encode(string $url): string
    {
        return str_replace("watch?v=", "embed/", $url);
    }
}
