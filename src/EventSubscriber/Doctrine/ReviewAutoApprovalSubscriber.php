<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EventSubscriber\Doctrine;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Setono\SyliusReviewPlugin\Checker\AutoApproval\AutoApprovalCheckerInterface;
use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Component\Core\Model\ProductReviewInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ReviewAutoApprovalSubscriber
{
    /**
     * @param AutoApprovalCheckerInterface<StoreReviewInterface> $storeAutoApprovalChecker
     * @param AutoApprovalCheckerInterface<ProductReviewInterface> $productAutoApprovalChecker
     */
    public function __construct(
        private readonly AutoApprovalCheckerInterface $storeAutoApprovalChecker,
        private readonly AutoApprovalCheckerInterface $productAutoApprovalChecker,
    ) {
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof StoreReviewInterface && $this->storeAutoApprovalChecker->shouldAutoApprove($entity)) {
            $entity->setStatus(ReviewInterface::STATUS_ACCEPTED);
        }

        if ($entity instanceof ProductReviewInterface && $this->productAutoApprovalChecker->shouldAutoApprove($entity)) {
            $entity->setStatus(ReviewInterface::STATUS_ACCEPTED);
        }
    }
}
