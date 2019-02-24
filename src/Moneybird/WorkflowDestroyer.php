<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Moneybird;

use App\Entity\Workflow;
use App\Event\MoneybirdWebhookReceivedEvent;
use App\Repository\WorkflowRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorkflowDestroyer implements ModelDestroyerInterface
{
    private const ENTITY_TYPE = 'InvoiceWorkflow';
    private const ACTION      = 'workflow_destroyed';

    /** @var WorkflowRepository */
    private $repository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param WorkflowRepository     $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        WorkflowRepository $repository,
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
        $workflow = $this->repository->find(
            $event->getState()->get('id')
        );

        if ($workflow instanceof Workflow) {
            $this->entityManager->remove($workflow);
            $this->entityManager->flush();
        }
    }
}
