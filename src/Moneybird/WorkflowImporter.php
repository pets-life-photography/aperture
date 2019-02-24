<?php
/**
 * Copyright MediaCT. All rights reserved.
 * https://www.mediact.nl
 */

namespace App\Moneybird;

use App\Entity\Workflow;
use App\Repository\WorkflowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mediact\DataContainer\DataContainer;
use Picqer\Financials\Moneybird\Entities\Workflow as Remote;
use Picqer\Financials\Moneybird\Model;

class WorkflowImporter implements ModelImporterInterface
{
    private const SUPPORTED_WORKFLOW_TYPES = [
        'InvoiceWorkflow'
    ];

    /** @var WorkflowRepository */
    private $repository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param WorkflowRepository     $repository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        WorkflowRepository $repository,
        EntityManagerInterface $entityManager
    ) {
        $this->repository    = $repository;
        $this->entityManager = $entityManager;
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
        return (
            $model instanceof Remote
            && in_array(
                $model->__get('type'),
                static::SUPPORTED_WORKFLOW_TYPES,
                true
            )
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
        if ($model instanceof Remote) {
            $data     = new DataContainer($model->attributes());
            $workflow = $this->repository->find($data->get('id'));

            if (!$workflow instanceof Workflow) {
                $workflow = $this->createWorkflow($data);
            }

            if ($this->repository->count(['selected' => true]) === 0
                && $data->get('default') === true
            ) {
                $workflow->setSelected(true);
            }

            $this->entityManager->persist(
                $this->updateWorkflow($workflow, $data)
            );
            $this->entityManager->flush();
        }
    }

    /**
     * Create a workflow for the given data.
     *
     * @param DataContainer $data
     *
     * @return Workflow
     */
    private function createWorkflow(DataContainer $data): Workflow
    {
        $workflow = new Workflow();
        $workflow->setId($data->get('id'));
        $workflow->setSelected(false);

        return $workflow;
    }

    /**
     * Update the given workflow with the given data.
     *
     * @param Workflow      $workflow
     * @param DataContainer $data
     *
     * @return Workflow
     */
    private function updateWorkflow(
        Workflow $workflow,
        DataContainer $data
    ): Workflow {
        $workflow->setName($data->get('name'));
        $workflow->setType($data->get('type'));
        $workflow->setActive($data->get('active'));
        $workflow->setCurrency($data->get('currency'));
        $workflow->setLanguage($data->get('language'));
        $workflow->setTaxIncluded($data->get('prices_are_incl_tax'));

        return $workflow;
    }
}
