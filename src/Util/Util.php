<?php


namespace App\Util;


class Util
{
    public function getSlug(string $title):string
    {
        $title = $title;
        $title = preg_replace('#Ç#', 'C', $title);
        $title = preg_replace('#ç#', 'c', $title);
        $title = preg_replace('#è|é|ê|ë#', 'e', $title);
        $title = preg_replace('#È|É|Ê|Ë#', 'E', $title);
        $title = preg_replace('#à|á|â|ã|ä|å#', 'a', $title);
        $title = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $title);
        $title = preg_replace('#ì|í|î|ï#', 'i', $title);
        $title = preg_replace('#Ì|Í|Î|Ï#', 'I', $title);
        $title = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $title);
        $title = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $title);
        $title = preg_replace('#ù|ú|û|ü#', 'u', $title);
        $title = preg_replace('#Ù|Ú|Û|Ü#', 'U', $title);
        $title = preg_replace('#ý|ÿ#', 'y', $title);
        $title = preg_replace('#Ý#', 'Y', $title);
        $title = str_replace(' ','-',$title);
        $title = strtolower($title);

        return $title;


    }

}