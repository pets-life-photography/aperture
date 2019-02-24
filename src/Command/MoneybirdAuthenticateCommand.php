<?php

namespace App\Command;

use Picqer\Financials\Moneybird\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MoneybirdAuthenticateCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'moneybird:authenticate';

    /** @var Connection */
    private $connection;

    /**
     * Constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        parent::__construct();
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
        $table = new Table($output);

        $table->addRow(
            [
                'Authorize URL',
                $this->connection->getAuthUrl()
            ]
        );
        $table->addRow(
            [
                'Token URL',
                $this->connection->getTokenUrl()
            ]
        );
        $table->addRow(
            [
                'Access token',
                $this->connection->getAccessToken()
            ]
        );
        $table->addRow(
            [
                'Administration ID',
                $this->connection->getAdministrationId()
            ]
        );

        $table->render();

        return 0;
    }
}
