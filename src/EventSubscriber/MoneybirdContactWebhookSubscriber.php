<?php

namespace App\EventSubscriber;

use App\Event\MoneybirdWebhookReceivedEvent;
use App\Moneybird\ContactSynchronizerInterface;
use Picqer\Financials\Moneybird\Entities\Contact;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MoneybirdContactWebhookSubscriber implements EventSubscriberInterface
{
    /** @var Contact */
    private $resource;

    /** @var ContactSynchronizerInterface */
    private $synchronizer;

    /**
     * Constructor.
     *
     * @param Contact                      $resource
     * @param ContactSynchronizerInterface $synchronizer
     */
    public function __construct(
        Contact $resource,
        ContactSynchronizerInterface $synchronizer
    ) {
        $this->resource     = $resource;
        $this->synchronizer = $synchronizer;
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
        return [
            sprintf(
                '%s.%s',
                MoneybirdWebhookReceivedEvent::NAME,
                'contact_changed'
            ) => 'onContactChanged',
            sprintf(
                '%s.%s',
                MoneybirdWebhookReceivedEvent::NAME,
                'contact_created'
            ) => 'onContactChanged',
            sprintf(
                '%s.%s',
                MoneybirdWebhookReceivedEvent::NAME,
                'contact_merged'
            ) => 'onContactChanged'
        ];
    }

    /**
     * Process reveived changes to Moneybird contacts.
     *
     * @param MoneybirdWebhookReceivedEvent $event
     *
     * @return void
     */
    public function onContactChanged(
        MoneybirdWebhookReceivedEvent $event
    ): void {
        $contact = $this->resource->makeFromResponse(
            $event->getState()->all()
        );

        if ($this->synchronizer->isCandidate($contact)) {
            $this->synchronizer->synchronize($contact);
        }
    }
}
