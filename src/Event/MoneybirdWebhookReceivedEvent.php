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

    /** @var DataContainerInterface */
    private $state;

    /**
     * Constructor.
     *
     * @param string                 $action
     * @param DataContainerInterface $state
     */
    public function __construct(string $action, DataContainerInterface $state)
    {
        $this->action = $action;
        $this->state  = $state;
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
     * Get the state of the entity when the webhook was triggered.
     *
     * @return DataContainerInterface
     */
    public function getState(): DataContainerInterface
    {
        return $this->state;
    }
}
