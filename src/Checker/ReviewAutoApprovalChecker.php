<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Checker;

use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ReviewAutoApprovalChecker implements ReviewAutoApprovalCheckerInterface
{
    public function __construct(
        private readonly int $minimumRatingForAutoApproval = 4,
    ) {
    }

    public function shouldAutoApprove(StoreReviewInterface $review): bool
    {
        $rating = $review->getRating();

        return null !== $rating && $rating >= $this->minimumRatingForAutoApproval;
    }

    public function shouldAutoApproveProductReview(ReviewInterface $review): bool
    {
        $rating = $review->getRating();

        return null !== $rating && $rating >= $this->minimumRatingForAutoApproval;
    }
}
