<?php

namespace App\Command;

use App\Event\MoneybirdWebhookReceivedEvent;
use Picqer\Financials\Moneybird\Entities\Webhook;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MoneybirdWebhookRegisterCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'moneybird:webhook:register';

    /** @var Webhook */
    private $resource;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param Webhook                  $resource
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        Webhook $resource,
        EventDispatcherInterface $dispatcher
    ) {
        $this->resource   = $resource;
        $this->dispatcher = $dispatcher;
        parent::__construct();
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Register Moneybird events');
        $this->addArgument(
            'callback_uri',
            InputArgument::REQUIRED,
            'The URI called by Moneybird.'
        );
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io  = new SymfonyStyle($input, $output);
        $uri = $input->getArgument('callback_uri');

        $io->comment(
            sprintf('Creating / updating webhook: %s', $uri)
        );

        // Cache the existing webhooks, to clean up duplicates afterwards.
        $existing = $this->resource->getAll();

        $events = array_reduce(
            array_keys(
                $this->dispatcher->getListeners()
            ),
            function (array $carry, string $event): array {
                if (preg_match(
                    sprintf(
                        '/^%s\.(?P<action>[a-z_]+)$/',
                        preg_quote(MoneybirdWebhookReceivedEvent::NAME, '/')
                    ),
                    $event,
                    $matches
                )) {
                    $carry[] = $matches['action'];
                }

                return $carry;
            },
            []
        );

        if (!empty($events)) {
            $io->title('Events found');
            $io->listing($events);

            $this->resource->__set('url', $uri);
            $this->resource->__set('events', $events);

            $io->comment('Installing new webhook');

            $this->resource->insert();
        }

        /** @var Webhook $webhook */
        foreach ($existing as $webhook) {
            if ($webhook->__get('url') === $uri) {
                $io->warning(
                    sprintf(
                        'Removing existing webhook: %s',
                        $webhook->__get('id')
                    )
                );
                $webhook->delete();
            }
        }

        $io->success('Installed webhook!');

        return 0;
    }
}
