<?php

namespace App\Command;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Picqer\Financials\Moneybird\Entities\Contact;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MoneybirdClientSyncCommand extends Command
{
    private const REQUIRED_ATTRIBUTES = [
        'firstname',
        'lastname',
        'address1',
        'zipcode',
        'city',
        'country',
        'email'
    ];

    /** @var string */
    protected static $defaultName = 'moneybird:client:sync';

    /** @var Contact */
    private $resource;

    /** @var ClientRepository */
    private $repository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param Contact                $resource
     * @param ClientRepository       $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        Contact $resource,
        ClientRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->resource      = $resource;
        $this->repository    = $repository;
        $this->entityManager = $entityManager;
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
            if (!$this->isCandidate($contact)) {
                continue;
            }

            $remote = $this->createClient($contact);
            $client = $this->merge(
                $this->repository->find($remote->getId()) ?? $remote,
                $remote
            );

            $io->text(sprintf('<info>Synchronizing</info> %s', $client->getEmail()));
            $this->entityManager->persist($client);
        }

        $this->entityManager->flush();

        return 0;
    }

    /**
     * Determine whether the given contact is candidate to become a client.
     *
     * @param Contact $contact
     *
     * @return bool
     */
    private function isCandidate(Contact $contact): bool
    {
        $attributes = $contact->attributes();

        return array_reduce(
            static::REQUIRED_ATTRIBUTES,
            function (bool $carry, string $attribute) use ($attributes): bool {
                return $carry && !empty($attributes[$attribute]);
            },
            !empty($attributes)
        );
    }

    /**
     * Create a client using the given contact.
     *
     * @param Contact $contact
     *
     * @return Client
     */
    private function createClient(Contact $contact): Client
    {
        $client     = new Client();
        $attributes = $contact->attributes();

        $client->setId($attributes['customer_id']);
        $client->setEmail($attributes['email']);
        $client->setFirstName($attributes['firstname']);
        $client->setLastName($attributes['lastname']);
        $client->setVersion($attributes['version']);
        $client->setAddress($attributes['address1']);
        $client->setZipcode($attributes['zipcode']);
        $client->setCity($attributes['city']);
        $client->setCountry($attributes['country']);

        return $client;
    }

    /**
     * Merge the remote client with the local client.
     *
     * @param Client $local
     * @param Client $remote
     *
     * @return Client
     */
    private function merge(Client $local, Client $remote): Client
    {
        if ($remote->getVersion() !== $local->getVersion()) {
            $local->setEmail($remote->getEmail());
            $local->setFirstName($remote->getFirstName());
            $local->setLastName($remote->getLastName());
            $local->setCountry($remote->getCountry());
            $local->setCity($remote->getCity());
            $local->setCountry($remote->getCountry());
            $local->setAddress($remote->getAddress());
            $local->setZipcode($remote->getZipcode());
            $local->setVersion($remote->getVersion());
        }

        return $local;
    }
}
