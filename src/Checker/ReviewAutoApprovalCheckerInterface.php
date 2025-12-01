<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Checker;

use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Component\Review\Model\ReviewInterface;

interface ReviewAutoApprovalCheckerInterface
{
    /**
     * Returns true if the store review should be auto-approved
     */
    public function shouldAutoApprove(StoreReviewInterface $review): bool;

    /**
     * Returns true if the product review should be auto-approved
     */
    public function shouldAutoApproveProductReview(ReviewInterface $review): bool;
}
