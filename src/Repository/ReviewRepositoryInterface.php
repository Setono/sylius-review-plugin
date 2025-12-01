<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Setono\SyliusReviewPlugin\Model\ReviewInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<ReviewInterface>
 */
interface ReviewRepositoryInterface extends RepositoryInterface
{
    public function findOneByOrder(OrderInterface $order): ?ReviewInterface;
}
