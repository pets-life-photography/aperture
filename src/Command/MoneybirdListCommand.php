<?php

namespace App\Command;

use Picqer\Financials\Moneybird\Model;
use Picqer\Financials\Moneybird\Moneybird;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MoneybirdListCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'moneybird:list';

    /** @var Moneybird */
    private $client;

    /**
     * Constructor.
     *
     * @param Moneybird $client
     */
    public function __construct(Moneybird $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('List API entities');
        $this->addArgument(
            'entity',
            InputArgument::REQUIRED,
            'The entity type to query.'
        );
        $this->addArgument(
            'method',
            InputArgument::OPTIONAL,
            'The API method to invoke.',
            'getAll'
        );
        $this->addArgument(
            'arguments',
            InputArgument::IS_ARRAY,
            'Arguments to supply to the API call',
            []
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
        $resource = call_user_func(
            [
                $this->client,
                $input->getArgument('entity')
            ]
        );

        $result = (array)call_user_func_array(
            [
                $resource,
                $input->getArgument('method')
            ],
            $input->getArgument('arguments')
        );

        /** @var Model $model */
        foreach ($result as $model) {
            $entity = json_decode($model->json());

            $output->writeln(
                json_encode(
                    $entity,
                    JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_SLASHES
                    | JSON_UNESCAPED_UNICODE
                    | JSON_NUMERIC_CHECK
                )
            );
        }

        return 0;
    }
}
