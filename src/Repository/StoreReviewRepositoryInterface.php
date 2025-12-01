<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Setono\SyliusReviewPlugin\Model\StoreReviewInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<StoreReviewInterface>
 */
interface StoreReviewRepositoryInterface extends RepositoryInterface
{
    public function findOneByOrder(OrderInterface $order): ?StoreReviewInterface;
}
