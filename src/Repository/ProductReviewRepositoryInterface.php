<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Setono\SyliusReviewPlugin\Model\ProductReviewInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<ProductReviewInterface>
 */
interface ProductReviewRepositoryInterface extends RepositoryInterface
{
    /**
     * @return list<ProductReviewInterface>
     */
    public function findByOrder(OrderInterface $order): array;
}
