<?php

namespace App\EventSubscriber;

use App\Event\MoneybirdWebhookReceivedEvent;
use App\Moneybird\ModelImporterInterface;
use Picqer\Financials\Moneybird\Model;
use Picqer\Financials\Moneybird\Moneybird;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MoneybirdWebhookSubscriber implements EventSubscriberInterface
{
    private const EVENT_PREFIX = MoneybirdWebhookReceivedEvent::NAME;
    private const EVENTS       = [
        'contact_changed',
        'contact_created',
        'contact_merged',
        'tax_rate_activated',
        'tax_rate_created',
        'tax_rate_deactivated',
        'tax_rate_updated'
    ];

    /** @var Moneybird */
    private $client;

    /** @var ModelImporterInterface */
    private $importer;

    /**
     * Constructor.
     *
     * @param Moneybird              $client
     * @param ModelImporterInterface $importer
     */
    public function __construct(
        Moneybird $client,
        ModelImporterInterface $importer
    ) {
        $this->client   = $client;
        $this->importer = $importer;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return array_reduce(
            static::EVENTS,
            function (array $carry, string $event): array {
                $index         = sprintf(
                    '%s.%s',
                    static::EVENT_PREFIX,
                    $event
                );
                $carry[$index] = 'onWebhookReceived';

                return $carry;
            },
            []
        );
    }

    /**
     * Process received changes to Moneybird models.
     *
     * @param MoneybirdWebhookReceivedEvent $event
     *
     * @return void
     */
    public function onWebhookReceived(
        MoneybirdWebhookReceivedEvent $event
    ): void {
        $method   = lcfirst($event->getEntityType());
        $resource = $this->client->{$method}();

        if ($resource instanceof Model) {
            $model = $resource->makeFromResponse(
                $event->getState()->all()
            );

            if ($this->importer->isCandidate($model)) {
                $this->importer->import($model);
            }
        }
    }
}
