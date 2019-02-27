<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Moneybird;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\ProductTypeRepository;
use App\Repository\TaxRateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mediact\DataContainer\DataContainer;
use Picqer\Financials\Moneybird\Entities\Product as Resource;
use Picqer\Financials\Moneybird\Model;

class ProductImporter implements ModelImporterInterface
{
    /** @var ProductRepository */
    private $products;

    /** @var ProductTypeRepository */
    private $productTypes;

    /** @var TaxRateRepository */
    private $taxRates;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param ProductRepository      $products
     * @param ProductTypeRepository  $productTypes
     * @param TaxRateRepository      $taxRates
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ProductRepository $products,
        ProductTypeRepository $productTypes,
        TaxRateRepository $taxRates,
        EntityManagerInterface $entityManager
    ) {
        $this->products      = $products;
        $this->productTypes  = $productTypes;
        $this->taxRates      = $taxRates;
        $this->entityManager = $entityManager;
    }

    /**
     * Determine whether the given model is candidate to be imported.
     *
     * @param Model $model
     *
     * @return bool
     */
    public function isCandidate(Model $model): bool
    {
        return $model instanceof Resource;
    }

    /**
     * Import the given model.
     *
     * @param Model $model
     *
     * @return void
     */
    public function import(Model $model): void
    {
        if ($model instanceof Resource) {
            $data    = new DataContainer($model->attributes());
            $product = $this->products->find($data->get('id'));

            if ($product === null) {
                $product = $this->createProduct($data);
            }

            $this->entityManager->persist(
                $this->updateProduct($product, $data)
            );
            $this->entityManager->flush();
        }
    }

    /**
     * Create a new product for the given data.
     *
     * @param DataContainer $data
     *
     * @return Product
     */
    private function createProduct(DataContainer $data): Product
    {
        $product = new Product();
        $product->setId($data->get('id'));
        $product->setType(
            $this->productTypes->getDefaultType()
        );

        return $product;
    }

    /**
     * Update the given product
     *
     * @param Product       $product
     * @param DataContainer $data
     *
     * @return Product
     */
    private function updateProduct(
        Product $product,
        DataContainer $data
    ): Product {
        $product->setDescription($data->get('description'));
        $product->setPrice($data->get('price'));
        $product->setCurrency($data->get('currency'));
        $product->setTaxRate(
            $this->taxRates->find(
                $data->get('tax_rate_id')
            )
        );

        return $product;
    }
}
