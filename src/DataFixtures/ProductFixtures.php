<?php

namespace App\DataFixtures;

use App\Entity\ProductType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public const PRODUCT_TYPES = [
        'defined' => 'This product can only be selected by an administrator.',
        'selected' => 'This product can be selected by an end-user.'
    ];

    /**
     * Load the product types.
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        foreach (static::PRODUCT_TYPES as $name => $description) {
            $type = $manager->find(
                ProductType::class,
                $name
            ) ?? new ProductType();
            $type->setName($name);
            $type->setDescription($description);
            $manager->persist($type);
        }

        $manager->flush();
    }
}
