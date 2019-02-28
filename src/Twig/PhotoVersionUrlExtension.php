<?php

namespace App\Twig;

use App\Entity\PhotoVersion;
use App\Twig\PhotoExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class PhotoVersionUrlExtension extends AbstractExtension
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /**
     * Constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Get the extension filters.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('photo_version_url', $this, ['is_safe' => ['html']]),
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
            new TwigFunction('photo_version_url', $this),
        ];
    }

    /**
     * Get the URL for the given photo version.
     *
     * @param PhotoVersion $version
     *
     * @return string
     */
    public function __invoke(PhotoVersion $version): string
    {
        return $this->urlGenerator->generate(
            'photo_show',
            [
                'id' => $version->getPhoto()->getId(),
                'extension' => PhotoExtension::getExtensionForContentType(
                    $version->getPhoto()->getContentType()
                ),
                'hash' => $version->getHash()
            ]
        );
    }
}
