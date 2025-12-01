<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Checker\ReviewableOrder;

use Setono\SyliusReviewPlugin\Repository\StoreReviewRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class StoreReviewEditableReviewableOrderChecker implements ReviewableOrderCheckerInterface
{
    public function __construct(
        private readonly StoreReviewRepositoryInterface $storeReviewRepository,
        private readonly ?string $editablePeriod = '+24 hours',
    ) {
    }

    public function check(OrderInterface $order): ReviewableOrderCheck
    {
        $existingReview = $this->storeReviewRepository->findOneByOrder($order);

        if (null === $existingReview) {
            return ReviewableOrderCheck::reviewable();
        }

        // If editing is disabled, existing reviews cannot be edited
        if (null === $this->editablePeriod) {
            return ReviewableOrderCheck::notReviewable('setono_sylius_review.ui.review_already_submitted');
        }

        $createdAt = $existingReview->getCreatedAt();
        if (null === $createdAt) {
            return ReviewableOrderCheck::reviewable();
        }

        $editableUntil = \DateTimeImmutable::createFromInterface($createdAt)->modify($this->editablePeriod);

        if (new \DateTimeImmutable() < $editableUntil) {
            return ReviewableOrderCheck::reviewable();
        }

        return ReviewableOrderCheck::notReviewable('setono_sylius_review.ui.review_period_expired');
    }
}
