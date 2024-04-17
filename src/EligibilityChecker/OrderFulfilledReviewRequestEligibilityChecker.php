<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\EligibilityChecker;

use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Component\Order\Model\OrderInterface;

final class OrderFulfilledReviewRequestEligibilityChecker implements ReviewRequestEligibilityCheckerInterface
{
    public function check(ReviewRequestInterface $reviewRequest): EligibilityCheck
    {
        if ($reviewRequest->getOrder()?->getState() === OrderInterface::STATE_FULFILLED) {
            return EligibilityCheck::eligible();
        }

        return EligibilityCheck::ineligible('Order is not fulfilled');
    }
}
