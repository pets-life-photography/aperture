<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Moneybird;

use App\Entity\TaxRate;
use App\Event\MoneybirdWebhookReceivedEvent;
use App\Repository\TaxRateRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaxRateDestroyer implements ModelDestroyerInterface
{
    private const ENTITY_TYPE = 'TaxRate';
    private const ACTION      = 'taxrate_destroyed';

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
     * Whether the destroyer can process the given event.
     *
     * @param MoneybirdWebhookReceivedEvent $event
     *
     * @return bool
     */
    public function isSupported(MoneybirdWebhookReceivedEvent $event): bool
    {
        return (
            $event->getEntityType() === static::ENTITY_TYPE
            && $event->getAction() === static::ACTION
        );
    }

    /**
     * Destroy the entity associated with the given event.
     *
     * @param MoneybirdWebhookReceivedEvent $event
     *
     * @return void
     */
    public function destroy(MoneybirdWebhookReceivedEvent $event): void
    {
        $taxRate = $this->repository->find(
            $event->getState()->get('id')
        );

        if ($taxRate instanceof TaxRate) {
            $this->entityManager->remove($taxRate);
            $this->entityManager->flush();
        }
    }
}
