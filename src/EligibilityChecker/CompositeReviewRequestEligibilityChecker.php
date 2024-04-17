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
    public function check(ReviewRequestInterface $reviewRequest): EligibilityCheck
    {
        foreach ($this->services as $service) {
            $check = $service->check($reviewRequest);
            if (!$check->eligible) {
                return $check;
            }
        }

        return EligibilityCheck::eligible();
    }
}
