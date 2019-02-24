<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Moneybird;

use App\Entity\TaxRate;
use App\Repository\TaxRateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mediact\DataContainer\DataContainer;
use Picqer\Financials\Moneybird\Entities\TaxRate as Remote;
use Picqer\Financials\Moneybird\Model;

class TaxRateImporter implements ModelImporterInterface
{
    /** @var TaxRateRepository */
    private $repository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param TaxRateRepository      $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        TaxRateRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->repository    = $repository;
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
        return $model instanceof Remote;
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
        if ($model instanceof Remote) {
            $remote  = $this->createTaxRate($model);
            $taxRate = $this->merge(
                $this->repository->find($remote->getId()) ?? $remote,
                $remote
            );
            $this->entityManager->persist($taxRate);
            $this->entityManager->flush();
        }
    }

    /**
     * Create a tax rate for the given model.
     *
     * @param Remote $model
     *
     * @return TaxRate
     */
    private function createTaxRate(Remote $model): TaxRate
    {
        $taxRate = new TaxRate();
        $data    = new DataContainer($model->attributes());

        $taxRate->setId($data->get('id'));
        $taxRate->setActive($data->get('active', false));
        $taxRate->setName($data->get('name'));
        $taxRate->setPercentage($data->get('percentage'));
        $taxRate->setShowTax($data->get('show_tax', false));
        $taxRate->setType($data->get('tax_rate_type'));

        return $taxRate;
    }

    /**
     * Merge the remote tax rate with the local tax rate.
     *
     * @param TaxRate $local
     * @param TaxRate $remote
     *
     * @return TaxRate
     */
    private function merge(TaxRate $local, TaxRate $remote): TaxRate
    {
        $local->setType($remote->getType());
        $local->setName($remote->getName());
        $local->setShowTax($remote->getShowTax());
        $local->setActive($remote->getActive());
        $local->setPercentage($remote->getPercentage());

        return $local;
    }
}
