<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Checker\ReviewableOrder;

use Sylius\Component\Core\Model\OrderInterface;

final class OrderFulfilledReviewableOrderChecker implements ReviewableOrderCheckerInterface
{
    /**
     * @param list<string> $reviewableStates
     */
    public function __construct(
        private readonly array $reviewableStates = [OrderInterface::STATE_FULFILLED],
    ) {
    }

    public function check(OrderInterface $order): ReviewableOrderCheck
    {
        if (in_array($order->getState(), $this->reviewableStates, true)) {
            return ReviewableOrderCheck::reviewable();
        }

        return ReviewableOrderCheck::notReviewable('setono_sylius_review.ui.order_not_fulfilled');
    }
}
