<?php

namespace App\Command;

use App\Moneybird\ModelImporterInterface;
use Picqer\Financials\Moneybird\Actions\FindAll;
use Picqer\Financials\Moneybird\Model;
use Picqer\Financials\Moneybird\Moneybird;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoneybirdImportCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'moneybird:import';

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
        parent::__construct();
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Import Moneybird entities.');
        $this->addArgument(
            'entity',
            InputArgument::REQUIRED,
            sprintf(
                'The entity type to import. Must be one of: <comment>%s</comment>',
                implode(
                    ', ',
                    array_filter(
                        get_class_methods(Moneybird::class),
                        // Remove all get* and __* methods.
                        function (string $method): bool {
                            return preg_match(
                                '/^(get|__)/',
                                $method
                            ) === 0;
                        }
                    )
                )
            )
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
        $io     = new SymfonyStyle($input, $output);
        $entity = $input->getArgument('entity');

        $io->title(
            sprintf(
                'Synchronizing Moneybird %s entities',
                ucfirst($entity)
            )
        );

        /** @var Model|FindAll $resource */
        $resource = $this->client->{$entity}();

        /** @var Model $model */
        foreach ($resource->getAll() as $model) {
            $id = $model->__get('id');

            if (!$this->importer->isCandidate($model)) {
                $io->text(sprintf('<comment>Skipping</comment> %s', $id));
                continue;
            }

            $io->text(sprintf('<info>Importing</info> %s', $id));

            $this->importer->import($model);
        }

        return 0;
    }
}
