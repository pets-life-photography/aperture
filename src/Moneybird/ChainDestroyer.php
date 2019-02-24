<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Moneybird;

use App\Event\MoneybirdWebhookReceivedEvent;

class ChainDestroyer implements ModelDestroyerInterface
{
    /** @var ModelDestroyerInterface[] */
    private $destroyers;

    /**
     * Constructor.
     *
     * @param ModelDestroyerInterface ...$destroyers
     */
    public function __construct(ModelDestroyerInterface ...$destroyers)
    {
        $this->destroyers = $destroyers;
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
        return array_reduce(
            $this->destroyers,
            function (
                bool $carry,
                ModelDestroyerInterface $destroyer
            ) use ($event): bool {
                return $carry || $destroyer->isSupported($event);
            },
            false
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
        foreach ($this->destroyers as $destroyer) {
            if (!$destroyer->isSupported($event)) {
                continue;
            }

            $destroyer->destroy($event);
            break;
        }
    }
}
