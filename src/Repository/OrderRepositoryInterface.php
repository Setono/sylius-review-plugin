<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface as BaseOrderRepositoryInterface;

/**
 * @extends BaseOrderRepositoryInterface<OrderInterface>
 */
interface OrderRepositoryInterface extends BaseOrderRepositoryInterface
{
    public function findLatestByCustomer(CustomerInterface $customer): ?OrderInterface;
}
