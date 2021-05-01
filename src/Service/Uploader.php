<?php


namespace App\Service;


use App\Entity\Image;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

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

    /**
     * Save new media
     *
     * @param Image $image
     * @return bool $image
     */
    public function deleteImage(Image $image): bool
    {
        $fs = new Filesystem();
        try {
            $fs->remove($image->getPath());
            return true;
        } catch (IOException $e) {
            return false;

        }
    }
}