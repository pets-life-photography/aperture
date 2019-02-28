<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Entity;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class PhotoVersionFactory implements PhotoVersionFactoryInterface
{
    /**
     * Create a version for the given photo, using the given type.
     *
     * @param Photo     $photo
     * @param PhotoType $type
     *
     * @return PhotoVersion
     */
    public function createVersion(Photo $photo, PhotoType $type): PhotoVersion
    {
        $buffer = tempnam(sys_get_temp_dir(), sha1(__METHOD__));
        file_put_contents($buffer, $photo->getContent());

        $image = new Image($buffer);
        $this->resizeImage($image, $type);
        $this->applyWatermark($image, $photo, $type);
        $image->optimize();
        $image->save();

        $content = file_get_contents($buffer);

        $version = new PhotoVersion();
        $version->setType($type);
        $version->setPhoto($photo);
        $version->setContentType($photo->getContentType());
        $version->setWidth($image->getWidth());
        $version->setHeight($image->getHeight());
        $version->setAvailable($type->getAvailable());
        $version->setContent($content);
        $version->setHash(sha1($content));

        return $version;
    }

    /**
     * Resize the given image using the given type.
     *
     * @param Image     $image
     * @param PhotoType $type
     *
     * @return void
     */
    private function resizeImage(Image $image, PhotoType $type): void
    {
        if ($type->getWidth() !== null || $type->getHeight() !== null) {
            $width  = $type->getWidth() ?? $image->getWidth();
            $height = $type->getHeight() ?? $image->getHeight();

            $image->fit(
                $type->getCrop()
                    ? Manipulations::FIT_CROP
                    : Manipulations::FIT_MAX,
                $width,
                $height
            );
        }
    }

    /**
     * Apply the watermark of the given photo, to the given image, depending on
     * the given type.
     *
     * @param Image     $image
     * @param Photo     $photo
     * @param PhotoType $type
     *
     * @return void
     */
    private function applyWatermark(
        Image $image,
        Photo $photo,
        PhotoType $type
    ): void {
        if ($type->getWatermark()) {
            $configuration   = $photo->getWatermarkConfiguration();
            $watermark       = $configuration->getWatermark();
            $watermarkBuffer = tempnam(
                sys_get_temp_dir(),
                sha1(__METHOD__ . ':watermark')
            );

            file_put_contents(
                $watermarkBuffer,
                $configuration->getWatermark()->getContent()
            );

            $image->watermark($watermarkBuffer);
            $image->watermarkPosition($configuration->getPosition());
            $image->watermarkOpacity($configuration->getOpacity());
            $image->watermarkWidth($watermark->getWidth());
            $image->watermarkHeight($watermark->getHeight());
            $image->watermarkPadding(
                $configuration->getPaddingX(),
                $configuration->getPaddingY(),
                $configuration->getPaddingUnit()
            );
            $image->watermarkFit(
                $configuration->getCrop()
                    ? Manipulations::FIT_CROP
                    : Manipulations::FIT_MAX
            );
        }
    }
}
