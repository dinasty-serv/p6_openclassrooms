<?php


namespace App\Service;


use App\Entity\Image;

class Uploader
{
    /**
     * Save new media
     *
     * @param Image $image
     * @return Image $image
     */
    public function saveImage(Image $image): Image
    {
        $file = $image->getFile();
        $name = md5(uniqid()) . '.' . $file->guessExtension();
        $entry = 'upload/tricks';
        $file->move($entry, $name);
        $path = $entry.'/'.$name;
        $image->setPath($path);

        return $image;
    }
}