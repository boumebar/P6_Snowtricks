<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\Video;


class VideoService
{


    public function addVideo($form, Trick $trick)
    {
        // onrecupere les videos
        $videos = $form->get('videos')->getData();
        foreach ($videos as $video) {
            // $encodeUrl = $this->encode($video->getUrl());
            $url = $video->getUrl();
            $file = new Video;
            $file->setUrl($url);
            $trick->addVideo($file);
        }
    }

    public function encode(string $url): string
    {
        return str_replace("watch?v=", "embed/", $url);
    }
}
