<?php

declare(strict_types=1);

namespace Setono\SyliusReviewPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;

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
}
