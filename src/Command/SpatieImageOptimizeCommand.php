<?php

namespace App\Command;

use ReflectionClass;
use Spatie\ImageOptimizer\Image;
use Spatie\ImageOptimizer\Optimizer;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Svgo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class SpatieImageOptimizeCommand extends Command
{
    private const OPTIMIZERS = [
        Jpegoptim::class,
        Optipng::class,
        Pngquant::class,
        Gifsicle::class,
        Svgo::class
    ];

    private const EXIT_SUCCESS       = 0;
    private const EXIT_NOT_SUPPORTED = 1;
    private const EXIT_NOT_OPTIMIZED = 2;

    /** @var string */
    protected static $defaultName = 'spatie:image:optimize';

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription('Optimize the given image.');
        $this->addArgument(
            'image',
            InputArgument::REQUIRED,
            'Image to test against available optimizers'
        );
        $this->addArgument(
            'destination',
            InputArgument::OPTIONAL,
            'Destination of the optimized image'
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
        $io         = new SymfonyStyle($input, $output);
        $table      = new Table($output);
        $image      = new Image($input->getArgument('image'));
        $optimizers = [];

        $io->comment(
            sprintf('Input: %s (%s)', $image->path(), $image->mime())
        );

        $table->setHeaders(['Class', 'Binary', 'Supported']);

        foreach (static::OPTIMIZERS as $class) {
            $optimizer = $this->createOptimizer($class);
            $process   = Process::fromShellCommandline(
                sprintf('which %s', $optimizer->binaryName())
            );

            $process->run();
            $binary = trim($process->getOutput());
            $cells  = [
                $class,
                is_executable($binary)
                    ? $binary
                    : '<error>Not installed</error>'
            ];

            $isSupported = is_executable($binary) && $optimizer->canHandle($image);
            $cells[]     = $isSupported ? '<info>Yes</info>' : 'No';

            if ($isSupported) {
                $optimizers[] = $optimizer;
            }

            $table->addRow($cells);
        }

        $table->render();

        if (count($optimizers) < 1) {
            $io->error('No compatible optimizers installed.');
            return static::EXIT_NOT_SUPPORTED;
        }

        $output = $input->getArgument('destination')
            ?: tempnam(sys_get_temp_dir(), 'spatie_optimizer');
        $chain  = new OptimizerChain();
        $chain->setOptimizers($optimizers);

        $io->comment(sprintf("Original:\t%s", $image->path()));
        $io->comment(sprintf("Output:\t%s", $output));
        $chain->optimize($image->path(), $output);

        $original  = filesize($image->path());
        $optimized = filesize($output);

        if ($optimized < $original) {
            $delta = $original - $optimized;

            $io->success(
                sprintf(
                    '%.2fkb -> %.2fkb - (%.2f%% improvement)',
                    $original / 1024,
                    $optimized / 1024,
                    ($delta / $original) * 100
                )
            );
        } else {
            $io->error(
                'Optimized version is not smaller than the original file.'
            );
            return static::EXIT_NOT_OPTIMIZED;
        }

        return static::EXIT_SUCCESS;
    }

    /**
     * Create an optimizer for the given class name.
     *
     * @param string $class
     *
     * @return Optimizer
     */
    private function createOptimizer(string $class): Optimizer
    {
        $reflection = new ReflectionClass($class);

        /** @var Optimizer $optimizer */
        $optimizer = $reflection->newInstance();

        return $optimizer;
    }
}
