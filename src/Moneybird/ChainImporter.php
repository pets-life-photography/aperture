<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Moneybird;

use Picqer\Financials\Moneybird\Model;

class ChainImporter implements ModelImporterInterface
{
    /** @var ModelImporterInterface[] */
    private $importers;

    /**
     * Constructor.
     *
     * @param ModelImporterInterface ...$importers
     */
    public function __construct(ModelImporterInterface ...$importers)
    {
        $this->importers = $importers;
    }

    /**
     * Determine whether the given model is candidate to be imported.
     *
     * @param Model $model
     *
     * @return bool
     */
    public function isCandidate(Model $model): bool
    {
        return array_reduce(
            $this->importers,
            function (
                bool $carry,
                ModelImporterInterface $importer
            ) use ($model): bool {
                return $carry || $importer->isCandidate($model);
            },
            false
        );
    }

    /**
     * Import the given model.
     *
     * @param Model $model
     *
     * @return void
     */
    public function import(Model $model): void
    {
        foreach ($this->importers as $importer) {
            if (!$importer->isCandidate($model)) {
                continue;
            }

            $importer->import($model);
            break;
        }
    }
}
