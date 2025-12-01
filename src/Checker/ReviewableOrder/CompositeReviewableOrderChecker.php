<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Checker\ReviewableOrder;

use Setono\CompositeCompilerPass\CompositeService;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @extends CompositeService<ReviewableOrderCheckerInterface>
 */
final class CompositeReviewableOrderChecker extends CompositeService implements ReviewableOrderCheckerInterface
{
    public function check(OrderInterface $order): ReviewableOrderCheck
    {
        foreach ($this->services as $service) {
            $check = $service->check($order);
            if (!$check->reviewable) {
                return $check;
            }
        }

        return ReviewableOrderCheck::reviewable();
    }
}
