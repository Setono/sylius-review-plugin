<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Factory;

use Setono\SyliusReviewPlugin\Model\ReviewInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @extends FactoryInterface<ReviewInterface>
 */
interface ReviewFactoryInterface extends FactoryInterface
{
    public function createFromOrder(OrderInterface $order): ReviewInterface;
}
