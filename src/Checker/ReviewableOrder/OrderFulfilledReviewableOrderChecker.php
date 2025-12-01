<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Checker\ReviewableOrder;

use Sylius\Component\Core\Model\OrderInterface;

final class OrderFulfilledReviewableOrderChecker implements ReviewableOrderCheckerInterface
{
    public function check(OrderInterface $order): ReviewableOrderCheck
    {
        if ($order->getState() === OrderInterface::STATE_FULFILLED) {
            return ReviewableOrderCheck::reviewable();
        }

        return ReviewableOrderCheck::notReviewable('setono_sylius_review.ui.order_not_fulfilled');
    }
}
