<?php

namespace App\Service;

use App\Entity\Trick;
use App\Service\MediaService;


class TrickService
{

    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
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
}
