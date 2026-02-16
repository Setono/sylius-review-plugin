<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\DataProvider;

use Sylius\Component\Core\Model\OrderInterface;

interface OrderForReviewRequestDataProviderInterface
{
    /**
     * @return iterable<OrderInterface>
     */
    public function getOrders(): iterable;
}
