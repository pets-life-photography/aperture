<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Moneybird;

use App\Event\MoneybirdWebhookReceivedEvent;

interface ModelDestroyerInterface
{
    /**
     * Whether the destroyer can process the given event.
     *
     * @param MoneybirdWebhookReceivedEvent $event
     *
     * @return bool
     */
    public function isSupported(MoneybirdWebhookReceivedEvent $event): bool;

    /**
     * Destroy the entity associated with the given event.
     *
     * @param MoneybirdWebhookReceivedEvent $event
     *
     * @return void
     */
    public function destroy(MoneybirdWebhookReceivedEvent $event): void;
}
