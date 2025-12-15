<?php

namespace App\Service;

use App\Entity\MediaObject;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

/**
 * Directory namer that organizes uploaded files by their type.
 */
class MediaTypeDirectoryNamer implements DirectoryNamerInterface
{
    public function directoryName(object|array $object, PropertyMapping $mapping): string
    {
        if (!$object instanceof MediaObject) {
            return 'other';
        }

        return match ($object->getType()) {
            MediaObject::TYPE_PROFILE => 'profiles',
            MediaObject::TYPE_MOVIE_COVER => 'movies',
            default => 'other',
        };
    }
}
