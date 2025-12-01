<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Checker\ReviewableOrder;

use Sylius\Component\Core\Model\OrderInterface;

interface ReviewableOrderCheckerInterface
{
    public function check(OrderInterface $order): ReviewableOrderCheck;
}
