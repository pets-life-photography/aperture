<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Event;

use Mediact\DataContainer\DataContainerInterface;
use Symfony\Component\EventDispatcher\Event;

class MoneybirdWebhookReceivedEvent extends Event
{
    public const NAME = 'moneybird.webhook.receive';

    /** @var string */
    private $action;

    /** @var string */
    private $entityType;

    /** @var DataContainerInterface */
    private $state;

    /**
     * Constructor.
     *
     * @param string                 $action
     * @param string                 $entityType
     * @param DataContainerInterface $state
     */
    public function __construct(
        string $action,
        string $entityType,
        DataContainerInterface $state
    ) {
        $this->action     = $action;
        $this->entityType = $entityType;
        $this->state      = $state;
    }

    /**
     * Get the action for which the webhook was triggered.
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Get the entity type of the changed entity.
     *
     * @return string
     */
    public function getEntityType(): string
    {
        return $this->entityType;
    }

    /**
     * Get the state of the entity when the webhook was triggered.
     *
     * @return DataContainerInterface
     */
    public function getState(): DataContainerInterface
    {
        return $this->state;
    }
}
