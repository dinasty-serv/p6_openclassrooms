<?php


namespace App\Service;


use App\Entity\Video;
use Symfony\Component\HttpFoundation\Request;

class VideoService
{
    /**
     * Return embded url youtube
     *
     * @param Video $video
     * @return Video $image
     */
    public function getUrl(Video $video): Video
    {
        $urlBrut = $video->getUrl();
        $request = Request::create($urlBrut, 'GET');
        $urlYoutube = "https://www.youtube.com/embed/".$request->get('v');
        $video->setUrl($urlYoutube);
        return $video;
    }
}