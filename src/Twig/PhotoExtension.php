<?php

namespace App\Twig;

use App\Controller\PhotoController;
use App\Entity\Photo;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PhotoExtension extends AbstractExtension
{
    /**
     * Get the extension filters.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('photo_extension', $this, ['is_safe' => ['html']]),
        ];
    }

    /**
     * Get the extension functions.
     *
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [$this, 'doSomething']),
        ];
    }

    /**
     * Get the file extension for the given content type.
     *
     * @param string $contentType
     *
     * @return string
     */
    public static function getExtensionForContentType(string $contentType): string
    {
        return array_search(
            $contentType,
            PhotoController::EXTENSIONS,
            true
        ) ?: key(PhotoController::EXTENSIONS);
    }

    /**
     * Get the file extension for the given photo.
     *
     * @param Photo $photo
     *
     * @return string
     */
    public function __invoke(Photo $photo): string
    {
        return static::getExtensionForContentType($photo->getContentType());
    }
}
