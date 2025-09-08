<?php

namespace App\Service;

Use Cocur\Slugify\Slugify;

class SlugifieService
{
    public function __construct(){}

    function slugify($string)
    {
        $slugify = new Slugify();
        return $slugify->slugify($string);
    }
}
