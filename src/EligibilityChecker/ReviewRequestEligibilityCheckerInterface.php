<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EligibilityChecker;

use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;

interface ReviewRequestEligibilityCheckerInterface
{
    /**
     * Returns true if the given review request is eligible for being sent to the customer
     */
    public function isEligible(ReviewRequestInterface $reviewRequest): bool;
}
