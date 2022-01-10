<?php

namespace App\Service;

use DateTime;
use App\Entity\Trick;
use App\Service\MediaService;
use Symfony\Component\String\Slugger\SluggerInterface;


class TrickService
{
    private $slugger;
    private $mediaService;

    public function __construct(MediaService $mediaService, SluggerInterface $slugger)
    {
        $this->mediaService = $mediaService;
        $this->slugger = $slugger;
    }

    public function addMedias($form, Trick $trick)
    {
        // Ajoute l image principale
        $this->mediaService->addMainPicture($form, $trick);

        // Ajoute la video au bon format
        $this->mediaService->addVideos($form, $trick);

        // Ajoute les photos dans la bdd et le dossier local
        $this->mediaService->addPictures($form, $trick);
    }

    public function valide($form, $trick)
    {
        $this->addMedias($form, $trick);

        $trick->setUpdatedAt(new DateTime());
        $trick->setSlug(strtolower($this->slugger->slug($trick->getName())));
    }
}
