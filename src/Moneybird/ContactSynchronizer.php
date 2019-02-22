<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Moneybird;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Picqer\Financials\Moneybird\Entities\Contact;

class ContactSynchronizer implements ContactSynchronizerInterface
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

    /** @var ClientRepository */
    private $repository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param ClientRepository       $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ClientRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->repository    = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * Determine whether the given contact is candidate to become a client.
     *
     * @param Contact $contact
     *
     * @return bool
     */
    public function isCandidate(Contact $contact): bool
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
     * Synchronize the given contact with local clients.
     *
     * @param Contact $contact
     *
     * @return void
     */
    public function synchronize(Contact $contact): void
    {
        $remote = $this->createClient($contact);
        $client = $this->merge(
            $this->repository->find($remote->getId()) ?? $remote,
            $remote
        );
        $this->entityManager->persist($client);
        $this->entityManager->flush();
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
