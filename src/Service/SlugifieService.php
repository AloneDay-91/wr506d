<?php

namespace App\Service;

use Cocur\Slugify\Slugify;

class SlugifieService
{
    public function __construct()
    {
    }

    public function slugify(string $string): string
    {
        $slugify = new Slugify();
        return $slugify->slugify($string);
    }
}
