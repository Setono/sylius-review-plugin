<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Factory;

use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @extends FactoryInterface<ReviewRequestInterface>
 */
interface ReviewRequestFactoryInterface extends FactoryInterface
{
    public function createFromOrder(OrderInterface $order): ReviewRequestInterface;
}
