<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Entity;

use App\Repository\WatermarkRepository;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class PhotoFactory implements PhotoFactoryInterface
{
    /** @var WatermarkRepository */
    private $watermarks;

    /**
     * Constructor.
     *
     * @param WatermarkRepository $watermarks
     */
    public function __construct(WatermarkRepository $watermarks)
    {
        $this->watermarks = $watermarks;
    }

    /**
     * Create a photo for the given file.
     *
     * @param string $file
     *
     * @return Photo
     */
    public function create(string $file): Photo
    {
        $image = new Image($file);
        $photo = new Photo();

        $photo->setHeight($image->getHeight());
        $photo->setWidth($image->getWidth());
        $photo->setContentType(mime_content_type($file));
        $photo->setContent(file_get_contents($file));
        $photo->setMetaData([]);

        $configuration = new WatermarkConfiguration();
        $configuration->setCrop(false);
        $configuration->setOpacity(100);
        $configuration->setPaddingUnit(Manipulations::UNIT_PIXELS);
        $configuration->setPaddingX(0);
        $configuration->setPaddingY(0);
        $configuration->setPosition(Manipulations::POSITION_CENTER);
        $configuration->setWatermark(
            $this->watermarks->findOneBy([])
        );

        $photo->setWatermarkConfiguration($configuration);

        return $photo;
    }
}
