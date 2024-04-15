<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EligibilityChecker;

use Setono\CompositeCompilerPass\CompositeService;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;

/**
 * @extends CompositeService<ReviewRequestEligibilityCheckerInterface>
 */
final class CompositeReviewRequestEligibilityChecker extends CompositeService implements ReviewRequestEligibilityCheckerInterface
{
    public function isEligible(ReviewRequestInterface $reviewRequest): bool
    {
        foreach ($this->services as $service) {
            if (!$service->isEligible($reviewRequest)) {
                return false;
            }
        }

        return true;
    }
}
