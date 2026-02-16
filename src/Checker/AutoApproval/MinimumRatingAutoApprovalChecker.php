<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Checker\AutoApproval;

use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @implements AutoApprovalCheckerInterface<ReviewInterface>
 */
final class MinimumRatingAutoApprovalChecker implements AutoApprovalCheckerInterface
{
    public function __construct(
        private readonly int $minimumRatingForAutoApproval = 4,
    ) {
    }

    public function shouldAutoApprove(ReviewInterface $review): bool
    {
        return (int) $review->getRating() >= $this->minimumRatingForAutoApproval;
    }
}
