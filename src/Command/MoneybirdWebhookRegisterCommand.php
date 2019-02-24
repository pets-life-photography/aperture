<?php

namespace App\Command;

use App\Event\MoneybirdWebhookReceivedEvent;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Picqer\Financials\Moneybird\Entities\Webhook;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MoneybirdWebhookRegisterCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'moneybird:webhook:register';

    /** @var Webhook */
    private $resource;

    /** @var HttpClient */
    private $client;

    /** @var MessageFactory */
    private $messageFactory;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param Webhook                  $resource
     * @param HttpClient               $client
     * @param MessageFactory           $messageFactory
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        Webhook $resource,
        HttpClient $client,
        MessageFactory $messageFactory,
        EventDispatcherInterface $dispatcher
    ) {
        $this->resource       = $resource;
        $this->client         = $client;
        $this->messageFactory = $messageFactory;
        $this->dispatcher     = $dispatcher;
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
        $this->addOption(
            'purge',
            'p',
            InputOption::VALUE_NONE,
            'Purge all other registered webhooks'
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

        $response = $this->client->sendRequest(
            $this->messageFactory->createRequest('GET', $uri)
        );

        if ($response->getStatusCode() !== 200) {
            $io->error(
                sprintf('Could not access webhook: %s', $uri)
            );
            return 1;
        }

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

        $purgeAll = $input->getOption('purge');

        /** @var Webhook $webhook */
        foreach ($existing as $webhook) {
            if ($purgeAll || $webhook->__get('url') === $uri) {
                $io->warning(
                    sprintf(
                        'Removing existing webhook: %s (%s)',
                        $webhook->__get('id'),
                        $webhook->__get('url')
                    )
                );
                $webhook->delete();
            }
        }

        $io->success('Installed webhook!');

        return 0;
    }
}
