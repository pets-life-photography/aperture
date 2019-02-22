<?php

namespace App\Command;

use App\Moneybird\ContactSynchronizerInterface;
use Picqer\Financials\Moneybird\Entities\Contact;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoneybirdClientSyncCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'moneybird:client:sync';

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
        parent::__construct();
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription(
            'Synchronize the Moneybird contacts with Aperture clients.'
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
        $io = new SymfonyStyle($input, $output);

        $io->title('Synchronizing Moneybird contacts');

        /** @var Contact $contact */
        foreach ($this->resource->getAll() as $contact) {
            if (!$this->synchronizer->isCandidate($contact)) {
                continue;
            }

            $io->text(
                sprintf(
                    '<info>Synchronizing</info> %s',
                    $contact->__get('email')
                )
            );
            $this->synchronizer->synchronize($contact);
        }

        return 0;
    }
}
