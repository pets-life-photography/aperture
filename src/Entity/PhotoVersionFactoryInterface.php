<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Entity;

interface PhotoVersionFactoryInterface
{
    /**
     * Create a version for the given photo, using the given type.
     *
     * @param Photo     $photo
     * @param PhotoType $type
     *
     * @return PhotoVersion
     */
    public function createVersion(Photo $photo, PhotoType $type): PhotoVersion;
}
