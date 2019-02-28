<?php

namespace App\DataFixtures;

use App\Entity\PhotoType;
use App\Entity\Watermark;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Mediact\DataContainer\DataContainer;
use Spatie\Image\Image;

class PhotoFixtures extends Fixture
{
    private const DEFAULT_WATERMARK = __DIR__ . '/../../public/watermark.png';

    private const TYPES = [
        'source' => [],
        'preview' => [
            'width' => 800,
            'height' => 600,
            'watermark' => true,
            'available' => true
        ],
        'thumbnail' => [
            'width' => 64,
            'height' => 64,
            'available' => true,
            'crop' => true
        ]
    ];

    /**
     * Load fixtures for photos.
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (static::TYPES as $name => $properties) {
            $data = new DataContainer($properties);
            $type = $manager->find(
                PhotoType::class,
                $name
            ) ?? new PhotoType();

            $type->setName($name);
            $type->setWidth($data->get('width'));
            $type->setHeight($data->get('height'));
            $type->setWatermark($data->get('watermark', false));
            $type->setCrop($data->get('crop', false));
            $type->setAvailable($data->get('available', false));

            $manager->persist($type);
        }

        $image     = new Image(static::DEFAULT_WATERMARK);
        $watermark = $manager
            ->getRepository(Watermark::class)
            ->findOneBy([]) ?? new Watermark();

        $watermark->setName('default');
        $watermark->setContentType(
            mime_content_type(static::DEFAULT_WATERMARK)
        );
        $watermark->setContent(file_get_contents(static::DEFAULT_WATERMARK));
        $watermark->setWidth($image->getWidth());
        $watermark->setHeight($image->getHeight());

        $manager->persist($watermark);

        $manager->flush();
    }
}
