<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Moneybird;

use Picqer\Financials\Moneybird\Model;

interface ModelImporterInterface
{
    /**
     * Determine whether the given model is candidate to be imported.
     *
     * @param Model $model
     *
     * @return bool
     */
    public function isCandidate(Model $model): bool;

    /**
     * Import the given model.
     *
     * @param Model $model
     *
     * @return void
     */
    public function import(Model $model): void;
}
