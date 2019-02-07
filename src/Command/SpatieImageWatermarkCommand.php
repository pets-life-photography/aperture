<?php

namespace App\Command;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SpatieImageWatermarkCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'spatie:image:watermark';

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Place the given watermark on the given image.');
        $this->addArgument(
            'image',
            InputArgument::REQUIRED,
            'The source image'
        );
        $this->addArgument(
            'watermark',
            InputArgument::REQUIRED,
            'The watermark image'
        );
        $this->addArgument(
            'destination',
            InputArgument::OPTIONAL,
            'The destination image'
        );
        $this->addOption(
            'opacity',
            'o',
            InputOption::VALUE_REQUIRED,
            'The opacity',
            100
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
        /** @var Image|Manipulations $image */
        $image  = Image::load($input->getArgument('image'));
        $io     = new SymfonyStyle($input, $output);
        $output = $input->getArgument('destination') ?: tempnam(
            sys_get_temp_dir(),
            'spatie_image_watermark'
        );

        $io->comment(sprintf('Image: %s', $input->getArgument('image')));

        $image->watermark($input->getArgument('watermark'));
        $image->watermarkOpacity((int)$input->getOption('opacity'));
        $image->watermarkPosition(Manipulations::POSITION_BOTTOM_RIGHT);

        $image->save($output);
        $io->success($output);

        return 0;
    }
}
