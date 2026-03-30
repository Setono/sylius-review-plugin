<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventListener\Doctrine;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\AutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\Workflow\AbstractReviewWorkflow;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class AutoApprovalListener
{
    /**
     * @param class-string<ReviewInterface> $reviewClass
     * @param AutoApprovalCheckerInterface<ReviewInterface> $checker
     */
    public function __construct(
        private readonly string $reviewClass,
        private readonly AutoApprovalCheckerInterface $checker,
        private readonly StateMachineInterface $stateMachine,
        private readonly string $workflowName,
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->handleAutoApproval($args->getObject());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->handleAutoApproval($args->getObject());
    }

    private function handleAutoApproval(object $entity): void
    {
        if (!$entity instanceof $this->reviewClass) {
            return;
        }

        if (!$this->stateMachine->can($entity, $this->workflowName, AbstractReviewWorkflow::TRANSITION_ACCEPT)) {
            return;
        }

        if ($this->checker->shouldAutoApprove($entity)) {
            $this->stateMachine->apply($entity, $this->workflowName, AbstractReviewWorkflow::TRANSITION_ACCEPT);
        }
    }
}
