<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Entity;

interface PhotoFactoryInterface
{
    /**
     * Create a photo for the given file.
     *
     * @param string $file
     *
     * @return Photo
     */
    public function create(string $file): Photo;
}
