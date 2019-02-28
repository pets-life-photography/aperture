<?php

namespace App\Command;

use App\Entity\PhotoFactoryInterface;
use App\Entity\PhotoVersionFactoryInterface;
use App\Repository\PhotoTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PhotoImportCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'photo:import';

    /** @var PhotoTypeRepository */
    private $types;

    /** @var PhotoFactoryInterface */
    private $photoFactory;

    /** @var PhotoVersionFactoryInterface */
    private $versionFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param PhotoTypeRepository          $types
     * @param PhotoFactoryInterface        $photoFactory
     * @param PhotoVersionFactoryInterface $versionFactory
     * @param EntityManagerInterface       $entityManager
     */
    public function __construct(
        PhotoTypeRepository $types,
        PhotoFactoryInterface $photoFactory,
        PhotoVersionFactoryInterface $versionFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->types          = $types;
        $this->photoFactory   = $photoFactory;
        $this->versionFactory = $versionFactory;
        $this->entityManager  = $entityManager;
        parent::__construct();
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Import the given photo file');
        $this->addArgument(
            'file',
            InputArgument::REQUIRED,
            'Path to the photo file.'
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
        $io   = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        $io->note('Creating source photo');

        $photo = $this->photoFactory->create($file);

        foreach ($this->types->findAll() as $type) {
            $io->note(
                sprintf('Creating photo version: %s', $type->getName())
            );
            $version = $this->versionFactory->createVersion($photo, $type);
            $photo->addVersion($version);
        }

        $io->note('Persisting photo entities');
        $this->entityManager->persist($photo);
        $this->entityManager->flush();

        $io->success('Imported photo');

        return 0;
    }
}
