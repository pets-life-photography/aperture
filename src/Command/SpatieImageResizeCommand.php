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

class SpatieImageResizeCommand extends Command
{
    private const EXIT_RESIZE_FAILED = 1;
    private const EXIT_SUCCESS       = 0;

    /** @var string */
    protected static $defaultName = 'spatie:image:resize';

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Resize the given image');
        $this->addArgument(
            'image',
            InputArgument::REQUIRED,
            'The source image'
        );
        $this->addArgument(
            'width',
            InputArgument::REQUIRED,
            'The image width'
        );
        $this->addArgument(
            'height',
            InputArgument::REQUIRED,
            'The image height'
        );
        $this->addArgument(
            'destination',
            InputArgument::OPTIONAL,
            'The destination image'
        );
        $this->addOption(
            'crop',
            'c',
            InputOption::VALUE_NONE,
            'Whether the image will be cropped'
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
        $image  = Image::load($input->getArgument('image'));
        $width  = $input->getArgument('width');
        $height = $input->getArgument('height');
        $output = $input->getArgument('destination') ?: tempnam(
            sys_get_temp_dir(),
            'spatie_image_resize'
        );

        $io->comment(
            sprintf(
                'Original: %s (%dx%d)',
                $input->getArgument('image'),
                $image->getWidth(),
                $image->getHeight()
            )
        );

        $image->fit(
            $input->getOption('crop')
                ? Manipulations::FIT_CROP
                : Manipulations::FIT_MAX,
            $width,
            $height
        );

        $image->save($output);

        $result = Image::load($output);

        if (!$result->getWidth() === $width
            && !$result->getHeight() === $height
        ) {
            $io->error('Failed to resize image.');
            return static::EXIT_RESIZE_FAILED;
        }

        $io->success(
            sprintf(
                'Resized image: %s (%dx%d)',
                $output,
                $result->getWidth(),
                $result->getHeight()
            )
        );

        return static::EXIT_SUCCESS;
    }
}
