<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Setono\SyliusReviewPlugin\Model\ReviewRequestInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @extends RepositoryInterface<ReviewRequestInterface>
 */
interface ReviewRequestRepositoryInterface extends RepositoryInterface
{
    public function createForProcessingQueryBuilder(): QueryBuilder;

    /**
     * Removes all review requests created before the threshold
     */
    public function removeBefore(\DateTimeInterface $threshold): void;

    /**
     * Removes all review requests that are cancelled
     */
    public function removeCancelled(): void;

    public function hasExistingForOrder(OrderInterface $order): bool;
}
