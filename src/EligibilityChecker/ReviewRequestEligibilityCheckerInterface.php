<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EligibilityChecker;

use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;

interface ReviewRequestEligibilityCheckerInterface
{
    /**
     * The method should return an instance of EligibilityCheck with the result of the eligibility check.
     * A reason is provided if the review request is not eligible.
     */
    public function check(ReviewRequestInterface $reviewRequest): EligibilityCheck;
}
