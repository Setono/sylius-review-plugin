<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventListener\Doctrine;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\AutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Setono\SyliusReviewPlugin\Workflow\ProductReviewWorkflow;
use Setono\SyliusReviewPlugin\Workflow\StoreReviewWorkflow;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class ReviewAutoApprovalListener
{
    /**
     * @param AutoApprovalCheckerInterface<StoreReviewInterface> $storeAutoApprovalChecker
     * @param AutoApprovalCheckerInterface<ProductReviewInterface> $productAutoApprovalChecker
     */
    public function __construct(
        private readonly AutoApprovalCheckerInterface $storeAutoApprovalChecker,
        private readonly AutoApprovalCheckerInterface $productAutoApprovalChecker,
        private readonly WorkflowInterface $storeReviewWorkflow,
        private readonly WorkflowInterface $productReviewWorkflow,
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
        if ($entity instanceof StoreReviewInterface
            && $this->storeReviewWorkflow->can($entity, StoreReviewWorkflow::TRANSITION_ACCEPT)
            && $this->storeAutoApprovalChecker->shouldAutoApprove($entity)
        ) {
            $this->storeReviewWorkflow->apply($entity, StoreReviewWorkflow::TRANSITION_ACCEPT);
        }

        if ($entity instanceof ProductReviewInterface
            && $this->productReviewWorkflow->can($entity, ProductReviewWorkflow::TRANSITION_ACCEPT)
            && $this->productAutoApprovalChecker->shouldAutoApprove($entity)
        ) {
            $this->productReviewWorkflow->apply($entity, ProductReviewWorkflow::TRANSITION_ACCEPT);
        }
    }
}
