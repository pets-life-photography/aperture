<?php

namespace App\Twig;

use App\Entity\Photo;
use App\Entity\PhotoVersion;
use Traversable;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PhotoVersionExtension extends AbstractExtension
{
    /**
     * Get the extension filters.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('photo_version', $this),
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
            new TwigFunction('photo_version', $this),
        ];
    }

    /**
     * Get the version for the given type of the given photo.
     *
     * @param Photo  $photo
     * @param string $type
     *
     * @return PhotoVersion|null
     */
    public function __invoke(Photo $photo, string $type): ?PhotoVersion
    {
        $versions = $photo->getVersions();

        return array_reduce(
            iterator_to_array($versions),
            function (
                ?PhotoVersion $carry,
                PhotoVersion $version
            ) use ($type): ?PhotoVersion {
                if ($carry === null
                    && $version->getType()->getName() === $type
                ) {
                    $carry = $version;
                }

                return $carry;
            }
        );
    }
}
