<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventSubscriber\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Setono\SyliusReviewPlugin\Model\ProductReviewInterface;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Setono\SyliusReviewPlugin\Workflow\ProductReviewWorkflow;
use Setono\SyliusReviewPlugin\Workflow\StoreReviewWorkflow;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsDoctrineListener(event: 'prePersist')]
final class ReviewPersistSubscriber
{
    public function __construct(
        private readonly WorkflowInterface $storeReviewStateMachine,
        private readonly WorkflowInterface $productReviewStateMachine,
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof StoreReviewInterface) {
            $this->transitionToNew($this->storeReviewStateMachine, $entity, StoreReviewWorkflow::TRANSITION_SUBMIT);
        }

        if ($entity instanceof ProductReviewInterface) {
            $this->transitionToNew($this->productReviewStateMachine, $entity, ProductReviewWorkflow::TRANSITION_SUBMIT);
        }
    }

    private function transitionToNew(WorkflowInterface $workflow, object $entity, string $transition): void
    {
        if ($workflow->can($entity, $transition)) {
            $workflow->apply($entity, $transition);
        }
    }
}
